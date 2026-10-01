<?php
/**
 * Optional page context for GA4 events.
 *
 * @package SEOPress
 */

namespace SEOPress\Actions\Front;

defined( 'ABSPATH' ) || exit;

use SEOPress\Core\Hooks\ExecuteHooksFrontend;

class AnalyticsPageContext implements ExecuteHooksFrontend {
	/** Register on the existing GA4 output, including consent-delayed output. */
	public function hooks() {
		add_filter( 'seopress_gtag_ga4', array( $this, 'add_not_found_context' ) );
	}

	/**
	 * Label events on a 404 page without generating an additional page_view.
	 *
	 * @param string $script Existing GA4 configuration script.
	 * @return string
	 */
	public function add_not_found_context( $script ) {
		if ( empty( $script ) || ! is_404()
			|| '1' !== seopress_get_service( 'GoogleAnalyticsOption' )->searchOptionByKey( 'seopress_google_analytics_not_found_tracking' ) ) {
			return $script;
		}

		// GA4 event-scoped custom dimensions use string values. Set the page
		// context before config sends its automatic page_view; this also labels
		// other GA4 events from this error page without another tracking request.
		return "gtag('set', " . wp_json_encode( array( 'page_not_found' => 'true' ) ) . ");\n" . $script;
	}
}
