<?php // phpcs:ignore

namespace SEOPress\Constants;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Options
 */
abstract class Options {
	/**
	 * The key toggle option.
	 *
	 * @since 4.5.0
	 *
	 * @var string
	 */
	const KEY_TOGGLE_OPTION = 'seopress_toggle';

	/**
	 * The key notice option.
	 *
	 * @since 6.0.0
	 *
	 * @var string
	 */
	const KEY_OPTION_NOTICE = 'seopress_notices';

	/**
	 * The key dashboard option.
	 *
	 * @since 6.6.0
	 *
	 * @var string
	 */
	const KEY_OPTION_DASHBOARD = 'seopress_dashboard_option_name';

	/**
	 * The key title option.
	 *
	 * @since 4.3.0
	 *
	 * @var string
	 */
	const KEY_OPTION_TITLE = 'seopress_titles_option_name';

	/**
	 * The key sitemap option.
	 *
	 * @since 4.3.0
	 *
	 * @var string
	 */
	const KEY_OPTION_SITEMAP = 'seopress_xml_sitemap_option_name';

	/**
	 * The key social option.
	 *
	 * @since 4.5.0
	 *
	 * @var string
	 */
	const KEY_OPTION_SOCIAL = 'seopress_social_option_name';

	/**
	 * The key google analytics option.
	 *
	 * @since 5.8.0
	 *
	 * @var string
	 */
	const KEY_OPTION_GOOGLE_ANALYTICS = 'seopress_google_analytics_option_name';

	/**
	 * The key advanced option.
	 *
	 * @since 4.6.0
	 *
	 * @var string
	 */
	const KEY_OPTION_ADVANCED = 'seopress_advanced_option_name';
}
