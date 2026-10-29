<?php // phpcs:ignore

namespace SEOPress\Services\InstantIndexing;

defined( 'ABSPATH' ) || exit;

/**
 * History of the URLs submitted through Instant Indexing.
 *
 * One row per URL and per engine, so a batch of 100 URLs sent to Google and
 * IndexNow is 200 rows: the question this answers is "was this URL submitted,
 * when, and what did the engine say", which a row per request cannot answer
 * for the 2nd to 100th URL of a batch.
 *
 * The "Latest indexing request" option is left alone: it still answers "did my
 * last click work" and is still replaced on every submission.
 *
 * @since 10.4.0
 */
class IndexingHistory {

	/**
	 * Default number of rows kept.
	 *
	 * @var int
	 */
	const DEFAULT_MAX_ROWS = 5000;

	/**
	 * Option flagging that the legacy log has been migrated.
	 *
	 * @var string
	 */
	const MIGRATION_OPTION = 'seopress_instant_indexing_history_version';

	/**
	 * Longest message stored, in characters.
	 *
	 * @var int
	 */
	const MAX_MESSAGE_LENGTH = 500;

	/**
	 * Table name without the prefix.
	 *
	 * @var string
	 */
	const TABLE = 'seopress_instant_indexing_history';

	/**
	 * Full table name, prefix included.
	 *
	 * @return string
	 */
	public function get_table_name() {
		global $wpdb;

		return $wpdb->prefix . self::TABLE;
	}

	/**
	 * Create the table when it is missing.
	 *
	 * Created on the first submission rather than on activation: activation
	 * does not run on plugin updates, an automatic submission on publish can
	 * come before anyone opens an admin page, and sites that never use
	 * Instant Indexing do not get an empty table.
	 *
	 * One CREATE TABLE with its index inline: a separate CREATE INDEX commits
	 * any open transaction, even on a temporary table.
	 *
	 * @return bool Whether the table exists.
	 */
	public function ensure_table() {
		global $wpdb;

		if ( $this->table_exists() ) {
			return true;
		}

		if ( ! function_exists( 'dbDelta' ) ) {
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		}

		$table   = $this->get_table_name();
		$collate = $wpdb->get_charset_collate();

		dbDelta(
			"CREATE TABLE {$table} (
				id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				url text NOT NULL,
				post_id bigint(20) unsigned NOT NULL DEFAULT 0,
				engine varchar(20) NOT NULL DEFAULT '',
				source varchar(20) NOT NULL DEFAULT '',
				status_code smallint(5) unsigned NOT NULL DEFAULT 0,
				message text NOT NULL,
				date_gmt datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				PRIMARY KEY  (id),
				KEY post_id (post_id)
			) {$collate};"
		);

		return $this->table_exists();
	}

	/**
	 * Whether the table can be queried.
	 *
	 * A query on the table itself rather than SHOW TABLES, which does not
	 * list temporary tables: the test framework creates every table as one.
	 *
	 * @return bool
	 */
	public function table_exists() {
		global $wpdb;

		$table      = $this->get_table_name();
		$suppressed = $wpdb->suppress_errors( true );
		$result     = $wpdb->query( "SELECT 1 FROM {$table} LIMIT 0" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery
		$wpdb->suppress_errors( $suppressed );

		return false !== $result;
	}

	/**
	 * Number of rows kept.
	 *
	 * @return int 0 when the history is turned off.
	 */
	public function get_max_rows() {
		/**
		 * Filter how many rows the Instant Indexing history keeps.
		 *
		 * Each submitted URL is one row per engine. The oldest rows are
		 * removed when a submission is recorded. Return 0 to stop recording.
		 *
		 * @since 10.4.0
		 *
		 * @param int $max_rows Number of rows kept. Default 5000.
		 */
		$max_rows = apply_filters( 'seopress_instant_indexing_history_max_rows', self::DEFAULT_MAX_ROWS );

		return max( 0, (int) $max_rows );
	}

	/**
	 * Record submitted URLs.
	 *
	 * @param array $entries Rows, each with `url`, `engine` (indexnow|google),
	 *                       `source` (manual|auto), `status_code`, `message`
	 *                       and `post_id`.
	 * @param int   $timestamp Unix timestamp of the submission. Default now.
	 *
	 * @return int Number of rows recorded.
	 */
	public function record( array $entries, $timestamp = null ) {
		global $wpdb;

		$max_rows = $this->get_max_rows();

		if ( empty( $entries ) || 0 === $max_rows || ! $this->ensure_table() ) {
			return 0;
		}

		$date_gmt = gmdate( 'Y-m-d H:i:s', null === $timestamp ? time() : (int) $timestamp );
		$rows     = array();

		foreach ( $entries as $entry ) {
			$row = $this->normalize_entry( $entry, $date_gmt );

			if ( null !== $row ) {
				$rows[] = $row;
			}
		}

		if ( empty( $rows ) ) {
			return 0;
		}

		$table        = $this->get_table_name();
		$placeholders = array();
		$values       = array();

		foreach ( $rows as $row ) {
			$placeholders[] = '(%s, %d, %s, %s, %d, %s, %s)';
			array_push( $values, $row['url'], $row['post_id'], $row['engine'], $row['source'], $row['status_code'], $row['message'], $row['date_gmt'] );
		}

		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare, WordPress.DB.DirectDatabaseQuery -- One placeholder group per row, all values prepared.
		$inserted = $wpdb->query(
			$wpdb->prepare(
				"INSERT INTO {$table} (url, post_id, engine, source, status_code, message, date_gmt) VALUES " . implode( ', ', $placeholders ),
				$values
			)
		);
		// phpcs:enable

		if ( ! $inserted ) {
			return 0;
		}

		$this->trim( $max_rows );

		/**
		 * Fires after submitted URLs have been recorded in the history.
		 *
		 * @since 10.4.0
		 *
		 * @param array $rows Rows recorded, each with `url`, `post_id`, `engine`,
		 *                    `source`, `status_code`, `message` and `date_gmt` (UTC).
		 */
		do_action( 'seopress_instant_indexing_history_recorded', $rows );

		return (int) $inserted;
	}

	/**
	 * Keep the newest rows only.
	 *
	 * Everything at or below the id of the first row past the cap goes, in
	 * one range delete on the primary key: no COUNT(*) and no cron. The cut
	 * is read from the rows themselves rather than computed from MAX(id),
	 * because ids are not contiguous: a cluster steps them by its node count
	 * (auto_increment_increment), and a rolled-back insert leaves a gap.
	 *
	 * @param int $max_rows Rows kept.
	 *
	 * @return void
	 */
	protected function trim( $max_rows ) {
		global $wpdb;

		$table = $this->get_table_name();

		$cut = (int) $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$table} ORDER BY id DESC LIMIT 1 OFFSET %d", $max_rows ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery

		if ( 0 === $cut ) {
			return;
		}

		$wpdb->query( $wpdb->prepare( "DELETE FROM {$table} WHERE id <= %d", $cut ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery
	}

	/**
	 * Validate one row before it is written.
	 *
	 * @param array  $entry    Row as built by the submission.
	 * @param string $date_gmt Submission date, UTC.
	 *
	 * @return array|null
	 */
	protected function normalize_entry( $entry, $date_gmt ) {
		if ( ! is_array( $entry ) || empty( $entry['url'] ) || ! is_string( $entry['url'] ) ) {
			return null;
		}

		$engine = isset( $entry['engine'] ) ? (string) $entry['engine'] : '';
		$source = isset( $entry['source'] ) ? (string) $entry['source'] : '';

		if ( ! in_array( $engine, array( 'indexnow', 'google' ), true ) ) {
			return null;
		}

		$message = isset( $entry['message'] ) && is_scalar( $entry['message'] ) ? wp_strip_all_tags( (string) $entry['message'] ) : '';

		return array(
			'url'         => esc_url_raw( $entry['url'] ),
			'post_id'     => isset( $entry['post_id'] ) ? absint( $entry['post_id'] ) : 0,
			'engine'      => $engine,
			'source'      => in_array( $source, array( 'manual', 'auto' ), true ) ? $source : '',
			'status_code' => isset( $entry['status_code'] ) ? min( 999, absint( $entry['status_code'] ) ) : 0,
			'message'     => function_exists( 'mb_substr' ) ? mb_substr( $message, 0, self::MAX_MESSAGE_LENGTH ) : substr( $message, 0, self::MAX_MESSAGE_LENGTH ),
			'date_gmt'    => $date_gmt,
		);
	}

	/**
	 * Read a page of the history, newest first.
	 *
	 * @param array $args {
	 *     Optional. Query arguments.
	 *
	 *     @type int    $page     Page number, from 1.
	 *     @type int    $per_page Rows per page, up to 100.
	 *     @type string $search   Part of a URL.
	 *     @type string $source   manual|auto, anything else for both.
	 *     @type int    $post_id  Only the rows of this post.
	 * }
	 *
	 * @return array{items: array, total: int}
	 */
	public function query( array $args = array() ) {
		global $wpdb;

		$args = wp_parse_args(
			$args,
			array(
				'page'     => 1,
				'per_page' => 20,
				'search'   => '',
				'source'   => '',
				'post_id'  => 0,
			)
		);

		$empty = array(
			'items' => array(),
			'total' => 0,
		);

		if ( ! $this->table_exists() ) {
			return $empty;
		}

		$table    = $this->get_table_name();
		$page     = max( 1, (int) $args['page'] );
		$per_page = min( 100, max( 1, (int) $args['per_page'] ) );
		$where    = array( '1=1' );
		$values   = array();

		if ( is_string( $args['search'] ) && '' !== trim( $args['search'] ) ) {
			$where[]  = 'url LIKE %s';
			$values[] = '%' . $wpdb->esc_like( trim( $args['search'] ) ) . '%';
		}

		if ( in_array( $args['source'], array( 'manual', 'auto' ), true ) ) {
			$where[]  = 'source = %s';
			$values[] = $args['source'];
		}

		if ( absint( $args['post_id'] ) > 0 ) {
			$where[]  = 'post_id = %d';
			$values[] = absint( $args['post_id'] );
		}

		$where_sql = implode( ' AND ', $where );

		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.PreparedSQLPlaceholders.ReplacementsWrongNumber, WordPress.DB.DirectDatabaseQuery -- WHERE built from fixed clauses, values prepared.
		$count_sql = "SELECT COUNT(*) FROM {$table} WHERE {$where_sql}";
		$total     = (int) $wpdb->get_var( empty( $values ) ? $count_sql : $wpdb->prepare( $count_sql, $values ) );

		if ( 0 === $total ) {
			return $empty;
		}

		$rows = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT id, url, post_id, engine, source, status_code, message, date_gmt FROM {$table} WHERE {$where_sql} ORDER BY id DESC LIMIT %d OFFSET %d",
				array_merge( $values, array( $per_page, ( $page - 1 ) * $per_page ) )
			),
			ARRAY_A
		);
		// phpcs:enable

		return array(
			'items' => array_map( array( $this, 'format_row' ), (array) $rows ),
			'total' => $total,
		);
	}

	/**
	 * The latest rows of one post.
	 *
	 * @param int $post_id Post ID.
	 * @param int $limit   Rows returned.
	 *
	 * @return array
	 */
	public function get_for_post( $post_id, $limit = 20 ) {
		$post_id = absint( $post_id );

		if ( 0 === $post_id ) {
			return array();
		}

		$result = $this->query(
			array(
				'post_id'  => $post_id,
				'per_page' => $limit,
			)
		);

		return $result['items'];
	}

	/**
	 * Delete every row.
	 *
	 * @return bool
	 */
	public function clear() {
		global $wpdb;

		if ( ! $this->table_exists() ) {
			return true;
		}

		$table = $this->get_table_name();

		return false !== $wpdb->query( "DELETE FROM {$table}" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery
	}

	/**
	 * Shape a row for the REST API and the screens.
	 *
	 * @param array $row Database row.
	 *
	 * @return array
	 */
	public function format_row( $row ) {
		$timestamp = strtotime( $row['date_gmt'] . ' UTC' );
		$code      = (int) $row['status_code'];

		return array(
			'id'          => (int) $row['id'],
			'url'         => $row['url'],
			'post_id'     => (int) $row['post_id'],
			'engine'      => $row['engine'],
			'source'      => $row['source'],
			'status_code' => $code,
			'success'     => $code >= 200 && $code < 300,
			'message'     => $row['message'],
			'date_gmt'    => false === $timestamp ? '' : gmdate( 'c', $timestamp ),
			'date'        => false === $timestamp ? '' : wp_date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $timestamp ),
		);
	}

	/**
	 * Turn the "Latest indexing request" log into history rows.
	 *
	 * The same function serves the live submission and the migration of the
	 * last request recorded before the history existed, so both read the
	 * engine responses the same way.
	 *
	 * @param array  $log      Log as stored in `seopress_instant_indexing_log_option_name`.
	 * @param string $source   manual|auto.
	 * @param array  $post_ids Post ID of each URL, keyed by URL, when known.
	 *
	 * @return array Rows for record().
	 */
	public function entries_from_log( $log, $source, array $post_ids = array() ) {
		if ( ! is_array( $log ) || empty( $log['log']['urls'] ) || ! is_array( $log['log']['urls'] ) ) {
			return array();
		}

		$urls     = array_values( array_filter( $log['log']['urls'], 'is_string' ) );
		$statuses = array();

		// IndexNow answers once for the whole batch.
		if ( isset( $log['bing']['response'] ) ) {
			$statuses['indexnow'] = array_fill( 0, count( $urls ), $this->read_indexnow_response( $log['bing']['response'] ) );
		}

		if ( isset( $log['google']['response'] ) ) {
			$statuses['google'] = $this->read_google_response( $log['google']['response'], count( $urls ) );
		}

		$entries = array();

		foreach ( $statuses as $engine => $engine_statuses ) {
			foreach ( $urls as $index => $url ) {
				$entries[] = array(
					'url'         => $url,
					'post_id'     => isset( $post_ids[ $url ] ) ? (int) $post_ids[ $url ] : 0,
					'engine'      => $engine,
					'source'      => $source,
					'status_code' => $engine_statuses[ $index ]['status_code'],
					'message'     => $engine_statuses[ $index ]['message'],
				);
			}
		}

		return $entries;
	}

	/**
	 * Status of an IndexNow request: one HTTP response for the whole batch.
	 *
	 * @param mixed $response Response as stored in the log.
	 *
	 * @return array{status_code: int, message: string}
	 */
	protected function read_indexnow_response( $response ) {
		if ( ! is_array( $response ) ) {
			return array(
				'status_code' => 0,
				'message'     => '',
			);
		}

		// No HTTP status: the request failed before or while being sent
		// (missing or invalid key, WP_Error).
		if ( ! isset( $response['code'] ) ) {
			return array(
				'status_code' => 0,
				'message'     => isset( $response['error']['message'] ) ? (string) $response['error']['message'] : '',
			);
		}

		$code    = (int) $response['code'];
		$message = '';

		if ( $code < 200 || $code >= 300 ) {
			if ( isset( $response['error'] ) ) {
				$message = is_array( $response['error'] ) ? $this->read_error_message( $response['error'] ) : (string) $response['error'];
			}

			if ( '' === $message && isset( $response['body'] ) && is_string( $response['body'] ) ) {
				$json    = json_decode( $response['body'], true );
				$message = is_array( $json ) && isset( $json['message'] ) && is_string( $json['message'] ) ? $json['message'] : $response['body'];
			}
		}

		return array(
			'status_code' => $code,
			'message'     => $message,
		);
	}

	/**
	 * Status of each URL of a Google batch.
	 *
	 * @param mixed $response Response as stored in the log.
	 * @param int   $count    Number of URLs submitted.
	 *
	 * @return array List of {status_code, message}, one per URL, in URL order.
	 */
	protected function read_google_response( $response, $count ) {
		$failed_all = function ( $code, $message ) use ( $count ) {
			return array_fill(
				0,
				$count,
				array(
					'status_code' => (int) $code,
					'message'     => (string) $message,
				)
			);
		};

		// The whole batch failed: an exception message, often the JSON body.
		if ( is_string( $response ) ) {
			$json = json_decode( $response, true );

			if ( is_array( $json ) && isset( $json['error'] ) && is_array( $json['error'] ) ) {
				return $failed_all( isset( $json['error']['code'] ) ? (int) $json['error']['code'] : 0, $this->read_error_message( $json['error'] ) );
			}

			return $failed_all( 0, $response );
		}

		if ( ! is_array( $response ) ) {
			return $failed_all( 0, '' );
		}

		// Nothing was sent: missing or invalid service account.
		if ( isset( $response['error'] ) && is_array( $response['error'] ) ) {
			return $failed_all( 0, isset( $response['error']['message'] ) ? (string) $response['error']['message'] : '' );
		}

		// One result per URL, in the order the URLs were added to the batch.
		$results  = array_values( $response );
		$statuses = array();

		for ( $index = 0; $index < $count; $index++ ) {
			$result = isset( $results[ $index ] ) ? $results[ $index ] : null;

			if ( is_array( $result ) && isset( $result['error'] ) ) {
				$error      = is_array( $result['error'] ) ? $result['error'] : array( 'message' => (string) $result['error'] );
				$code       = isset( $error['code'] ) ? (int) $error['code'] : 0;
				$message    = $this->read_error_message( $error );
				$statuses[] = array(
					'status_code' => $code,
					'message'     => $message,
				);
			} elseif ( is_array( $result ) ) {
				$statuses[] = array(
					'status_code' => 200,
					'message'     => '',
				);
			} else {
				$statuses[] = array(
					'status_code' => 0,
					'message'     => '',
				);
			}
		}

		return $statuses;
	}

	/**
	 * Message of an error, unwrapping the JSON body Google puts in it.
	 *
	 * @param array $error Error with a `message`.
	 *
	 * @return string
	 */
	protected function read_error_message( $error ) {
		$message = isset( $error['message'] ) && is_scalar( $error['message'] ) ? (string) $error['message'] : '';
		$json    = json_decode( $message, true );

		if ( is_array( $json ) && isset( $json['error']['message'] ) && is_string( $json['error']['message'] ) ) {
			return $json['error']['message'];
		}

		return $message;
	}

	/**
	 * Post IDs of the URLs that belong to this site.
	 *
	 * @param array $urls URLs.
	 *
	 * @return array Post ID keyed by URL, for the URLs that resolve to a post.
	 */
	public function resolve_post_ids( array $urls ) {
		$home_host = wp_parse_url( home_url(), PHP_URL_HOST );
		$post_ids  = array();

		foreach ( $urls as $url ) {
			if ( ! is_string( $url ) || wp_parse_url( $url, PHP_URL_HOST ) !== $home_host ) {
				continue;
			}

			$post_id = url_to_postid( $url );

			if ( $post_id > 0 ) {
				$post_ids[ $url ] = $post_id;
			}
		}

		return $post_ids;
	}

	/**
	 * Read the date of the log as written before 10.4.
	 *
	 * The date used to be stored only as a string formatted with the site's
	 * date and time formats, in the site's time zone (`current_time()`, which
	 * does not translate month names).
	 *
	 * @param string $date Formatted date.
	 *
	 * @return int|null Unix timestamp, or null when it cannot be read back.
	 */
	public function parse_legacy_date( $date ) {
		if ( ! is_string( $date ) || '' === trim( $date ) ) {
			return null;
		}

		$format   = get_option( 'date_format' ) . ', ' . get_option( 'time_format' );
		$datetime = \DateTimeImmutable::createFromFormat( '!' . $format, $date, wp_timezone() );

		// Only trust a date that formats back to the stored string: the
		// formats may have changed since it was written.
		if ( false === $datetime || $datetime->format( $format ) !== $date ) {
			return null;
		}

		// A format without a year reads back as 1970: not a real date either.
		if ( (int) $datetime->format( 'Y' ) < 2015 ) {
			return null;
		}

		return $datetime->getTimestamp();
	}

	/**
	 * Convert the log written before the history existed, once.
	 *
	 * Adds a Unix timestamp next to the formatted date of the "Latest indexing
	 * request" log, and copies that request into the history, when its date
	 * can be read back. A date that cannot be read is left as it is rather
	 * than recorded at a made-up time.
	 *
	 * @return void
	 */
	public function maybe_migrate() {
		if ( get_option( self::MIGRATION_OPTION ) ) {
			return;
		}

		update_option( self::MIGRATION_OPTION, 1, true );

		$log = get_option( 'seopress_instant_indexing_log_option_name' );

		if ( ! is_array( $log ) || empty( $log['log']['date'] ) || isset( $log['log']['timestamp'] ) ) {
			return;
		}

		$timestamp = $this->parse_legacy_date( $log['log']['date'] );

		if ( null === $timestamp ) {
			return;
		}

		$log['log']['timestamp'] = $timestamp;
		update_option( 'seopress_instant_indexing_log_option_name', $log, false );

		// Only automatic runs leave this note, see #1940. Otherwise it may
		// have been either, which is what an empty source says.
		$source = isset( $log['google']['skipped'] ) ? 'auto' : '';
		$urls   = isset( $log['log']['urls'] ) && is_array( $log['log']['urls'] ) ? $log['log']['urls'] : array();

		$this->record( $this->entries_from_log( $log, $source, $this->resolve_post_ids( $urls ) ), $timestamp );
	}
}
