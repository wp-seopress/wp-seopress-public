<?php
defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

/**
 * Uninstall SEOPress
 *
 * @since 6.2
 *
 * @author Benjamin, inspired by Polylang
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) { // If uninstall not called from WordPress exit
	exit;
}

class SEOPRESS_Uninstall {

	/**
	 * Constructor: manages uninstall for multisite
	 *
	 * @since 6.2
	 */
	public function __construct() {
		global $wpdb;

		// Don't do anything except if the constant SEOPRESS_UNINSTALL is explicitely defined and true.
		if ( ! defined( 'SEOPRESS_UNINSTALL' ) || ! SEOPRESS_UNINSTALL ) {
			return;
		}

		// Check if it is a multisite uninstall - if so, run the uninstall function for each blog id
		if ( is_multisite() ) {
			foreach ( $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" ) as $blog_id ) {
				switch_to_blog( $blog_id );
				$this->uninstall();
			}
			restore_current_blog();
		}
		else {
			$this->uninstall();
		}
	}

	/**
	 * Delete all entries in the DB related to SEOPress Free AND PRO:
	 * Transients, post meta, options, custom tables
     *
	 * @since 6.2
     * @updated 7.4
	 */
	public function uninstall() {
		global $wpdb;

		do_action( 'seopress_uninstall' );

        // Delete post meta
        $wpdb->query( "DELETE FROM $wpdb->postmeta WHERE meta_key LIKE '_seopress_%'" );

        // Delete redirections / 404 errors
        $sql = 'DELETE `posts`, `pm`
		FROM `' . $wpdb->prefix . 'posts` AS `posts`
		LEFT JOIN `' . $wpdb->prefix . 'postmeta` AS `pm` ON `pm`.`post_id` = `posts`.`ID`
		WHERE `posts`.`post_type` = \'seopress_404\'';

        $sql = $wpdb->prepare($sql);
        $wpdb->query($sql);

        // Delete schemas
        $sql = 'DELETE `posts`, `pm`
		FROM `' . $wpdb->prefix . 'posts` AS `posts`
		LEFT JOIN `' . $wpdb->prefix . 'postmeta` AS `pm` ON `pm`.`post_id` = `posts`.`ID`
		WHERE `posts`.`post_type` = \'seopress_schemas\'';

        $sql = $wpdb->prepare($sql);
        $wpdb->query($sql);

        // Delete broken links
        $sql = 'DELETE `posts`, `pm`
		FROM `' . $wpdb->prefix . 'posts` AS `posts`
		LEFT JOIN `' . $wpdb->prefix . 'postmeta` AS `pm` ON `pm`.`post_id` = `posts`.`ID`
		WHERE `posts`.`post_type` = \'seopress_bot\'';

        $sql = $wpdb->prepare($sql);
        $wpdb->query($sql);

        // Delete global settings
        $options = $wpdb->get_col( "SELECT option_name FROM $wpdb->options WHERE option_name LIKE 'seopress_%'" );
        array_map( 'delete_option', $options );

        // Delete widget options
        $options = $wpdb->get_col( "SELECT option_name FROM $wpdb->options WHERE option_name LIKE 'widget_seopress_%'" );
        array_map( 'delete_option', $options );

		// Delete transients
		delete_transient( '_seopress_sitemap_ids_video' );
		delete_transient( 'seopress_results_page_speed' );
		delete_transient( 'seopress_results_page_speed_desktop' );
		delete_transient( 'seopress_results_google_analytics' );
		delete_transient( 'seopress_results_matomo' );
		delete_transient( 'seopress_prevent_title_redirection_already_exist' );

        // Delete custom tables
        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}seopress_significant_keywords");
		$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}seopress_content_analysis");
        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}seopress_seo_issues");

        // Clear CRON
        wp_clear_scheduled_hook('seopress_xml_sitemaps_ping_cron');
        wp_clear_scheduled_hook('seopress_404_cron_cleaning');
        wp_clear_scheduled_hook('seopress_google_analytics_cron');
        wp_clear_scheduled_hook('seopress_page_speed_insights_cron');
        wp_clear_scheduled_hook('seopress_404_email_alerts_cron');
        wp_clear_scheduled_hook('seopress_insights_gsc_cron');
        wp_clear_scheduled_hook('seopress_matomo_analytics_cron');
        wp_clear_scheduled_hook('seopress_alerts_cron');
	}
}

new SEOPRESS_Uninstall();
