<?php
	defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

	if (defined('SEOPRESS_WL_ADMIN_HEADER') && SEOPRESS_WL_ADMIN_HEADER === false) {
		//do nothing
	} else {
			$docs = seopress_get_docs_links();

			$class = '1' !== seopress_get_service('NoticeOption')->getNoticeIntegrations() ? 'is-active' : '';
		?>

		<div id="seopress-integration-panel" class="seopress-card <?php echo esc_attr($class); ?>" style="display: none">
			<div class="seopress-card-title">
				<div class="seopress-d-flex seopress-space-between">
					<h2><?php esc_attr_e('Integrations', 'wp-seopress'); ?></h2>
					<div>
						<a href="<?php echo esc_url($docs['integrations']['all']); ?>" class="seopress-help" target="_blank" title="<?php esc_attr_e('See all our integrations - Open in a new tab', 'wp-seopress'); ?>"><?php esc_attr_e('See all our integrations', 'wp-seopress'); ?></a>
						<span class="seopress-help dashicons dashicons-external"></span>
					</div>
				</div>
				<div>
					<p><?php esc_attr_e( 'You’re using these plugins / themes on your site. We provide advanced integrations with them to improve your SEO.', 'wp-seopress' ); ?></p>
				</div>
			</div>
			<div class="seopress-card-content">
				<?php
					$integrations = [
                        'astra' => [
                            'title' => 'Astra'
                        ],
                        'codepress-admin-columns/codepress-admin-columns.php' => [
                            'title' => 'Admin Columns'
                        ],
                        'admin-columns-pro/admin-columns-pro.php' => [
                            'title' => 'Admin Columns PRO'
                        ],
						'advanced-custom-fields/acf.php' => [
							'title' => 'Advanced Custom Fields',
						],
						'advanced-custom-fields-pro/acf.php' => [
							'title' => 'Advanced Custom Fields PRO',
						],
                        'amp/amp.php' => [
                            'title' => 'AMP'
                        ],
                        'bbpress/bbpress.php' => [
							'title' => 'bbPress',
						],
                        'beaver-builder-lite-version/fl-builder.php' => [
							'title' => 'Beaver Builder Lite',
						],
                        'bb-plugin/fl-builder.php' => [
							'title' => 'Beaver Builder Agency',
						],
                        'bricks' => [
                            'title' => 'Bricks'
                        ],
                        'Divi' => [
                            'title' => 'Divi'
                        ],
                        'breakdance/plugin.php' => [
							'title' => 'Breakdance',
						],
                        'buddypress/bp-loader.php' => [
							'title' => 'BuddyPress',
						],
                        'easy-digital-downloads/easy-digital-downloads.php' => [
							'title' => 'Easy Digital Downloads',
						],
                        'easy-digital-downloads-pro/easy-digital-downloads.php' => [
							'title' => 'Easy Digital Downloads PRO',
						],
                        'elementor/elementor.php' => [
							'title' => 'Elementor',
						],
                        'elementor-pro/elementor-pro.php' => [
							'title' => 'Elementor PRO',
						],
                        'enfold' => [
                            'title' => 'Enfold'
                        ],
                        'the-events-calendar/the-events-calendar.php' => [
							'title' => 'The Events Calendar',
						],
                        'events-calendar-pro/events-calendar-pro.php' => [
							'title' => 'The Events Calendar PRO',
						],
                        'jetpack/jetpack.php' => [
							'title' => 'Jetpack',
						],
                        'js_composer/js_composer.php' => [
							'title' => 'WPBakery Page Builder',
						],
                        'multilingual-press/multilingual-press.php' => [
							'title' => 'MultilingualPress',
						],
                        'oxygen/functions.php' => [
							'title' => 'Oxygen Builder',
						],
                        'permalink-manager/permalink-manager.php' => [
							'title' => 'Permalink Manager',
						],
                        'permalink-manager-pro/permalink-manager.php' => [
							'title' => 'Permalink Manager PRO',
						],
                        'polylang/polylang.php' => [
							'title' => 'Polylang',
						],
                        'polylang-pro/polylang.php' => [
							'title' => 'Polylang PRO',
						],
						'sitepress-multilingual-cms/sitepress.php' => [
							'title' => 'WPML',
                        ],
						'weglot/weglot.php' => [
							'title' => 'Weglot',
                        ],
                        'wp-rocket/wp-rocket.php' => [
							'title' => 'WP Rocket',
                        ],
						'woocommerce/woocommerce.php' => [
							'title' => 'WooCommerce',
                        ]
					];
				?>
				<div class="seopress-integrations">
					<?php
                        //Get active theme
                        $theme = wp_get_theme();
                        $i = 0;
						foreach($integrations as $key => $integration) {
                            if (is_plugin_active( $key ) || ($key === $theme->template || $key === $theme->parent_theme)) {
                                $title = $integration['title'];
                                $status = 'status-active';
                                $label = esc_attr__( 'Active', 'wp-seopress' );
							?>
							<div class="seopress-integration">
								<div class="details">
									<h3 class="name"><?php echo esc_html($title); ?></h3>

									<div class="status">
										<span class="badge <?php echo esc_attr( $status ); ?>"></span>
										<span class="label"><?php echo esc_attr( $label ); ?></span>
									</div>
								</div>
							</div>
							<?php
                            $i++;
                            }
						}
                        if ($i === 0) {
                            ?>
                            <div class="seopress-notice">
                                <p>
                                    <?php esc_html_e('Currently, no specific integration found for your site. Contact us if you have any doubts about the compatibility between your plugins/themes and our products.', 'wp-seopress'); ?>
                                </p>
                                <p>
                                    <a href="<?php echo esc_url($docs['contact']); ?>" class="seopress-help btn btnSecondary" target="_blank" title="<?php esc_attr_e('Request an integration - Open in a new tab', 'wp-seopress'); ?>">
                                        <?php esc_attr_e('Request an integration', 'wp-seopress'); ?>
                                    </a>
                                </p>
                            </div>
                            <?php
                        }
					?>
				</div>
			</div>
		</div>
<?php }
