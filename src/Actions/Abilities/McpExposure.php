<?php // phpcs:ignore

namespace SEOPress\Actions\Abilities;

defined( 'ABSPATH' ) || exit;

/**
 * Decide which registered abilities the SEOPress MCP server is allowed to serve.
 *
 * The Abilities API is a site-wide registry: any plugin can register an ability
 * and flag it as an MCP tool. Serving every one of them would mean the SEOPress
 * setting labelled "expose abilities" silently hands an AI client whatever other
 * plugins happen to expose, including product and order write tools. That is a
 * decision the site owner has to make on purpose, not a side effect of turning
 * SEOPress's own SEO tools on.
 *
 * So the MCP server is scoped to the "seopress" ability namespace by default.
 * Abilities from other namespaces are only served when the site owner turns on
 * the separate opt-in, or when a developer widens the allow list through the
 * seopress_mcp_exposed_namespaces filter.
 *
 * @since 10.3.0
 */
class McpExposure {

	/**
	 * The ability namespace SEOPress owns, in the free plugin and in PRO.
	 *
	 * @var string
	 */
	const OWN_NAMESPACE = 'seopress';

	/**
	 * Key, inside seopress_advanced_option_name, of the "serve other plugins'
	 * abilities too" opt-in.
	 *
	 * @var string
	 */
	const FOREIGN_OPTION_KEY = 'seopress_advanced_abilities_api_mcp_foreign';

	/**
	 * Where the site records the abilities it has switched off.
	 *
	 * A list of ability names. Empty, which is the default, means every
	 * ability in an exposed namespace is served.
	 *
	 * @since 10.3.0
	 * @var string
	 */
	const DISABLED_OPTION_KEY = 'seopress_advanced_abilities_api_mcp_disabled';

	/**
	 * Whether the site owner opted in to serving abilities from other plugins.
	 *
	 * Off by default. On, the MCP server stops filtering by namespace and serves
	 * every ability that carries meta.mcp.public, whichever plugin registered it.
	 *
	 * @since 10.3.0
	 *
	 * @return bool
	 */
	public static function foreign_namespaces_enabled() {
		$options = get_option( 'seopress_advanced_option_name' );

		$enabled = is_array( $options ) && ! empty( $options[ self::FOREIGN_OPTION_KEY ] );

		/**
		 * Filter whether the SEOPress MCP server serves other plugins' abilities.
		 *
		 * @since 10.3.0
		 *
		 * @param bool $enabled Whether abilities outside the allowed namespaces are served.
		 */
		return (bool) apply_filters( 'seopress_mcp_foreign_namespaces_enabled', $enabled );
	}

	/**
	 * The ability namespaces the MCP server serves when the opt-in is off.
	 *
	 * @since 10.3.0
	 *
	 * @return string[] Unique, non-empty namespace slugs.
	 */
	public static function exposed_namespaces() {
		$namespaces = array( self::OWN_NAMESPACE );

		/**
		 * Filter the ability namespaces served by the SEOPress MCP server.
		 *
		 * Adding a namespace here serves every MCP-public ability registered in
		 * it, so only add a namespace you control or trust.
		 *
		 * @since 10.3.0
		 *
		 * @param string[] $namespaces Allowed ability namespaces, without a trailing slash.
		 */
		$namespaces = apply_filters( 'seopress_mcp_exposed_namespaces', $namespaces );

		if ( ! is_array( $namespaces ) ) {
			return array( self::OWN_NAMESPACE );
		}

		$clean = array();
		foreach ( $namespaces as $namespace ) {
			if ( ! is_string( $namespace ) || '' === trim( $namespace ) ) {
				continue;
			}

			$clean[] = trim( $namespace );
		}

		// A filter that removed everything would serve nothing, which is a
		// harmless but confusing configuration. SEOPress's own namespace is the
		// floor: this server exists to serve it.
		if ( empty( $clean ) ) {
			return array( self::OWN_NAMESPACE );
		}

		return array_values( array_unique( $clean ) );
	}

	/**
	 * The namespace part of a registered ability name.
	 *
	 * Ability names match ^[a-z0-9-]+/[a-z0-9-]+$, so the namespace is whatever
	 * sits before the first forward slash.
	 *
	 * @since 10.3.0
	 *
	 * @param string $ability_name The registered ability name.
	 *
	 * @return string Empty string when the name carries no namespace.
	 */
	public static function ability_namespace( $ability_name ) {
		if ( ! is_string( $ability_name ) ) {
			return '';
		}

		$position = strpos( $ability_name, '/' );
		if ( false === $position || 0 === $position ) {
			return '';
		}

		return substr( $ability_name, 0, $position );
	}

	/**
	 * Whether an ability name is inside the namespaces served by MCP.
	 *
	 * @since 10.3.0
	 *
	 * @param string $ability_name The registered ability name.
	 *
	 * @return bool
	 */
	public static function is_namespace_exposed( $ability_name ) {
		if ( self::foreign_namespaces_enabled() ) {
			return true;
		}

		$namespace = self::ability_namespace( $ability_name );
		if ( '' === $namespace ) {
			return false;
		}

		return in_array( $namespace, self::exposed_namespaces(), true );
	}

	/**
	 * Every registered ability that declared itself an MCP tool.
	 *
	 * Namespace scoping is deliberately not applied here: the settings screen
	 * needs the unscoped set to show what the opt-in would add.
	 *
	 * @since 10.3.0
	 *
	 * @return \WP_Ability[] Keyed by ability name.
	 */
	public static function mcp_public_abilities() {
		if ( ! function_exists( 'wp_get_abilities' ) ) {
			return array();
		}

		$abilities = wp_get_abilities(
			array(
				'meta' => array(
					'mcp' => array(
						'public' => true,
						'type'   => 'tool',
					),
				),
			)
		);

		return is_array( $abilities ) ? $abilities : array();
	}

	/**
	 * Keep only the abilities whose namespace the MCP server is allowed to serve.
	 *
	 * @since 10.3.0
	 *
	 * @param \WP_Ability[] $abilities The abilities to filter.
	 *
	 * @return \WP_Ability[] Keyed by ability name.
	 */
	/**
	 * The scope the request being served was granted, if any.
	 *
	 * Null means no OAuth token is in play: an Application Password, which a
	 * person sets up by hand on their own machine and for which there is no
	 * consent screen to choose a scope on. Those keep the full set.
	 *
	 * @since 10.3.0
	 * @var string|null
	 */
	protected static $granted_scope = null;

	/**
	 * Record the scope the bearer token being served was issued with.
	 *
	 * @since 10.3.0
	 *
	 * @param string|null $scope The granted scope, or null for no token.
	 *
	 * @return void
	 */
	public static function set_granted_scope( $scope ) {
		self::$granted_scope = null === $scope ? null : (string) $scope;
	}

	/**
	 * Is the request being served limited to the read-only tools.
	 *
	 * A site can force this on every connection with the filter, which is the
	 * floor under the per-connection choice: it can narrow what a token was
	 * granted, never widen it.
	 *
	 * @since 10.3.0
	 *
	 * @return bool
	 */
	public static function is_read_only() {
		$read_only = McpOauthStore::SCOPE_READ === self::$granted_scope;

		$site_read_only = (bool) apply_filters( 'seopress_mcp_read_only', $read_only );

		return $read_only || $site_read_only;
	}

	/**
	 * The abilities this site has switched off, by name.
	 *
	 * Read defensively rather than trusted: the option is a list of ability
	 * names and anything else in it is dropped, so a hand edited or corrupted
	 * value can hide a tool but can never bring one back or break the listing.
	 *
	 * @since 10.3.0
	 *
	 * @return string[] Ability names, as a lookup keyed by name.
	 */
	public static function disabled_abilities() {
		$options = get_option( 'seopress_advanced_option_name' );
		$stored  = ( is_array( $options ) && isset( $options[ self::DISABLED_OPTION_KEY ] ) )
			? $options[ self::DISABLED_OPTION_KEY ]
			: array();

		$names = array();

		foreach ( (array) $stored as $name ) {
			if ( is_string( $name ) && '' !== $name ) {
				$names[ $name ] = true;
			}
		}

		/**
		 * Filter the abilities this site has switched off.
		 *
		 * @since 10.3.0
		 *
		 * @param array $names Ability names, keyed by name.
		 */
		$names = apply_filters( 'seopress_mcp_disabled_abilities', $names );

		return is_array( $names ) ? $names : array();
	}

	public static function filter_abilities( $abilities ) {
		if ( ! is_array( $abilities ) ) {
			return array();
		}

		$allowed  = array();
		$disabled = self::disabled_abilities();

		foreach ( $abilities as $ability ) {
			if ( ! is_object( $ability ) || ! method_exists( $ability, 'get_name' ) ) {
				continue;
			}

			$name = $ability->get_name();

			if ( ! self::is_namespace_exposed( $name ) ) {
				continue;
			}

			if ( self::is_read_only() && ! self::is_read_ability( $ability ) ) {
				continue;
			}

			if ( isset( $disabled[ $name ] ) ) {
				continue;
			}

			$allowed[ $name ] = $ability;
		}

		/**
		 * Filter the abilities the MCP server serves.
		 *
		 * The last word on what a client is handed, after the namespace
		 * scoping and the granted scope have had theirs. A site that wants to
		 * keep one tool to itself, say the one that deletes redirections, can
		 * drop it here and no client will see it or be able to call it by name.
		 *
		 * @since 10.3.0
		 *
		 * @param \WP_Ability[] $allowed The abilities about to be served, keyed by name.
		 */
		$allowed = apply_filters( 'seopress_mcp_served_abilities', $allowed );

		if ( ! is_array( $allowed ) ) {
			return array();
		}

		// Extensions may add tools, but may not widen the token's scope.
		if ( self::is_read_only() ) {
			$allowed = array_filter( $allowed, array( self::class, 'is_read_ability' ) );
		}

		return $allowed;
	}

	/**
	 * Does this ability only read.
	 *
	 * The same annotation the tool list hands to clients as readOnlyHint, read
	 * here rather than trusted there: a client that ignores the hint still
	 * never sees the tool, and an ability that forgot to declare itself counts
	 * as a writer.
	 *
	 * @since 10.3.0
	 *
	 * @param \WP_Ability $ability The ability.
	 *
	 * @return bool
	 */
	public static function is_read_ability( $ability ) {
		$meta = $ability->get_meta();

		return isset( $meta['annotations']['readonly'] ) && true === (bool) $meta['annotations']['readonly'];
	}

	/**
	 * Every MCP-public ability the namespace scoping allows, scope aside.
	 *
	 * This is what would be served to a connection granted everything, which
	 * is what tells a tool the scope held back apart from one that does not
	 * exist.
	 *
	 * @since 10.3.0
	 *
	 * @return \WP_Ability[] Keyed by ability name.
	 */
	public static function abilities_in_scope_of_namespace() {
		$allowed = array();

		foreach ( self::mcp_public_abilities() as $ability ) {
			if ( ! is_object( $ability ) || ! method_exists( $ability, 'get_name' ) ) {
				continue;
			}

			if ( self::is_namespace_exposed( $ability->get_name() ) ) {
				$allowed[ $ability->get_name() ] = $ability;
			}
		}

		return $allowed;
	}

	/**
	 * The abilities the MCP server serves right now.
	 *
	 * @since 10.3.0
	 *
	 * @return \WP_Ability[] Keyed by ability name.
	 */
	public static function exposed_abilities() {
		return self::filter_abilities( self::mcp_public_abilities() );
	}

	/**
	 * The MCP-public abilities held back by the namespace scoping right now.
	 *
	 * This is what the opt-in would add, which is what the settings screen has
	 * to show before the site owner turns it on.
	 *
	 * @since 10.3.0
	 *
	 * @return \WP_Ability[] Keyed by ability name.
	 */
	public static function withheld_abilities() {
		$withheld = array();

		foreach ( self::mcp_public_abilities() as $ability ) {
			if ( ! is_object( $ability ) || ! method_exists( $ability, 'get_name' ) ) {
				continue;
			}

			$name      = $ability->get_name();
			$namespace = self::ability_namespace( $name );

			if ( '' !== $namespace && in_array( $namespace, self::exposed_namespaces(), true ) ) {
				continue;
			}

			$withheld[ $name ] = $ability;
		}

		return $withheld;
	}
}
