<?php defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)'); ?>

<div id="seopress-page-list" class="seopress-page-list seopress-card">
	<div class="seopress-card-title">
		<div>
			<h2><?php esc_attr_e('SEO management', 'wp-seopress'); ?></h2>
			<p>
				<?php echo wp_kses_post(__('Quickly enable / disable SEO modules to fit your needs. Click the <strong>Manage settings</strong> button to configure it.', 'wp-seopress')); ?>
			</p>
		</div>
		<div>
			<button type="button" class="seopress-btn-view-switch seopress-btn" title="<?php esc_attr_e('Toggle simple / default view', 'wp-seopress'); ?>">
				<span class="dashicons dashicons-grid-view"></span>
			</button>
		</div>
	</div>

	<?php
		$docs = seopress_get_docs_links();

		$features = [
			'titles' => [
				'svg'           => SEOPRESS_URL_ASSETS . '/img/ico-titles-metas.svg',
				'title'         => __('Titles & Metas', 'wp-seopress'),
				'desc'          => __('Manage all your titles & metas for post types, taxonomies, archives...', 'wp-seopress'),
				'btn_primary'   => admin_url('admin.php?page=seopress-titles'),
				'filter'        => 'seopress_remove_feature_titles',
			],
			'xml-sitemap' => [
				'svg'           => SEOPRESS_URL_ASSETS . '/img/ico-sitemaps.svg',
				'title'         => __('XML & HTML Sitemaps', 'wp-seopress'),
				'desc'          => __('Manage your XML - Image - Video - HTML Sitemap.', 'wp-seopress'),
				'btn_primary'   => admin_url('admin.php?page=seopress-xml-sitemap'),
				'filter'        => 'seopress_remove_feature_xml_sitemap',
			],
			'social' => [
				'svg'           => SEOPRESS_URL_ASSETS . '/img/ico-social-networks.svg',
				'title'         => __('Social Networks', 'wp-seopress'),
				'desc'          => __('Open Graph, X Cards, Google Knowledge Graph and more...', 'wp-seopress'),
				'btn_primary'   => admin_url('admin.php?page=seopress-social'),
				'filter'        => 'seopress_remove_feature_social',
			],
			'google-analytics' => [
				'svg'           => SEOPRESS_URL_ASSETS . '/img/ico-analytics.svg',
				'title'         => __('Analytics', 'wp-seopress'),
				'desc'          => __('Track everything about your visitors with Google Analytics / Matomo / Microsoft Clarity.', 'wp-seopress'),
				'btn_primary'   => admin_url('admin.php?page=seopress-google-analytics'),
				'filter'        => 'seopress_remove_feature_google_analytics',
			],
			'instant-indexing' => [
				'svg'           => SEOPRESS_URL_ASSETS . '/img/ico-instant-indexing.svg',
				'title'         => __('Instant Indexing', 'wp-seopress'),
				'desc'          => __('Ping Google & Bing to quickly index your content.', 'wp-seopress'),
				'btn_primary'   => admin_url('admin.php?page=seopress-instant-indexing'),
				'filter'        => 'seopress_remove_feature_instant_indexing',
			],
			'advanced' => [
				'svg'           => SEOPRESS_URL_ASSETS . '/img/ico-advanced.svg',
				'title'         => __('Image SEO & Advanced settings', 'wp-seopress'),
				'desc'          => __('Optimize your images for SEO. Configure advanced settings.', 'wp-seopress'),
				'btn_primary'   => admin_url('admin.php?page=seopress-advanced'),
				'filter'        => 'seopress_remove_feature_advanced',
			],
			'universal-metabox' => [
				'svg'           => SEOPRESS_URL_ASSETS . '/img/ico-universal-metabox.svg',
				'title'         => __('Universal SEO metabox', 'wp-seopress'),
				'desc'          => __('Easily manage your SEO settings from your favorite page builder or editor.', 'wp-seopress'),
				'btn_primary'   => admin_url('admin.php?page=seopress-advanced#tab=tab_seopress_advanced_appearance'),
				'filter'        => 'seopress_remove_feature_advanced',
			],
		];

		$features = apply_filters('seopress_features_list_before_tools', $features);

		$features['tools'] = [
			'svg'           => SEOPRESS_URL_ASSETS . '/img/ico-tools.svg',
			'title'         => __('Tools', 'wp-seopress'),
			'desc'          => __('Import/Export plugin settings from site to site.', 'wp-seopress'),
			'btn_primary'   => admin_url('admin.php?page=seopress-import-export'),
			'filter'        => 'seopress_remove_feature_tools',
			'toggle'        => false,
		];

		$features = apply_filters('seopress_features_list_after_tools', $features);

		if (! empty($features)) { ?>
			<div class="seopress-card-content">

				<?php foreach ($features as $key => $value) {
					if (isset($value['filter'])) {
						$seopress_feature = apply_filters($value['filter'], true);
					}

					if (true === $seopress_feature) {
						$svg              = isset($value['svg']) ? $value['svg'] : null;
						$title            = isset($value['title']) ? $value['title'] : null;
						$desc             = isset($value['desc']) ? $value['desc'] : null;
						$btn_primary      = isset($value['btn_primary']) ? $value['btn_primary'] : '';
						$actions          = isset($value['actions']) ? $value['actions'] : '';
						$toggle           = isset($value['toggle']) ? $value['toggle'] : true;

						if (true === $toggle) {
							$class = "";

							if ('universal-metabox' === $key) {
								$toggle = get_option('seopress_advanced_option_name');
								$toggle = $toggle['seopress_advanced_appearance_universal_metabox_disable'];

								if ('1' === $toggle) {
									$seopress_get_toggle_option = '0';
								} else {
									$seopress_get_toggle_option = '1';
									$class = ' is-seopress-feature-active';
								}

								$toggle = true;
							} else {
								if ('1' == seopress_get_toggle_option($key)) {
									$seopress_get_toggle_option = '1';
									$class = ' is-seopress-feature-active';
								} else {
									$seopress_get_toggle_option = '0';
								}
							}	
						}
					?>

					<div class="seopress-cart-list<?php echo esc_attr($class);?>">
						<div class="seopress-card-item">
							<div class="seopress-card-header seopress-d-flex seopress-align-items-center seopress-space-between">
								<div class="seopress-card-name seopress-d-flex seopress-align-items-center">
									<div class="seopress-item-icons">
										<?php if (isset($svg)) { ?>
											<div class="seopress-item-icon"><img src="<?php echo esc_url($svg); ?>" alt="" width="40" height="40"/></div>
										<?php } ?>
									</div>

									<h3 class="name">
										<a href="<?php echo esc_url($btn_primary); ?>">
											<?php echo esc_html($title); ?>
										</a>
									</h3>
								</div>

								<?php if (true === $toggle) { ?>
									<div class="seopress-d-flex">
										<span class="screen-reader-text"><?php
											/* translators: %s name of the feature, eg: Titles and metas */
											printf(esc_attr__('Toggle %s','wp-seopress'), esc_attr($title)); ?></span>
										<input type="checkbox" name="toggle-<?php echo esc_attr($key); ?>" id="toggle-<?php echo esc_attr($key); ?>" class="toggle" data-toggle="<?php echo esc_attr($seopress_get_toggle_option); ?>">
										<label for="toggle-<?php echo esc_attr($key); ?>"></label>
									</div>
								<?php } ?>
							</div>
							<p class="item-desc"><?php echo esc_html($desc); ?></p>
						</div>
						<div class="seopress-item-footer">
							<a href="<?php echo esc_url($btn_primary); ?>" class="seopress-btn" title="<?php esc_attr_e( 'Manage settings', 'wp-seopress' ); ?>">
								<?php esc_attr_e( 'Manage settings', 'wp-seopress' ); ?>
							</a>
						</div>
					</div>
				<?php
					}
				} ?>
			</div>
	<?php }
	?>
</div>
