<?php
	// To prevent calling the plugin directly
	if ( !function_exists( 'add_action' ) ) {
		echo 'Please don&rsquo;t call the plugin directly. Thanks :)';
		exit;
	}
	//Notifications Center
	function seopress_advanced_appearance_notifications_option() {
		$seopress_advanced_appearance_notifications_option = get_option("seopress_advanced_option_name");
		if ( ! empty ( $seopress_advanced_appearance_notifications_option ) ) {
			foreach ($seopress_advanced_appearance_notifications_option as $key => $seopress_advanced_appearance_notifications_value)
				$options[$key] = $seopress_advanced_appearance_notifications_value;
				if (isset($seopress_advanced_appearance_notifications_option['seopress_advanced_appearance_notifications'])) { 
				return $seopress_advanced_appearance_notifications_option['seopress_advanced_appearance_notifications'];
				}
		}
	}
	//SEO Tools
	function seopress_advanced_appearance_seo_tools_option() {
		$seopress_advanced_appearance_seo_tools_option = get_option("seopress_advanced_option_name");
		if ( ! empty ( $seopress_advanced_appearance_seo_tools_option ) ) {
			foreach ($seopress_advanced_appearance_seo_tools_option as $key => $seopress_advanced_appearance_seo_tools_value)
				$options[$key] = $seopress_advanced_appearance_seo_tools_value;
				if (isset($seopress_advanced_appearance_seo_tools_option['seopress_advanced_appearance_seo_tools'])) { 
				return $seopress_advanced_appearance_seo_tools_option['seopress_advanced_appearance_seo_tools'];
				}
		}
	}
	//Useful links
	function seopress_advanced_appearance_useful_links_option() {
		$seopress_advanced_appearance_useful_links_option = get_option("seopress_advanced_option_name");
		if ( ! empty ( $seopress_advanced_appearance_useful_links_option ) ) {
			foreach ($seopress_advanced_appearance_useful_links_option as $key => $seopress_advanced_appearance_useful_links_value)
				$options[$key] = $seopress_advanced_appearance_useful_links_value;
				if (isset($seopress_advanced_appearance_useful_links_option['seopress_advanced_appearance_useful_links'])) { 
				return $seopress_advanced_appearance_useful_links_option['seopress_advanced_appearance_useful_links'];
				}
		}
	}
?>     

<?php if (defined('SEOPRESS_WL_ADMIN_HEADER') && SEOPRESS_WL_ADMIN_HEADER === false) {
	//do nothing
} else { ?>
	<div id="seopress-admin-tabs" class="wrap">
		<?php 
			if (is_plugin_active('wp-seopress-pro/seopress-pro.php')) {
				$dashboard_settings_tabs = array(
					'tab_seopress_notifications' => __( "Notifications Center", "wp-seopress" ),
					'tab_seopress_seo_tools' => __( "SEO Tools", "wp-seopress" ),
					'tab_seopress_links' => __( "Useful links", "wp-seopress" )
				);
			} else {
				$dashboard_settings_tabs = array(
					'tab_seopress_notifications' => __( "Notifications Center", "wp-seopress" ),
					'tab_seopress_links' => __( "Useful links", "wp-seopress" )
				);
			}

			if (seopress_advanced_appearance_notifications_option() !='') {
				unset($dashboard_settings_tabs['tab_seopress_notifications']);
			}
			if (is_plugin_active('wp-seopress-pro/seopress-pro.php')) {
				if (seopress_advanced_appearance_seo_tools_option() !='') {
					unset($dashboard_settings_tabs['tab_seopress_seo_tools']);
				}
			}
			if (seopress_advanced_appearance_useful_links_option() !='') {
				unset($dashboard_settings_tabs['tab_seopress_links']);
			}
			
			echo '<div class="nav-tab-wrapper">';
			foreach ( $dashboard_settings_tabs as $tab_key => $tab_caption ) {
				echo '<a id="'. $tab_key .'-tab" class="nav-tab" href="?page=seopress-option#tab=' . $tab_key . '">' . $tab_caption . '</a>';
			}
			echo '</div>';
		?>

		<div class="wrap-seopress-tab-content">
			<?php if(seopress_advanced_appearance_notifications_option() !='1') { ?>
				<div id="tab_seopress_notifications" class="seopress-tab <?php if ($current_tab == 'tab_seopress_notifications') { echo 'active'; } ?>">
					<div id="seopress-notifications-center">
						<?php 
						function seopress_get_hidden_notices_wizard_option() {
							$seopress_get_hidden_notices_wizard_option = get_option("seopress_notices");
							if ( ! empty ( $seopress_get_hidden_notices_wizard_option ) ) {
								foreach ($seopress_get_hidden_notices_wizard_option as $key => $seopress_get_hidden_notices_wizard_value)
									$options[$key] = $seopress_get_hidden_notices_wizard_value;
									if (isset($seopress_get_hidden_notices_wizard_option['notice-wizard'])) {
										return $seopress_get_hidden_notices_wizard_option['notice-wizard'];
									}
							}
						}
						if(seopress_get_hidden_notices_wizard_option() !='1') {
							$args = [
								'id' => 'notice-wizard',
								'title' => __('Configure SEOPress in a few minutes with our installation wizard','wp-seopress'),
								'desc' => __('The best way to quickly setup SEOPress on your site.','wp-seopress'),
								'impact' => [
									'info' => __('Wizard','wp-seopress')
								],
								'link' => [
									'en' => admin_url( 'admin.php?page=seopress-setup' ),
									'title' => __('Start the wizard','wp-seopress'),
									'external' => true
								],
								'icon' => 'dashicons-admin-tools',
								'deleteable' => true
							];
							seopress_notification($args);
						}
						if (is_plugin_active('wp-seopress-insights/seopress-insights.php')) {
							function seopress_get_hidden_notices_insights_wizard_option() {
								$seopress_get_hidden_notices_insights_wizard_option = get_option("seopress_notices");
								if ( ! empty ( $seopress_get_hidden_notices_insights_wizard_option ) ) {
									foreach ($seopress_get_hidden_notices_insights_wizard_option as $key => $seopress_get_hidden_notices_insights_wizard_value)
										$options[$key] = $seopress_get_hidden_notices_insights_wizard_value;
										if (isset($seopress_get_hidden_notices_insights_wizard_option['notice-insights-wizard'])) {
											return $seopress_get_hidden_notices_insights_wizard_option['notice-insights-wizard'];
										}
								}
							}
							if(seopress_get_hidden_notices_insights_wizard_option() !='1') {
								$args = [
									'id' => 'notice-insights-wizard',
									'title' => __('Configure SEOPress Insights in a few minutes with our installation wizard','wp-seopress'),
									'desc' => __('Track your keywords positions and backlinks directly on your WordPress site.','wp-seopress'),
									'impact' => [
										'info' => __('Wizard','wp-seopress')
									],
									'link' => [
										'en' => admin_url( 'admin.php?page=seopress-insights-setup' ),
										'title' => __('Start the wizard','wp-seopress'),
										'external' => true
									],
									'icon' => 'dashicons-admin-tools',
									'deleteable' => true
								];
								seopress_notification($args);
							}
						}
						if (get_theme_support('title-tag') !='1') {
							function seopress_get_hidden_notices_title_tag_option() {
								$seopress_get_hidden_notices_title_tag_option = get_option("seopress_notices");
								if ( !empty ( $seopress_get_hidden_notices_title_tag_option ) ) {
									foreach ($seopress_get_hidden_notices_title_tag_option as $key => $seopress_get_hidden_notices_title_tag_value)
										$options[$key] = $seopress_get_hidden_notices_title_tag_value;
										if (isset($seopress_get_hidden_notices_title_tag_option['notice-title-tag'])) { 
										return $seopress_get_hidden_notices_title_tag_option['notice-title-tag'];
										}
								}
							}
							if(seopress_get_hidden_notices_title_tag_option() !='1') {
								$args = [
									'id' => 'notice-title-tag',
									'title' => __('Your theme doesn\'t use <strong>add_theme_support(\'title-tag\');</strong>','wp-seopress'),
									'desc' => __('This error indicates that your theme uses a deprecated function to generate the title tag of your pages. SEOPress will not be able to generate your custom title tags if this error is not fixed.','wp-seopress'),
									'impact' => [
										'high' => __('High impact','wp-seopress')
									],
									'link' => [
										'fr' => 'https://www.seopress.org/fr/support/guides/resoudre-add_theme_support-manquant-dans-votre-theme/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress',
										'en' => 'https://www.seopress.org/support/guides/fixing-missing-add_theme_support-in-your-theme/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress',
										'title' => __('Learn more','wp-seopress'),
										'external' => true
									],
									'icon' => 'dashicons-admin-customizer',
									'deleteable' => false
								];
								seopress_notification($args);
							}
						}
						$seo_plugins = array(
							'wordpress-seo/wp-seo.php' => 'Yoast SEO',
							'wordpress-seo-premium/wp-seo-premium.php' => 'Yoast SEO Premium',
							'all-in-one-seo-pack/all_in_one_seo_pack.php' => 'All In One SEO',
							'autodescription/autodescription.php' => 'The SEO Framework',
							'squirrly-seo/squirrly.php' => 'Squirrly SEO',
							'seo-by-rank-math/rank-math.php' => 'Rank Math',
							'seo-ultimate/seo-ultimate.php' => 'SEO Ultimate',
							'wp-meta-seo/wp-meta-seo.php' => 'WP Meta SEO',
							'premium-seo-pack/plugin.php' => 'Premium SEO Pack',
						);

						foreach($seo_plugins as $key => $value) {
							if (is_plugin_active($key)) { 
								$args = [
									'id' => 'notice-seo-plugins',
									'title' => sprintf(__('We noticed that you use <strong>%s</strong> plugin.','wp-seopress'), $value),
									'desc' => __('Do you want to migrate all your metadata to SEOPress? Do not use multiple SEO plugins at once to avoid conflicts!','wp-seopress'),
									'impact' => [
										'high' => __('High impact','wp-seopress')
									],
									'link' => [
										'en' => admin_url( 'admin.php?page=seopress-import-export' ),
										'title' => __('Migrate!','wp-seopress'),
										'external' => false
									],
									'icon' => 'dashicons-admin-plugins',
									'deleteable' => false
								];
								seopress_notification($args);
							}
						}
						if (is_plugin_active('wp-seopress-pro/seopress-pro.php')) {
							if (seopress_404_cleaning_option() == 1 && !wp_next_scheduled('seopress_404_cron_cleaning')) { 
								$args = [
									'id' => 'notice-title-tag',
									'title' => __('You have enabled 404 cleaning BUT the scheduled task is not running.','wp-seopress'),
									'desc' => __('To solve this, please disable and re-enable SEOPress PRO. No data will be lost.','wp-seopress'),
									'icon' => 'dashicons-clock',
									'deleteable' => false
								];
								seopress_notification($args);
							}
						}
						$pbuilder_plugins = array(
							'oxygen/functions.php' => 'Oxygen',
							'js_composer/js_composer.php' => 'WP Bakery',
						);
						foreach($pbuilder_plugins as $key => $value) {
							if (is_plugin_active($key)) { 
								function seopress_get_hidden_notices_pbuilders_option() {
									$seopress_get_hidden_notices_pbuilders_option = get_option("seopress_notices");
									if ( ! empty ( $seopress_get_hidden_notices_pbuilders_option ) ) {
										foreach ($seopress_get_hidden_notices_pbuilders_option as $key => $seopress_get_hidden_notices_pbuilders_value)
											$options[$key] = $seopress_get_hidden_notices_pbuilders_value;
											if (isset($seopress_get_hidden_notices_pbuilders_option['notice-page-builders'])) {
												return $seopress_get_hidden_notices_pbuilders_option['notice-page-builders'];
											}
									}
								}
								if(seopress_get_hidden_notices_pbuilders_option() !='1') {
									$args = [
										'id' => 'notice-page-builders',
										'title' => sprintf(__('Generate automatic meta description for <strong>%s</strong> plugin.','wp-seopress'), $value),
										'desc' => __('Your page builder is using shortcodes to save its data. To automatically generate your meta description based on your post content, you will have to add some hooks to your functions.php.','wp-seopress'),
										'impact' => [
											'medium' => __('Medium impact','wp-seopress')
										],
										'link' => [
											'en' => 'https://www.seopress.org/support/guides/generate-automatic-meta-description-from-page-builders/',
											'fr' => 'https://www.seopress.org/fr/support/guides/generez-automatiquement-meta-descriptions-divi-oxygen-builder/',
											'title' => __('Learn more','wp-seopress'),
											'external' => true
										],
										'icon' => 'dashicons-admin-tools',
										'deleteable' => true
									];
									seopress_notification($args);
								}
								break;
							}
						}
						$theme = wp_get_theme();
						$pbuilder_themes = array(
							'Divi' => 'Divi',
							'enfold' => 'Enfold (Avia Layout Builder)',
						);
						foreach($pbuilder_themes as $key => $value) {
							if ( $key == $theme->template || $key == $theme->parent_theme ) {
								function seopress_get_hidden_notices_themes_option() {
									$seopress_get_hidden_notices_themes_option = get_option("seopress_notices");
									if ( ! empty ( $seopress_get_hidden_notices_themes_option ) ) {
										foreach ($seopress_get_hidden_notices_themes_option as $key => $seopress_get_hidden_notices_themes_value)
											$options[$key] = $seopress_get_hidden_notices_themes_value;
											if (isset($seopress_get_hidden_notices_themes_option['notice-themes'])) {
												return $seopress_get_hidden_notices_themes_option['notice-themes'];
											}
									}
								}
								if(seopress_get_hidden_notices_themes_option() !='1') {
									$args = [
										'id' => 'notice-themes',
										'title' => sprintf(__('Generate automatic meta description for <strong>%s</strong> theme.','wp-seopress'), $value),
										'desc' => __('Your theme is using shortcodes to save its data. To automatically generate your meta description based on your post content, you will have to add some hooks to your functions.php.','wp-seopress'),
										'impact' => [
											'medium' => __('Medium impact','wp-seopress')
										],
										'link' => [
											'en' => 'https://www.seopress.org/support/guides/generate-automatic-meta-description-from-page-builders/',
											'fr' => 'https://www.seopress.org/fr/support/guides/generez-automatiquement-meta-descriptions-divi-oxygen-builder/',
											'title' => __('Learn more','wp-seopress'),
											'external' => true
										],
										'icon' => 'dashicons-admin-tools',
										'deleteable' => true
									];
									seopress_notification($args);
								}
								break;
							}
						}
						//Enfold theme
						$avia_options_enfold = get_option( 'avia_options_enfold' );
						$avia_options_enfold_child = get_option( 'avia_options_enfold_child' );
						$theme = wp_get_theme();
						if ( 'enfold' == $theme->template || 'enfold' == $theme->parent_theme ) {
							if ( $avia_options_enfold['avia']['seo_robots'] !='plugin' || $avia_options_enfold_child['avia']['seo_robots'] !='plugin' ) {
								function seopress_get_hidden_notices_enfold_option() {
									$seopress_get_hidden_notices_enfold_option = get_option("seopress_notices");
									if ( ! empty ( $seopress_get_hidden_notices_enfold_option ) ) {
										foreach ($seopress_get_hidden_notices_enfold_option as $key => $seopress_get_hidden_notices_enfold_value)
											$options[$key] = $seopress_get_hidden_notices_enfold_value;
											if (isset($seopress_get_hidden_notices_enfold_option['notice-enfold'])) { 
												return $seopress_get_hidden_notices_enfold_option['notice-enfold'];
											}
									}
								}
								if(seopress_get_hidden_notices_enfold_option() !='1') {
									$args = [
										'id' => 'notice-enfold',
										'title' => __('Enfold theme is not correctly setup for SEO!','wp-seopress'),
										'desc' => __('You must disable "Meta tag robots" option from Enfold settings (SEO Support tab) to avoid any SEO issues.','wp-seopress'),
										'impact' => [
											'low' => __('High impact','wp-seopress')
										],
										'link' => [
											'en' => admin_url('admin.php?avia_welcome=true&page=avia'),
											'title' => __('Fix this!','wp-seopress'),
											'external' => true
										],
										'icon' => 'dashicons-admin-tools',
										'deleteable' => true
									];
									seopress_notification($args);
								}
							}
						}
						if (!is_ssl()) {
							function seopress_get_hidden_notices_ssl_option() {
								$seopress_get_hidden_notices_ssl_option = get_option("seopress_notices");
								if ( ! empty ( $seopress_get_hidden_notices_ssl_option ) ) {
									foreach ($seopress_get_hidden_notices_ssl_option as $key => $seopress_get_hidden_notices_ssl_value)
										$options[$key] = $seopress_get_hidden_notices_ssl_value;
										if (isset($seopress_get_hidden_notices_ssl_option['notice-ssl'])) { 
										return $seopress_get_hidden_notices_ssl_option['notice-ssl'];
										}
								}
							}
							if(seopress_get_hidden_notices_ssl_option() !='1') {
								$args = [
									'id' => 'notice-ssl',
									'title' => __('Your site doesn\'t use an SSL certificate!','wp-seopress'),
									'desc' => __('Https is considered by Google as a positive signal for the ranking of your site. It also reassures your visitors for data security, and improves trust.','wp-seopress').' <a href="https://webmasters.googleblog.com/2014/08/https-as-ranking-signal.html" target="_blank">'.__('Learn more','wp-seopress').'</a>',
									'impact' => [
										'low' => __('Low impact','wp-seopress')
									],
									'link' => [
										'en' => 'https://www.seopress.org/go/namecheap',
										'title' => __('Buy an SSL!','wp-seopress'),
										'external' => true
									],
									'icon' => 'dashicons-unlock',
									'deleteable' => true
								];
								seopress_notification($args);
							}
						}
						if (function_exists('extension_loaded') && !extension_loaded('dom')) {
							$args = [
								'id' => 'notice-ssl-alert',
								'title' => __('PHP module "DOM" is missing on your server.','wp-seopress'),
								'desc' => __('This PHP module, installed by default with PHP, is required by many plugins including SEOPress. Please contact your host as soon as possible to solve this.','wp-seopress'),
								'impact' => [
									'high' => __('High impact','wp-seopress')
								],
								'link' => [
									'fr' => 'https://www.seopress.org/fr/support/guides/debutez-seopress/',
									'en' => 'https://www.seopress.org/support/guides/get-started-seopress/',
									'title' => __('Learn more','wp-seopress'),
									'external' => true
								],
								'deleteable' => true
							];
							seopress_notification($args);
						}
						if (!function_exists('seopress_titles_noindex_option')) {
							function seopress_titles_noindex_option() {
								$seopress_titles_noindex_option = get_option("seopress_titles_option_name");
								if ( ! empty ( $seopress_titles_noindex_option ) ) {
									foreach ($seopress_titles_noindex_option as $key => $seopress_titles_noindex_value)
										$options[$key] = $seopress_titles_noindex_value;
									if (isset($seopress_titles_noindex_option['seopress_titles_noindex'])) { 
										return $seopress_titles_noindex_option['seopress_titles_noindex'];
									}
								}
							}
						}
						if (seopress_titles_noindex_option()=='1' || get_option('blog_public') !='1') { 
							$args = [
								'id' => 'notice-noindex',
								'title' => __('Your site is not visible to Search Engines!','wp-seopress'),
								'desc' => __('You have activated the blocking of the indexing of your site. If your site is under development, this is probably normal. Otherwise, check your settings. Delete this notification using the cross on the right if you are not concerned.','wp-seopress'),
								'impact' => [
									'high' => __('High impact','wp-seopress')
								],
								'link' => [
									'en' => admin_url( 'options-reading.php' ),
									'title' => __('Fix this!','wp-seopress'),
									'external' => false
								],
								'icon' => 'dashicons-warning',
								'deleteable' => true
							];
							seopress_notification($args);
						}
						if (get_option('blogname') =='') { 
							$args = [
								'id' => 'notice-title-empty',
								'title' => __('Your site title is empty!','wp-seopress'),
								'desc' => __('Your Site Title is used by WordPress, your theme and your plugins including SEOPress. It is an essential component in the generation of title tags, but not only. Enter one!','wp-seopress'),
								'impact' => [
									'high' => __('High impact','wp-seopress')
								],
								'link' => [
									'en' => admin_url( 'options-general.php' ),
									'title' => __('Fix this!','wp-seopress'),
									'external' => false
								],
								'deleteable' => false
							];
							seopress_notification($args);
						}
						if (get_option('permalink_structure') =='') {
							$args = [
								'id' => 'notice-permalinks',
								'title' => __('Your permalinks are not SEO Friendly! Enable pretty permalinks to fix this.','wp-seopress'),
								'desc' => __('Why is this important? Showing only the summary of each article significantly reduces the theft of your content by third party sites. Not to mention, the increase in your traffic, your advertising revenue, conversions...','wp-seopress'),
								'impact' => [
									'high' => __('High impact','wp-seopress')
								],
								'link' => [
									'en' => admin_url( 'options-permalink.php' ),
									'title' => __('Fix this!','wp-seopress'),
									'external' => false
								],
								'icon' => 'dashicons-admin-links',
								'deleteable' => false
							];
							seopress_notification($args);
						}
						if(get_option('rss_use_excerpt') =='0') {
							function seopress_get_hidden_notices_rss_use_excerpt_option() {
								$seopress_get_hidden_notices_rss_use_excerpt_option = get_option("seopress_notices");
								if ( ! empty ( $seopress_get_hidden_notices_rss_use_excerpt_option ) ) {
									foreach ($seopress_get_hidden_notices_rss_use_excerpt_option as $key => $seopress_get_hidden_notices_rss_use_excerpt_value)
										$options[$key] = $seopress_get_hidden_notices_rss_use_excerpt_value;
										if (isset($seopress_get_hidden_notices_rss_use_excerpt_option['notice-rss-use-excerpt'])) {
											return $seopress_get_hidden_notices_rss_use_excerpt_option['notice-rss-use-excerpt'];
										}
								}
							}
							if(seopress_get_hidden_notices_rss_use_excerpt_option() !='1') {
								$args = [
									'id' => 'notice-rss-use-excerpt',
									'title' => __('Your RSS feed shows full text!','wp-seopress'),
									'desc' => __('Why is this important? Showing only the summary of each article significantly reduces the theft of your content by third party sites. Not to mention, the increase in your traffic, your advertising revenue, conversions...','wp-seopress'),
									'impact' => [
										'medium' => __('Medium impact','wp-seopress')
									],
									'link' => [
										'en' => admin_url( 'options-reading.php' ),
										'title' => __('Fix this!','wp-seopress'),
										'external' => false
									],
									'icon' => 'dashicons-rss',
									'deleteable' => true
								];
								seopress_notification($args);
							}
						}
						
						function seopress_get_hidden_notices_review_option() {
							$seopress_get_hidden_notices_review_option = get_option("seopress_notices");
							if ( ! empty ( $seopress_get_hidden_notices_review_option ) ) {
								foreach ($seopress_get_hidden_notices_review_option as $key => $seopress_get_hidden_notices_review_value)
									$options[$key] = $seopress_get_hidden_notices_review_value;
									if (isset($seopress_get_hidden_notices_review_option['notice-review'])) {
										return $seopress_get_hidden_notices_review_option['notice-review'];
									}
							}
						}
						if(seopress_get_hidden_notices_review_option() !='1') {
							$args = [
								'id' => 'notice-review',
								'title' => __('You like SEOPress? Please help us by rating us 5 stars!','wp-seopress'),
								'desc' => __('Support the development and improvement of the plugin by taking 15 seconds of your time to leave us a user review on the official WordPress plugins repository. Thank you!','wp-seopress'),
								'impact' => [
									'info' => __('Information','wp-seopress')
								],
								'link' => [
									'en' => 'https://wordpress.org/support/view/plugin-reviews/wp-seopress?rate=5#postform',
									'title' => __('Rate us!','wp-seopress'),
									'external' => true
								],
								'icon' => 'dashicons-thumbs-up',
								'deleteable' => true
							];
							seopress_notification($args);
						}
						if(get_option('page_comments') =='1') {
							function seopress_get_hidden_notices_divide_comments_option() {
								$seopress_get_hidden_notices_divide_comments_option = get_option("seopress_notices");
								if ( ! empty ( $seopress_get_hidden_notices_divide_comments_option ) ) {
									foreach ($seopress_get_hidden_notices_divide_comments_option as $key => $seopress_get_hidden_notices_divide_comments_value)
										$options[$key] = $seopress_get_hidden_notices_divide_comments_value;
										if (isset($seopress_get_hidden_notices_divide_comments_option['notice-divide-comments'])) {
											return $seopress_get_hidden_notices_divide_comments_option['notice-divide-comments'];
										}
								}
							}
							if(seopress_get_hidden_notices_divide_comments_option() !='1') {
								$args = [
									'id' => 'notice-divide-comments',
									'title' => __('Break comments into pages is ON!','wp-seopress'),
									'desc' => __('Enabling this option will create duplicate content for each article beyond x comments. This can have a disastrous effect by creating a large number of poor quality pages, and slowing the Google bot unnecessarily, so your ranking in search results.','wp-seopress'),
									'impact' => [
										'high' => __('High impact','wp-seopress')
									],
									'link' => [
										'en' => admin_url( 'options-discussion.php' ),
										'title' => __('Disable this!','wp-seopress'),
										'external' => false
									],
									'icon' => 'dashicons-admin-comments',
									'deleteable' => true
								];
								seopress_notification($args);
							}
						} 
						if(get_option('posts_per_page') < '16') {
							function seopress_get_hidden_notices_posts_number_option() {
								$seopress_get_hidden_notices_posts_number_option = get_option("seopress_notices");
								if ( ! empty ( $seopress_get_hidden_notices_posts_number_option ) ) {
									foreach ($seopress_get_hidden_notices_posts_number_option as $key => $seopress_get_hidden_notices_posts_number_value)
										$options[$key] = $seopress_get_hidden_notices_posts_number_value;
										if (isset($seopress_get_hidden_notices_posts_number_option['notice-posts-number'])) {
											return $seopress_get_hidden_notices_posts_number_option['notice-posts-number'];
										}
								}
							}
							if(seopress_get_hidden_notices_posts_number_option() !='1') {
								$args = [
									'id' => 'notice-posts-number',
									'title' => __('Display more posts per page on homepage and archives','wp-seopress'),
									'desc' => __('To reduce the number pages search engines have to crawl to find all your articles, it is recommended displaying more posts per page. This should not be a problem for your users. Using mobile, we prefer to scroll down rather than clicking on next page links.','wp-seopress'),
									'impact' => [
										'medium' => __('Medium impact','wp-seopress')
									],
									'link' => [
										'en' => admin_url( 'options-reading.php' ),
										'title' => __('Fix this!','wp-seopress'),
										'external' => false
									],
									'deleteable' => true
								];
								seopress_notification($args);
							}
						}
						if (seopress_xml_sitemap_general_enable_option() !='1') {
							$args = [
								'id' => 'notice-xml-sitemaps',
								'title' => __('You don\'t have an XML Sitemap!','wp-seopress'),
								'desc' => __('XML Sitemaps are useful to facilitate the crawling of your content by search engine robots. Indirectly, this can benefit your ranking by reducing the crawl bugdet.','wp-seopress'),
								'impact' => [
									'medium' => __('Medium impact','wp-seopress')
								],
								'link' => [
									'en' => admin_url( 'admin.php?page=seopress-xml-sitemap' ),
									'title' => __('Fix this!','wp-seopress'),
									'external' => false
								],
								'icon' => 'dashicons-warning',
								'deleteable' => false
							];
							seopress_notification($args);
						}
						function seopress_get_hidden_notices_google_business_option() {
							$seopress_get_hidden_notices_google_business_option = get_option("seopress_notices");
							if ( ! empty ( $seopress_get_hidden_notices_google_business_option ) ) {
								foreach ($seopress_get_hidden_notices_google_business_option as $key => $seopress_get_hidden_notices_google_business_value)
									$options[$key] = $seopress_get_hidden_notices_google_business_value;
									if (isset($seopress_get_hidden_notices_google_business_option['notice-google-business'])) { 
									return $seopress_get_hidden_notices_google_business_option['notice-google-business'];
									}
							}
						}
						if(seopress_get_hidden_notices_google_business_option() !='1') { 
							$args = [
								'id' => 'notice-google-business',
								'title' => __('Do you have a Google My Business page? It\'s free!','wp-seopress'),
								'desc' => __('Local Business websites should have a My Business page to improve visibility in search results. Click on the cross on the right to delete this notification if you are not concerned.','wp-seopress'),
								'impact' => [
									'high' => __('High impact','wp-seopress')
								],
								'link' => [
									'en' => 'https://www.google.com/business/go/',
									'title' => __('Create your page now!','wp-seopress'),
									'external' => true
								],
								'deleteable' => true
							];
							seopress_notification($args);
						}
						function seopress_get_hidden_notices_search_console_option() {
							$seopress_get_hidden_notices_search_console_option = get_option("seopress_notices");
							if ( ! empty ( $seopress_get_hidden_notices_search_console_option ) ) {
								foreach ($seopress_get_hidden_notices_search_console_option as $key => $seopress_get_hidden_notices_search_console_value)
									$options[$key] = $seopress_get_hidden_notices_search_console_value;
									if (isset($seopress_get_hidden_notices_search_console_option['notice-search-console'])) { 
									return $seopress_get_hidden_notices_search_console_option['notice-search-console'];
									}
							}
						}
						function seopress_get_google_site_verification_option() {
							$seopress_get_google_site_verification_option = get_option("seopress_advanced_option_name");
							if ( ! empty ( $seopress_get_google_site_verification_option ) ) {
								foreach ($seopress_get_google_site_verification_option as $key => $seopress_get_google_site_verification_value)
									$options[$key] = $seopress_get_google_site_verification_value;
									if (isset($seopress_get_google_site_verification_option['seopress_advanced_advanced_google'])) { 
									return $seopress_get_google_site_verification_option['seopress_advanced_advanced_google'];
									}
							}
						}
						if(seopress_get_hidden_notices_search_console_option() !='1' && seopress_get_google_site_verification_option() =='') { 
							$args = [
								'id' => 'notice-search-console',
								'title' => __('Add your site to Google. It\'s free!','wp-seopress'),
								'desc' => __('Is your brand new site online? So reference it as quickly as possible on Google to get your first visitors via Google Search Console. Already the case? Click on the cross on the right to remove this alert.','wp-seopress'),
								'impact' => [
									'high' => __('High impact','wp-seopress')
								],
								'link' => [
									'en' => 'https://www.google.com/webmasters/tools/home',
									'title' => __('Add your site to Search Console!','wp-seopress'),
									'external' => true
								],
								'deleteable' => true
							];
							seopress_notification($args);
						}
						if (is_plugin_active('wp-seopress-pro/seopress-pro.php')) {
							if(function_exists('seopress_rich_snippets_enable_option') && seopress_rich_snippets_enable_option() !="1") {
								$args = [
									'id' => 'notice-schemas-metabox',
									'title' => __('Structured data types is not correctly enabled','wp-seopress'),
									'desc' => __('Please enable <strong>Structured Data Types metabox for your posts, pages and custom post types</strong> option in order to use automatic and manual schemas. (SEO > PRO > Structured Data Types (schema.org)','wp-seopress'),
									'impact' => [
										'high' => __('High impact','wp-seopress')
									],
									'link' => [
										'en' => esc_url( admin_url('admin.php?page=seopress-pro-page#tab=tab_seopress_rich_snippets')),
										'title' => __('Fix this!','wp-seopress'),
										'external' => false
									],
									'icon' => 'dashicons-warning',
									'deleteable' => false
								];
								seopress_notification($args);
							}
						} 
						if (get_option( 'seopress_pro_license_status' ) !='valid' && is_plugin_active('wp-seopress-pro/seopress-pro.php')) {
							$args = [
								'id' => 'notice-license',
								'title' => __('You have to enter your licence key to get updates and support','wp-seopress'),
								'desc' => __('Please activate the SEOPress PRO license key to automatically receive updates to guarantee you the best user experience possible.','wp-seopress'),
								'impact' => [
									'info' => __('License','wp-seopress')
								],
								'link' => [
									'en' => admin_url( 'admin.php?page=seopress-license' ),
									'title' => __('Fix this!','wp-seopress'),
									'external' => false
								],
								'icon' => 'dashicons-admin-network',
								'deleteable' => false
							];
							seopress_notification($args);
						}
						if (!is_plugin_active('wp-seopress-pro/seopress-pro.php')) {
							function seopress_get_hidden_notices_go_pro_option() {
								$seopress_get_hidden_notices_go_pro_option = get_option("seopress_notices");
								if ( ! empty ( $seopress_get_hidden_notices_go_pro_option ) ) {
									foreach ($seopress_get_hidden_notices_go_pro_option as $key => $seopress_get_hidden_notices_go_pro_value)
										$options[$key] = $seopress_get_hidden_notices_go_pro_value;
									if (isset($seopress_get_hidden_notices_go_pro_option['notice-go-pro'])) { 
										return $seopress_get_hidden_notices_go_pro_option['notice-go-pro'];
									}
								}
							}
							if(seopress_get_hidden_notices_go_pro_option() !='1' && seopress_get_hidden_notices_go_pro_option() =='') { 
								$args = [
									'id' => 'notice-go-pro',
									'title' => __('Take your SEO to the next level with SEOPress PRO!','wp-seopress'),
									'desc' => __('The PRO version of SEOPress allows you to easily manage your structured data (schemas), add a breadcrumb optimized for SEO and accessibility, improve SEO for WooCommerce, gain productivity with our import / export tool from a CSV of your metadata and so much more.','wp-seopress'),
									'impact' => [
										'info' => __('PRO','wp-seopress')
									],
									'link' => [
										'fr' => 'https://www.seopress.org/fr?utm_source=plugin&utm_medium=notification&utm_campaign=dashboard',
										'en' => 'https://www.seopress.org/?utm_source=plugin&utm_medium=notification&utm_campaign=dashboard',
										'title' => __('Upgrade now!','wp-seopress'),
										'external' => true
									],
									'deleteable' => true
								];
								seopress_notification($args);
							}
						} ?>
					</div><!--#seopress-notifications-center-->
				</div>
			<?php } ?>

			<?php if (is_plugin_active('wp-seopress-pro/seopress-pro.php')) { ?>
				<div id="tab_seopress_seo_tools" class="seopress-tab seopress-useful-tools <?php if ($current_tab == 'tab_seopress_seo_tools') { echo 'active'; } ?>">
					
					<!-- Reverse -->
					<div class="widget widget-reverse">
						<h3 class="widget-title"><span class="dashicons dashicons-welcome-view-site"></span><?php _e('Check websites setup on your server','wp-seopress'); ?></h3>

						<p>
						<?php
							if ( get_transient( 'seopress_results_reverse' ) !='' ) { 

								$seopress_results_reverse = (array)json_decode(get_transient( 'seopress_results_reverse' ));

								//Init
								$seopress_results_reverse_remote_ip_address = __('Not found','wp-seopress');
								if(isset($seopress_results_reverse['remoteIpAddress'])) { 
									$seopress_results_reverse_remote_ip_address = $seopress_results_reverse['remoteIpAddress'];
								}

								$seopress_results_reverse_last_scrape = __('No scrape.','wp-seopress');
								if(isset($seopress_results_reverse['lastScrape'])) { 
									$seopress_results_reverse_last_scrape = $seopress_results_reverse['lastScrape'];
								}

								$seopress_results_reverse_domain_count = __('No domain found.','wp-seopress');
								if(isset($seopress_results_reverse['domainCount'])) { 
									$seopress_results_reverse_domain_count = $seopress_results_reverse['domainCount'];
								}

								$seopress_results_reverse_domain_array = '';
								if(isset($seopress_results_reverse['domainArray'])) { 
									$seopress_results_reverse_domain_array = $seopress_results_reverse['domainArray'];
								}
									
								echo '<p class="remote-ip"><strong>'.__('Server IP Address: ','wp-seopress').'</strong>'.$seopress_results_reverse_remote_ip_address.'</p>';
								

								echo '<p class="last-scrape"><strong>'.__('Last scrape: ','wp-seopress').'</strong>'.$seopress_results_reverse_last_scrape.'</p>';
								echo '<p class="domain-count"><strong>'.__('Number of websites on your server: ','wp-seopress').'</strong>'.$seopress_results_reverse_domain_count.'</p>';
								
								if ($seopress_results_reverse_domain_array !='') {
									echo '<ul>';
										foreach ($seopress_results_reverse_domain_array as $key => $value) {
											echo '<li><span class="dashicons dashicons-minus"></span><a href="//'.preg_replace('#^https?://#', '', $value[0]).'" target="_blank">'.$value[0].'</a><span class="dashicons dashicons-external"></span></li>';
										}
									echo '</ul>';
								}
							}
						?>
						<br>
						<button id="seopress-reverse-submit"  type="button" class="button button-primary" name="submit">
							<?php _e('Get list','wp-seopress'); ?>
						</button>

						<span id="spinner-reverse" class="spinner"></span>
					</div>
				</div>
			<?php } ?>
			<div id="tab_seopress_links" class="seopress-tab seopress-useful-tools <?php if ($current_tab == 'tab_seopress_links') { echo 'active'; } ?>">
				<ul>
					<li><span class="dashicons dashicons-arrow-right-alt2"></span><a href="https://www.seopress.org/blog/" target="_blank"><?php _e('Our blog: SEO news, how-to, tips and tricks...','wp-seopress'); ?></a><span class="dashicons dashicons-external"></span></li>
					<li><span class="dashicons dashicons-arrow-right-alt2"></span><a href="https://www.google.com/webmasters/tools/disavow-links-main" target="_blank"><?php _e('Upload a list of links to disavow to Google','wp-seopress'); ?></a><span class="dashicons dashicons-external"></span></li>
					<?php if ( !is_plugin_active( 'imageseo/imageseo.php' )) {
						echo '<li><span class="dashicons dashicons-arrow-right-alt2"></span><a href="https://imageseo.io?_from=seopress" target="_blank">'.__('Image SEO plugin to optimize your image ALT texts and names for Search Engines.','wp-seopress').'</a><span class="dashicons dashicons-external"></span></li>';
					} ?>
					<li><span class="dashicons dashicons-arrow-right-alt2"></span><a href="https://www.dareboost.com/en/home" target="_blank"><?php _e('Dareboost: Test, analyze and optimize your website','wp-seopress'); ?></a><span class="dashicons dashicons-external"></span></li>
					<li><span class="dashicons dashicons-arrow-right-alt2"></span><a href="https://ga-dev-tools.appspot.com/campaign-url-builder/" target="_blank"><?php _e('Google Campaign URL Builder tool','wp-seopress'); ?></a><span class="dashicons dashicons-external"></span></li>
				</ul>
			</div>
		</div>
	</div>
<?php } ?>