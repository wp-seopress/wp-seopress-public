<?php // phpcs:ignore

namespace SEOPress\Actions\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Core\Hooks\ExecuteHooks;

/**
 * Freeze Modified Date
 *
 * Prevents the post modified date from being updated when the freeze option is enabled.
 *
 * Uses a multi-layer strategy:
 * 1. Preventive: modifies post data in wp_insert_post_data to preserve the original date
 *    before WordPress writes to the database.
 * 2. Corrective: restores dates via wp_after_insert_post (safety net) and
 *    woocommerce_update_product (for WC products, whose data store bypasses
 *    wp_insert_post_data by using $wpdb->update() directly).
 * 3. Cross-request: uses a short-lived transient to bridge Gutenberg's two-phase save
 *    (REST API save + separate metabox POST). The REST save captures pre-save dates
 *    into a transient; the metabox POST recovers them when freeze is enabled.
 *
 * @since 9.6
 */
class FreezeModifiedDate implements ExecuteHooks {

	/**
	 * Transient prefix for storing pre-save dates across HTTP requests.
	 *
	 * @var string
	 */
	const TRANSIENT_PREFIX = '_seopress_freeze_dates_';

	/**
	 * Stores original dates captured before a post update.
	 *
	 * @var array
	 */
	private $original_dates = array();

	/**
	 * The FreezeModifiedDate hooks.
	 *
	 * @since 9.6
	 *
	 * @return void
	 */
	public function hooks() {
		// Capture original dates AND preserve them in the post data being written.
		// Priority 9999 ensures this runs last, making our date preservation the final
		// modification before WordPress writes to the database.
		add_filter( 'wp_insert_post_data', array( $this, 'maybePreserveModifiedDate' ), 9999, 4 );

		// Backup capture just before the DB write. pre_post_update fires after
		// wp_insert_post_data but before $wpdb->update(), giving us one last chance
		// to capture the original dates from the database.
		add_action( 'pre_post_update', array( $this, 'capturePreUpdateDate' ), 10, 2 );

		// Safety net: restore dates after the post AND meta are both saved.
		// Handles edge cases where something modifies dates after our filter.
		// If WooCommerce already handled the restore, original_dates will be unset.
		add_action( 'wp_after_insert_post', array( $this, 'maybeRestoreDate' ), 9999, 4 );

		// WooCommerce CRUD bypasses wp_insert_post_data when doing_action('save_post')
		// is true, using $wpdb->update() directly. We need WC-specific hooks.
		add_action( 'woocommerce_before_product_object_save', array( $this, 'captureWcOriginalDate' ), 10 );
		add_action( 'woocommerce_update_product', array( $this, 'maybeRestoreWcDate' ), 10 );
	}

	/**
	 * Capture original dates and preserve them in the post data being written.
	 *
	 * This filter both captures the original modified date from the database AND
	 * modifies the data array to keep the original date, preventing WordPress from
	 * setting post_modified to the current time.
	 *
	 * For classic editor: checks $_POST for the freeze checkbox value, since the
	 * metabox save handler runs later on save_post and the DB meta value is stale.
	 * For block editor / REST API: checks post meta (saved via REST before post save).
	 *
	 * @since 9.6
	 *
	 * @param array $data                Post data being saved.
	 * @param array $postarr             Raw post data.
	 * @param array $unsanitized_postarr Unsanitized post data.
	 * @param bool  $update              Whether this is an update.
	 *
	 * @return array Post data, with post_modified preserved if freeze is enabled.
	 */
	public function maybePreserveModifiedDate( $data, $postarr, $unsanitized_postarr, $update ) {
		if ( ! $update ) {
			return $data;
		}

		$post_id = isset( $postarr['ID'] ) ? absint( $postarr['ID'] ) : 0;

		if ( ! $post_id ) {
			return $data;
		}

		// Capture original dates if not already captured.
		// Skips re-capture on nested wp_update_post() calls (e.g. WooCommerce CRUD
		// calling $product->save() during save_post), preserving the true original dates.
		if ( ! isset( $this->original_dates[ $post_id ] ) ) {
			$original_post = get_post( $post_id );

			if ( $original_post ) {
				$this->original_dates[ $post_id ] = array(
					'post_modified'     => $original_post->post_modified,
					'post_modified_gmt' => $original_post->post_modified_gmt,
				);
			}
		}

		if ( ! isset( $this->original_dates[ $post_id ] ) ) {
			return $data;
		}

		$freeze = $this->isFreezeEnabled( $post_id );

		// In REST requests (Gutenberg Request 1), save pre-save dates to a transient.
		// A subsequent metabox POST (Request 2) may need them if the user checked
		// the freeze checkbox in the classic metabox.
		if ( 'yes' !== $freeze && defined( 'REST_REQUEST' ) && REST_REQUEST ) {
			set_transient(
				self::TRANSIENT_PREFIX . $post_id,
				$this->original_dates[ $post_id ],
				30
			);
		}

		if ( 'yes' !== $freeze ) {
			return $data;
		}

		// In non-REST context (classic metabox Request 2), the dates from get_post()
		// may already be stale (updated by Gutenberg's REST save in Request 1).
		// Recover the true pre-save dates from the transient if available.
		if ( ! defined( 'REST_REQUEST' ) || ! REST_REQUEST ) {
			$transient_dates = get_transient( self::TRANSIENT_PREFIX . $post_id );

			if ( $transient_dates ) {
				$this->original_dates[ $post_id ] = $transient_dates;
				delete_transient( self::TRANSIENT_PREFIX . $post_id );
			}
		}

		// Preserve the original modified dates in the data being written to the DB.
		$data['post_modified']     = $this->original_dates[ $post_id ]['post_modified'];
		$data['post_modified_gmt'] = $this->original_dates[ $post_id ]['post_modified_gmt'];

		return $data;
	}

	/**
	 * Backup capture of original dates just before the database write.
	 *
	 * pre_post_update fires after wp_insert_post_data but immediately before
	 * $wpdb->update(). If the dates were not captured by maybePreserveModifiedDate()
	 * for any reason, this ensures we still have them before they are overwritten.
	 *
	 * @since 9.6
	 *
	 * @param int   $post_id Post ID.
	 * @param array $data    Array of unslashed post data about to be written.
	 *
	 * @return void
	 */
	public function capturePreUpdateDate( $post_id, $data ) {
		$post_id = absint( $post_id );

		if ( ! $post_id || isset( $this->original_dates[ $post_id ] ) ) {
			return;
		}

		$original_post = get_post( $post_id );

		if ( $original_post ) {
			$this->original_dates[ $post_id ] = array(
				'post_modified'     => $original_post->post_modified,
				'post_modified_gmt' => $original_post->post_modified_gmt,
			);
		}
	}

	/**
	 * Restore the original modified date after the post and meta are saved.
	 *
	 * Acts as a safety net. wp_after_insert_post fires after wp_insert_post AND
	 * after all save_post handlers (including WooCommerce) have completed.
	 *
	 * If the WooCommerce woocommerce_update_product hook already handled restoration,
	 * original_dates will have been unset and this method returns early.
	 *
	 * @since 9.6
	 *
	 * @param int      $post_id     Post ID.
	 * @param \WP_Post $post        Post object.
	 * @param bool     $update      Whether this is an update.
	 * @param \WP_Post $post_before Post object before the update.
	 *
	 * @return void
	 */
	public function maybeRestoreDate( $post_id, $post, $update, $post_before ) {
		if ( ! $update || ! isset( $this->original_dates[ $post_id ] ) ) {
			return;
		}

		$freeze = $this->isFreezeEnabled( $post_id );

		if ( 'yes' !== $freeze ) {
			// In REST context, save dates to transient before discarding.
			// A subsequent metabox POST may need them.
			if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
				set_transient(
					self::TRANSIENT_PREFIX . $post_id,
					$this->original_dates[ $post_id ],
					30
				);
			}

			unset( $this->original_dates[ $post_id ] );
			return;
		}

		// In non-REST context, recover true pre-save dates from transient
		// (set during Gutenberg's REST save in Request 1).
		if ( ! defined( 'REST_REQUEST' ) || ! REST_REQUEST ) {
			$transient_dates = get_transient( self::TRANSIENT_PREFIX . $post_id );

			if ( $transient_dates ) {
				$this->original_dates[ $post_id ] = $transient_dates;
				delete_transient( self::TRANSIENT_PREFIX . $post_id );
			}
		}

		$frozen = $this->original_dates[ $post_id ];

		global $wpdb;

		$wpdb->update(
			$wpdb->posts,
			array(
				'post_modified'     => $frozen['post_modified'],
				'post_modified_gmt' => $frozen['post_modified_gmt'],
			),
			array( 'ID' => $post_id ),
			array( '%s', '%s' ),
			array( '%d' )
		);

		clean_post_cache( $post_id );
		unset( $this->original_dates[ $post_id ] );
	}

	/**
	 * Capture original date before WooCommerce saves a product.
	 *
	 * WooCommerce CRUD bypasses wp_insert_post_data entirely when
	 * doing_action('save_post') is true. This hook captures the dates
	 * for pure CRUD saves that happen outside of wp_insert_post().
	 *
	 * @since 9.6
	 *
	 * @param \WC_Product $product The WooCommerce product object.
	 *
	 * @return void
	 */
	public function captureWcOriginalDate( $product ) {
		$post_id = $product->get_id();

		if ( ! $post_id || isset( $this->original_dates[ $post_id ] ) ) {
			return;
		}

		$post = get_post( $post_id );

		if ( $post ) {
			$this->original_dates[ $post_id ] = array(
				'post_modified'     => $post->post_modified,
				'post_modified_gmt' => $post->post_modified_gmt,
			);
		}
	}

	/**
	 * Restore the frozen modified date after WooCommerce saves a product.
	 *
	 * WooCommerce data store uses $wpdb->update() directly when
	 * doing_action('save_post'), bypassing our wp_insert_post_data filter.
	 * This hook runs after WC has written its changes and restores the frozen dates.
	 *
	 * @since 9.6
	 *
	 * @param int $product_id The product post ID.
	 *
	 * @return void
	 */
	public function maybeRestoreWcDate( $product_id ) {
		if ( ! isset( $this->original_dates[ $product_id ] ) ) {
			return;
		}

		$freeze = $this->isFreezeEnabled( $product_id );

		if ( 'yes' !== $freeze ) {
			unset( $this->original_dates[ $product_id ] );
			return;
		}

		global $wpdb;

		$frozen = $this->original_dates[ $product_id ];

		$wpdb->update(
			$wpdb->posts,
			array(
				'post_modified'     => $frozen['post_modified'],
				'post_modified_gmt' => $frozen['post_modified_gmt'],
			),
			array( 'ID' => $product_id ),
			array( '%s', '%s' ),
			array( '%d' )
		);

		clean_post_cache( $product_id );
		unset( $this->original_dates[ $product_id ] );
	}

	/**
	 * Check if the freeze modified date option is enabled for a post.
	 *
	 * For classic editor: reads from $_POST because the metabox save handler
	 * (which persists meta to the DB) runs on save_post at priority 10, which
	 * may not have fired yet when this is called.
	 *
	 * For block editor / REST API: reads from post meta, which was already
	 * saved via the REST API before the post save.
	 *
	 * @since 9.6
	 *
	 * @param int $post_id Post ID.
	 *
	 * @return string 'yes' if freeze is enabled, empty string otherwise.
	 */
	private function isFreezeEnabled( $post_id ) {
		$is_classic_editor = isset( $_POST['seopress_cpt_nonce'] );

		if ( $is_classic_editor ) {
			return ! empty( $_POST['seopress_robots_freeze_modified_date'] ) ? 'yes' : '';
		}

		return get_post_meta( $post_id, '_seopress_robots_freeze_modified_date', true );
	}
}
