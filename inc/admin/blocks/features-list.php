<?php
	// To prevent calling the plugin directly
	if (! function_exists('add_action')) {
		echo 'Please don&rsquo;t call the plugin directly. Thanks :)';
		exit;
	}
?>
<div id="seopress-page-list" class="seopress-page-list seopress-card">
	<div class="seopress-card-title">
		<h2><?php _e('SEO management', 'wp-seopress'); ?></h2>
	</div>

	<?php
		$docs = seopress_get_docs_links();

		$features = [
			'titles' => [
				'svg'           => SEOPRESS_URL_ASSETS . '/img/ico-titles-metas.svg',
				'title'         => __('Titles & Metas', 'wp-seopress'),
				'desc'          => __('Manage all your titles & metas for post types, taxonomies, archives...', 'wp-seopress'),
				'btn_primary'   => admin_url('admin.php?page=seopress-titles'),
				'help'          => $docs['titles']['manage'],
				'filter'        => 'seopress_remove_feature_titles',
			],
			'xml-sitemap' => [
				'svg'           => SEOPRESS_URL_ASSETS . '/img/ico-sitemaps.svg',
				'title'         => __('XML & HTML Sitemaps', 'wp-seopress'),
				'desc'          => __('Manage your XML - Image - Video - HTML Sitemap.', 'wp-seopress'),
				'btn_primary'   => admin_url('admin.php?page=seopress-xml-sitemap'),
				'help'          => $docs['sitemaps']['xml'],
				'filter'        => 'seopress_remove_feature_xml_sitemap',
			],
			'social' => [
				'svg'           => SEOPRESS_URL_ASSETS . '/img/ico-social-networks.svg',
				'title'         => __('Social Networks', 'wp-seopress'),
				'desc'          => __('Open Graph, Twitter Card, Google Knowledge Graph and more...', 'wp-seopress'),
				'btn_primary'   => admin_url('admin.php?page=seopress-social'),
				'help'          => $docs['social']['og'],
				'filter'        => 'seopress_remove_feature_social',
			],
			'google-analytics' => [
				'svg'           => SEOPRESS_URL_ASSETS . '/img/ico-analytics.svg',
				'title'         => __('Analytics', 'wp-seopress'),
				'desc'          => __('Track everything about your visitors with Google Analytics / Matomo / Microsoft Clarity.', 'wp-seopress'),
				'btn_primary'   => admin_url('admin.php?page=seopress-google-analytics'),
                'help'          => $docs['analytics']['quick_start'],
				'filter'        => 'seopress_remove_feature_google_analytics',
			],
			'instant-indexing' => [
				'svg'           => SEOPRESS_URL_ASSETS . '/img/ico-instant-indexing.svg',
				'title'         => __('Instant Indexing', 'wp-seopress'),
				'desc'          => __('Ping Google & Bing to quickly index your content.', 'wp-seopress'),
				'btn_primary'   => admin_url('admin.php?page=seopress-instant-indexing'),
                'help'          => $docs['indexing_api']['google'],
				'filter'        => 'seopress_remove_feature_instant_indexing',
			],
			'advanced' => [
				'svg'           => SEOPRESS_URL_ASSETS . '/img/ico-advanced.svg',
				'title'         => __('Image SEO & Advanced settings', 'wp-seopress'),
				'desc'          => __('Optimize your images for SEO. Configure advanced settings.', 'wp-seopress'),
				'btn_primary'   => admin_url('admin.php?page=seopress-advanced'),
                'help'          => $docs['advanced']['imageseo'],
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
					?>

					<div class="seopress-cart-list">

						<?php
						if (true === $seopress_feature) {
							$svg              = isset($value['svg']) ? $value['svg'] : null;
							$title            = isset($value['title']) ? $value['title'] : null;
							$desc             = isset($value['desc']) ? $value['desc'] : null;
							$btn_primary      = isset($value['btn_primary']) ? $value['btn_primary'] : '';
							$help             = isset($value['help']) ? $value['help'] : null;
							$toggle           = isset($value['toggle']) ? $value['toggle'] : true;

							if (true === $toggle) {
								$class = "";
								if ('1' == seopress_get_toggle_option($key)) {
									$seopress_get_toggle_option = '1';
									$class = ' is-seopress-feature-active';
								} else {
									$seopress_get_toggle_option = '0';
								}
							}
							?>

							<div class="seopress-card-item">
								<div class="seopress-item-icons">
									<?php if (isset($svg)) { ?>
										<div class="seopress-item-icon"><img src="<?php echo $svg; ?>" alt="" width="40" height="40"/></div>
									<?php } ?>

									<?php if (isset($help)) { ?>
										<a href="<?php echo $help; ?>" class="seopress-item-help seopress-help" target="_blank">
											<svg width="7" height="10" viewBox="0 0 7 10" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4.0625 8.4375C4.0625 8.62292 4.00752 8.80418 3.9045 8.95835C3.80149 9.11252 3.65507 9.23268 3.48377 9.30364C3.31246 9.37459 3.12396 9.39316 2.9421 9.35699C2.76025 9.32081 2.5932 9.23152 2.46209 9.10041C2.33098 8.9693 2.24169 8.80225 2.20551 8.6204C2.16934 8.43854 2.18791 8.25004 2.25886 8.07874C2.32982 7.90743 2.44998 7.76101 2.60415 7.658C2.75832 7.55498 2.93958 7.5 3.125 7.5C3.37364 7.5 3.6121 7.59877 3.78791 7.77459C3.96373 7.9504 4.0625 8.18886 4.0625 8.4375ZM3.125 0C1.40156 0 0 1.26172 0 2.8125V3.125C0 3.29076 0.0658485 3.44973 0.183059 3.56694C0.300269 3.68415 0.45924 3.75 0.625 3.75C0.79076 3.75 0.949732 3.68415 1.06694 3.56694C1.18415 3.44973 1.25 3.29076 1.25 3.125V2.8125C1.25 1.95312 2.09141 1.25 3.125 1.25C4.15859 1.25 5 1.95312 5 2.8125C5 3.67188 4.15859 4.375 3.125 4.375C2.95924 4.375 2.80027 4.44085 2.68306 4.55806C2.56585 4.67527 2.5 4.83424 2.5 5V5.625C2.5 5.79076 2.56585 5.94973 2.68306 6.06694C2.80027 6.18415 2.95924 6.25 3.125 6.25C3.29076 6.25 3.44973 6.18415 3.56694 6.06694C3.68415 5.94973 3.75 5.79076 3.75 5.625V5.56875C5.175 5.30703 6.25 4.17031 6.25 2.8125C6.25 1.26172 4.84844 0 3.125 0Z" fill="#0C082F"/></svg>
										</a>
									<?php } ?>
								</div>

								<h3><?php echo $title; ?></h3>
								<p><?php echo $desc; ?></p>
							</div>

							<div class="seopress-item-footer<?php echo $class;?>">
								<a href="<?php echo $btn_primary; ?>" class="seopress-btn">
									<?php _e('Manage', 'wp-seopress'); ?>
								</a>

								<?php if (true === $toggle) { ?>
									<span class="screen-reader-text"><?php printf(__('Toggle %s','wp-seopress'), $title); ?></span>
									<input type="checkbox" name="toggle-<?php echo $key; ?>" id="toggle-<?php echo $key; ?>" class="toggle" data-toggle="<?php echo $seopress_get_toggle_option; ?>">
									<label for="toggle-<?php echo $key; ?>"></label>
								<?php } ?>
							</div>
						<?php
						}
					?>
					</div>
				<?php
				} ?>
			</div>
	<?php }
	?>
</div>
