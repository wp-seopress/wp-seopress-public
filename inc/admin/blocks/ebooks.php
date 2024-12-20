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

			$class = '1' !== seopress_get_service('NoticeOption')->getNoticeEbooks() ? 'is-active' : '';
		?>

		<div id="seopress-ebook-panel" class="seopress-card <?php echo esc_attr($class); ?>" style="display: none">
			<div class="seopress-card-title">
				<div class="seopress-d-flex seopress-space-between">
					<h2><?php esc_attr_e('Free SEO ebooks', 'wp-seopress'); ?></h2>
					<div>
						<a href="<?php echo esc_url($docs['get_started']['ebooks']['link']); ?>" class="seopress-help" target="_blank" title="<?php esc_attr_e('See all our ebooks - Open in a new tab', 'wp-seopress'); ?>">
							<?php esc_attr_e('See all our ebooks', 'wp-seopress'); ?>
						</a>
						<span class="seopress-help dashicons dashicons-external"></span>
					</div>
				</div>
				<div>
					<p><?php esc_attr_e( 'Learn how to improve your rankings, traffic, conversions and sales.', 'wp-seopress' ); ?></p>
				</div>
			</div>
			<div class="seopress-card-content">
				<?php
					$wplang = get_locale();

					$fr = [
						'fr_FR',
						'fr_BE',
						'fr_CA'
					];

					$lang = 'en';
					if (in_array($wplang, $fr)) {
						$lang = 'fr';
					}

					$ebooks = [
						'seo-success' => [
							'en' => [
								'title' => 'SEO Success for WordPress with a Two-Hours-a-Week Routine',
								'url' => 'https://www.seopress.org/support/ebooks/seo-success-for-wordpress-with-a-two-hours-a-week-routine/',
								'cover' => 'https://www.seopress.org/wp-content/uploads/2023/07/seopress-2hours-seo-en-cover-scaled.jpg'
							],
							'fr' => [
								'title' => 'Réussir son SEO sur WordPress avec deux heures de travail par semaine',
								'url' => 'https://www.seopress.org/fr/support/ebooks/reussir-son-seo-sur-wordpress-avec-deux-heures-de-travail-semaine/',
								'cover' => 'https://www.seopress.org/fr/wp-content/uploads/sites/2/2023/07/seopress-2hours-seo-fr-cover-scaled.jpg'
							]
						],
						'search-console' => [
							'en' => [
								'title' => 'Maximizing Your WordPress SEO with Google Search Console',
								'url' => 'https://www.seopress.org/support/ebooks/maximizing-your-wordpress-seo-with-google-search-console/',
								'cover' => 'https://www.seopress.org/wp-content/uploads/2023/05/seopress-google-search-console-en.png'
							],
							'fr' => [
								'title' => 'Maximiser votre référencement WordPress avec Google Search Console',
								'url' => 'https://www.seopress.org/fr/support/ebooks/maximiser-votre-referencement-wordpress-avec-google-search-console/',
								'cover' => 'https://www.seopress.org/fr/wp-content/uploads/sites/2/2023/05/seopress-google-search-console-fr.png'
							]
						],
						'image-seo' => [
							'en' => [
								'title' => 'More Visibility with Image SEO',
								'url' => 'https://www.seopress.org/support/ebooks/more-visibility-with-image-seo/',
								'cover' => 'https://www.seopress.org/wp-content/uploads/2022/07/seopress-ebook-more-visibility-image-seo-en@2x.png'
							],
							'fr' => [
								'title' => 'Devenez plus visible grace au référencement de vos images',
								'url' => 'https://www.seopress.org/fr/support/ebooks/devenez-plus-visible-grace-au-referencement-de-vos-images/',
								'cover' => 'https://www.seopress.org/fr/wp-content/uploads/sites/2/2022/07/seopress-ebook-more-visibility-image-seo-fr@2x.png'
							]
						]
					];
				?>
				<div class="seo-ebooks">
					<?php
						foreach($ebooks as $ebook) {
							$title = $ebook[$lang]['title'];
							$url = $ebook[$lang]['url'];
							$cover = $ebook[$lang]['cover'];
							?>
							<div class="seo-ebook">
								<div class="details">
									<h3 class="name"><?php echo esc_html($title); ?></h3>
									<a href="<?php echo esc_url($url); ?>" target="_blank" class="btn btnSecondary">
										<?php esc_html_e('Download', 'wp-seopress'); ?>
									</a>
								</div>
								<div class="cover">
									<img src="<?php echo esc_url($cover); ?>" alt="<?php echo esc_html($title); ?>" width="100%" height="auto"/>
								</div>
							</div>
							<?php
						}
					?>
				</div>
			</div>
		</div>
<?php }
