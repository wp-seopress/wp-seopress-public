<?php
/**
 * Setup Wizard Class.
 *
 * Takes new users through some basic steps to setup SEOPress.
 *
 * @version     3.5.8
 */
defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

/**
 * SEOPRESS_Admin_Setup_Wizard class.
 */
class SEOPRESS_Admin_Setup_Wizard {
	/**
	 * Current step.
	 *
	 * @var string
	 */
	private $step = '';

	/**
	 * Parent step.
	 *
	 * @var string
	 */
	private $parent = '';

	/**
	 * Steps for the setup wizard.
	 *
	 * @var array
	 */
	private $steps = [];

	/**
	 * SEO title.
	 *
	 * @var string
	 */
	private $seo_title = '';

	/**
	 * Unique plugin slug identifier.
	 *
	 * @var string
	 */
	public $plugin_slug = 'seopress-setup';

	/**
	 * Hook in tabs.
	 */
	public function __construct() {
		if (apply_filters('seopress_enable_setup_wizard', true) && current_user_can(seopress_capability('manage_options', 'Admin_Setup_Wizard'))) {
			add_action('admin_menu', [$this, 'load_wizard'], 20);

			add_action('admin_head', [ $this, 'hide_from_menus' ], 20);

			//Remove notices
			remove_all_actions( 'admin_notices' );
			remove_all_actions( 'all_admin_notices' );

            $this->seo_title = 'SEOPress';
            if (is_plugin_active('wp-seopress-pro/seopress-pro.php')) {
                if (method_exists(seopress_get_service('ToggleOption'), 'getToggleWhiteLabel') && '1' === seopress_get_service('ToggleOption')->getToggleWhiteLabel()) {
                    $this->seo_title = function_exists('seopress_pro_get_service') && method_exists(seopress_pro_get_service('OptionPro'), 'getWhiteLabelListTitle') && seopress_pro_get_service('OptionPro')->getWhiteLabelListTitle() ? seopress_pro_get_service('OptionPro')->getWhiteLabelListTitle() : 'SEOPress';
                }
            }
		}
	}

	/**
	 * Add dashboard page.
	 */
	public function load_wizard() {
		add_submenu_page('seopress-option', __('Wizard', 'wp-seopress'), __('Wizard', 'wp-seopress'), seopress_capability('manage_options', 'menu'), $this->plugin_slug, [$this, 'setup_wizard'], 10);
	}

	/**
	 * Hide Wizard item from SEO menu.
	 */
	public function hide_from_menus()
	{
		global $submenu;

		if (!empty($submenu)) {
			foreach ($submenu as $key => $value) {
				if ($key === 'seopress-option') {
					foreach ($value as $_key => $_value) {
						if ($this->plugin_slug === $_value[2]) {
							unset($submenu[ $key ][$_key]);
						}
					}
				}
			}
		}
	}

	/**
	 * Show the setup wizard.
	 */
	public function setup_wizard() {
		if (empty($_GET['page']) || 'seopress-setup' !== $_GET['page']) {
			return;
		}

		$default_steps = [
			'welcome' => [
				'breadcrumbs' => true,
				'name'    => __('Welcome', 'wp-seopress'),
				'view'    => [$this, 'seopress_setup_welcome'],
				'handler' => [$this, 'seopress_setup_import_settings_save'],
				'sub_steps' => [
					'welcome' => __('Welcome','wp-seopress'),
					'import_settings' => __('Import metadata','wp-seopress')
				],
				'parent' => 'welcome'
			],
			'import_settings' => [
				'name'    => __('Import SEO metadata', 'wp-seopress'),
				'view'    => [$this, 'seopress_setup_import_settings'],
				'handler' => [$this, 'seopress_setup_import_settings_save'],
				'sub_steps' => [
					'welcome' => __('Welcome','wp-seopress'),
					'import_settings' => __('Import metadata','wp-seopress')
				],
				'parent' => 'welcome'
			],
			'site'     => [
				'breadcrumbs' => true,
				'name'    => __('Your site', 'wp-seopress'),
				'view'    => [$this, 'seopress_setup_site'],
				'handler' => [$this, 'seopress_setup_site_save'],
				'sub_steps' => [
					'site' => __('General','wp-seopress'),
					'social_accounts' => __('Your social accounts','wp-seopress')
				],
				'parent' => 'site'
			],
			'social_accounts'     => [
				'name'    => __('Your site', 'wp-seopress'),
				'view'    => [$this, 'seopress_setup_social_accounts'],
				'handler' => [$this, 'seopress_setup_social_accounts_save'],
				'sub_steps' => [
					'site' => __('General','wp-seopress'),
					'social_accounts' => __('Your social accounts','wp-seopress')
				],
				'parent' => 'site'
			],
			'indexing_post_types'    => [
				'breadcrumbs' => true,
				'name'    => __('Indexing', 'wp-seopress'),
				'view'    => [$this, 'seopress_setup_indexing_post_types'],
				'handler' => [$this, 'seopress_setup_indexing_post_types_save'],
				'sub_steps' => [
					'indexing_post_types' => __('Post Types','wp-seopress'),
					'indexing_archives' => __('Archives','wp-seopress'),
					'indexing_taxonomies' => __('Taxonomies','wp-seopress')
				],
				'parent' => 'indexing_post_types'
			],
			'indexing_archives'    => [
				'name'    => __('Indexing', 'wp-seopress'),
				'view'    => [$this, 'seopress_setup_indexing_archives'],
				'handler' => [$this, 'seopress_setup_indexing_archives_save'],
				'sub_steps' => [
					'indexing_post_types' => __('Post Types','wp-seopress'),
					'indexing_archives' => __('Archives','wp-seopress'),
					'indexing_taxonomies' => __('Taxonomies','wp-seopress')
				],
				'parent' => 'indexing_post_types'
			],
			'indexing_taxonomies'    => [
				'name'    => __('Indexing', 'wp-seopress'),
				'view'    => [$this, 'seopress_setup_indexing_taxonomies'],
				'handler' => [$this, 'seopress_setup_indexing_taxonomies_save'],
				'sub_steps' => [
					'indexing_post_types' => __('Post Types','wp-seopress'),
					'indexing_archives' => __('Archives','wp-seopress'),
					'indexing_taxonomies' => __('Taxonomies','wp-seopress')
				],
				'parent' => 'indexing_post_types'
			],
			'advanced'    => [
				'breadcrumbs' => true,
				'name'    => __('Advanced options', 'wp-seopress'),
				'view'    => [$this, 'seopress_setup_advanced'],
				'handler' => [$this, 'seopress_setup_advanced_save'],
				'sub_steps' => [
					'advanced' => __('Advanced','wp-seopress'),
					'universal' => __('Universal SEO metabox','wp-seopress'),
				],
				'parent' => 'advanced'
			],
			'universal'    => [
				'name'    => __('Advanced options', 'wp-seopress'),
				'view'    => [$this, 'seopress_setup_universal'],
				'handler' => [$this, 'seopress_setup_universal_save'],
				'sub_steps' => [
					'advanced' => __('Advanced','wp-seopress'),
					'universal' => __('Universal SEO metabox','wp-seopress'),
				],
				'parent' => 'advanced'
			],
		];
		if (method_exists(seopress_get_service('ToggleOption'), 'getToggleWhiteLabel') && '1' === seopress_get_service('ToggleOption')->getToggleWhiteLabel()) {
			//do nothing
		} elseif (
				(
					! is_plugin_active('wp-seopress-insights/seopress-insights.php') && ! is_multisite()
				)
				||
				! is_plugin_active('wp-seopress-pro/seopress-pro.php')
			)
		{
			$default_steps['pro'] = [
				'breadcrumbs' => true,
				'name'    => /* translators: %s default: SEOPress */ sprintf(__('Extend %s', 'wp-seopress'), $this->seo_title),
				'view'    => [$this, 'seopress_setup_pro'],
				'handler' => '',
				'sub_steps' => [
					'pro' => __('Go further!','wp-seopress'),
				],
				'parent' => 'pro'
			];
		}

		$default_steps['ready']  = [
			'breadcrumbs' => true,
			'name'    => __('Ready!', 'wp-seopress'),
			'view'    => [$this, 'seopress_setup_ready'],
			'handler' => [$this, 'seopress_final_subscribe'],
			'sub_steps' => [
				'ready' => __('Ready!', 'wp-seopress')
			]
		];

		$this->steps = apply_filters('seopress_setup_wizard_steps', $default_steps);
		$this->step  = isset($_GET['step']) ? sanitize_key($_GET['step']) : current(array_keys($this->steps));
		$this->parent  = isset($_GET['parent']) ? sanitize_key($_GET['parent']) : current(array_keys($this->steps));

		if ( ! empty($_POST['save_step']) && isset($this->steps[$this->step]['handler'])) {
			call_user_func($this->steps[$this->step]['handler'], $this);
		}

		$this->setup_wizard_steps();
		$this->setup_wizard_content();
		$this->setup_wizard_footer();
	}

	/**
	 * Get the URL for the next step's screen.
	 *
	 * @param string $step slug (default: current step)
	 *
	 * @return string URL for next step if a next step exists.
	 *                Admin URL if it's the last step.
	 *                Empty string on failure.
	 *
	 * @since 3.5.8
	 */
	public function get_next_step_link($step = '') {
		if ( ! $step) {
			$step = $this->step;
		}

		$keys = array_keys($this->steps);
		if (end($keys) === $step) {
			return admin_url();
		}

		$step_index = array_search($step, $keys, true);
		if (false === $step_index) {
			return '';
		}

		$parent = '';
		$all = $this->steps;
		if (isset($all[$step]['parent'])) {
			$key = $keys[$step_index + 1];
			if (isset($all[$key]['parent'])) {
				$parent = $all[$key]['parent'];
			}
		}

		return add_query_arg(
			[
				'step' => $keys[$step_index + 1],
				'parent' => $parent,
			],
			remove_query_arg( 'parent' )
		);
	}

	/**
	 * Setup Wizard Footer.
	 */
	public function setup_wizard_footer() {
		?>
		<div class="seopress-setup-footer">
			<?php if ('welcome' === $this->step) { ?>
			<a class="seopress-setup-footer-links"
				href="<?php echo esc_url(admin_url('admin.php?page=seopress-option')); ?>"><?php esc_html_e('Not right now', 'wp-seopress'); ?></a>
			<?php } elseif (
				'import_settings' === $this->step ||
				'social_accounts' === $this->step ||
				'indexing_post_types' === $this->step ||
				'indexing_archives' === $this->step ||
				'indexing_taxonomies' === $this->step ||
				'universal' === $this->step ||
				'site' === $this->step ||
				'indexing' === $this->step ||
				'advanced' === $this->step ||
				'pro' === $this->step
				) {
				$skip_link = esc_url($this->get_next_step_link());
				if ('advanced' === $this->step && defined('SEOPRESS_WL_ADMIN_HEADER') && SEOPRESS_WL_ADMIN_HEADER === false) {
					$skip_link = esc_url_raw($this->get_next_step_link('pro'));
				}
				?>
			<a class="seopress-setup-footer-links" href="<?php echo esc_url($skip_link); ?>"><?php esc_html_e('Skip this step', 'wp-seopress'); ?></a>
			<?php } ?>
		</div>
<?php
	}

	/**
	 * Output the steps.
	 */
	public function setup_wizard_steps() {
		$output_steps      = $this->steps;
		$parent            = $this->parent; ?>
	<ol class="seopress-setup-steps">
		<?php
				$i = 1;
		foreach ($output_steps as $step_key => $step) {
			if (!isset($step['breadcrumbs'])) {
				continue;
			}

			$is_completed = array_search($this->step, array_keys($this->steps), true) > array_search($step_key, array_keys($this->steps), true);

			if ($step_key === $this->step || $step_key === $this->parent) {
				?>
		<li class="active">
			<div class="icon" data-step="<?php echo absint($i); ?>"></div>
			<span><?php echo esc_html($step['name']); ?></span>
			<div class="divider"></div>
		</li>
		<?php
			} elseif ($is_completed) {
				?>
		<li class="done">
			<div class="icon" data-step="<?php echo absint($i); ?>"></div>
			<a
				href="<?php echo esc_url(add_query_arg(
					['step' => $step_key, 'parent' => $parent]
				)); ?>">
				<?php echo esc_html($step['name']); ?>
			</a>
			<div class="divider"></div>
		</li>
		<?php
			} else {
				?>
		<li>
			<div class="icon" data-step="<?php echo absint($i); ?>"></div>
			<span><?php echo esc_html($step['name']); ?></span>
			<div class="divider"></div>
		</li>
		<?php
			}
			++$i;
		} ?>
	</ol>
	<?php
	}

	/**
	 * Output the sub steps.
	 */
	public function setup_wizard_sub_steps() {
		$output_steps      = $this->steps;
		$current_step      = $this->step;
		$parent            = $this->parent;
		?>
		<div id="seopress-tabs" class="wrap">
			<div class="nav-tab-wrapper">
				<?php
					if (!empty($output_steps[$current_step]['sub_steps'])) {
						foreach($output_steps[$current_step]['sub_steps'] as $key => $value) {
							$class = $key === $current_step ? 'nav-tab-active' : '';
							?>
							<a <?php echo 'class="nav-tab '.esc_attr($class).'"'; ?> href="<?php echo esc_url(admin_url('admin.php?page=seopress-setup&step='.$key.'&parent='.$parent)); ?>">
								<?php echo esc_html($value); ?>
							</a>
						<?php }
					}
				?>
			</div>
	<?php
	}

	/**
	 * Output the content for the current step.
	 */
	public function setup_wizard_content() {
		if ( ! empty($this->steps[$this->step]['view'])) {
			call_user_func($this->steps[$this->step]['view'], $this);
		}
	}

	/**
	 * Init "Step 1.1: Welcome".
	 */
	public function seopress_setup_welcome() {
		?>
	<div class="seopress-setup-content seopress-option">
		<h1>
			<?php
				/* translators: %s plugin name, default: SEOPress */
				printf(esc_html__('Welcome to %s!', 'wp-seopress'), esc_html($this->seo_title));
			?>
		</h1>

		<?php $this->setup_wizard_sub_steps(); ?>

		<div class="seopress-tab active">
			<h2><?php /* translators: %s default: SEOPress */ printf(esc_html__('Configure %s with the best settings for your site','wp-seopress'), esc_html($this->seo_title)); ?></h2>
			<p class="store-setup intro"><?php /* translators: %s default: SEOPress */ printf(esc_html__('The following wizard will help you configure %s and get you started quickly.', 'wp-seopress'), esc_html($this->seo_title)); ?>
			</p>

			<?php
				$requirements = [];

				if (function_exists('extension_loaded') && ! extension_loaded('dom')) {
					$requirements[] = [
							'title'  => __('PHP module "DOM" is missing on your server.', 'wp-seopress'),
							'desc'   => __('This PHP module, installed by default with PHP, is required by many plugins including SEOPress. Please contact your host as soon as possible to solve this.', 'wp-seopress'),
					];
				}

				if (function_exists('extension_loaded') && ! extension_loaded('mbstring')) {
					$requirements[] = [
						'title'  => __('PHP module "mbstring" is missing on your server.', 'wp-seopress'),
						'desc'   => __('This PHP module, installed by default with PHP, is required by many plugins including SEOPress. Please contact your host as soon as possible to solve this.', 'wp-seopress')
					];
				};

				if (function_exists('extension_loaded') && ! extension_loaded('intl')) {
					$requirements[] = [
						'title'  => __('PHP module "intl" is missing on your server.', 'wp-seopress'),
						'desc'   => __('This PHP module, installed by default with PHP, is required by many plugins including SEOPress. Please contact your host as soon as possible to solve this.', 'wp-seopress')
					];
				};

				if (function_exists( 'ini_get' )) {
					$upload_max_filesize = ini_get( 'upload_max_filesize' ) ?? 1;
					$post_max_size = ini_get( 'post_max_size' ) ?? 1;
					$memory_limit = ini_get( 'memory_limit' ) ?? 1;

					if ( wp_convert_hr_to_bytes($upload_max_filesize) / 1024 / 1024 < 24 ) {
						$requirements[] = [
							'title'  => __('PHP upload max filesize is too low.', 'wp-seopress'),
							'desc'   => /* translators: %s: "upload max filesize" */ sprintf(__('Please contact your host to increase this value to at least <code>24M</code> (current value: <code>%dM</code>).', 'wp-seopress'), absint(wp_convert_hr_to_bytes($upload_max_filesize) / 1024 / 1024))
						];
					}

					if ( wp_convert_hr_to_bytes($post_max_size) / 1024 / 1024 < 23 ) {
						$requirements[] = [
							'title'  => __('PHP post max size is too low.', 'wp-seopress'),
							'desc'   => /* translators: %s: "post max size" */ sprintf(__('Please contact your host to increase this value to at least <code>24M</code> (current value: <code>%dM</code>).', 'wp-seopress'), absint(wp_convert_hr_to_bytes($post_max_size) / 1024 / 1024))
						];
					}

					if ( wp_convert_hr_to_bytes($memory_limit) / 1024 / 1024 < 256 ) {
						$requirements[] = [
							'title'  => __('PHP memory limit is too low.', 'wp-seopress'),
							'desc'   => /* translators: %s: "memory limit" */ sprintf(__('Please contact your host to increase this value to at least <code>256M</code> (current value: <code>%dM</code>).', 'wp-seopress'), absint(wp_convert_hr_to_bytes($memory_limit) / 1024 / 1024))
						];
					}
				}

				if (!empty($requirements)) { ?>
					<h3><?php esc_attr_e('Requirements', 'wp-seopress'); ?></h3>
					<p><?php esc_attr_e('We have detected that your server configuration may pose problems for running WordPress and our plugins.', 'wp-seopress'); ?></p>

					<?php foreach($requirements as $key => $value) { ?>
						<div class="seopress-notice is-warning">
							<h4><?php echo esc_html($value['title']); ?></h4>
							<p><?php echo wp_kses_post($value['desc']); ?></p>
						</div>
						<?php
					}
				}
			?>

			<a href="<?php echo esc_url($this->get_next_step_link()); ?>" class="btnPrimary btn btnNext"><?php esc_html_e('Here we go!', 'wp-seopress'); ?></a>
		</div>
	</div>
<?php
	}

	/**
	 * Init "Step 1.2: Import SEO settings".
	 */
	public function seopress_setup_import_settings() {
		?>
		<div class="seopress-setup-content seopress-option">
			<h1><?php esc_attr_e('Migration','wp-seopress'); ?></h1>

			<?php $this->setup_wizard_sub_steps(); ?>

			<div class="seopress-tab active">
				<form method="post">
					<h2><?php /* translators: %s default: SEOPress */ printf(esc_html__('Migrate your SEO metadata to %s', 'wp-seopress'), esc_html($this->seo_title)); ?></h2>

					<p class="store-setup intro"><?php esc_html_e('The first step is to import your previous post and term metadata from other plugins to keep your SEO.', 'wp-seopress'); ?></p>

					<?php
					$plugins = [
						'yoast'            => [
							'slug' => [
								'wordpress-seo/wp-seo.php',
								'wordpress-seo-premium/wp-seo-premium.php',
							],
							'name' => 'Yoast SEO',
							'img' => SEOPRESS_URL_ASSETS . '/img/import/yoast.png',
						],
						'aio'              => [
							'slug' => [
								'all-in-one-seo-pack/all_in_one_seo_pack.php',
							],
							'name' => 'All In One SEO',
							'img' => SEOPRESS_URL_ASSETS . '/img/import/aio.svg',
						],
						'seo-framework'    => [
							'slug' => [
								'autodescription/autodescription.php',
							],
							'name' => 'The SEO Framework',
							'img' => SEOPRESS_URL_ASSETS . '/img/import/seo-framework.svg',
						],
						'rk'               => [
							'slug' => [
								'seo-by-rank-math/rank-math.php',
							],
							'name' => 'Rank Math',
							'img' => SEOPRESS_URL_ASSETS . '/img/import/rk.svg',
						],
						'squirrly'         => [
							'slug' => [
								'squirrly-seo/squirrly.php',
							],
							'name' => 'Squirrly SEO',
							'img' => SEOPRESS_URL_ASSETS . '/img/import/squirrly.png',
						],
						'seo-ultimate'     => [
							'slug' => [
								'seo-ultimate/seo-ultimate.php',
							],
							'name' => 'SEO Ultimate',
							'img' => SEOPRESS_URL_ASSETS . '/img/import/seo-ultimate.svg',
						],
						'wp-meta-seo'      => [
							'slug' => [
								'wp-meta-seo/wp-meta-seo.php',
							],
							'name' => 'WP Meta SEO',
							'img' => SEOPRESS_URL_ASSETS . '/img/import/wp-meta-seo.png',
						],
						'premium-seo-pack' => [
							'slug' => [
								'premium-seo-pack/plugin.php',
							],
							'name' => 'Premium SEO Pack',
							'img' => SEOPRESS_URL_ASSETS . '/img/import/premium-seo-pack.png',
						],
						'smart-crawl'      => [
							'slug' => [
								'smartcrawl-seo/wpmu-dev-seo.php',
							],
							'name' => 'SmartCrawl',
							'img' => SEOPRESS_URL_ASSETS . '/img/import/smart-crawl.png',
						],
						'slim-seo'         => [
							'slug' => [
								'slim-seo/slim-seo.php',
							],
							'name' => 'Slim SEO',
							'img' => SEOPRESS_URL_ASSETS . '/img/import/slim-seo.svg',
						],
					];

					$active_seo_plugins = [];

					foreach ($plugins as $plugin => $detail) {
						foreach($detail['slug'] as $key => $slug) {
							if (is_plugin_active($slug)) {
								$active_seo_plugins['name'][] = $detail['name'];
								$active_seo_plugins['slug'][] = $detail['slug'];
							}
						}
					}

					if (!empty($active_seo_plugins)) { ?>
						<div class="seopress-notice is-error">
							<p><?php echo wp_kses_post(__('One ore more <strong>SEO plugins</strong> are enabled on your site, please deactivate them to avoid any conflicts:', 'wp-seopress')); ?></p>
							<ul>
								<?php
								foreach($active_seo_plugins['name'] as $key => $value) {
									?>
										<li><span class="dashicons dashicons-minus"></span><?php echo esc_html($value); ?></li>
									<?php
								} ?>
							</ul>
						</div>
						<?php
					} ?>

					<fieldset class="seopress-import-tools-wrapper" role="group" aria-labelledby="import-tools-legend">
						<div class="seopress-notice">
							<legend id="import-tools-legend"><?php esc_attr_e('Select an SEO plugin to migrate from (you don\'t have to enable the selected one to run the import):', 'wp-seopress'); ?></legend>
						</div>
						<div class="seopress-import-tools" role="radiogroup" aria-labelledby="import-tools-legend">
							<?php
								foreach ($plugins as $plugin => $detail) {
									?>
									<div class="seopress-import-tool <?php if (!empty($active_seo_plugins) && in_array($detail['slug'], $active_seo_plugins['slug'])) { echo 'active'; } ?>">
										<label for="<?php echo esc_attr($plugin); ?>-migration-tool" tabindex="0">
											<input type="radio" id="<?php echo esc_attr($plugin); ?>-migration-tool" name="select-wizard-import" value="<?php echo esc_attr($plugin); ?>-migration-tool"
											aria-describedby="<?php echo esc_attr($plugin); ?>-description"
											aria-label="<?php echo /* translators: %s: "SEO plugin name" */ esc_attr(sprintf(__('Select %s for migration', 'wp-seopress'), $detail['name'])); ?>"
											<?php
												if (!empty($active_seo_plugins) && in_array($detail['slug'], $active_seo_plugins['slug'])) {
													echo 'checked';
												}
											?>
											/>
											<?php if (!empty($detail['img'])): ?>
												<img src="<?php echo esc_url($detail['img']); ?>" alt="<?php echo esc_attr($detail['name']); ?> logo">
											<?php endif; ?>
											<span><?php echo esc_html($detail['name']); ?></span>
										</label>
										<p id="<?php echo esc_attr($plugin); ?>-description" class="screen-reader-text"><?php echo /* translators: %s: "SEO plugin name" */ wp_kses_post(sprintf(__('Import metadata from %s, including titles and meta descriptions.', 'wp-seopress'), esc_html($detail['name']))); ?></p>
									</div>
								<?php } 
							?>
						</div>

						<div class="seopress-import-tools-details" aria-live="polite">
							<?php
								foreach ($plugins as $plugin => $detail) {
									$checked = false;
									if (!empty($active_seo_plugins) && in_array($detail['slug'], $active_seo_plugins['slug'])) {
										$checked = true;
									} 
									echo wp_kses_post(seopress_migration_tool($plugin, $detail, $checked));
								}
							?>
						</div>
					</fieldset>

					<hr>

					<p class="store-setup"><?php esc_html_e('No data to migrate? Click "Next step" button!', 'wp-seopress'); ?></p>

					<p class="seopress-setup-actions step">
						<button type="submit" class="btnPrimary btn btnNext"
							value="<?php esc_attr_e('Next step', 'wp-seopress'); ?>"
							name="save_step">
							<?php esc_html_e('Next step', 'wp-seopress'); ?>
						</button>

						<?php wp_nonce_field('seopress-setup'); ?>
					</p>
				</form>
			</div>
		</div>
	<?php
	}

	/**
	 * Save step 1.2 settings.
	 */
	public function seopress_setup_import_settings_save() {
		check_admin_referer('seopress-setup');
		wp_safe_redirect(esc_url_raw($this->get_next_step_link()));
		exit;
	}
	/**
	 * Init "Step 2.0: Your site - General".
	 */
	public function seopress_setup_site() {
		$docs = seopress_get_docs_links();
		$seopress_titles_option = get_option('seopress_titles_option_name');
		$seopress_social_option = get_option('seopress_social_option_name');

		$current_user = wp_get_current_user();
		$current_user_email = isset($current_user->user_email) ? $current_user->user_email : null;

		$site_title      = isset($seopress_titles_option['seopress_titles_home_site_title']) ? $seopress_titles_option['seopress_titles_home_site_title'] : null;
		$alt_site_title  = isset($seopress_titles_option['seopress_titles_home_site_title_alt']) ? $seopress_titles_option['seopress_titles_home_site_title_alt'] : null;
		$knowledge_type  = isset($seopress_social_option['seopress_social_knowledge_type']) ? $seopress_social_option['seopress_social_knowledge_type'] : null;
		$knowledge_name  = isset($seopress_social_option['seopress_social_knowledge_name']) ? $seopress_social_option['seopress_social_knowledge_name'] : null;
		$knowledge_img   = isset($seopress_social_option['seopress_social_knowledge_img']) ? $seopress_social_option['seopress_social_knowledge_img'] : '';
		$knowledge_email = isset($seopress_social_option['seopress_social_knowledge_email']) ? $seopress_social_option['seopress_social_knowledge_email'] : $current_user_email;
		$knowledge_phone = isset($seopress_social_option['seopress_social_knowledge_phone']) ? $seopress_social_option['seopress_social_knowledge_phone'] : null;
		$knowledge_tax_id = isset($seopress_social_option['seopress_social_knowledge_tax_id']) ? $seopress_social_option['seopress_social_knowledge_tax_id'] : null;
		$knowledge_nl    = isset($seopress_social_option['seopress_social_knowledge_nl']); ?>

		<div class="seopress-setup-content seopress-option">
			<h1><?php esc_html_e('Your site', 'wp-seopress'); ?></h1>

			<?php $this->setup_wizard_sub_steps(); ?>

			<div class="seopress-tab active">

				<form method="post">
					<h2><?php esc_html_e('Tell us more about your site','wp-seopress'); ?></h2>
					<p><?php esc_html_e('To build title tags and knowledge graph for Google, you need to fill out the fields below to configure the general settings.', 'wp-seopress'); ?>
					</p>

					<p>
						<label for="site_title"><?php esc_html_e('Site title', 'wp-seopress'); ?></label>
						<input type="text" id="site_title" class="location-input" name="site_title"
							placeholder="<?php esc_html_e('e.g. My super website', 'wp-seopress'); ?>"
							required value="<?php echo esc_html($site_title); ?>" />
					</p>

					<p class="description">
						<?php echo wp_kses_post(__('The site title will be used by the dynamic variable <strong>%%sitetitle%%</strong> in your title and meta description templates.', 'wp-seopress')); ?>
					</p>

					<p>
						<label for="alt_site_title"><?php esc_html_e('Alternative site title', 'wp-seopress'); ?></label>
						<input type="text" id="alt_site_title" class="location-input" name="alt_site_title" placeholder="<?php esc_html_e('e.g. My alternative site title', 'wp-seopress'); ?>" value="<?php echo esc_attr($alt_site_title); ?>" />
					</p>

					<p class="description"><?php /* translators: %s documentation URL  */ echo wp_kses_post(sprintf(__('The alternate name of the website (for example, if there\'s a commonly recognized acronym or shorter name for your site), if applicable. Make sure the name meets the <a href="%s" target="_blank">content guidelines</a>.<span class="dashicons dashicons-external"></span>','wp-seopress'), esc_url($docs['titles']['alt_title']))); ?></p>

					<p>
						<label for="knowledge_type"><?php esc_html_e('Person or organization', 'wp-seopress'); ?></label>
						<?php
						echo '<select id="knowledge_type" name="knowledge_type" data-placeholder="' . esc_attr__('Choose a knowledge type', 'wp-seopress') . '"	class="location-input dropdown">';
						echo ' <option ';
						if ('None' == $knowledge_type) {
							echo 'selected="selected"';
						}
						echo ' value="none">' . esc_html__('None (will disable this feature)', 'wp-seopress') . '</option>';
						echo ' <option ';
						if ('Person' == $knowledge_type) {
							echo 'selected="selected"';
						}
						echo ' value="Person">' . esc_html__('Person', 'wp-seopress') . '</option>';
						echo '<option ';
						if ('Organization' == $knowledge_type) {
							echo 'selected="selected"';
						}
						echo ' value="Organization">' . esc_html__('Organization', 'wp-seopress') . '</option>';
						echo '</select>'; ?>
					</p>

					<p class="description">
						<?php echo wp_kses_post(__('Choose between <strong>"Organization"</strong> (for companies, associations, organizations), or <strong>"Personal"</strong> for a personal site, to help Google better understand your type of website and generate a Knowledge Graph panel.', 'wp-seopress')); ?>
					</p>

					<p>
						<label for="knowledge_name"><?php esc_html_e('Your name/organization', 'wp-seopress'); ?></label>
						<input type="text" id="knowledge_name" class="location-input" name="knowledge_name"
							placeholder="<?php esc_html_e('e.g. My Company Name', 'wp-seopress'); ?>"
							value="<?php echo esc_html($knowledge_name); ?>" />
					</p>

					<p>
						<label for="knowledge_img_meta"><?php esc_html_e('Your photo/organization logo', 'wp-seopress'); ?></label>
						<input type="text" id="knowledge_img_meta" class="location-input" name="knowledge_img"
						placeholder="<?php esc_html_e('e.g. https://www.example.com/logo.png', 'wp-seopress'); ?>"
						value="<?php echo esc_url($knowledge_img); ?>" />

						<input id="knowledge_img_upload" class="btn btnSecondary" type="button" value="<?php esc_attr_e('Upload an Image', 'wp-seopress'); ?>" />
					</p>

					<p class="description">
						<?php esc_html_e('JPG, PNG, WebP and GIF allowed. The minimum allowed image dimension is 200 x 200 pixels.', 'wp-seopress'); ?><br>
						<?php esc_html_e('The size of the image file must not exceed 8 MB. Use images that are at least 1200 x 630 pixels for the best display on high resolution devices.', 'wp-seopress'); ?><br>
						<?php esc_html_e('At the minimum, you should use images that are 600 x 315 pixels to display link page posts with larger images.', 'wp-seopress'); ?>
					</p>

					<p>
						<label for="knowledge_phone_meta"><?php esc_html_e('Organization‘s phone number (only for Organizations)', 'wp-seopress'); ?></label>
						<input type="text" id="knowledge_phone_meta" class="location-input" name="knowledge_phone"
						placeholder="<?php esc_html_e('e.g. +33123456789 (internationalized version required)', 'wp-seopress'); ?>"
						value="<?php echo esc_html($knowledge_phone); ?>" />
					</p>

					<p>
						<label for="knowledge_tax_id_meta"><?php esc_html_e('VAT ID (only for Organizations)', 'wp-seopress'); ?></label>
						<input type="text" id="knowledge_tax_id_meta" class="location-input" name="knowledge_tax_id"
						placeholder="<?php esc_html_e('e.g. FR12345678901', 'wp-seopress'); ?>"
						value="<?php echo esc_html($knowledge_tax_id); ?>" />
					</p>

					<p class="seopress-setup-actions step">
						<button type="submit" class="btnPrimary btn btnNext"
							value="<?php esc_attr_e('Save & Continue', 'wp-seopress'); ?>"
							name="save_step">
							<?php esc_html_e('Save & Continue', 'wp-seopress'); ?>
						</button>
						<?php wp_nonce_field('seopress-setup'); ?>
					</p>
				</form>
			</div>
		</div>
	<?php
	}

	/**
	 * Save step 2.0 settings.
	 */
	public function seopress_setup_site_save() {
		check_admin_referer('seopress-setup');

		//Get options
		$seopress_titles_option = get_option('seopress_titles_option_name');
		$seopress_social_option = get_option('seopress_social_option_name');

		//Titles
		$seopress_titles_option['seopress_titles_home_site_title'] = isset($_POST['site_title']) ? sanitize_text_field(wp_unslash($_POST['site_title'])) : '';
		$seopress_titles_option['seopress_titles_home_site_title_alt'] = isset($_POST['alt_site_title']) ? sanitize_text_field(wp_unslash($_POST['alt_site_title'])) : '';

		//Social
		$seopress_social_option['seopress_social_knowledge_type']   = isset($_POST['knowledge_type']) ? esc_attr(wp_unslash($_POST['knowledge_type'])) : '';
		$seopress_social_option['seopress_social_knowledge_name']   = isset($_POST['knowledge_name']) ? sanitize_text_field(wp_unslash($_POST['knowledge_name'])) : '';
		$seopress_social_option['seopress_social_knowledge_img']    = isset($_POST['knowledge_img']) ? sanitize_text_field(wp_unslash($_POST['knowledge_img'])) : '';
		$seopress_social_option['seopress_social_knowledge_email']  = isset($_POST['knowledge_email']) ? sanitize_text_field(wp_unslash($_POST['knowledge_email'])) : '';
		$seopress_social_option['seopress_social_knowledge_phone']  = isset($_POST['knowledge_phone']) ? sanitize_text_field(wp_unslash($_POST['knowledge_phone'])) : '';
		$seopress_social_option['seopress_social_knowledge_tax_id']  = isset($_POST['knowledge_tax_id']) ? sanitize_text_field(wp_unslash($_POST['knowledge_tax_id'])) : '';
		$seopress_social_option['seopress_social_knowledge_nl']     = isset($_POST['knowledge_nl']) ? esc_attr(wp_unslash($_POST['knowledge_nl'])) : null;

		//Save options
		update_option('seopress_titles_option_name', $seopress_titles_option, false);
		update_option('seopress_social_option_name', $seopress_social_option, false);

		wp_safe_redirect(esc_url_raw($this->get_next_step_link()));
		exit;
	}

	/**
	 * Init "Step 2.1: Your site - Social accounts".
	 */
	public function seopress_setup_social_accounts() {
		$seopress_social_option = get_option('seopress_social_option_name');

		$knowledge_fb    = isset($seopress_social_option['seopress_social_accounts_facebook']) ? $seopress_social_option['seopress_social_accounts_facebook'] : '';
		$knowledge_tw    = isset($seopress_social_option['seopress_social_accounts_twitter']) ? $seopress_social_option['seopress_social_accounts_twitter'] : '';
		$knowledge_pin   = isset($seopress_social_option['seopress_social_accounts_pinterest']) ? $seopress_social_option['seopress_social_accounts_pinterest'] : '';
		$knowledge_insta = isset($seopress_social_option['seopress_social_accounts_instagram']) ? $seopress_social_option['seopress_social_accounts_instagram'] : '';
		$knowledge_yt    = isset($seopress_social_option['seopress_social_accounts_youtube']) ? $seopress_social_option['seopress_social_accounts_youtube'] : '';
		$knowledge_li    = isset($seopress_social_option['seopress_social_accounts_linkedin']) ? $seopress_social_option['seopress_social_accounts_linkedin'] : '';
		$knowledge_extra = isset($seopress_social_option['seopress_social_accounts_extra']) ? $seopress_social_option['seopress_social_accounts_extra'] : null;

		$social_media_accounts = [
			'knowledge_fb' => [
				'label' => __('Facebook page URL', 'wp-seopress'),
				'placeholder' => __('e.g. https://facebook.com/my-page-url', 'wp-seopress'),
				'value' => esc_url($knowledge_fb),
				'icon' => 'facebook.svg',
				'alt' => 'Facebook'
			],
			'knowledge_tw' => [
				'label' => __('X Username', 'wp-seopress'),
				'placeholder' => __('e.g. @my_x_account', 'wp-seopress'),
				'value' => esc_url($knowledge_tw),
				'icon' => 'x.svg',
				'alt' => 'X'
			],
			'knowledge_pin' => [
				'label' => __('Pinterest URL', 'wp-seopress'),
				'placeholder' => __('e.g. https://pinterest.com/my-page-url/', 'wp-seopress'),
				'value' => esc_url($knowledge_pin),
				'icon' => 'pinterest.svg',
				'alt' => 'Pinterest'
			],
			'knowledge_insta' => [
				'label' => __('Instagram URL', 'wp-seopress'),
				'placeholder' => __('e.g. https://www.instagram.com/my-page-url/', 'wp-seopress'),
				'value' => esc_url($knowledge_insta),
				'icon' => 'instagram.svg',
				'alt' => 'Instagram'
			],
			'knowledge_yt' => [
				'label' => __('YouTube URL', 'wp-seopress'),
				'placeholder' => __('e.g. https://www.youtube.com/my-channel-url', 'wp-seopress'),
				'value' => esc_url($knowledge_yt),
				'icon' => 'youtube.svg',
				'alt' => 'YouTube'
			],
			'knowledge_li' => [
				'label' => __('LinkedIn URL', 'wp-seopress'),
				'placeholder' => __('e.g. http://linkedin.com/company/my-company-url/', 'wp-seopress'),
				'value' => esc_url($knowledge_li),
				'icon' => 'linkedin.svg',
				'alt' => 'LinkedIn'
			]
		];
		?>
		<div class="seopress-setup-content seopress-option">
			<h1><?php esc_html_e('Your site', 'wp-seopress'); ?></h1>

			<?php $this->setup_wizard_sub_steps(); ?>

			<div class="seopress-tab active">

				<form method="post">
					<h2><?php esc_html_e('Link your site to your social networks','wp-seopress'); ?></h2>

					<p><?php esc_html_e('Fill in your social accounts for search engines.', 'wp-seopress'); ?>
					</p>

					<?php
					foreach ($social_media_accounts as $key => $account) {
					?>
						<p>
							<span class="seopress-social-icon seopress-d-flex seopress-align-items-center">
								<img src="<?php echo esc_url(SEOPRESS_URL_ASSETS . '/img/social/' . $account['icon']); ?>" alt="<?php echo esc_attr($account['alt']); ?>" width="24" height="24">
								<label for="<?php echo esc_attr($key); ?>"><?php echo esc_html($account['label']); ?></label>
							</span>
							<input type="text" id="<?php echo esc_attr($key); ?>" class="location-input" name="<?php echo esc_attr($key); ?>"
								placeholder="<?php echo esc_attr($account['placeholder']); ?>"
								value="<?php echo esc_attr($account['value']); ?>" />
						</p>
					<?php
					}
					?>

					<p>
						<span class="seopress-social-icon seopress-d-flex seopress-align-items-center">
							<label for="knowledge_extra"><?php esc_html_e('Additional accounts', 'wp-seopress'); ?></label>
						</span>
						<textarea id="knowledge_extra" class="location-input" name="knowledge_extra" rows="8"
						placeholder="<?php esc_html_e('Enter 1 URL per line (e.g. https://example.com/my-profile)', 'wp-seopress'); ?>"
						aria-label="<?php esc_html_e('Enter 1 URL per line (e.g. https://example.com/my-profile)', 'wp-seopress'); ?>"><?php echo esc_html($knowledge_extra); ?></textarea>
					</p>

					<p class="seopress-setup-actions step">
						<button type="submit" class="btnPrimary btn btnNext"
							value="<?php esc_attr_e('Save & Continue', 'wp-seopress'); ?>"
							name="save_step">
							<?php esc_html_e('Save & Continue', 'wp-seopress'); ?>
						</button>
						<?php wp_nonce_field('seopress-setup'); ?>
					</p>
				</form>
			</div>
		</div>
	<?php
	}

	/**
	 * Save step 2.1 settings.
	 */
	public function seopress_setup_social_accounts_save() {
		check_admin_referer('seopress-setup');

		//Get options
		$seopress_social_option = get_option('seopress_social_option_name');

		//Social accounts
		$seopress_social_option['seopress_social_accounts_facebook']   = isset($_POST['knowledge_fb']) ? sanitize_text_field(wp_unslash($_POST['knowledge_fb'])) : '';
		$seopress_social_option['seopress_social_accounts_twitter']    = isset($_POST['knowledge_tw']) ? sanitize_text_field(wp_unslash($_POST['knowledge_tw'])) : '';
		$seopress_social_option['seopress_social_accounts_pinterest']  = isset($_POST['knowledge_pin']) ? sanitize_text_field(wp_unslash($_POST['knowledge_pin'])) : '';
		$seopress_social_option['seopress_social_accounts_instagram']  = isset($_POST['knowledge_insta']) ? sanitize_text_field(wp_unslash($_POST['knowledge_insta'])) : '';
		$seopress_social_option['seopress_social_accounts_youtube']    = isset($_POST['knowledge_yt']) ? sanitize_text_field(wp_unslash($_POST['knowledge_yt'])) : '';
		$seopress_social_option['seopress_social_accounts_linkedin']   = isset($_POST['knowledge_li']) ? sanitize_text_field(wp_unslash($_POST['knowledge_li'])) : '';
		$seopress_social_option['seopress_social_accounts_extra']   = isset($_POST['knowledge_extra']) ? sanitize_textarea_field(wp_unslash($_POST['knowledge_extra'])) : '';

		//Save options
		update_option('seopress_social_option_name', $seopress_social_option, false);

		wp_safe_redirect(esc_url_raw($this->get_next_step_link()));
		exit;
	}

	/**
	 *	Init "Step 3.0: Indexing Post Types Step".
	 */
	public function seopress_setup_indexing_post_types() {
		$seopress_titles_option = get_option('seopress_titles_option_name'); ?>

		<div class="seopress-setup-content seopress-option">
			<h1><?php esc_html_e('Indexing', 'wp-seopress'); ?></h1>

			<?php $this->setup_wizard_sub_steps(); ?>

			<div class="seopress-tab active">

				<form method="post" class="seopress-wizard-indexing-form">
					<?php
					$postTypes = seopress_get_service('WordPressData')->getPostTypes();
					if ( ! empty($postTypes)) { ?>
					<h2>
						<?php esc_html_e('For which single post types, should indexing be disabled?', 'wp-seopress'); ?>
					</h2>

					<div class="seopress-notice">
						<p><?php echo wp_kses_post(__('Custom post types are a content type in WordPress. <strong>Post</strong> and <strong>Page</strong> are the <strong>default post types</strong>.','wp-seopress')); ?></p>
						<p><?php echo wp_kses_post(__('You can create your own type of content like "product" or "business": these are <strong>custom post types</strong>.','wp-seopress')); ?></p>
					</div>

					<?php
						//Post Types
						foreach ($postTypes as $seopress_cpt_key => $seopress_cpt_value) {
							$seopress_titles_single_titles = isset($seopress_titles_option['seopress_titles_single_titles'][$seopress_cpt_key]['noindex']); ?>

							<h3><?php echo esc_html($seopress_cpt_value->labels->name); ?>
								<code>[<?php echo esc_html($seopress_cpt_value->name); ?>]</code>
							</h3>
							<ul>
								<li class="seopress-wizard-service-item">
									<label
										for="seopress_titles_single_cpt_noindex[<?php echo esc_html($seopress_cpt_key); ?>]">
										<input
											id="seopress_titles_single_cpt_noindex[<?php echo esc_html($seopress_cpt_key); ?>]"
											name="seopress_titles_option_name[seopress_titles_single_titles][<?php echo esc_html($seopress_cpt_key); ?>][noindex]"
											type="checkbox" <?php if ('1' == $seopress_titles_single_titles) {
												echo 'checked="yes"';
											} ?>
										value="1"/>
										<?php echo wp_kses_post(__('Do not display this single post type in search engine results <strong>(noindex)</strong>', 'wp-seopress')); ?>
									</label>
								</li>
							</ul>
						<?php
						}
					?>
					
					<?php } ?>

					<p class="seopress-setup-actions step">
						<button type="submit" class="btnPrimary btn btnNext"
							value="<?php esc_attr_e('Save & Continue', 'wp-seopress'); ?>"
							name="save_step">
							<?php esc_html_e('Save & Continue', 'wp-seopress'); ?>
						</button>

						<?php wp_nonce_field('seopress-setup'); ?>
					</p>
				</form>
			</div>
		</div>
	<?php
	}

	/**
	 * Save Step 3.0 Post Types settings.
	 */
	public function seopress_setup_indexing_post_types_save() {
		check_admin_referer('seopress-setup');

		//Get options
		$seopress_titles_option = get_option('seopress_titles_option_name');
		$postTypes = seopress_get_service('WordPressData')->getPostTypes();
		//Post Types noindex
		foreach ($postTypes as $seopress_cpt_key => $seopress_cpt_value) {
			if (isset($_POST['seopress_titles_option_name']['seopress_titles_single_titles'][$seopress_cpt_key]['noindex'])) {
				$noindex = esc_attr(wp_unslash($_POST['seopress_titles_option_name']['seopress_titles_single_titles'][$seopress_cpt_key]['noindex']));
			} else {
				$noindex = null;
			}
			$seopress_titles_option['seopress_titles_single_titles'][$seopress_cpt_key]['noindex'] = $noindex;
		}

		//Save options
		update_option('seopress_titles_option_name', $seopress_titles_option);

		wp_redirect(esc_url_raw($this->get_next_step_link()));
		exit;
	}

	/**
	 *	Init "Step 3.1: Indexing Archives Step".
	 */
	public function seopress_setup_indexing_archives() {
		$seopress_titles_option = get_option('seopress_titles_option_name'); ?>

		<div class="seopress-setup-content seopress-option">
			<h1><?php esc_html_e('Indexing', 'wp-seopress'); ?></h1>

			<?php $this->setup_wizard_sub_steps(); ?>

			<div class="seopress-tab active">

				<form method="post" class="seopress-wizard-indexing-form">
					<?php
					$postTypes = seopress_get_service('WordPressData')->getPostTypes();
					if ( ! empty($postTypes)) {
						$cpt = $postTypes;
						unset($cpt['post']);
						unset($cpt['page']);
						?>

						<?php
							if (!empty($cpt)) { ?>
								<h2>
									<?php esc_html_e('For which post type archives, should indexing be disabled?', 'wp-seopress'); ?>
								</h2>

								<div class="seopress-notice">
									<p><?php echo wp_kses_post(__('<strong>Archive pages</strong> are automatically generated by WordPress. They group specific content such as your latest articles, a product category or your content by author or date.', 'wp-seopress')); ?></p>
									<p><?php echo wp_kses_post(__('Below the list of your <strong>post type archives</strong>:', 'wp-seopress')); ?></p>
								</div>
								
								<?php
								foreach ($cpt as $seopress_cpt_key => $seopress_cpt_value) {
									$seopress_titles_archive_titles = isset($seopress_titles_option['seopress_titles_archive_titles'][$seopress_cpt_key]['noindex']); ?>
									<h3><?php echo esc_html($seopress_cpt_value->labels->name); ?>
										<code>[<?php echo esc_html($seopress_cpt_value->name); ?>]</code>
									</h3>

									<ul>

										<li class="seopress-wizard-service-item">
											<input
												id="seopress_titles_archive_cpt_noindex[<?php echo esc_html($seopress_cpt_key); ?>]"
												name="seopress_titles_option_name[seopress_titles_archive_titles][<?php echo esc_html($seopress_cpt_key); ?>][noindex]"
												type="checkbox" <?php if ('1' == $seopress_titles_archive_titles) {
													echo 'checked="yes"';
												} ?>
											value="1"/>
											<label
												for="seopress_titles_archive_cpt_noindex[<?php echo esc_html($seopress_cpt_key); ?>]">
												
												<?php echo wp_kses_post(__('Do not display this post type archive in search engine results <strong>(noindex)</strong>', 'wp-seopress')); ?>
											</label>
										</li>
									</ul>
									<?php
								}
							}

							if (empty($cpt)) { ?>
							<p><?php esc_html_e('You don’t have any post type archives, you can continue to the next step.','wp-seopress'); ?></p>
						<?php }
					}

					$seopress_titles_archives_date_noindex = isset($seopress_titles_option['seopress_titles_archives_date_noindex']);
					$seopress_titles_archives_search_title_noindex = isset($seopress_titles_option['seopress_titles_archives_search_title_noindex']);
					$seopress_titles_archives_author_noindex = isset($seopress_titles_option['seopress_titles_archives_author_noindex']);
					?>
					
					<h2>
						<?php esc_html_e('For which other archives, should indexing be disabled?', 'wp-seopress'); ?>
					</h2>

					<h3><?php esc_html_e('Date archives','wp-seopress'); ?></h3>
					<p><?php echo wp_kses_post(__('Date archives are automatically generated by WordPress. They group specific content by date.', 'wp-seopress')); ?></p>
					<p><?php echo wp_kses_post(__('Example: <strong>https://example.com/2025/01/01/</strong>', 'wp-seopress')); ?></p>

					<ul>
						<li class="seopress-wizard-service-item">
							<input
								id="seopress_titles_option_name[seopress_titles_archives_date_noindex]"
								name="seopress_titles_option_name[seopress_titles_archives_date_noindex]"
								type="checkbox" <?php if ('1' == $seopress_titles_archives_date_noindex) {
									echo 'checked="yes"';
								} ?>
							value="1"/>	
							<label for="seopress_titles_option_name[seopress_titles_archives_date_noindex]">
								<?php echo wp_kses_post(__('Do not display date archives in search engine results <strong>(noindex)</strong>', 'wp-seopress')); ?>
							</label>
						</li>
					</ul>

					<hr>
					<h3><?php esc_html_e('Search archives','wp-seopress'); ?></h3>
					<p>
						<?php echo wp_kses_post(__('Search archives are automatically generated by WordPress. They group specific content by search term.', 'wp-seopress')); ?>
					</p>
					<p><?php echo wp_kses_post(__('Example: <strong>https://example.com/?s=keyword</strong>', 'wp-seopress')); ?></p>
					<ul>
						<li class="seopress-wizard-service-item">
							<input
								id="seopress_titles_option_name[seopress_titles_archives_search_title_noindex]"
								name="seopress_titles_option_name[seopress_titles_archives_search_title_noindex]"
								type="checkbox" <?php if ('1' == $seopress_titles_archives_search_title_noindex) {
									echo 'checked="yes"';
								} ?>
							value="1"/>
							<label for="seopress_titles_option_name[seopress_titles_archives_search_title_noindex]">
								<?php echo wp_kses_post(__('Do not display search archives in search engine results <strong>(noindex)</strong>', 'wp-seopress')); ?>
							</label>
						</li>
					</ul>

					<hr>
					<h3><?php esc_html_e('Author archives','wp-seopress'); ?></h3>
					<p><?php echo wp_kses_post(__('Author archives are automatically generated by WordPress. They group specific content by author.', 'wp-seopress')); ?></p>
					<p><?php echo wp_kses_post(__('Example: <strong>https://example.com/author/john-doe/</strong>', 'wp-seopress')); ?></p>
					<p><?php esc_html_e('You only have one author on your site? Check this option to avoid duplicate content.', 'wp-seopress'); ?></p>
					<ul>
						<li class="seopress-wizard-service-item">
							<input
								id="seopress_titles_option_name[seopress_titles_archives_author_noindex]"
								name="seopress_titles_option_name[seopress_titles_archives_author_noindex]"
								type="checkbox" <?php if ('1' == $seopress_titles_archives_author_noindex) {
									echo 'checked="yes"';
								} ?>
							value="1"/>
							<label for="seopress_titles_option_name[seopress_titles_archives_author_noindex]">
								<?php echo wp_kses_post(__('Do not display author archives in search engine results <strong>(noindex)</strong>', 'wp-seopress')); ?>
							</label>
						</li>
					</ul>

					<p class="seopress-setup-actions step">
						<button type="submit" class="btnPrimary btn btnNext"
							value="<?php esc_attr_e('Save & Continue', 'wp-seopress'); ?>"
							name="save_step">
							<?php esc_html_e('Save & Continue', 'wp-seopress'); ?>
						</button>

						<?php wp_nonce_field('seopress-setup'); ?>
					</p>
				</form>
			</div>
		</div>
	<?php
	}

	/**
	 * Save Step 3.1 Archives settings.
	 */
	public function seopress_setup_indexing_archives_save() {
		check_admin_referer('seopress-setup');

		//Get options
		$seopress_titles_option = get_option('seopress_titles_option_name');
		$postTypes = seopress_get_service('WordPressData')->getPostTypes();

		//Post Type archives noindex
		foreach ($postTypes as $seopress_cpt_key => $seopress_cpt_value) {
			if (isset($_POST['seopress_titles_option_name']['seopress_titles_archive_titles'][$seopress_cpt_key]['noindex'])) {
				$noindex = esc_attr(wp_unslash($_POST['seopress_titles_option_name']['seopress_titles_archive_titles'][$seopress_cpt_key]['noindex']));
			} else {
				$noindex = null;
			}
			$seopress_titles_option['seopress_titles_archive_titles'][$seopress_cpt_key]['noindex'] = $noindex;
		}

		//Date archives noindex
		if (isset($_POST['seopress_titles_option_name']['seopress_titles_archives_date_noindex'])) {
			$noindex = esc_attr(wp_unslash($_POST['seopress_titles_option_name']['seopress_titles_archives_date_noindex']));
		} else {
			$noindex = null;
		}
		$seopress_titles_option['seopress_titles_archives_date_noindex'] = $noindex;

		//Search archives noindex
		if (isset($_POST['seopress_titles_option_name']['seopress_titles_archives_search_title_noindex'])) {
			$noindex = esc_attr(wp_unslash($_POST['seopress_titles_option_name']['seopress_titles_archives_search_title_noindex']));
		} else {
			$noindex = null;
		}
		$seopress_titles_option['seopress_titles_archives_search_title_noindex'] = $noindex;

		//Author indexing
		if (isset($_POST['seopress_titles_option_name']['seopress_titles_archives_author_noindex'])) {
			$noindex = esc_attr(wp_unslash($_POST['seopress_titles_option_name']['seopress_titles_archives_author_noindex']));
		} else {
			$noindex = null;
		}
		$seopress_titles_option['seopress_titles_archives_author_noindex'] = $noindex;


		//Save options
		update_option('seopress_titles_option_name', $seopress_titles_option);

		wp_redirect(esc_url_raw($this->get_next_step_link()));
		exit;
	}

	/**
	 *	Init "Step 3.2: Indexing Taxonomies Step".
	 */
	public function seopress_setup_indexing_taxonomies() {
		$seopress_titles_option = get_option('seopress_titles_option_name'); ?>

		<div class="seopress-setup-content seopress-option">
			<h1><?php esc_html_e('Indexing', 'wp-seopress'); ?></h1>

			<?php $this->setup_wizard_sub_steps(); ?>

			<div class="seopress-tab active">

				<form method="post" class="seopress-wizard-indexing-form">
					<?php

					$taxonomies = seopress_get_service('WordPressData')->getTaxonomies();

					if ( ! empty($taxonomies)) { ?>
					<h2>
						<?php esc_html_e('For which taxonomy archives, should indexing be disabled?', 'wp-seopress'); ?>
					</h2>

					<div class="seopress-notice">
						<p><?php echo wp_kses_post(__('<strong>Taxonomies</strong> are the method of classifying content and data in WordPress. When you use a taxonomy you’re grouping similar things together. The taxonomy refers to the sum of those groups.','wp-seopress')); ?></p>
						<p><?php echo wp_kses_post(__('<strong>Categories</strong> and <strong>Tags</strong> are the default taxonomies. You can add your own taxonomies like "product categories": these are called <strong>custom taxonomies</strong>.','wp-seopress')); ?></p>
					</div>

					
					<?php
						//Archives
						foreach ($taxonomies as $seopress_tax_key => $seopress_tax_value) {
							$seopress_titles_tax_titles = isset($seopress_titles_option['seopress_titles_tax_titles'][$seopress_tax_key]['noindex']); ?>

							<h3><?php echo esc_html($seopress_tax_value->labels->name); ?>
								<code>[<?php echo esc_html($seopress_tax_value->name); ?>]</code>
							</h3>

							<ul>
								<li class="seopress-wizard-service-item">
									<input
											id="seopress_titles_tax_noindex[<?php echo esc_html($seopress_tax_key); ?>]"
											name="seopress_titles_option_name[seopress_titles_tax_titles][<?php echo esc_html($seopress_tax_key); ?>][noindex]"
											type="checkbox" <?php if ('1' == $seopress_titles_tax_titles) {
										echo 'checked="yes"';
									} ?>
									value="1"/>
									<label for="seopress_titles_tax_noindex[<?php echo esc_html($seopress_tax_key); ?>]">
										<?php echo wp_kses_post(__('Do not display this taxonomy archive in search engine results <strong>(noindex)</strong>', 'wp-seopress')); ?>
									</label>
								</li>
								<?php if ($seopress_tax_key =='post_tag') { ?>
									<div class="seopress-notice is-warning is-inline">
										<p>
											<?php echo wp_kses_post(__('We do not recommend indexing <strong>tags</strong> which are, in the vast majority of cases, a source of duplicate content.', 'wp-seopress')); ?>
										</p>
									</div>
								<?php } ?>
							</ul>
						<?php
						}
					?>

					<?php } ?>

					<p class="seopress-setup-actions step">
						<button type="submit" class="btnPrimary btn btnNext"
							value="<?php esc_attr_e('Save & Continue', 'wp-seopress'); ?>"
							name="save_step">
							<?php esc_html_e('Save & Continue', 'wp-seopress'); ?>
						</button>

						<?php wp_nonce_field('seopress-setup'); ?>
					</p>
				</form>
			</div>
		</div>
	<?php
	}

	/**
	 * Save Step 3.2 taxonomies settings.
	 */
	public function seopress_setup_indexing_taxonomies_save() {
		check_admin_referer('seopress-setup');

		//Get options
		$seopress_titles_option = get_option('seopress_titles_option_name');

		//Archives noindex
		foreach (seopress_get_service('WordPressData')->getTaxonomies() as $seopress_tax_key => $seopress_tax_value) {
			if (isset($_POST['seopress_titles_option_name']['seopress_titles_tax_titles'][$seopress_tax_key]['noindex'])) {
				$noindex = esc_attr(wp_unslash($_POST['seopress_titles_option_name']['seopress_titles_tax_titles'][$seopress_tax_key]['noindex']));
			} else {
				$noindex = null;
			}
			$seopress_titles_option['seopress_titles_tax_titles'][$seopress_tax_key]['noindex'] = $noindex;
		}

		//Save options
		update_option('seopress_titles_option_name', $seopress_titles_option);

		wp_redirect(esc_url_raw($this->get_next_step_link()));
		exit;
	}

	/**
	 *	Init "Step 4: Advanced Step".
	 */
	public function seopress_setup_advanced() {
		$seopress_advanced_option         = get_option('seopress_advanced_option_name');
		$attachments_file                 = isset($seopress_advanced_option['seopress_advanced_advanced_attachments_file']);
		$category_url                     = isset($seopress_advanced_option['seopress_advanced_advanced_category_url']);
		$product_category_url             = isset($seopress_advanced_option['seopress_advanced_advanced_product_cat_url']);
		$image_auto_alt_txt               = isset($seopress_advanced_option['seopress_advanced_advanced_image_auto_alt_txt']); ?>

		<div class="seopress-setup-content seopress-option">

			<h1><?php esc_html_e('Advanced options', 'wp-seopress'); ?></h1>

			<?php $this->setup_wizard_sub_steps(); ?>

			<div class="seopress-tab active">
				<h2><?php esc_html_e('Almost done!','wp-seopress'); ?></h2>

				<p><?php esc_html_e('Final step before being ready to rank on search engines.', 'wp-seopress'); ?></p>

				<form method="post">
					<ul>
						<!-- Redirect attachment pages to URL -->
						<li class="seopress-wizard-service-item">
							<label for="attachments_file">
								<input id="attachments_file" class="location-input" name="attachments_file" type="checkbox" <?php if ('1' == $attachments_file) {
							echo 'checked="yes"';
						} ?> value="1"/>
								<?php esc_html_e('Redirect attachment pages to their file URL (https://www.example.com/my-image-file.jpg)', 'wp-seopress'); ?>
							</label>
						</li>
						<li class="description">
							<?php /* translators: %s default: SEOPress */ printf(esc_html__('By default, %s redirects your Attachment pages to the parent post. Optimize this by redirecting the user directly to the URL of the media file.', 'wp-seopress'), esc_html($this->seo_title)); ?>
						</li>

						<!-- Automatically set alt text on already inserted image -->
						<li class="seopress-wizard-service-item">
							<label for="image_auto_alt_txt">
								<input id="image_auto_alt_txt" class="location-input" name="image_auto_alt_txt" type="checkbox" <?php if ('1' == $image_auto_alt_txt) {
							echo 'checked="yes"';
						} ?> value="1"/>
								<?php esc_html_e('Automatically set alt text on already inserted image', 'wp-seopress'); ?>
							</label>
						</li>
						<li class="description">
							<?php esc_html_e('By default, WordPress does not update image alt texts entered from the media library after they are inserted into the content of a post, page, or post type. By checking this box, this will be done when the page loads on the fly as long as this option remains active.', 'wp-seopress'); ?>
						</li>

						<!-- Remove /category/ in URLs -->
						<li class="seopress-wizard-service-item">
							<label for="category_url">
								<input id="category_url" name="category_url" type="checkbox" class="location-input" <?php if ('1' == $category_url) {
							echo 'checked="yes"';
						} ?> value="1"/>
								<?php
									$category_base = '/category/';
						if (get_option('category_base')) {
							$category_base = '/' . get_option('category_base');
						}

						/* translators: %s permalink category base  */
						echo wp_kses_post(sprintf(__('Remove <strong>%s</strong> in your permalinks', 'wp-seopress'), esc_attr($category_base))); ?>
							</label>
						</li>
						<li class="description">
							<?php /* translators: %s category base */ echo wp_kses_post(sprintf(__('Shorten your URLs by removing %s and improve your SEO.', 'wp-seopress'), esc_attr($category_base))); ?>
						</li>

						<?php if (is_plugin_active('woocommerce/woocommerce.php')) { ?>
							<!-- Remove /product-category/ in URLs -->
							<li class="seopress-wizard-service-item">
								<label for="product_category_url">
									<input id="product_category_url" name="product_category_url" type="checkbox" class="location-input"
										<?php if ('1' == $product_category_url) {
								echo 'checked="yes"';
							} ?> value="1"/>
									<?php
										$category_base = get_option('woocommerce_permalinks');
							$category_base             = $category_base['category_base'];

							if ('' != $category_base) {
								$category_base = '/' . $category_base . '/';
							} else {
								$category_base = '/product-category/';
							}

							/* translators: %s permalink category / product category base  */
							echo wp_kses_post(sprintf(__('Remove <strong>%s</strong> in your permalinks', 'wp-seopress'), esc_attr($category_base))); ?>
								</label>
							</li>
							<li class="description">
								<?php /* translators: %s permalink category / product category base  */ echo wp_kses_post(sprintf(__('Shorten your URLs by removing %s and improve your SEO.', 'wp-seopress'), esc_attr($category_base))); ?>
							</li>
						<?php } ?>
					</ul>

					<p class="seopress-setup-actions step">
						<button type="submit" class="btn btnPrimary btnNext"
							value="<?php esc_attr_e('Save & Continue', 'wp-seopress'); ?>"
							name="save_step">
							<?php esc_html_e('Save & Continue', 'wp-seopress'); ?>
						</button>

						<?php wp_nonce_field('seopress-setup'); ?>
					</p>
				</form>
			</div>
		</div>
	<?php
	}

	/**
	 * Save step 4.1 settings.
	 */
	public function seopress_setup_advanced_save() {
		check_admin_referer('seopress-setup');

		//Get options
		$seopress_advanced_option = get_option('seopress_advanced_option_name');

		//Advanced
		$seopress_advanced_option['seopress_advanced_advanced_attachments_file']    = isset($_POST['attachments_file']) ? esc_attr(wp_unslash($_POST['attachments_file'])) : null;
		$seopress_advanced_option['seopress_advanced_advanced_image_auto_alt_txt']    = isset($_POST['image_auto_alt_txt']) ? esc_attr(wp_unslash($_POST['image_auto_alt_txt'])) : null;
		$seopress_advanced_option['seopress_advanced_advanced_category_url']        = isset($_POST['category_url']) ? esc_attr(wp_unslash($_POST['category_url'])) : null;

		if (is_plugin_active('woocommerce/woocommerce.php')) {
			$seopress_advanced_option['seopress_advanced_advanced_product_cat_url']        = isset($_POST['product_category_url']) ? esc_attr(wp_unslash($_POST['product_category_url'])) : null;
		}

		//Save options
		update_option('seopress_advanced_option_name', $seopress_advanced_option, false);

		wp_redirect(esc_url_raw($this->get_next_step_link()));

		exit;
	}

	/**
	 *	Init "Step 4.2: Advanced Step".
	 */
	public function seopress_setup_universal() {
		$seopress_advanced_option         = get_option('seopress_advanced_option_name');
		$universal_seo_metabox            = isset($seopress_advanced_option['seopress_advanced_appearance_universal_metabox_disable']) ? esc_attr($seopress_advanced_option['seopress_advanced_appearance_universal_metabox_disable']) : null;
		?>

		<div class="seopress-setup-content seopress-option">

			<h1><?php esc_html_e('Advanced options', 'wp-seopress'); ?></h1>

			<?php $this->setup_wizard_sub_steps(); ?>

			<div class="seopress-tab active">

				<form method="post">
					<h2>
						<?php esc_html_e('Improve your workflow with the Universal SEO metabox', 'wp-seopress'); ?>
					</h2>

					<p><?php esc_html_e('Edit your SEO metadata directly from your page or theme builder.', 'wp-seopress'); ?></p>
					<ul>
						<!-- Universal SEO metabox overview -->
						<?php if (method_exists(seopress_get_service('ToggleOption'), 'getToggleWhiteLabel') && '1' !== seopress_get_service('ToggleOption')->getToggleWhiteLabel()) { ?>
							<li class="description">
								<a class="wrap-yt-embed" href="https://www.youtube.com/watch?v=sf0ocG7vQMM" target="_blank" title="<?php esc_attr_e('Watch the universal SEO metabox overview video - Open in a new window', 'wp-seopress'); ?>">
									<img src="<?php echo esc_url(SEOPRESS_ASSETS_DIR . '/img/yt-universal-metabox.webp'); ?>" alt="<?php esc_attr_e('Universal SEO metabox video thumbnail','wp-seopress'); ?>" width="500" />
								</a>
							</li>
						<?php } ?>

						<!-- Universal SEO metabox for page builers -->
						<li class="seopress-wizard-service-item">
							<label for="universal_seo_metabox">
								<input id="universal_seo_metabox" name="universal_seo_metabox" type="checkbox" class="location-input" <?php if ('1' !== $universal_seo_metabox) {
							echo 'checked="yes"';
						} ?> value="1"/>
								<?php esc_html_e('Yes, please enable the universal SEO metabox!', 'wp-seopress'); ?>
							</label>
						</li>
						<li class="description">
							<?php esc_html_e('You can change this setting at anytime from SEO, Advanced settings page, Appearance tab.', 'wp-seopress'); ?>
						</li>
					</ul>

					<p class="seopress-setup-actions step">
						<button type="submit" class="btn btnPrimary btnNext"
							value="<?php esc_attr_e('Save & Continue', 'wp-seopress'); ?>"
							name="save_step">
							<?php esc_html_e('Save & Continue', 'wp-seopress'); ?>
						</button>

						<?php wp_nonce_field('seopress-setup'); ?>
					</p>
				</form>
			</div>
		</div>
	<?php
	}

	/**
	 * Save step 4.2 settings.
	 */
	public function seopress_setup_universal_save() {
		check_admin_referer('seopress-setup');

		//Get options
		$seopress_advanced_option = get_option('seopress_advanced_option_name');

		//Advanced
		$seopress_advanced_option['seopress_advanced_appearance_universal_metabox_disable']         = isset($_POST['universal_seo_metabox']) ? '' : '1';
		$seopress_advanced_option['seopress_advanced_appearance_universal_metabox']                 = isset($_POST['universal_seo_metabox']) ? '1' : '';

		//Save options
		update_option('seopress_advanced_option_name', $seopress_advanced_option, false);

		if (defined('SEOPRESS_WL_ADMIN_HEADER') && SEOPRESS_WL_ADMIN_HEADER === false) {
			wp_redirect(esc_url_raw($this->get_next_step_link('pro')));
		} else {
			wp_redirect(esc_url_raw($this->get_next_step_link()));
		}

		exit;
	}

	/**
	 *	Init "Step 5.0: PRO Step".
	 */
	public function seopress_setup_pro() {
		$docs = seopress_get_docs_links(); ?>
		<!-- SEOPress Insights -->
		<div class="seopress-setup-content seopress-option">

			<h1 class="seopress-setup-actions step">
				<?php esc_html_e('Our Premium SEO plugins','wp-seopress'); ?>
			</h1>

			<?php $this->setup_wizard_sub_steps(); ?>

			<div class="seopress-tab active">
				<div class="cols">
					<?php if (! is_plugin_active('wp-seopress-pro/seopress-pro.php')) { ?>
						<div class="col col-pro">
							<h2>
								<img alt="<?php esc_html_e('SEOPress PRO logo','wp-seopress'); ?>" width="50" height="50" src="<?php echo esc_url(SEOPRESS_ASSETS_DIR . '/img/logo-seopress-pro.svg'); ?>" />
								<a href="<?php echo esc_url($docs['addons']['pro']); ?>" target="_blank">
									<?php esc_html_e('SEOPress PRO', 'wp-seopress'); ?>
								</a>
							</h2>

							<img alt="" width="100%" style="max-width: 500px" src="<?php echo esc_url(SEOPRESS_ASSETS_DIR . '/img/seopress-pro-featured.png'); ?>" />

							<h3><?php esc_html_e('Premium SEO features to increase your rankings', 'wp-seopress'); ?></h3>

							<p class="seopress-setup-actions step">
								<span class="dashicons dashicons-minus"></span><?php echo wp_kses_post(__('Generate automatically <strong>SEO metadata using AI</strong>.', 'wp-seopress')); ?>
							</p>
							<p class="seopress-setup-actions step">
								<span class="dashicons dashicons-minus"></span><?php echo wp_kses_post(__('Site Audit to find and fix SEO issues.', 'wp-seopress')); ?>
							</p>
							<p class="seopress-setup-actions step">
								<span class="dashicons dashicons-minus"></span><?php echo wp_kses_post(__('Receive <strong>SEO alerts by email / Slack</strong>, twice a day, as long as the problem persists. Act before it’t too late!', 'wp-seopress')); ?>
							</p>
							<p class="seopress-setup-actions step">
								<span class="dashicons dashicons-minus"></span><?php echo wp_kses_post(__('Connect your site with <strong>Google Search Console</strong> to get relevant data: clicks, positions, impressions and CTR.', 'wp-seopress')); ?>
							</p>
							<p class="seopress-setup-actions step">
								<span class="dashicons dashicons-minus"></span><?php echo wp_kses_post(__('Improve your business’s presence in <strong>local search results</strong>.', 'wp-seopress')); ?>
							</p>
							<p class="seopress-setup-actions step">
								<span class="dashicons dashicons-minus"></span><?php echo wp_kses_post(__('Optimize your SEO from your favorite e-commerce plugin: <strong>WooCommerce or Easy Digital Downloads</strong>.', 'wp-seopress')); ?>
							</p>
							<p class="seopress-setup-actions step">
								<span class="dashicons dashicons-minus"></span><?php echo wp_kses_post(__('Add an infinity of <strong>Google structured data (schema)</strong> to your content to improve its visibility in search results.', 'wp-seopress')); ?>
							</p>
							<p class="seopress-setup-actions step">
								<span class="dashicons dashicons-minus"></span><?php echo wp_kses_post(__('Add your custom <strong>breadcrumbs</strong>.', 'wp-seopress')); ?>
							</p>
							<p class="seopress-setup-actions step">
								<span class="dashicons dashicons-minus"></span><?php echo wp_kses_post(__('Configure your <strong>robots.txt and .htaccess files</strong>.', 'wp-seopress')); ?>
							</p>
							<p class="seopress-setup-actions step">
								<span class="dashicons dashicons-minus"></span><?php echo wp_kses_post(__('Manage your <strong>redirections</strong>.', 'wp-seopress')); ?>
							</p>
							<p class="seopress-setup-actions step">
								<span class="dashicons dashicons-minus"></span><?php echo wp_kses_post(__('Observe the evolution of your site via <strong>Google Analytics stats</strong> (or Matomo) directly from your WordPress Dashboard.', 'wp-seopress')); ?>
							</p>

							<p class="seopress-setup-actions step">
								<span class="dashicons dashicons-minus"></span><?php echo wp_kses_post(__('And so many other features to increase your rankings, sales and productivity.', 'wp-seopress')); ?>
							</p>

							<p class="seopress-setup-actions step">
								<a class="btn btnPrimary btnPro"
									href="<?php echo esc_url($docs['addons']['pro']); ?>"
									target="_blank">
									<?php esc_html_e('Get SEOPress PRO', 'wp-seopress'); ?>
								</a>
							</p>
						</div>
					<?php } ?>

					<?php if (! is_plugin_active('wp-seopress-insights/seopress-insights.php') && ! is_multisite()) { ?>
						<div class="col col-insights">
							<h2>
								<img alt="<?php esc_html_e('SEOPress Insights logo','wp-seopress'); ?>" width="50" height="50" src="<?php echo esc_url(SEOPRESS_ASSETS_DIR . '/img/logo-seopress-insights.svg'); ?>" />

								<a href="<?php echo esc_url($docs['addons']['insights']); ?>" target="_blank">
									<?php esc_html_e('SEOPress Insights', 'wp-seopress'); ?>
								</a>
							</h2>

							<img alt="" width="100%" style="max-width: 500px" src="<?php echo esc_url(SEOPRESS_ASSETS_DIR . '/img/seopress-insights-featured.png'); ?>" />

							<h3><?php esc_html_e('Start monitoring your rankings and backlinks directly from your WordPress admin', 'wp-seopress'); ?></h3>

							<p class="seopress-setup-actions step">
								<span class="dashicons dashicons-minus"></span><?php esc_html_e('Track your keyword positions from Google Search results daily.', 'wp-seopress'); ?>
							</p>
							<p class="seopress-setup-actions step">
								<span class="dashicons dashicons-minus"></span><?php esc_html_e('Track your competitors.', 'wp-seopress'); ?>
							</p>
							<p class="seopress-setup-actions step">
								<span class="dashicons dashicons-minus"></span><?php esc_html_e('Monitor and analyse your top 1,000 Backlinks weekly.', 'wp-seopress'); ?>
							</p>
							<p class="seopress-setup-actions step">
								<span class="dashicons dashicons-minus"></span><?php esc_html_e('Export your data to CSV, PDF, Excel.', 'wp-seopress'); ?>
							</p>
							<p class="seopress-setup-actions step">
								<span class="dashicons dashicons-minus"></span><?php esc_html_e('Receive your rankings / backlinks in your inbox / Slack.', 'wp-seopress'); ?>
							</p>

							<p class="seopress-setup-actions step">
								<a class="btn btnPrimary btnInsights"
									href="<?php echo esc_url($docs['addons']['insights']); ?>"
									target="_blank">
									<?php esc_html_e('Get SEOPress Insights', 'wp-seopress'); ?>
								</a>
							</p>
						</div>
					<?php } ?>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Final step.
	 */
	public function seopress_setup_ready() {
		//Remove SEOPress notice
		$seopress_notices                  = get_option('seopress_notices', []);

		$seopress_notices['notice-wizard'] = '1';
		update_option('seopress_notices', $seopress_notices, false);

		$docs = seopress_get_docs_links();

		//Flush permalinks
		flush_rewrite_rules(false); ?>

		<div class="seopress-setup-content seopress-option">

			<?php $this->setup_wizard_sub_steps(); ?>

			<div class="seopress-tab active">

				<div class="final">
					<h1>🎉 <?php esc_html_e('Congratulations!', 'wp-seopress'); ?></h2>
					<h2><?php esc_html_e('Your site is now ready for search engines.', 'wp-seopress'); ?></h2>
					<p><?php esc_html_e('We have automatically applied some SEO optimizations on your site to help you rank higher in search engines.','wp-seopress'); ?></p>
					<ul>
						<li>
							<span class="dashicons dashicons-yes-alt"></span>
							<?php echo wp_kses_post(__('<strong>IndexNow</strong> is enabled to improve your site indexing.', 'wp-seopress')); ?>
						</li>
						<li>
							<span class="dashicons dashicons-yes-alt"></span>
							<?php echo wp_kses_post(__('<strong>Open Graph / Twitter card</strong> is enabled to improve your social media sharing.', 'wp-seopress')); ?>
						</li>
						<li>
							<span class="dashicons dashicons-yes-alt"></span>
							<?php echo wp_kses_post(__('<strong>Content Analysis</strong> is enabled to help you improve your content.', 'wp-seopress')); ?>
						</li>
						<li>
							<span class="dashicons dashicons-yes-alt"></span>
							<?php echo wp_kses_post(__('<strong>XML sitemaps</strong> are enabled to improve Google\'s crawling of your site.', 'wp-seopress')); ?>
						</li>
						<li>
							<span class="dashicons dashicons-yes-alt"></span>
							<?php echo wp_kses_post(__('<strong>Indexing</strong> configured for your post types, taxonomies, authors, dates, search, 404, etc.', 'wp-seopress')); ?>
						</li>
					</ul>
				</div>

				<ul class="seopress-wizard-next-steps">
					<?php do_action('seopress_wizard_setup_ready'); ?>

					<li class="seopress-wizard-next-step-item">
						<div class="seopress-wizard-next-step-description">
							<p class="next-step-heading"><?php esc_html_e('Next step', 'wp-seopress'); ?>
							</p>
							<h3 class="next-step-description"><?php esc_html_e('Create your XML sitemaps', 'wp-seopress'); ?>
							</h3>
							<p class="next-step-extra-info"><?php esc_html_e("Build custom XML sitemaps to improve Google's crawling of your site.", 'wp-seopress'); ?>
							</p>
						</div>
						<div class="seopress-wizard-next-step-action">
							<p class="seopress-setup-actions step">
								<a class="btn btnPrimary"
									href="<?php echo esc_url(admin_url('admin.php?page=seopress-xml-sitemap')); ?>">
									<?php esc_html_e('Configure your XML sitemaps', 'wp-seopress'); ?>
								</a>
							</p>
						</div>
					</li>
					<?php if (!method_exists(seopress_get_service('ToggleOption'), 'getToggleWhiteLabel') || '1' !== seopress_get_service('ToggleOption')->getToggleWhiteLabel()) {
						$current_user = wp_get_current_user();
						$user_email = $current_user->user_email ? esc_html( $current_user->user_email ) : '';
						?>
						<li class="seopress-wizard-next-step-item wrap-seopress-wizard-nl seopress-d-flex">
							<div class="seopress-wizard-next-step-description seopress-wizard-nl">
								<div class="seopress-d-flex seopress-space-between seopress-wizard-nl-items">
									<div>
										<img src="<?php echo esc_url(SEOPRESS_ASSETS_DIR . '/img/cover-seo-routine.jpg'); ?>" alt="" width="106" height="150" />
									</div>
									<div>
										<p class="next-step-heading"><?php esc_html_e('Subscribe for free', 'wp-seopress'); ?></p>
										<h3 class="next-step-description"><?php esc_html_e('SEO Success for WordPress with a Two-Hours-a-Week Routine. Free.', 'wp-seopress'); ?></h3>

										<?php $nl_pros = [
											__('Introducing your 2-hour-a-week SEO plan with SEOPress', 'wp-seopress'),
											__('Week 1: 2 Hours to Check SEO Progress and Issues', 'wp-seopress'),
											__('Week 2: 2 Hours to Create SEO Optimized Content', 'wp-seopress'),
											__('Week 3: 2 Hours to do On-Page Optimizations', 'wp-seopress'),
											__('Week 4: 2 Hours to do Off-Page Optimizations', 'wp-seopress')
										];
										?>
										<ul class="next-step-extra-info">
											<?php foreach($nl_pros as $value) { ?>
												<li>
													<span class="dashicons dashicons-minus"></span>
													<?php echo wp_kses_post($value); ?>
												</li>
											<?php } ?>
										</ul>
									</div>
								</div>
								<div class="col">
									<?php if (!isset($_GET['sub_routine'])) { ?>
										<p class="seopress-setup-actions step">
											<form method="post">
												<input type="text" id="seopress_nl_routine" class="location-input" name="seopress_nl_routine" placeholder="<?php esc_html_e('Enter your email address', 'wp-seopress'); ?>" value="<?php echo esc_html($user_email); ?>" />

												<button id="seopress_nl_routine_submit" type="submit" class="btnPrimary btn" value="<?php esc_attr_e('Subscribe', 'wp-seopress'); ?>" name="save_step">
													<?php esc_html_e('Subscribe', 'wp-seopress'); ?>
												</button>
												<?php wp_nonce_field('seopress-setup'); ?>
											</form>
										</p>

										<?php /* translators: %s URL of our privacy policy  */ echo wp_kses_post(sprintf(__('I accept that SEOPress can store and use my email address in order to send me a newsletter. Read our <a href="%s" target="_blank" title="Open in a new window">privacy policy</a>.', 'wp-seopress'), esc_url('https://www.seopress.org/privacy-policy/')));
									} elseif (isset($_GET['sub_routine']) && $_GET['sub_routine'] ==='1') { ?>
										<p style="font-size: 16px;font-weight: bold;"><?php esc_html_e('Thank you for your subscription!', 'wp-seopress'); ?></p>
									<?php } ?>
								</div>
							</div>
						</li>
					<?php } ?>
				</ul>
			</div>
		</div>
	</div>
<?php
	}

	public function seopress_final_subscribe() {
		check_admin_referer('seopress-setup');

		//Send email to SG if we have user consent
		if (method_exists(seopress_get_service('ToggleOption'), 'getToggleWhiteLabel') && '1' !== seopress_get_service('ToggleOption')->getToggleWhiteLabel()) {

			$endpoint_url = 'https://www.seopress.org/wizard-nl/';
			$endpoint_url_routine = 'https://www.seopress.org/wizard-nl-routine/';

			$email = isset($_POST['seopress_nl']) ? sanitize_text_field(wp_unslash($_POST['seopress_nl'])) : '';
			$email_routine = isset($_POST['seopress_nl_routine']) ? sanitize_text_field(wp_unslash($_POST['seopress_nl_routine'])) : '';

			if (!empty($email)) {

				$body = ['email' => $email, 'lang' => seopress_get_locale()];

				$response = wp_remote_post( $endpoint_url, array(
						'method' => 'POST',
						'body' => $body,
						'timeout' => 5,
						'blocking' => true
					)
				);
			}

			if (!empty($email_routine)) {

				$body = ['email' => $email_routine, 'lang' => seopress_get_locale()];

				$response = wp_remote_post( $endpoint_url_routine, array(
						'method' => 'POST',
						'body' => $body,
						'timeout' => 5,
						'blocking' => true
					)
				);
			}
		}
		if (!empty($email_routine)) {
			wp_safe_redirect(esc_url_raw('admin.php?page=seopress-setup&step=ready&parent&sub_routine=1'));
			exit;
		} else {
			wp_safe_redirect(esc_url_raw('admin.php?page=seopress-setup&step=ready&parent&sub=1'));
			exit;
		}
	}
}
new SEOPRESS_Admin_Setup_Wizard();
