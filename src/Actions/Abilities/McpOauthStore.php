<?php // phpcs:ignore

namespace SEOPress\Actions\Abilities;

defined( 'ABSPATH' ) || exit;

/**
 * Where the MCP OAuth server keeps clients, authorization codes and tokens.
 *
 * Three stores, chosen one by one rather than by reflex.
 *
 * - Clients live in a single non-autoloaded option. A client registration is
 *   site-level, not user-level: an MCP client registers itself before anybody
 *   has consented, so there is no user to hang it off. There are a handful of
 *   them on a real site, they are read once per authorization, and an option
 *   needs no schema, no dbDelta and no upgrade routine.
 * - Authorization codes live in transients. They are valid for one minute and
 *   for one exchange, which is exactly what a transient already expresses, and
 *   the expiry is enforced by WordPress rather than by code that could forget.
 * - Access and refresh tokens live in user meta on the user they belong to,
 *   the way WordPress core stores Application Passwords. That makes "which
 *   clients is this account connected to" one read, revoking a connection one
 *   write, and deleting a user take their tokens with them.
 *
 * Nothing that is a credential is stored as it was issued. Client secrets,
 * access tokens and refresh tokens are hashed with wp_fast_hash(), the same
 * function core hashes Application Passwords with since 6.8. An authorization
 * code is never written down at all: its transient key is an HMAC of it, so
 * the store can find the record without holding the code.
 *
 * A token string carries the id of the user it was issued to, in clear, in
 * front of its random part. That is the same shape as an Application Password,
 * where the login travels in the Basic credential beside the secret: the id is
 * an identifier, not a secret, and it is what lets the server find the one
 * user meta row to verify against instead of scanning every user.
 *
 * @since 10.3.0
 */
class McpOauthStore {

	/** Locks held by this request, including nested store operations. */
	private static $locks = array();

	/**
	 * Serialize a read/modify/write operation in the database, including when
	 * persistent object caching is enabled. Database locks are released on
	 * connection loss and never expire while an operation is still running.
	 * A failed lock fails closed; no credential mutation is attempted.
	 *
	 * @param string   $resource Database-qualified resource identifier.
	 * @param callable $operation Internal store operation.
	 * @return mixed|null Null when the lock could not be acquired.
	 */
	private static function synchronized( $resource, $operation ) {
		global $wpdb;
		// MySQL before 5.7.5 releases the first named lock when acquiring a
		// second one. Use one reentrant lock for this database on those servers,
		// including across multisite blogs, whose user token records are shared.
		if ( version_compare( (string) $wpdb->db_version(), '5.7.5', '<' ) ) {
			$resource = 'legacy_oauth_store';
		}
		$key = 'seopress_mcp_' . md5( DB_NAME . ':' . $resource );
		if ( isset( self::$locks[ $key ] ) ) {
			return $operation();
		}
		if ( '1' !== (string) $wpdb->get_var( $wpdb->prepare( 'SELECT GET_LOCK(%s, 5)', $key ) ) ) {
			return null;
		}
		self::$locks[ $key ] = true;
		try {
			return $operation();
		} finally {
			unset( self::$locks[ $key ] );
			$wpdb->get_var( $wpdb->prepare( 'SELECT RELEASE_LOCK(%s)', $key ) );
		}
	}

	/** Run a token mutation against fresh user metadata under the account lock. */
	private static function mutate_tokens( $user_id, $operation ) {
		global $wpdb;
		return self::synchronized( $wpdb->usermeta . ':' . (int) $user_id, function () use ( $user_id, $operation ) {
			wp_cache_delete( (int) $user_id, 'user_meta' );
			return $operation();
		} );
	}

	/** Serialize registry changes and discard request-local option snapshots. */
	private static function mutate_clients( $operation ) {
		global $wpdb;
		return self::synchronized( $wpdb->options . ':' . self::CLIENTS_OPTION, function () use ( $operation ) {
			wp_cache_delete( self::CLIENTS_OPTION, 'options' );
			wp_cache_delete( 'notoptions', 'options' );
			return $operation();
		} );
	}

	/**
	 * Rate-limit public registration in a bounded set of expiring buckets.
	 * Only the direct peer address is trusted, never a forwarded header.
	 * Registry locking makes the read/increment atomic with persistent caches.
	 */
	public static function allow_registration( $address ) {
		return self::mutate_clients( function () use ( $address ) {
			$bucket = substr( hash_hmac( 'sha256', (string) $address, wp_salt( 'auth' ) ), 0, 2 );
			$key = 'seopress_mcp_register_' . $bucket;
			wp_cache_delete( '_transient_' . $key, 'options' );
			wp_cache_delete( '_transient_timeout_' . $key, 'options' );
			$state = get_transient( $key );
			if ( ! is_array( $state ) || $state['until'] <= time() ) {
				$state = array( 'count' => 0, 'until' => time() + 60 );
			}
			if ( $state['count'] >= 10 ) {
				return false;
			}
			++$state['count'];
			set_transient( $key, $state, max( 1, $state['until'] - time() ) );
			return true;
		} );
	}

	/** Maintain live connection references without ever storing token secrets. */
	public static function track_connection( $client_id, $uuid, $expires ) {
		return self::mutate_clients( function () use ( $client_id, $uuid, $expires ) {
			$clients = self::get_clients();
			if ( ! isset( $clients[ $client_id ] ) ) {
				return false;
			}
			$client = $clients[ $client_id ];
			$connections = isset( $client['connections'] ) ? $client['connections'] : self::legacy_connections( $client_id );
			$connections = array_filter( $connections, function ( $expiry ) { return $expiry > time(); } );
			if ( $expires > time() ) {
				$connections[ $uuid ] = $expires;
				$client['last_used'] = time();
			} else {
				unset( $connections[ $uuid ] );
			}
			if ( empty( $connections ) ) {
				unset( $clients[ $client_id ] );
			} else {
				$client['connections'] = $connections;
				$clients[ $client_id ] = $client;
			}
			self::save_clients( $clients );
			return true;
		} );
	}

	/** Index pre-upgrade connections once, before removing a legacy registration. */
	private static function legacy_connections( $client_id ) {
		global $wpdb;
		$rows = $wpdb->get_col( $wpdb->prepare(
			"SELECT meta_value FROM {$wpdb->usermeta} WHERE meta_key = %s AND meta_value LIKE %s",
			self::TOKENS_META_KEY,
			'%' . $wpdb->esc_like( serialize( (string) $client_id ) ) . '%'
		) );
		$connections = array();
		foreach ( $rows as $row ) {
			$records = maybe_unserialize( $row );
			foreach ( is_array( $records ) ? $records : array() as $record ) {
				if ( isset( $record['client_id'], $record['uuid'], $record['refresh_expires'] ) && $client_id === $record['client_id'] && $record['refresh_expires'] > time() ) {
					$connections[ $record['uuid'] ] = $record['refresh_expires'];
				}
			}
		}
		return $connections;
	}

	/**
	 * Option holding every registered OAuth client, keyed by client id.
	 *
	 * @var string
	 */
	const CLIENTS_OPTION = 'seopress_mcp_oauth_clients';

	/**
	 * User meta key holding one user's OAuth tokens.
	 *
	 * @var string
	 */
	const TOKENS_META_KEY = '_seopress_mcp_oauth_tokens';

	/**
	 * Transient name prefix for a pending authorization code.
	 *
	 * @var string
	 */
	const CODE_TRANSIENT_PREFIX = 'seopress_mcp_code_';

	/**
	 * The single scope this server issues.
	 *
	 * One scope, covering the MCP endpoint as a whole. What a token may
	 * actually do is decided by the WordPress capabilities of the user it was
	 * issued to, not by the scope string.
	 *
	 * @var string
	 */
	const SCOPE = 'mcp';

	/**
	 * The narrower scope: the read-only tools and nothing else.
	 *
	 * A connection is granted this when the person approving it chose read-only
	 * on the consent screen. It lives on the token rather than in a setting,
	 * because that is the only place no client can argue with: a client that
	 * ignores the readOnlyHint annotation, as some do, is not served the write
	 * tools at all and is refused if it calls one by name.
	 *
	 * @since 10.3.0
	 * @var string
	 */
	const SCOPE_READ = 'mcp.read';

	/**
	 * How long an authorization code stays exchangeable, in seconds.
	 *
	 * @var int
	 */
	const CODE_TTL = 60;

	/**
	 * How long an access token stays valid, in seconds.
	 *
	 * @var int
	 */
	const ACCESS_TOKEN_TTL = 3600;

	/**
	 * How long a refresh token stays valid, in seconds.
	 *
	 * @var int
	 */
	const REFRESH_TOKEN_TTL = 2592000;

	/**
	 * Prefix identifying an access token.
	 *
	 * @var string
	 */
	const ACCESS_TOKEN_PREFIX = 'seopress_mcp_at_';

	/**
	 * Prefix identifying a refresh token.
	 *
	 * @var string
	 */
	const REFRESH_TOKEN_PREFIX = 'seopress_mcp_rt_';

	/**
	 * How many clients may be registered at once.
	 *
	 * Registration is open, as RFC 7591 intends it for MCP, so it needs a
	 * ceiling: without one, anybody who can reach the site could grow an
	 * option without limit.
	 *
	 * @var int
	 */
	const MAX_CLIENTS = 50;

	/**
	 * How many live tokens one account may hold.
	 *
	 * @var int
	 */
	const MAX_TOKENS_PER_USER = 25;

	/**
	 * How long an unused client registration is kept before it can be pruned.
	 *
	 * @var int
	 */
	const UNUSED_CLIENT_TTL = 86400;

	/**
	 * Hash a credential for storage.
	 *
	 * Every value handed to this method is generated here from random_bytes(),
	 * so the entropy requirement wp_fast_hash() documents is met.
	 *
	 * @since 10.3.0
	 *
	 * @param string $secret The credential as it was issued.
	 *
	 * @return string
	 */
	public static function hash_secret( $secret ) {
		return wp_fast_hash( (string) $secret );
	}

	/**
	 * Whether a credential matches a stored hash.
	 *
	 * @since 10.3.0
	 *
	 * @param string $secret The credential presented by the client.
	 * @param string $hash   The stored hash.
	 *
	 * @return bool
	 */
	public static function verify_secret( $secret, $hash ) {
		if ( ! is_string( $hash ) || '' === $hash ) {
			return false;
		}

		return wp_verify_fast_hash( (string) $secret, $hash );
	}

	/**
	 * A fresh random string, hex encoded.
	 *
	 * @since 10.3.0
	 *
	 * @param int $bytes How many random bytes to draw.
	 *
	 * @return string
	 */
	public static function random( $bytes = 32 ) {
		return bin2hex( random_bytes( (int) $bytes ) );
	}

	/* ---------------------------------------------------------------------
	 * Clients
	 * ------------------------------------------------------------------ */

	/**
	 * Every registered client, keyed by client id.
	 *
	 * @since 10.3.0
	 *
	 * @return array[]
	 */
	public static function get_clients() {
		$clients = get_option( self::CLIENTS_OPTION, array() );

		return is_array( $clients ) ? $clients : array();
	}

	/**
	 * Replace the whole client list.
	 *
	 * @since 10.3.0
	 *
	 * @param array[] $clients The clients, keyed by client id.
	 *
	 * @return void
	 */
	protected static function save_clients( $clients ) {
		update_option( self::CLIENTS_OPTION, $clients, false );
	}

	/**
	 * One registered client.
	 *
	 * @since 10.3.0
	 *
	 * @param string $client_id The client id.
	 *
	 * @return array|null
	 */
	public static function get_client( $client_id ) {
		$clients = self::get_clients();

		if ( ! is_string( $client_id ) || '' === $client_id || ! isset( $clients[ $client_id ] ) ) {
			return null;
		}

		return $clients[ $client_id ];
	}

	/**
	 * Store a client registration.
	 *
	 * @since 10.3.0
	 *
	 * @param array $client The client record.
	 *
	 * @return bool|null Whether the registration was saved.
	 */
	public static function put_client( $client ) {
		return self::mutate_clients( function () use ( $client ) {
			self::prune_clients();
			$clients = self::get_clients();
			if ( ! isset( $clients[ $client['client_id'] ] ) && count( $clients ) >= self::MAX_CLIENTS ) {
				$pending = array_filter( $clients, function ( $entry ) { return empty( $entry['last_used'] ); } );
				if ( empty( $pending ) ) {
					return false;
				}
				// An unapproved registration cannot reserve a slot indefinitely.
				// Evict only the oldest pending client, never an approved client.
				uasort( $pending, function ( $a, $b ) { return $a['created'] <=> $b['created']; } );
				unset( $clients[ key( $pending ) ] );
			}
			$clients[ $client['client_id'] ] = $client;
			self::save_clients( $clients );
			return true;
		} );
	}

	/**
	 * Forget a client registration.
	 *
	 * @since 10.3.0
	 *
	 * @param string $client_id The client id.
	 *
	 * @return bool Whether something was removed.
	 */
	public static function delete_client( $client_id ) {
		return self::mutate_clients( function () use ( $client_id ) {
			$clients = self::get_clients();

			if ( ! isset( $clients[ $client_id ] ) ) {
				return false;
			}

			unset( $clients[ $client_id ] );
			self::save_clients( $clients );

			return true;
		} );
	}

	/**
	 * Record that a client was actually used to obtain an authorization.
	 *
	 * Only a used client is kept past the pruning window, so a burst of
	 * registrations that nobody ever consented to cannot fill the option.
	 *
	 * @since 10.3.0
	 *
	 * @param string $client_id The client id.
	 *
	 * @return void
	 */
	public static function touch_client( $client_id ) {
		return self::mutate_clients( function () use ( $client_id ) {
			$clients = self::get_clients();

			if ( ! isset( $clients[ $client_id ] ) ) {
				return;
			}

			$clients[ $client_id ]['last_used'] = time();

			self::save_clients( $clients );
		} );
	}

	/**
	 * Drop expired registrations, retaining live connections and pending consent codes.
	 *
	 * @since 10.3.0
	 *
	 * @return int How many registrations were dropped.
	 */
	public static function prune_clients() {
		return self::mutate_clients( function () {
			$clients = self::get_clients();
			$cutoff  = time() - self::UNUSED_CLIENT_TTL;
			$kept    = array();
			$dropped = 0;

			foreach ( $clients as $client_id => $client ) {
				$created   = isset( $client['created'] ) ? (int) $client['created'] : 0;
				$last_used = isset( $client['last_used'] ) ? (int) $client['last_used'] : 0;

				$live = isset( $client['connections'] ) ? array_filter( $client['connections'], function ( $expiry ) { return $expiry > time(); } ) : null;
				if ( ( null !== $live && empty( $live ) && $last_used > 0 && $last_used + self::CODE_TTL < time() )
					|| ( 0 === $last_used && $created < $cutoff )
					|| ( $last_used > 0 && $last_used + self::REFRESH_TOKEN_TTL + self::CODE_TTL < time() ) ) {
					++$dropped;
					continue;
				}

				$kept[ $client_id ] = $client;
			}

			if ( $dropped > 0 ) {
				self::save_clients( $kept );
			}

			return $dropped;
		} );
	}

	/* ---------------------------------------------------------------------
	 * Authorization codes
	 * ------------------------------------------------------------------ */

	/**
	 * The transient name a code is filed under.
	 *
	 * An HMAC keyed with this site's auth salt, so a dump of the options table
	 * cannot be turned back into a usable code, and so a code from one site is
	 * meaningless on another.
	 *
	 * @since 10.3.0
	 *
	 * @param string $code The authorization code.
	 *
	 * @return string
	 */
	protected static function code_key( $code ) {
		return self::CODE_TRANSIENT_PREFIX . hash_hmac( 'sha256', (string) $code, wp_salt( 'auth' ) );
	}

	/**
	 * Mint an authorization code for a consent that was just given.
	 *
	 * @since 10.3.0
	 *
	 * @param array $payload What the code stands for: user_id, client_id,
	 *                       redirect_uri, code_challenge, resource, scope.
	 *
	 * @return string The code, which is the only copy that ever exists.
	 */
	public static function create_authorization_code( $payload ) {
		$code = self::random( 32 );

		$payload['issued'] = time();

		set_transient( self::code_key( $code ), $payload, self::CODE_TTL );

		return $code;
	}

	/**
	 * Read an authorization code and burn it.
	 *
	 * Read and deleted under one database lock, so only one exchange can consume it.
	 *
	 * @since 10.3.0
	 *
	 * @param string $code The code presented at the token endpoint.
	 *
	 * @return array|null
	 */
	public static function consume_authorization_code( $code ) {
		global $wpdb;
		if ( ! is_string( $code ) || '' === $code || strlen( $code ) > 128 ) {
			return null;
		}
		$key = self::code_key( $code );
		return self::synchronized( $wpdb->options . ':' . $key, function () use ( $key ) {
			wp_cache_delete( '_transient_' . $key, 'options' );
			wp_cache_delete( '_transient_timeout_' . $key, 'options' );
			$payload = get_transient( $key );
			if ( ! delete_transient( $key ) ) {
				return null;
			}
			if ( ! is_array( $payload ) || empty( $payload['issued'] ) || time() - (int) $payload['issued'] > self::CODE_TTL ) {
				return null;
			}
			return $payload;
		} );
	}

	/* ---------------------------------------------------------------------
	 * Tokens
	 * ------------------------------------------------------------------ */

	/**
	 * Every token record held by one account.
	 *
	 * @since 10.3.0
	 *
	 * @param int $user_id The user id.
	 *
	 * @return array[]
	 */
	public static function get_user_tokens( $user_id ) {
		$records = get_user_meta( (int) $user_id, self::TOKENS_META_KEY, true );

		return is_array( $records ) ? array_values( $records ) : array();
	}

	/**
	 * Replace the token records of one account.
	 *
	 * @since 10.3.0
	 *
	 * @param int     $user_id The user id.
	 * @param array[] $records The records to keep.
	 *
	 * @return bool Whether the token mutation was persisted.
	 */
	public static function save_user_tokens( $user_id, $records ) {
		$records = array_values( $records );

		if ( empty( $records ) ) {
			return delete_user_meta( (int) $user_id, self::TOKENS_META_KEY );
		}

		return false !== update_user_meta( (int) $user_id, self::TOKENS_META_KEY, $records );
	}

	/**
	 * Drop the records of one account whose refresh token has expired.
	 *
	 * A record outlives its access token on purpose: the refresh token is what
	 * keeps the connection alive, so a record is only worthless once that has
	 * expired too.
	 *
	 * @since 10.3.0
	 *
	 * @param int $user_id The user id.
	 *
	 * @return int How many records were dropped.
	 */
	public static function prune_user_tokens( $user_id ) {
		return self::mutate_tokens( $user_id, function () use ( $user_id ) {
			$records = self::get_user_tokens( $user_id );
			$now     = time();
			$kept    = array();

			foreach ( $records as $record ) {
				$refresh_expires = isset( $record['refresh_expires'] ) ? (int) $record['refresh_expires'] : 0;

				if ( $refresh_expires > 0 && $refresh_expires <= $now ) {
					continue;
				}

				$kept[] = $record;
			}

			$dropped = count( $records ) - count( $kept );

			if ( $dropped > 0 ) {
				self::save_user_tokens( $user_id, $kept );
			}

			return $dropped;
		} );
	}

	/**
	 * The scope to store, out of whatever was asked for.
	 *
	 * Anything this server does not issue falls back to the read-only scope,
	 * never to the full one: a typo, an old record or a value from somewhere
	 * else must not be able to widen a connection.
	 *
	 * @since 10.3.0
	 *
	 * @param string $scope The scope asked for.
	 *
	 * @return string
	 */
	public static function normalize_scope( $scope ) {
		return self::SCOPE === (string) $scope ? self::SCOPE : self::SCOPE_READ;
	}

	/**
	 * Build a token string carrying the user it belongs to.
	 *
	 * @since 10.3.0
	 *
	 * @param string $prefix  One of the *_TOKEN_PREFIX constants.
	 * @param int    $user_id The user the token is issued to.
	 *
	 * @return string
	 */
	protected static function build_token( $prefix, $user_id ) {
		return $prefix . (int) $user_id . '_' . self::random( 32 );
	}

	/** A refresh token authenticates its family even after its secret has rotated. */
	protected static function build_refresh_token( $user_id, $uuid ) {
		$value = self::REFRESH_TOKEN_PREFIX . (int) $user_id . '_' . $uuid . '_' . self::random( 32 );
		return $value . '_' . hash_hmac( 'sha256', $value, wp_salt( 'auth' ) );
	}

	/** Validate the signed family identifier; never trust a caller-supplied UUID. */
	private static function refresh_family( $token ) {
		if ( ! is_string( $token ) || ! preg_match( '/^seopress_mcp_rt_([1-9][0-9]*)_([a-f0-9-]{36})_([a-f0-9]{64})_([a-f0-9]{64})$/D', $token, $parts ) ) {
			return null;
		}
		$value = substr( $token, 0, -65 );
		if ( ! hash_equals( hash_hmac( 'sha256', $value, wp_salt( 'auth' ) ), $parts[4] ) ) {
			return null;
		}
		return array( 'user_id' => (int) $parts[1], 'uuid' => $parts[2] );
	}

	/**
	 * Split a token string back into its prefix, user id and random part.
	 *
	 * @since 10.3.0
	 *
	 * @param string $prefix One of the *_TOKEN_PREFIX constants.
	 * @param string $token  The token presented by the client.
	 *
	 * @return int The user id, or 0 when the string is not one of ours.
	 */
	public static function token_user_id( $prefix, $token ) {
		if ( ! is_string( $token ) || '' === $token ) {
			return 0;
		}

		if ( self::REFRESH_TOKEN_PREFIX === $prefix ) {
			$family = self::refresh_family( $token );
			if ( null !== $family ) {
				return $family['user_id'];
			}
		}

		$pattern = '#^' . preg_quote( $prefix, '#' ) . '([1-9][0-9]*)_([0-9a-f]{64})$#';

		if ( 1 !== preg_match( $pattern, $token, $matches ) ) {
			return 0;
		}

		return (int) $matches[1];
	}

	/**
	 * Issue an access token and a refresh token for one consent.
	 *
	 * @since 10.3.0
	 *
	 * @param int    $user_id     The user the tokens act as.
	 * @param string $client_id   The client the tokens were issued to.
	 * @param string $client_name The client name, snapshot for the settings screen.
	 * @param string $resource    The MCP server URI these tokens are for.
	 *
	 * @return array {
	 *     @type string $access_token  The access token, returned once.
	 *     @type string $refresh_token The refresh token, returned once.
	 *     @type int    $expires_in    Access token lifetime in seconds.
	 *     @type array  $record        The stored record.
	 * }
	 */
	public static function issue_tokens( $user_id, $client_id, $client_name, $resource, $scope = self::SCOPE ) {
		return self::mutate_tokens( $user_id, function () use ( $user_id, $client_id, $client_name, $resource, $scope ) {
			$user_id = (int) $user_id;
			$now     = time();

			$uuid    = wp_generate_uuid4();
			$access  = self::build_token( self::ACCESS_TOKEN_PREFIX, $user_id );
			$refresh = self::build_refresh_token( $user_id, $uuid );

			$record = array(
				'uuid'            => $uuid,
				'client_id'       => (string) $client_id,
				'client_name'     => (string) $client_name,
				'resource'        => (string) $resource,
				'scope'           => self::normalize_scope( $scope ),
				'access_hash'     => self::hash_secret( $access ),
				'access_expires'  => $now + self::ACCESS_TOKEN_TTL,
				'refresh_hash'    => self::hash_secret( $refresh ),
				'refresh_expires' => $now + self::REFRESH_TOKEN_TTL,
				'created'         => $now,
				'last_used'       => 0,
			);

			self::prune_user_tokens( $user_id );

			$records   = self::get_user_tokens( $user_id );
			$records[] = $record;

			// Oldest first out, so a client that keeps reconnecting cannot grow the
			// meta row without end.
			$evicted = array();
			if ( count( $records ) > self::MAX_TOKENS_PER_USER ) {
				$evicted = array_slice( $records, 0, count( $records ) - self::MAX_TOKENS_PER_USER );
				$records = array_slice( $records, count( $records ) - self::MAX_TOKENS_PER_USER );
			}

			if ( true !== self::track_connection( $client_id, $uuid, $record['refresh_expires'] ) ) {
				return null;
			}
			if ( ! self::save_user_tokens( $user_id, $records ) ) {
				self::track_connection( $client_id, $uuid, 0 );
				return null;
			}
			foreach ( $evicted as $old_record ) {
				self::track_connection( $old_record['client_id'], $old_record['uuid'], 0 );
			}

			return array(
				'access_token'  => $access,
				'refresh_token' => $refresh,
				'expires_in'    => self::ACCESS_TOKEN_TTL,
				'record'        => $record,
			);
		} );
	}

	/**
	 * Replace the tokens of an existing record, rotating the refresh token.
	 *
	 * OAuth 2.1 requires refresh token rotation for a public client, so the old
	 * refresh token stops working the moment a new one is handed out.
	 *
	 * @since 10.3.0
	 *
	 * @param int    $user_id The user the record belongs to.
	 * @param string $uuid    The record identifier.
	 * @param string $refresh_token The presented token, rechecked under the account lock.
	 *
	 * @return array|null The same shape as issue_tokens(), or null when gone.
	 */
	public static function rotate_tokens( $user_id, $uuid, $refresh_token ) {
		return self::mutate_tokens( $user_id, function () use ( $user_id, $uuid, $refresh_token ) {
			$user_id = (int) $user_id;
			$records = self::get_user_tokens( $user_id );
			$now     = time();

			foreach ( $records as $index => $record ) {
				if ( ! isset( $record['uuid'] ) || $record['uuid'] !== $uuid ) {
					continue;
				}

				if ( ! self::verify_secret( $refresh_token, $record['refresh_hash'] ) ) {
					$family = self::refresh_family( $refresh_token );
					if ( ( null !== $family && $family['user_id'] === $user_id && $family['uuid'] === $uuid )
						|| ( isset( $record['legacy_refresh_hash'] ) && self::verify_secret( $refresh_token, $record['legacy_refresh_hash'] ) ) ) {
						self::revoke_connection( $user_id, $uuid );
					}
					return null;
				}
				if ( empty( $record['refresh_expires'] ) || (int) $record['refresh_expires'] <= $now ) {
					self::revoke_connection( $user_id, $uuid );
					return null;
				}

				$access  = self::build_token( self::ACCESS_TOKEN_PREFIX, $user_id );
				$refresh = self::build_refresh_token( $user_id, $uuid );

				// Keep the one pre-upgrade token traceable; signed tokens need no history.
				if ( null === self::refresh_family( $refresh_token ) ) {
					$record['legacy_refresh_hash'] = $record['refresh_hash'];
				}
				$record['access_hash']     = self::hash_secret( $access );
				$record['access_expires']  = $now + self::ACCESS_TOKEN_TTL;
				$record['refresh_hash']    = self::hash_secret( $refresh );
				$record['refresh_expires'] = $now + self::REFRESH_TOKEN_TTL;

				$records[ $index ] = $record;

				if ( true !== self::track_connection( $record['client_id'], $uuid, $record['refresh_expires'] ) ) {
					return null;
				}
				if ( ! self::save_user_tokens( $user_id, $records ) ) {
					return null;
				}

				return array(
					'access_token'  => $access,
					'refresh_token' => $refresh,
					'expires_in'    => self::ACCESS_TOKEN_TTL,
					'record'        => $record,
				);
			}

			return null;
		} );
	}

	/**
	 * Find the record an access token belongs to, without checking expiry.
	 *
	 * Expiry is the caller's decision so the caller can tell "no such token"
	 * from "this token has expired" and answer accordingly.
	 *
	 * @since 10.3.0
	 *
	 * @param string $token The access token presented by the client.
	 *
	 * @return array|null {
	 *     @type int   $user_id The user the token acts as.
	 *     @type array $record  The stored record.
	 * }
	 */
	public static function find_access_token( $token ) {
		return self::find_token( self::ACCESS_TOKEN_PREFIX, 'access_hash', $token );
	}

	/**
	 * Find the family of a current or spent refresh token, without authorizing it.
	 *
	 * @since 10.3.0
	 *
	 * @param string $token The refresh token presented by the client.
	 *
	 * @return array|null
	 */
	public static function find_refresh_token( $token ) {
		$found = self::find_token( self::REFRESH_TOKEN_PREFIX, 'refresh_hash', $token );
		if ( null === $found ) {
			$found = self::find_token( self::REFRESH_TOKEN_PREFIX, 'legacy_refresh_hash', $token );
		}
		if ( null !== $found ) {
			return $found;
		}
		$family = self::refresh_family( $token );
		if ( null !== $family ) {
			foreach ( self::get_user_tokens( $family['user_id'] ) as $record ) {
				if ( $record['uuid'] === $family['uuid'] ) {
					return array( 'user_id' => $family['user_id'], 'record' => $record );
				}
			}
		}
		return null;
	}

	/**
	 * Shared lookup behind find_access_token() and find_refresh_token().
	 *
	 * @since 10.3.0
	 *
	 * @param string $prefix    One of the *_TOKEN_PREFIX constants.
	 * @param string $hash_key  Which hash on the record to verify against.
	 * @param string $token     The token presented by the client.
	 *
	 * @return array|null
	 */
	protected static function find_token( $prefix, $hash_key, $token ) {
		$user_id = self::token_user_id( $prefix, $token );

		if ( $user_id <= 0 ) {
			return null;
		}

		foreach ( self::get_user_tokens( $user_id ) as $record ) {
			if ( empty( $record[ $hash_key ] ) ) {
				continue;
			}

			if ( self::verify_secret( $token, $record[ $hash_key ] ) ) {
				return array(
					'user_id' => $user_id,
					'record'  => $record,
				);
			}
		}

		return null;
	}

	/**
	 * Note that a token was just used.
	 *
	 * @since 10.3.0
	 *
	 * @param int    $user_id The user the record belongs to.
	 * @param string $uuid    The record identifier.
	 *
	 * @return void
	 */
	public static function record_usage( $user_id, $uuid ) {
		foreach ( self::get_user_tokens( $user_id ) as $record ) {
			if ( isset( $record['uuid'], $record['last_used'] ) && $record['uuid'] === $uuid && time() - (int) $record['last_used'] < 300 ) {
				return;
			}
		}
		return self::mutate_tokens( $user_id, function () use ( $user_id, $uuid ) {
			$records = self::get_user_tokens( $user_id );
			$changed = false;

			foreach ( $records as $index => $record ) {
				if ( ! isset( $record['uuid'] ) || $record['uuid'] !== $uuid ) {
					continue;
				}

				if ( isset( $record['last_used'] ) && time() - (int) $record['last_used'] < 300 ) {
					return;
				}
				$records[ $index ]['last_used'] = time();
				$changed                        = true;
				break;
			}

			if ( $changed ) {
				self::save_user_tokens( $user_id, $records );
			}
		} );
	}

	/**
	 * Delete one connection, which revokes both of its tokens at once.
	 *
	 * @since 10.3.0
	 *
	 * @param int    $user_id The user the record belongs to.
	 * @param string $uuid    The record identifier.
	 *
	 * @return bool|null Whether something was revoked, or null on a storage failure.
	 */
	public static function revoke_connection( $user_id, $uuid ) {
		return self::mutate_tokens( $user_id, function () use ( $user_id, $uuid ) {
			$records = self::get_user_tokens( $user_id );
			$kept    = array();

			foreach ( $records as $record ) {
				if ( isset( $record['uuid'] ) && $record['uuid'] === $uuid ) {
					$client_id = $record['client_id'];
					continue;
				}

				$kept[] = $record;
			}

			if ( count( $kept ) === count( $records ) ) {
				return false;
			}

			if ( ! self::save_user_tokens( $user_id, $kept ) ) {
				return null;
			}
			self::track_connection( $client_id, $uuid, 0 );

			return true;
		} );
	}

	/**
	 * Revoke whichever connection a token string belongs to.
	 *
	 * @since 10.3.0
	 *
	 * @param string $token An access token or a refresh token.
	 *
	 * @return bool|null Whether something was revoked, or null on a storage failure.
	 */
	public static function revoke_token( $token ) {
		$found = self::find_access_token( $token );

		if ( null === $found ) {
			$found = self::find_refresh_token( $token );
		}

		if ( null === $found ) {
			return false;
		}

		return self::revoke_connection( $found['user_id'], $found['record']['uuid'] );
	}

	/**
	 * The live connections of one account, for the settings screen.
	 *
	 * No hash is ever included: this is what the site owner reads, not what the
	 * server verifies against.
	 *
	 * @since 10.3.0
	 *
	 * @param int $user_id The user id.
	 *
	 * @return array[]
	 */
	public static function connections_for_user( $user_id ) {
		self::prune_user_tokens( $user_id );

		$connections = array();

		foreach ( self::get_user_tokens( $user_id ) as $record ) {
			$client = self::get_client( isset( $record['client_id'] ) ? $record['client_id'] : '' );

			$connections[] = array(
				'uuid'        => isset( $record['uuid'] ) ? (string) $record['uuid'] : '',
				'client_id'   => isset( $record['client_id'] ) ? (string) $record['client_id'] : '',
				'client_name' => self::connection_label( $record, $client ),
				'client_uri'  => ( is_array( $client ) && ! empty( $client['client_uri'] ) ) ? (string) $client['client_uri'] : '',
				'scope'       => isset( $record['scope'] ) ? (string) $record['scope'] : self::SCOPE,
				// A token is bound to the address the site had when it was
				// issued. Change the site address and every token issued for
				// the old one is refused with invalid_audience, so listing it
				// as connected would be listing something this server will not
				// accept. It stays in the list, because the owner still has to
				// be able to revoke it, but it says what it is.
				'stale'       => ! McpOauth::resources_match(
					isset( $record['resource'] ) ? $record['resource'] : '',
					McpOauth::canonical_resource()
				),
				'resource'    => isset( $record['resource'] ) ? (string) $record['resource'] : '',
				'created'     => isset( $record['created'] ) ? (int) $record['created'] : 0,
				'last_used'   => isset( $record['last_used'] ) ? (int) $record['last_used'] : 0,
				'expires'     => isset( $record['refresh_expires'] ) ? (int) $record['refresh_expires'] : 0,
			);
		}

		return $connections;
	}

	/**
	 * What to call a connection on screen.
	 *
	 * @since 10.3.0
	 *
	 * @param array      $record The token record.
	 * @param array|null $client The client registration, when it still exists.
	 *
	 * @return string
	 */
	protected static function connection_label( $record, $client ) {
		if ( is_array( $client ) && ! empty( $client['client_name'] ) ) {
			return (string) $client['client_name'];
		}

		if ( ! empty( $record['client_name'] ) ) {
			return (string) $record['client_name'];
		}

		return __( 'Unnamed client', 'wp-seopress' );
	}
}
