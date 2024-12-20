<?php
	defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

	if (is_plugin_active('wp-seopress-pro/seopress-pro.php')) {
        if (method_exists(seopress_get_service('ToggleOption'), 'getToggleWhiteLabel') && '1' === seopress_get_service('ToggleOption')->getToggleWhiteLabel()) {
            return;
        }
    }
	
	if (defined('SEOPRESS_WL_ADMIN_HEADER') && SEOPRESS_WL_ADMIN_HEADER === false) {
		//do nothing
	} else {
			$docs = seopress_get_docs_links();

			$class = '1' !== seopress_get_service('AdvancedOption')->getAppearanceNews() ? 'is-active' : '';
		?>

		<div id="seopress-news-panel" class="seopress-card <?php echo esc_attr($class); ?>" style="display: none">
			<div class="seopress-card-title">
                <div class="seopress-d-flex seopress-space-between">
                    <h2><?php esc_attr_e('Latest News from SEOPress Blog', 'wp-seopress'); ?></h2>
					<div>
						<a href="<?php echo esc_url($docs['blog']); ?>" class="seopress-help" target="_blank" title="<?php esc_attr_e('See all our blog posts - Open in a new tab', 'wp-seopress'); ?>">
							<?php esc_attr_e('See all our blog posts', 'wp-seopress'); ?>
						</a>
						<span class="seopress-help dashicons dashicons-external"></span>
					</div>
				</div>
				<div>
					<p><?php esc_attr_e( 'The latest news about SEOPress, SEO and WordPress.', 'wp-seopress' ); ?></p>
				</div>
			</div>
			<div class="seopress-card-content">
				<?php
				include_once ABSPATH . WPINC . '/feed.php';

				// Get a SimplePie feed object from the specified feed source.

				$wplang = get_locale();

				$rss = fetch_feed('https://www.seopress.org/feed');

				$fr = [
					'fr_FR',
					'fr_BE',
					'fr_CA'
				];

				if (in_array($wplang, $fr)) {
					$rss = fetch_feed('https://www.seopress.org/fr/feed');
				}

				$maxitems = 4;

				if ( ! is_wp_error($rss)) { // Checks that the object is created correctly
					// Build an array of all the items, starting with element 0 (first element).
					$rss_items = $rss->get_items(0, $maxitems);
				}
				?>

				<div class="seopress-articles">
					<?php if (0 == $maxitems) { ?>
						<p>
							<?php esc_attr_e('No items', 'wp-seopress'); ?>
						</p>
					<?php } else {
						foreach ($rss_items as $item) {
							$class = "seopress-article";
							?>
							<article class="<?php echo esc_attr($class); ?>">
								<div>
									<?php if ($enclosure = $item->get_enclosure()) {
										$img = $enclosure->get_link();
										echo '<img src="' . esc_url($img) . '" class="seopress-thumb" alt="'./* translators: %s blog post title */ sprintf(esc_attr__('Post thumbnail of %s', 'wp-seopress'), esc_html($item->get_title())).'" decoding="async" loading="lazy"/>';
									} ?>

									<p class="seopress-item-category">
										<?php foreach ($item->get_categories() as $category) {
											echo esc_attr($category->get_label());
										} ?>
									</p>

									<h3 class="seopress-item-title">
										<a href="<?php echo esc_url($item->get_permalink() . '?utm_source=plugin&utm_medium=dashboard'); ?>" target="_blank" title="<?php /* translators: %s blog post URL */ printf(esc_attr__('Learn more about %s in a new tab', 'wp-seopress'), esc_html($item->get_title())); ?>">
											<?php echo esc_html($item->get_title()); ?>
										</a>
									</h3>

									<p class="seopress-item-content"><?php echo esc_html($item->get_description()); ?></p>
								</div>
								<div class="seopress-item-wrap-content">
									<a class="btn btnSecondary" href="<?php echo esc_url($item->get_permalink() . '?utm_source=plugin&utm_medium=dashboard'); ?>" target="_blank" title="<?php /* translators: %s blog post URL */ printf(esc_attr__('Learn more about %s in a new tab', 'wp-seopress'), esc_html($item->get_title())); ?>">
										<?php esc_attr_e('Learn more', 'wp-seopress'); ?>
									</a>
								</div>
							</article>
						<?php
						}
					} ?>
				</div>
			</div>
		</div>
<?php }
