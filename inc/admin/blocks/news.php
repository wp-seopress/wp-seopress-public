<?php
/**
 * News block.
 *
 * @package SEOPress
 * @subpackage Blocks
 */

defined( 'ABSPATH' ) || exit( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

if ( is_plugin_active( 'wp-seopress-pro/seopress-pro.php' ) ) {
	if ( method_exists( seopress_get_service( 'ToggleOption' ), 'getToggleWhiteLabel' ) && '1' === seopress_get_service( 'ToggleOption' )->getToggleWhiteLabel() ) {
		return;
	}
}

if ( defined( 'SEOPRESS_WL_ADMIN_HEADER' ) && false === SEOPRESS_WL_ADMIN_HEADER ) {
	return;
}

$docs  = seopress_get_docs_links();
$class = '1' !== seopress_get_service( 'AdvancedOption' )->getAppearanceNews() ? 'is-active' : '';
?>

	<div id="seopress-news-panel" class="seopress-card <?php echo esc_attr( $class ); ?>" style="display: none">
		<div class="seopress-card-title">
			<div class="seopress-d-flex seopress-space-between">
				<h2><?php esc_attr_e( 'Latest News from SEOPress Blog', 'wp-seopress' ); ?></h2>
				<div>
					<a href="<?php echo esc_url( $docs['blog'] ); ?>" class="seopress-help" target="_blank" title="<?php esc_attr_e( 'See all our blog posts - Open in a new tab', 'wp-seopress' ); ?>">
						<?php esc_attr_e( 'See all our blog posts', 'wp-seopress' ); ?>
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
			require_once ABSPATH . WPINC . '/feed.php';

			// Get a SimplePie feed object from the specified feed source.
			$wplang = get_locale();

			$fr = array(
				'fr_FR',
				'fr_BE',
				'fr_CA',
			);

			$feed_url = in_array( $wplang, $fr, true ) ? 'https://www.seopress.org/fr/feed' : 'https://www.seopress.org/feed';

			$maxitems   = 4;
			$feed_items = array();
			$rss        = null;

			// Check if we have a cached version first.
			$cache_key   = 'seopress_news_feed_array_' . md5( $feed_url );
			$cached_data = get_transient( $cache_key );

			if ( false !== $cached_data && is_array( $cached_data ) ) {
				// Use cached version.
				$feed_items = $cached_data;
			} else {
				// Increase SimplePie timeout to 5 seconds.
				add_filter(
					'http_request_args',
					function ( $args, $url ) {
						if ( strpos( $url, 'seopress.org' ) !== false ) {
							$args['timeout'] = 5;
						}
						return $args;
					},
					10,
					2
				);

				$rss = fetch_feed( $feed_url );

				if ( ! is_wp_error( $rss ) ) {
					// Get SimplePie items.
					$rss_items = $rss->get_items( 0, $maxitems );

					// Convert SimplePie objects to plain arrays for caching.
					foreach ( $rss_items as $item ) {
						$feed_items[] = array(
							'title'       => $item->get_title(),
							'permalink'   => $item->get_permalink(),
							'description' => $item->get_description(),
							'image'       => $item->get_enclosure() ? $item->get_enclosure()->get_link() : '',
							'categories'  => array_map(
								function ( $cat ) {
									return $cat->get_label();
								},
								$item->get_categories()
							),
						);
					}

					// Cache the plain arrays for 24 hours.
					set_transient( $cache_key, $feed_items, 24 * HOUR_IN_SECONDS );
				}
			}
			?>

			<div class="seopress-articles">
				<?php if ( is_wp_error( $rss ) ) { ?>
					<p>
						<?php
						/* translators: %s error message */
						printf( esc_html__( 'Unable to load news feed: %s', 'wp-seopress' ), esc_html( $rss->get_error_message() ) );
						?>
					</p>
				<?php } elseif ( empty( $feed_items ) ) { ?>
					<p>
						<?php esc_html_e( 'No items', 'wp-seopress' ); ?>
					</p>
					<?php
				} else {
					foreach ( $feed_items as $item ) {
						$class = 'seopress-article';
						?>
						<article class="<?php echo esc_attr( $class ); ?>">
							<div>
								<?php if ( ! empty( $item['image'] ) ) { ?>
									<img src="<?php echo esc_url( $item['image'] ); ?>" class="seopress-thumb" alt="<?php /* translators: %s blog post title */ printf( esc_attr__( 'Post thumbnail of %s', 'wp-seopress' ), esc_html( $item['title'] ) ); ?>" decoding="async" loading="lazy"/>
								<?php } ?>

								<?php if ( ! empty( $item['categories'] ) ) { ?>
									<p class="seopress-item-category">
										<?php echo esc_html( implode( ', ', $item['categories'] ) ); ?>
									</p>
								<?php } ?>

								<h3 class="seopress-item-title">
									<a href="<?php echo esc_url( $item['permalink'] . '?utm_source=plugin&utm_medium=dashboard' ); ?>" target="_blank" title="<?php /* translators: %s blog post URL */ printf( esc_attr__( 'Learn more about %s in a new tab', 'wp-seopress' ), esc_html( $item['title'] ) ); ?>">
										<?php echo esc_html( $item['title'] ); ?>
									</a>
								</h3>

								<p class="seopress-item-content"><?php echo esc_html( $item['description'] ); ?></p>
							</div>
							<div class="seopress-item-wrap-content">
								<a class="btn btnSecondary" href="<?php echo esc_url( $item['permalink'] . '?utm_source=plugin&utm_medium=dashboard' ); ?>" target="_blank" title="<?php /* translators: %s blog post URL */ printf( esc_attr__( 'Learn more about %s in a new tab', 'wp-seopress' ), esc_html( $item['title'] ) ); ?>">
									<?php esc_html_e( 'Learn more', 'wp-seopress' ); ?>
								</a>
							</div>
						</article>
						<?php
					}
				}
				?>
			</div>
		</div>
	</div>
