<?php
/**
 * Setup Wizard Class.
 *
 * Takes new users through some basic steps to setup SEOPress.
 *
 * @version     3.5.8
 */
if ( ! defined('ABSPATH')) {
	exit;
}

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
	 * Hook in tabs.
	 */
	public function __construct() {
		if (apply_filters('seopress_enable_setup_wizard', true) && current_user_can(seopress_capability('manage_options', 'Admin_Setup_Wizard'))) {
			add_action('admin_menu', [$this, 'admin_menus']);
			add_action('admin_init', [$this, 'setup_wizard']);

            //Remove notices
            remove_all_actions( 'admin_notices' );
            remove_all_actions( 'all_admin_notices' );

            //Disable Query Monitor
            add_filter('user_has_cap', 'seopress_disable_qm', 10, 3);

            //Load our scripts and CSS
			add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts']);

            $this->seo_title = 'SEOPress';
            if (method_exists(seopress_get_service('ToggleOption'), 'getToggleWhiteLabel') && '1' === seopress_get_service('ToggleOption')->getToggleWhiteLabel()) {
                $this->seo_title = method_exists(seopress_pro_get_service('OptionPro'), 'getWhiteLabelListTitle') && seopress_pro_get_service('OptionPro')->getWhiteLabelListTitle() ? seopress_pro_get_service('OptionPro')->getWhiteLabelListTitle() : 'SEOPress';
            }
		}
	}

	/**
	 * Add admin menus/screens.
	 */
	public function admin_menus() {
		add_dashboard_page('', '', seopress_capability('manage_options', 'menu'), 'seopress-setup', '');
	}

	/**
	 * Register/enqueue scripts and styles for the Setup Wizard.
	 *
	 * Hooked onto 'admin_enqueue_scripts'.
	 */
	public function enqueue_scripts() {
		$prefix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
		wp_enqueue_style('seopress-setup', plugins_url('assets/css/seopress-setup' . $prefix . '.css', dirname(dirname(dirname(__FILE__)))), ['install'], SEOPRESS_VERSION);
		wp_register_script('seopress-migrate-ajax', plugins_url('assets/js/seopress-migrate' . $prefix . '.js', dirname(dirname(dirname(__FILE__)))), ['jquery'], SEOPRESS_VERSION, true);
        wp_enqueue_media();
		wp_register_script('seopress-media-uploader', plugins_url('assets/js/seopress-media-uploader' . $prefix . '.js', dirname(dirname(dirname(__FILE__)))), ['jquery'], SEOPRESS_VERSION, true);

		$seopress_migrate = [
			'seopress_aio_migrate'				=> [
				'seopress_nonce'					=> wp_create_nonce('seopress_aio_migrate_nonce'),
				'seopress_aio_migration'			=> admin_url('admin-ajax.php'),
			],
			'seopress_yoast_migrate'			=> [
				'seopress_nonce'					=> wp_create_nonce('seopress_yoast_migrate_nonce'),
				'seopress_yoast_migration'			=> admin_url('admin-ajax.php'),
			],
			'seopress_seo_framework_migrate'	=> [
				'seopress_nonce'					=> wp_create_nonce('seopress_seo_framework_migrate_nonce'),
				'seopress_seo_framework_migration' 	=> admin_url('admin-ajax.php'),
			],
			'seopress_rk_migrate'				=> [
				'seopress_nonce'					=> wp_create_nonce('seopress_rk_migrate_nonce'),
				'seopress_rk_migration'				=> admin_url('admin-ajax.php'),
			],
			'seopress_squirrly_migrate' 		=> [
				'seopress_nonce' 					=> wp_create_nonce('seopress_squirrly_migrate_nonce'),
				'seopress_squirrly_migration'		=> admin_url('admin-ajax.php'),
			],
			'seopress_seo_ultimate_migrate' 	=> [
				'seopress_nonce' 					=> wp_create_nonce('seopress_seo_ultimate_migrate_nonce'),
				'seopress_seo_ultimate_migration'	=> admin_url('admin-ajax.php'),
			],
			'seopress_wp_meta_seo_migrate'		=> [
				'seopress_nonce' 					=> wp_create_nonce('seopress_meta_seo_migrate_nonce'),
				'seopress_wp_meta_seo_migration'	=> admin_url('admin-ajax.php'),
			],
			'seopress_premium_seo_pack_migrate'	=> [
				'seopress_nonce'						=> wp_create_nonce('seopress_premium_seo_pack_migrate_nonce'),
				'seopress_premium_seo_pack_migration'	=> admin_url('admin-ajax.php'),
			],
			'seopress_wpseo_migrate'			=> [
				'seopress_nonce'						=> wp_create_nonce('seopress_wpseo_migrate_nonce'),
				'seopress_wpseo_migration'				=> admin_url('admin-ajax.php'),
			],
			'seopress_platinum_seo_migrate'			=> [
				'seopress_nonce'						=> wp_create_nonce('seopress_platinum_seo_migrate_nonce'),
				'seopress_platinum_seo_migration'		=> admin_url('admin-ajax.php'),
			],
			'seopress_smart_crawl_migrate'			=> [
				'seopress_nonce'						=> wp_create_nonce('seopress_smart_crawl_migrate_nonce'),
				'seopress_smart_crawl_migration'		=> admin_url('admin-ajax.php'),
			],
			'seopress_seopressor_migrate'			=> [
				'seopress_nonce'						=> wp_create_nonce('seopress_seopressor_migrate_nonce'),
				'seopress_seopressor_migration'			=> admin_url('admin-ajax.php'),
			],
			'seopress_slim_seo_migrate'			=> [
				'seopress_nonce'						=> wp_create_nonce('seopress_slim_seo_migrate_nonce'),
				'seopress_slim_seo_migration'			=> admin_url('admin-ajax.php'),
			],
			'seopress_metadata_csv'				=> [
				'seopress_nonce'				=> wp_create_nonce('seopress_export_csv_metadata_nonce'),
				'seopress_metadata_export'		=> admin_url('admin-ajax.php'),
			],
			'i18n'								=> [
				'migration'						=> __('Migration completed!', 'wp-seopress'),
				'export'						=> __('Export completed!', 'wp-seopress'),
			],
		];
		wp_localize_script('seopress-migrate-ajax', 'seopressAjaxMigrate', $seopress_migrate);
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
            $sub_steps = [
                'pro' => __('PRO','wp-seopress'),
                'insights' => __('Insights','wp-seopress'),
            ];

            if (is_plugin_active('wp-seopress-pro/seopress-pro.php')) {
                unset($sub_steps['pro']);
            }

            if (is_plugin_active('wp-seopress-insights/seopress-insights.php')) {
                unset($sub_steps['insights']);
            }

            if ( ! is_plugin_active('wp-seopress-pro/seopress-pro.php')) {
                $default_steps['pro'] = [
                    'name'    => sprintf(__('Extend %s', 'wp-seopress'), $this->seo_title),
                    'view'    => [$this, 'seopress_setup_pro'],
                    'handler' => '',
                    'sub_steps' => $sub_steps,
                    'parent' => 'pro'
                ];
            }

            if (! is_plugin_active('wp-seopress-insights/seopress-insights.php') && ! is_multisite()) {
                $default_steps['insights'] = [
                    'name'    => sprintf(__('Extend %s', 'wp-seopress'), $this->seo_title),
                    'view'    => [$this, 'seopress_setup_insights'],
                    'handler' => '',
                    'sub_steps' => $sub_steps,
                    'parent' => 'pro'
                ];
            }

            if (is_plugin_active('wp-seopress-pro/seopress-pro.php') && !is_plugin_active('wp-seopress-insights/seopress-insights.php')) {
                $default_steps['insights']['breadcrumbs'] = true;
            }

            if (!is_plugin_active('wp-seopress-pro/seopress-pro.php') && is_plugin_active('wp-seopress-insights/seopress-insights.php')) {
                $default_steps['pro']['breadcrumbs'] = true;
            }

            if (!is_plugin_active('wp-seopress-pro/seopress-pro.php') && !is_plugin_active('wp-seopress-insights/seopress-insights.php')) {
                $default_steps['pro']['breadcrumbs'] = true;
            }
        }

		$default_steps['ready']  = [
                'breadcrumbs' => true,
				'name'    => __('Ready!', 'wp-seopress'),
				'view'    => [$this, 'seopress_setup_ready'],
				'handler' => '',
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

		ob_start();
		$this->setup_wizard_header();
		$this->setup_wizard_steps();
		$this->setup_wizard_content();
		$this->setup_wizard_footer();
		exit;
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
	 * Setup Wizard Header.
	 */
	public function setup_wizard_header() {
		set_current_screen();
        ?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
	<meta name="viewport" content="width=device-width" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php printf(esc_html__('%s &rsaquo; Setup Wizard', 'wp-seopress'),$this->seo_title); ?></title>
    <script type="text/javascript">
        var ajaxurl = '<?php echo esc_url( admin_url( 'admin-ajax.php', 'relative' ) ); ?>';
    </script>
	<?php do_action('admin_print_styles'); ?>
	<?php do_action('admin_enqueue_scripts'); ?>
	<?php wp_print_scripts('seopress-migrate-ajax'); ?>
	<?php wp_print_scripts('seopress-media-uploader'); ?>
	<?php do_action('admin_head'); ?>
</head>

<body
	class="seopress-setup seopress-option wp-core-ui">
	<?php
	}

	/**
	 * Setup Wizard Footer.
	 */
	public function setup_wizard_footer() {
		?>
    </div>
	<div class="seopress-setup-footer">
		<?php if ('welcome' === $this->step) { ?>
		<a class="seopress-setup-footer-links"
			href="<?php echo esc_url(admin_url()); ?>"><?php esc_html_e('Not right now', 'wp-seopress'); ?></a>
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
            'pro' === $this->step ||
            'insights' === $this->step
            ) {
			$skip_link = esc_url($this->get_next_step_link());
			if ('advanced' === $this->step && defined('SEOPRESS_WL_ADMIN_HEADER') && SEOPRESS_WL_ADMIN_HEADER === false) {
				$skip_link = esc_url_raw($this->get_next_step_link('insights'));
			}
			?>
		<a class="seopress-setup-footer-links"
			href="<?php echo $skip_link; ?>"><?php esc_html_e('Skip this step', 'wp-seopress'); ?></a>
		<?php } ?>
		<?php do_action('seopress_setup_footer');
        do_action( 'admin_footer', '' );
		do_action( 'admin_print_footer_scripts' );
        ?>
	</div>
	</div>
</body>

</html>
<?php
	}

	/**
	 * Output the steps.
	 */
	public function setup_wizard_steps() {
		$output_steps      = $this->steps;
		$parent            = $this->parent; ?>
<div id="wpcontent" class="seopress-option">
	<ol class="seopress-setup-steps">
		<?php
				$i = 1;

				if (defined('SEOPRESS_WL_ADMIN_HEADER') && SEOPRESS_WL_ADMIN_HEADER === false) {
					unset($output_steps['insights']);
				}

		foreach ($output_steps as $step_key => $step) {
            if (!isset($step['breadcrumbs'])) {
                continue;
            }

			$is_completed = array_search($this->step, array_keys($this->steps), true) > array_search($step_key, array_keys($this->steps), true);

			if ($step_key === $this->step || $step_key === $this->parent) {
				?>
		<li class="active">
			<div class="icon" data-step="<?php echo $i; ?>"></div>
			<span><?php echo esc_html($step['name']); ?></span>
			<div class="divider"></div>
		</li>
		<?php
			} elseif ($is_completed) {
				?>
		<li class="done">
			<div class="icon" data-step="<?php echo $i; ?>"></div>
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
			<div class="icon" data-step="<?php echo $i; ?>"></div>
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
                <ol class="seopress-setup-sub-steps">
                    <?php
                        if (!empty($output_steps[$current_step]['sub_steps'])) {
                            foreach($output_steps[$current_step]['sub_steps'] as $key => $value) {
                                $class = $key === $current_step ? 'nav-tab-active' : '';
                                ?>
                                <a <?php echo 'class="nav-tab '.$class.'"'; ?> href="<?php echo admin_url('admin.php?page=seopress-setup&step='.$key.'&parent='.$parent); ?>">
                                    <?php echo $value; ?>
                                </a>
                            <?php }
                        }
                    ?>
                </ol>
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
    <div class="seopress-setup-content">
        <h1><?php printf(esc_html__('Welcome to %s!', 'wp-seopress'), $this->seo_title); ?></h1>

        <?php $this->setup_wizard_sub_steps(); ?>

        <div class="seopress-tab active">
            <form method="post">
                <?php wp_nonce_field('seopress-setup'); ?>
                <h2><?php printf(esc_html__('Configure %s with the best settings for your site','wp-seopress'), $this->seo_title); ?></h2>
                <p class="store-setup intro"><?php printf(esc_html__('The following wizard will help you configure %s and get you started quickly.', 'wp-seopress'), $this->seo_title); ?>
                </p>

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
	 * Init "Step 1.2: Import SEO settings".
	 */
	public function seopress_setup_import_settings() {
		?>
        <div class="seopress-setup-content">
            <h1><?php printf(esc_html__('Migrate your SEO metadata to %s!', 'wp-seopress'), $this->seo_title); ?></h1>

            <?php $this->setup_wizard_sub_steps(); ?>

            <div class="seopress-tab active">
	            <form method="post">
		            <?php wp_nonce_field('seopress-setup'); ?>

                    <p class="store-setup intro"><?php esc_html_e('The first step is to import your previous post and term metadata from other plugins to keep your SEO.', 'wp-seopress'); ?></p>

                    <?php
                    $plugins = [
                        'yoast'            => [
                            'slug' => [
                                'wordpress-seo/wp-seo.php',
                                'wordpress-seo-premium/wp-seo-premium.php',
                            ],
                            'name' => 'Yoast SEO',
                        ],
                        'aio'              => [
                            'slug' => [
                                'all-in-one-seo-pack/all_in_one_seo_pack.php',
                            ],
                            'name' => 'All In One SEO',
                        ],
                        'seo-framework'    => [
                            'slug' => [
                                'autodescription/autodescription.php',
                            ],
                            'name' => 'The SEO Framework',
                        ],
                        'rk'               => [
                            'slug' => [
                                'seo-by-rank-math/rank-math.php',
                            ],
                            'name' => 'Rank Math',
                        ],
                        'squirrly'         => [
                            'slug' => [
                                'squirrly-seo/squirrly.php',
                            ],
                            'name' => 'Squirrly SEO',
                        ],
                        'seo-ultimate'     => [
                            'slug' => [
                                'seo-ultimate/seo-ultimate.php',
                            ],
                            'name' => 'SEO Ultimate',
                        ],
                        'wp-meta-seo'      => [
                            'slug' => [
                                'wp-meta-seo/wp-meta-seo.php',
                            ],
                            'name' => 'WP Meta SEO',
                        ],
                        'premium-seo-pack' => [
                            'slug' => [
                                'premium-seo-pack/plugin.php',
                            ],
                            'name' => 'Premium SEO Pack',
                        ],
                        'wpseo'            => [
                            'slug' => [
                                'wpseo/wpseo.php',
                            ],
                            'name' => 'wpSEO',
                        ],
                        'platinum-seo'     => [
                            'slug' => [
                                'platinum-seo-pack/platinum-seo-pack.php',
                            ],
                            'name' => 'Platinum SEO Pack'

                        ],
                        'smart-crawl'      => [
                            'slug' => [
                                'smartcrawl-seo/wpmu-dev-seo.php',
                            ],
                            'name' => 'SmartCrawl',
                        ],
                        'seopressor'       => [
                            'slug' => [
                                'seo-pressor/seo-pressor.php',
                            ],
                            'name' => 'SEOPressor',
                        ],
                        'slim-seo'         => [
                            'slug' => [
                                'slim-seo/slim-seo.php',
                            ],
                            'name' => 'Slim SEO',
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
                            <p><?php _e('One ore more <strong>SEO plugins</strong> are enabled on your site, please deactivate them to avoid any conflicts:', 'wp-seopress'); ?></p>
                            <ul>
                                <?php
                                foreach($active_seo_plugins['name'] as $key => $value) {
                                    ?>
                                        <li><span class="dashicons dashicons-minus"></span><?php echo $value; ?></li>
                                    <?php
                                } ?>
                            </ul>
                        </div>
                        <?php
                    } ?>

                    <p>
                        <select id="select-wizard-import" name="select-wizard-import">
                            <option value="none"><?php _e('Select an option', 'wp-seopress'); ?></option>

                        <?php
                            foreach ($plugins as $plugin => $detail) {
                                ?>
                                    <option
                                    <?php
                                        if (!empty($active_seo_plugins)) {
                                            if (in_array($detail['slug'], $active_seo_plugins['slug'])) {
                                                echo 'selected';
                                            }
                                        }
                                    ?>
                                    value="<?php echo $plugin; ?>-migration-tool"><?php echo $detail['name']; ?></option>
                                <?php
                            } ?>
                        </select>
                    </p>

                    <p class="description"><?php _e('You don\'t have to enable the selected SEO plugin to run the import.', 'wp-seopress'); ?></p>

                    <?php
                        foreach ($plugins as $plugin => $detail) {
                            echo seopress_migration_tool($plugin, $detail['name']);
                        }
                    ?>

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

		$site_sep        = isset($seopress_titles_option['seopress_titles_sep']) ? $seopress_titles_option['seopress_titles_sep'] : null;
		$site_title      = isset($seopress_titles_option['seopress_titles_home_site_title']) ? $seopress_titles_option['seopress_titles_home_site_title'] : null;
		$alt_site_title  = isset($seopress_titles_option['seopress_titles_home_site_title_alt']) ? $seopress_titles_option['seopress_titles_home_site_title_alt'] : null;
		$knowledge_type  = isset($seopress_social_option['seopress_social_knowledge_type']) ? $seopress_social_option['seopress_social_knowledge_type'] : null;
		$knowledge_name  = isset($seopress_social_option['seopress_social_knowledge_name']) ? $seopress_social_option['seopress_social_knowledge_name'] : null;
		$knowledge_img   = isset($seopress_social_option['seopress_social_knowledge_img']) ? $seopress_social_option['seopress_social_knowledge_img'] : null;
		$knowledge_email = isset($seopress_social_option['seopress_social_knowledge_email']) ? $seopress_social_option['seopress_social_knowledge_email'] : $current_user_email;
		$knowledge_nl    = isset($seopress_social_option['seopress_social_knowledge_nl']); ?>

        <div class="seopress-setup-content">
            <h1><?php esc_html_e('Your site', 'wp-seopress'); ?></h1>

            <?php $this->setup_wizard_sub_steps(); ?>

            <div class="seopress-tab active">

                <form method="post">
                    <h2><?php _e('Tell us more about your site','wp-seopress'); ?></h2>
                    <p><?php esc_html_e('To build title tags and knowledge graph for Google, you need to fill out the fields below to configure the general settings.', 'wp-seopress'); ?>
                    </p>

                    <p>
                        <label for="site_sep"><?php esc_html_e('Separator', 'wp-seopress'); ?></label>
                        <input type="text" id="site_sep" class="location-input" name="site_sep"
                            placeholder="<?php esc_html_e('e.g. |', 'wp-seopress'); ?>"
                            required value="<?php echo $site_sep; ?>" />
                    </p>

                    <p class="description">
                        <?php _e('This separator will be used by the dynamic variable <strong>%%sep%%</strong> in your title and meta description templates.', 'wp-seopress'); ?>
                    </p>

                    <p>
                        <label for="site_title"><?php esc_html_e('Home site title', 'wp-seopress'); ?></label>
                        <input type="text" id="site_title" class="location-input" name="site_title"
                            placeholder="<?php esc_html_e('e.g. My super website', 'wp-seopress'); ?>"
                            required value="<?php echo $site_title; ?>" />
                    </p>

                    <p class="description">
                        <?php _e('The site title will be used by the dynamic variable <strong>%%sitetitle%%</strong> in your title and meta description templates.', 'wp-seopress'); ?>
                    </p>

                    <p>
                        <label for="alt_site_title"><?php esc_html_e('Alternative site title', 'wp-seopress'); ?></label>
                        <input type="text" id="alt_site_title" class="location-input" name="alt_site_title" placeholder="<?php esc_html_e('e.g. My alternative site title', 'wp-seopress'); ?>" value="<?php echo $alt_site_title; ?>" />
                    </p>

                    <p class="description"><?php printf(__('The alternate name of the website (for example, if there\'s a commonly recognized acronym or shorter name for your site), if applicable. Make sure the name meets the <a href="%s" target="_blank">content guidelines</a>.<span class="dashicons dashicons-external"></span>','wp-seopress'), $docs['titles']['alt_title']); ?></p>

                    <p>
                        <label for="knowledge_type"><?php esc_html_e('Person or organization', 'wp-seopress'); ?></label>
                        <?php
                        echo '<select id="knowledge_type" name="knowledge_type" data-placeholder="' . esc_attr__('Choose a knowledge type', 'wp-seopress') . '"	class="location-input wc-enhanced-select dropdown">';
                        echo ' <option ';
                        if ('None' == $knowledge_type) {
                            echo 'selected="selected"';
                        }
                        echo ' value="none">' . __('None (will disable this feature)', 'wp-seopress') . '</option>';
                        echo ' <option ';
                        if ('Person' == $knowledge_type) {
                            echo 'selected="selected"';
                        }
                        echo ' value="Person">' . __('Person', 'wp-seopress') . '</option>';
                        echo '<option ';
                        if ('Organization' == $knowledge_type) {
                            echo 'selected="selected"';
                        }
                        echo ' value="Organization">' . __('Organization', 'wp-seopress') . '</option>';
                        echo '</select>'; ?>
                    </p>

                    <p class="description">
                        <?php _e('Choose between <strong>"Organization"</strong> (for companies, associations, organizations), or <strong>"Personal"</strong> for a personal site, to help Google better understand your type of website and generate a Knowledge Graph panel.', 'wp-seopress'); ?>
                    </p>

                    <p>
                        <label for="knowledge_name"><?php esc_html_e('Your name/organization', 'wp-seopress'); ?></label>
                        <input type="text" id="knowledge_name" class="location-input" name="knowledge_name"
                            placeholder="<?php esc_html_e('e.g. My Company Name', 'wp-seopress'); ?>"
                            value="<?php echo $knowledge_name; ?>" />
                    </p>

                    <p>
                        <label for="knowledge_img_meta"><?php esc_html_e('Your photo/organization logo', 'wp-seopress'); ?></label>
                        <input type="text" id="knowledge_img_meta" class="location-input" name="knowledge_img"
                        placeholder="<?php esc_html_e('e.g. https://www.example.com/logo.png', 'wp-seopress'); ?>"
                        value="<?php echo $knowledge_img; ?>" />

                        <input id="knowledge_img_upload" class="btn btnSecondary" type="button" value="<?php _e('Upload an Image', 'wp-seopress'); ?>" />
                    </p>

                    <?php if (method_exists(seopress_get_service('ToggleOption'), 'getToggleWhiteLabel') && '1' !== seopress_get_service('ToggleOption')->getToggleWhiteLabel()) { ?>
                        <p>
                            <label for="knowledge_email"><?php esc_html_e('Your email', 'wp-seopress'); ?></label>
                            <input type="text" id="knowledge_email" class="location-input" name="knowledge_email"
                                placeholder="<?php esc_html_e('e.g. enter', 'wp-seopress'); ?>"
                                value="<?php echo $knowledge_email; ?>" />
                        </p>

                        <p>
                            <label for="knowledge_nl">
                                <input id="knowledge_nl" class="location-input" name="knowledge_nl" type="checkbox" <?php if ('1' == $knowledge_nl) {
                                echo 'checked="yes"';
                            } ?> value="1"/>
                                <?php _e('Be alerted to changes in Google’s algorithm, get product updates, tutorials and ebooks to improve your conversion and traffic.'); ?>
                            </label>
                        </p>
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
	 * Save step 2.0 settings.
	 */
	public function seopress_setup_site_save() {
		check_admin_referer('seopress-setup');

		//Get options
		$seopress_titles_option = get_option('seopress_titles_option_name');
		$seopress_social_option = get_option('seopress_social_option_name');

		//Titles
		$seopress_titles_option['seopress_titles_sep']             = isset($_POST['site_sep']) ? esc_attr(wp_unslash($_POST['site_sep'])) : '';
		$seopress_titles_option['seopress_titles_home_site_title'] = isset($_POST['site_title']) ? sanitize_text_field(wp_unslash($_POST['site_title'])) : '';
		$seopress_titles_option['seopress_titles_home_site_title_alt'] = isset($_POST['alt_site_title']) ? sanitize_text_field(wp_unslash($_POST['alt_site_title'])) : '';

		//Social
		$seopress_social_option['seopress_social_knowledge_type']   = isset($_POST['knowledge_type']) ? esc_attr(wp_unslash($_POST['knowledge_type'])) : '';
		$seopress_social_option['seopress_social_knowledge_name']   = isset($_POST['knowledge_name']) ? sanitize_text_field(wp_unslash($_POST['knowledge_name'])) : '';
		$seopress_social_option['seopress_social_knowledge_img']    = isset($_POST['knowledge_img']) ? sanitize_text_field(wp_unslash($_POST['knowledge_img'])) : '';
		$seopress_social_option['seopress_social_knowledge_email']  = isset($_POST['knowledge_email']) ? sanitize_text_field(wp_unslash($_POST['knowledge_email'])) : '';
		$seopress_social_option['seopress_social_knowledge_nl']     = isset($_POST['knowledge_nl']) ? esc_attr(wp_unslash($_POST['knowledge_nl'])) : null;

		//Save options
		update_option('seopress_titles_option_name', $seopress_titles_option, false);
		update_option('seopress_social_option_name', $seopress_social_option, false);

        //Send email to SG if we have user consent
        if (method_exists(seopress_get_service('ToggleOption'), 'getToggleWhiteLabel') && '1' !== seopress_get_service('ToggleOption')->getToggleWhiteLabel()) {
            if (isset($seopress_social_option['seopress_social_knowledge_email']) && $seopress_social_option['seopress_social_knowledge_nl'] === '1') {
                $endpoint_url = 'https://www.seopress.org/wizard-nl/';
                $body = ['email' => $seopress_social_option['seopress_social_knowledge_email'], 'lang' => seopress_get_locale()];

                $response = wp_remote_post( $endpoint_url, array(
                        'method' => 'POST',
                        'body' => $body,
                        'timeout' => 5,
                        'blocking' => true
                    )
                );
            }
        }

		wp_safe_redirect(esc_url_raw($this->get_next_step_link()));
		exit;
	}

	/**
	 * Init "Step 2.1: Your site - Social accounts".
	 */
	public function seopress_setup_social_accounts() {
		$seopress_social_option = get_option('seopress_social_option_name');

		$knowledge_fb    = isset($seopress_social_option['seopress_social_accounts_facebook']) ? $seopress_social_option['seopress_social_accounts_facebook'] : null;
		$knowledge_tw    = isset($seopress_social_option['seopress_social_accounts_twitter']) ? $seopress_social_option['seopress_social_accounts_twitter'] : null;
		$knowledge_pin   = isset($seopress_social_option['seopress_social_accounts_pinterest']) ? $seopress_social_option['seopress_social_accounts_pinterest'] : null;
		$knowledge_insta = isset($seopress_social_option['seopress_social_accounts_instagram']) ? $seopress_social_option['seopress_social_accounts_instagram'] : null;
		$knowledge_yt    = isset($seopress_social_option['seopress_social_accounts_youtube']) ? $seopress_social_option['seopress_social_accounts_youtube'] : null;
		$knowledge_li    = isset($seopress_social_option['seopress_social_accounts_linkedin']) ? $seopress_social_option['seopress_social_accounts_linkedin'] : null;
		$knowledge_extra = isset($seopress_social_option['seopress_social_accounts_extra']) ? $seopress_social_option['seopress_social_accounts_extra'] : null;
        ?>

        <div class="seopress-setup-content">
            <h1><?php esc_html_e('Your site', 'wp-seopress'); ?></h1>

            <?php $this->setup_wizard_sub_steps(); ?>

            <div class="seopress-tab active">

                <form method="post">
                    <h2><?php _e('Link your site to your social networks','wp-seopress'); ?></h2>

                    <p><?php esc_html_e('Fill in your social accounts for search engines.', 'wp-seopress'); ?>
                    </p>

                    <p>
                        <label for="knowledge_fb"><?php esc_html_e('Facebook page URL', 'wp-seopress'); ?></label>
                        <input type="text" id="knowledge_fb" class="location-input" name="knowledge_fb"
                            placeholder="<?php esc_html_e('e.g. https://facebook.com/my-page-url', 'wp-seopress'); ?>"
                            value="<?php echo $knowledge_fb; ?>" />
                    </p>

                    <p>
                        <label for="knowledge_tw"><?php esc_html_e('Twitter Username', 'wp-seopress'); ?></label>
                        <input type="text" id="knowledge_tw" class="location-input" name="knowledge_tw"
                            placeholder="<?php esc_html_e('e.g. @my_twitter_account', 'wp-seopress'); ?>"
                            value="<?php echo $knowledge_tw; ?>" />
                    </p>

                    <p>
                        <label for="knowledge_pin"><?php esc_html_e('Pinterest URL', 'wp-seopress'); ?></label>
                        <input type="text" id="knowledge_pin" class="location-input" name="knowledge_pin"
                            placeholder="<?php esc_html_e('e.g. https://pinterest.com/my-page-url/', 'wp-seopress'); ?>"
                            value="<?php echo $knowledge_pin; ?>" />
                    </p>

                    <p>
                        <label for="knowledge_insta"><?php esc_html_e('Instagram URL', 'wp-seopress'); ?></label>
                        <input type="text" id="knowledge_insta" class="location-input" name="knowledge_insta"
                            placeholder="<?php esc_html_e('e.g. https://www.instagram.com/my-page-url/', 'wp-seopress'); ?>"
                            value="<?php echo $knowledge_insta; ?>" />
                    </p>

                    <p>
                        <label for="knowledge_yt"><?php esc_html_e('YouTube URL', 'wp-seopress'); ?></label>
                        <input type="text" id="knowledge_yt" class="location-input" name="knowledge_yt"
                            placeholder="<?php esc_html_e('e.g. https://www.youtube.com/my-channel-url', 'wp-seopress'); ?>"
                            value="<?php echo $knowledge_yt; ?>" />
                    </p>

                    <p>
                        <label for="knowledge_li"><?php esc_html_e('LinkedIn URL', 'wp-seopress'); ?></label>
                        <input type="text" id="knowledge_li" class="location-input" name="knowledge_li"
                            placeholder="<?php esc_html_e('e.g. http://linkedin.com/company/my-company-url/', 'wp-seopress'); ?>"
                            value="<?php echo $knowledge_li; ?>" />
                    </p>

                    <p>
                        <label for="knowledge_extra"><?php esc_html_e('Additional accounts', 'wp-seopress'); ?></label>
                        <textarea id="knowledge_extra" class="location-input" name="knowledge_extra" rows="8"
                        placeholder="<?php esc_html_e('Enter 1 URL per line (e.g. https://example.com/my-profile)', 'wp-seopress'); ?>"
                        aria-label="<?php _e('Enter 1 URL per line (e.g. https://example.com/my-profile)', 'wp-seopress'); ?>"><?php esc_html_e($knowledge_extra); ?></textarea>
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

        <div class="seopress-setup-content">
            <h1><?php esc_html_e('Indexing', 'wp-seopress'); ?></h1>

            <?php $this->setup_wizard_sub_steps(); ?>

            <div class="seopress-tab active">

                <form method="post" class="seopress-wizard-indexing-form">
                    <?php
                    $postTypes = seopress_get_service('WordPressData')->getPostTypes();
                    if ( ! empty($postTypes)) { ?>
                    <h2>
                        <?php _e('For which single post types, should indexing be disabled?', 'wp-seopress'); ?>
                    </h2>

                    <p><?php _e('Custom post types are a content type in WordPress. By default, <strong>Post</strong> and <strong>Page</strong> are the <strong>default post types</strong>.','wp-seopress'); ?></p>
                    <p><?php _e('You can create your own type of content like "product" or "business": these are <strong>custom post types</strong>.','wp-seopress'); ?></p>

                    <ul>
                        <?php
                                        //Post Types
                                        foreach ($postTypes as $seopress_cpt_key => $seopress_cpt_value) {
                                            $seopress_titles_single_titles = isset($seopress_titles_option['seopress_titles_single_titles'][$seopress_cpt_key]['noindex']); ?>

                        <h3><?php echo $seopress_cpt_value->labels->name; ?>
                            <em><small>[<?php echo $seopress_cpt_value->name; ?>]</small></em>
                        </h3>

                        <li class="seopress-wizard-service-item checkbox">
                            <label
                                for="seopress_titles_single_cpt_noindex[<?php echo $seopress_cpt_key; ?>]">
                                <input
                                    id="seopress_titles_single_cpt_noindex[<?php echo $seopress_cpt_key; ?>]"
                                    name="seopress_titles_option_name[seopress_titles_single_titles][<?php echo $seopress_cpt_key; ?>][noindex]"
                                    type="checkbox" <?php if ('1' == $seopress_titles_single_titles) {
                                                echo 'checked="yes"';
                                            } ?>
                                value="1"/>
                                <?php _e('Do not display this single post type in search engine results <strong>(noindex)</strong>', 'wp-seopress'); ?>
                            </label>
                        </li>
                        <?php
                                        }
                                    ?>
                    </ul>
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

        <div class="seopress-setup-content">
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
                                <?php _e('For which post type archives, should indexing be disabled?', 'wp-seopress'); ?>
                            </h2>

                            <p><?php _e('<strong>Archive pages</strong> are automatically generated by WordPress. They group specific content such as your latest articles, a product category or your content by author or date.', 'wp-seopress'); ?></p>
                            <p><?php _e('Below the list of your <strong>post type archives</strong>:', 'wp-seopress'); ?></p>

                            <ul>
                            <?php
                                foreach ($cpt as $seopress_cpt_key => $seopress_cpt_value) {
                                        $seopress_titles_archive_titles = isset($seopress_titles_option['seopress_titles_archive_titles'][$seopress_cpt_key]['noindex']); ?>
                                        <h3><?php echo $seopress_cpt_value->labels->name; ?>
                                            <em><small>[<?php echo $seopress_cpt_value->name; ?>]</small></em>
                                        </h3>

                                        <li class="seopress-wizard-service-item checkbox">
                                            <label
                                                for="seopress_titles_archive_cpt_noindex[<?php echo $seopress_cpt_key; ?>]">
                                                <input
                                                    id="seopress_titles_archive_cpt_noindex[<?php echo $seopress_cpt_key; ?>]"
                                                    name="seopress_titles_option_name[seopress_titles_archive_titles][<?php echo $seopress_cpt_key; ?>][noindex]"
                                                    type="checkbox" <?php if ('1' == $seopress_titles_archive_titles) {
                                                                    echo 'checked="yes"';
                                                                } ?>
                                                value="1"/>
                                                <?php _e('Do not display this post type archive in search engine results <strong>(noindex)</strong>', 'wp-seopress'); ?>
                                            </label>
                                        </li>
                                    <?php
                                    }
                                }
                            if (!empty($cpt)) { ?>
                            </ul>
                        <?php }

                        if (empty($cpt)) { ?>
                        <p><?php _e('You don‘t have any post type archives, you can continue to the next step.','wp-seopress'); ?></p>
                        <?php }
                    } ?>

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

        <div class="seopress-setup-content">
            <h1><?php esc_html_e('Indexing', 'wp-seopress'); ?></h1>

            <?php $this->setup_wizard_sub_steps(); ?>

            <div class="seopress-tab active">

                <form method="post" class="seopress-wizard-indexing-form">
                    <?php

                    $taxonomies = seopress_get_service('WordPressData')->getTaxonomies();

                    if ( ! empty($taxonomies)) { ?>
                    <h2>
                        <?php _e('For which taxonomy archives, should indexing be disabled?', 'wp-seopress'); ?>
                    </h2>

                    <p><?php _e('<strong>Taxonomies</strong> are the method of classifying content and data in WordPress. When you use a taxonomy you’re grouping similar things together. The taxonomy refers to the sum of those groups.','wp-seopress'); ?></p>
                    <p><?php _e('<strong>Categories</strong> and <strong>Tags</strong> are the default taxonomies. You can add your own taxonomies like "product categories": these are called <strong>custom taxonomies</strong>.','wp-seopress'); ?></p>

                    <ul>
                        <?php
                        //Archives
                        foreach ($taxonomies as $seopress_tax_key => $seopress_tax_value) {
                            $seopress_titles_tax_titles = isset($seopress_titles_option['seopress_titles_tax_titles'][$seopress_tax_key]['noindex']); ?>
                        <h3><?php echo $seopress_tax_value->labels->name; ?>
                            <em><small>[<?php echo $seopress_tax_value->name; ?>]</small></em>
                        </h3>

                        <li class="seopress-wizard-service-item checkbox">
                            <label
                                for="seopress_titles_tax_noindex[<?php echo $seopress_tax_key; ?>]">
                                <input
                                    id="seopress_titles_tax_noindex[<?php echo $seopress_tax_key; ?>]"
                                    name="seopress_titles_option_name[seopress_titles_tax_titles][<?php echo $seopress_tax_key; ?>][noindex]"
                                    type="checkbox" <?php if ('1' == $seopress_titles_tax_titles) {
                                echo 'checked="yes"';
                            } ?>
                                value="1"/>
                                <?php _e('Do not display this taxonomy archive in search engine results <strong>(noindex)</strong>', 'wp-seopress'); ?>
                                <?php if ($seopress_tax_key =='post_tag') { ?>
                                    <div class="seopress-notice is-warning is-inline">
                                        <p>
                                            <?php _e('We do not recommend indexing <strong>tags</strong> which are, in the vast majority of cases, a source of duplicate content.', 'wp-seopress'); ?>
                                        </p>
                                    </div>
                                <?php } ?>
                            </label>
                        </li>
                        <?php
                        }
                        ?>
                    </ul>

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
		$seopress_titles_option           = get_option('seopress_titles_option_name');
		$author_noindex                   = isset($seopress_titles_option['seopress_titles_archives_author_noindex']);
		$seopress_advanced_option         = get_option('seopress_advanced_option_name');
		$attachments_file                 = isset($seopress_advanced_option['seopress_advanced_advanced_attachments_file']);
		$category_url                     = isset($seopress_advanced_option['seopress_advanced_advanced_category_url']);
		$product_category_url             = isset($seopress_advanced_option['seopress_advanced_advanced_product_cat_url']); ?>

        <div class="seopress-setup-content">

            <h1><?php esc_html_e('Advanced options', 'wp-seopress'); ?></h1>

            <?php $this->setup_wizard_sub_steps(); ?>

            <div class="seopress-tab active">
                <h2><?php _e('Almost done!','wp-seopress'); ?></h2>

                <p><?php esc_html_e('Final step before being ready to rank on search engines.', 'wp-seopress'); ?></p>

                <form method="post">
                    <ul>
                        <!-- Noindex on author archives -->
                        <li class="seopress-wizard-service-item checkbox">
                            <label for="author_noindex">
                                <input id="author_noindex" class="location-input" name="author_noindex" type="checkbox" <?php if ('1' == $author_noindex) {
                            echo 'checked="yes"';
                        } ?> value="1"/>
                                <?php _e('Do not display author archives in search engine results <strong>(noindex)</strong>', 'wp-seopress'); ?>
                            </label>
                        </li>
                        <li class="description">
                            <?php _e('You only have one author on your site? Check this option to avoid duplicate content.', 'wp-seopress'); ?>
                        </li>

                        <!-- Redirect attachment pages to URL -->
                        <li class="seopress-wizard-service-item checkbox">
                            <label for="attachments_file">
                                <input id="attachments_file" class="location-input" name="attachments_file" type="checkbox" <?php if ('1' == $attachments_file) {
                            echo 'checked="yes"';
                        } ?> value="1"/>
                                <?php _e('Redirect attachment pages to their file URL (https://www.example.com/my-image-file.jpg)', 'wp-seopress'); ?>
                            </label>
                        </li>
                        <li class="description">
                            <?php printf(__('By default, %s redirects your Attachment pages to the parent post. Optimize this by redirecting the user directly to the URL of the media file.', 'wp-seopress'), $this->seo_title); ?>
                        </li>

                        <!-- Remove /category/ in URLs -->
                        <li class="seopress-wizard-service-item checkbox">
                            <label for="category_url">
                                <input id="category_url" name="category_url" type="checkbox" class="location-input" <?php if ('1' == $category_url) {
                            echo 'checked="yes"';
                        } ?> value="1"/>
                                <?php
                                    $category_base = '/category/';
                        if (get_option('category_base')) {
                            $category_base = '/' . get_option('category_base');
                        }

                        printf(__('Remove <strong>%s</strong> in your permalinks', 'wp-seopress'), $category_base); ?>
                            </label>
                        </li>
                        <li class="description">
                            <?php printf(__('Shorten your URLs by removing %s and improve your SEO.', 'wp-seopress'), $category_base); ?>
                        </li>

                        <?php if (is_plugin_active('woocommerce/woocommerce.php')) { ?>
                            <!-- Remove /product-category/ in URLs -->
                            <li class="seopress-wizard-service-item checkbox">
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

                            printf(__('Remove <strong>%s</strong> in your permalinks', 'wp-seopress'), $category_base); ?>
                                </label>
                            </li>
                            <li class="description">
                                <?php printf(__('Shorten your URLs by removing %s and improve your SEO.', 'wp-seopress'), $category_base); ?>
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
		$seopress_titles_option   = get_option('seopress_titles_option_name');
		$seopress_advanced_option = get_option('seopress_advanced_option_name');

		//Author indexing
		$seopress_titles_option['seopress_titles_archives_author_noindex'] = isset($_POST['author_noindex']) ? esc_attr(wp_unslash($_POST['author_noindex'])) : null;

		//Advanced
		$seopress_advanced_option['seopress_advanced_advanced_attachments_file']    = isset($_POST['attachments_file']) ? esc_attr(wp_unslash($_POST['attachments_file'])) : null;
		$seopress_advanced_option['seopress_advanced_advanced_category_url']        = isset($_POST['category_url']) ? esc_attr(wp_unslash($_POST['category_url'])) : null;

        if (is_plugin_active('woocommerce/woocommerce.php')) {
		    $seopress_advanced_option['seopress_advanced_advanced_product_cat_url']        = isset($_POST['product_category_url']) ? esc_attr(wp_unslash($_POST['product_category_url'])) : null;
        }

		//Save options
		update_option('seopress_titles_option_name', $seopress_titles_option, false);
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

        <div class="seopress-setup-content">

            <h1><?php esc_html_e('Advanced options', 'wp-seopress'); ?></h1>

            <?php $this->setup_wizard_sub_steps(); ?>

            <div class="seopress-tab active">

                <form method="post">
                    <h2>
                        <?php _e('Improve your workflow with the Universal SEO metabox', 'wp-seopress'); ?>
                    </h2>

                    <p><?php _e('Edit your SEO metadata directly from your page or theme builder.', 'wp-seopress'); ?></p>
                    <ul>
                        <!-- Universal SEO metabox overview -->
                        <?php if (method_exists(seopress_get_service('ToggleOption'), 'getToggleWhiteLabel') && '1' !== seopress_get_service('ToggleOption')->getToggleWhiteLabel()) { ?>
                            <li class="description">
                                <?php echo wp_oembed_get('https://www.youtube.com/watch?v=sf0ocG7vQMM'); ?>
                            </li>
                        <?php } ?>

                        <!-- Universal SEO metabox for page builers -->
                        <li class="seopress-wizard-service-item checkbox">
                            <label for="universal_seo_metabox">
                                <input id="universal_seo_metabox" name="universal_seo_metabox" type="checkbox" class="location-input" <?php if ('1' !== $universal_seo_metabox) {
                            echo 'checked="yes"';
                        } ?> value="1"/>
                                <?php _e('Yes, please enable the universal SEO metabox!', 'wp-seopress'); ?>
                            </label>
                        </li>
                        <li class="description">
                            <?php _e('You can change this setting at anytime from SEO, Advanced settings page, Appearance tab.', 'wp-seopress'); ?>
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
			wp_redirect(esc_url_raw($this->get_next_step_link('insights')));
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
        <div class="seopress-setup-content">

            <h1 class="seopress-setup-actions step">
                <?php _e('SEOPress PRO','wp-seopress'); ?>
            </h1>

            <?php $this->setup_wizard_sub_steps(); ?>

            <div class="seopress-tab active">
                <h2><?php _e('Premium SEO features to increase your rankings', 'wp-seopress'); ?></h2>

                <p class="seopress-setup-actions step">
                    <span class="dashicons dashicons-minus"></span><?php _e('Generate automatically <strong>SEO metadata using AI</strong>.', 'wp-seopress'); ?>
                </p>
                <p class="seopress-setup-actions step">
                    <span class="dashicons dashicons-minus"></span><?php _e('Improve your business\'s presence in <strong>local search results</strong>.', 'wp-seopress'); ?>
                </p>
                <p class="seopress-setup-actions step">
                    <span class="dashicons dashicons-minus"></span><?php _e('Optimize your SEO from your favorite e-commerce plugin: <strong>WooCommerce or Easy Digital Downloads</strong>.', 'wp-seopress'); ?>
                </p>
                <p class="seopress-setup-actions step">
                    <span class="dashicons dashicons-minus"></span><?php _e('Add an infinity of <strong>Google structured data (schema)</strong> to your content to improve its visibility in search results.', 'wp-seopress'); ?>
                </p>
                <p class="seopress-setup-actions step">
                    <span class="dashicons dashicons-minus"></span><?php _e('Add your custom <strong>breadcrumbs</strong>.', 'wp-seopress'); ?>
                </p>
                <p class="seopress-setup-actions step">
                    <span class="dashicons dashicons-minus"></span><?php _e('Configure your <strong>robots.txt and .htaccess files</strong>.', 'wp-seopress'); ?>
                </p>
                <p class="seopress-setup-actions step">
                    <span class="dashicons dashicons-minus"></span><?php _e('Manage your <strong>redirections</strong>.', 'wp-seopress'); ?>
                </p>
                <p class="seopress-setup-actions step">
                    <span class="dashicons dashicons-minus"></span><?php _e('Observe the evolution of your site via <strong>Google Analytics stats</strong> (or Matomo) directly from your WordPress Dashboard.', 'wp-seopress'); ?>
                </p>

                <p class="seopress-setup-actions step">
                    <span class="dashicons dashicons-minus"></span><?php _e('And so many other features to increase your rankings, sales and productivity.', 'wp-seopress'); ?>
                </p>

                <img alt="" width="100%" style="max-width: 750px" src="https://www.seopress.org/wp-content/uploads/2021/08/hero-seopress-pro.png" />

                <p class="seopress-setup-actions step">
                    <a class="btn btnPrimary"
                        href="<?php echo $docs['addons']['pro']; ?>"
                        target="_blank">
                        <?php _e('Get SEOPress PRO', 'wp-seopress'); ?>
                    </a>
                </p>
            </div>
        </div>
        <?php
	}

	/**
	 *	Init "Step 5.1: Insights Step".
	 */
	public function seopress_setup_insights() {
		$docs = seopress_get_docs_links(); ?>
        <!-- SEOPress Insights -->
        <div class="seopress-setup-content">

            <h1 class="seopress-setup-actions step">
                <?php _e('SEOPress Insights','wp-seopress'); ?>
            </h1>

            <?php $this->setup_wizard_sub_steps(); ?>

            <div class="seopress-tab active">
                <h2><?php _e('Start monitoring your rankings and backlinks directly from your WordPress admin', 'wp-seopress'); ?></h2>

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

                <?php echo wp_oembed_get('https://www.youtube.com/watch?v=p6v9Jd5lRIU'); ?>

                <p class="seopress-setup-actions step">
                    <a class="btn btnPrimary"
                        href="<?php echo $docs['addons']['insights']; ?>"
                        target="_blank">
                        <?php _e('Get SEOPress Insights', 'wp-seopress'); ?>
                    </a>
                </p>
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

        <div class="seopress-setup-content">

            <h1><?php esc_html_e('Your site is now ready for search engines!', 'wp-seopress'); ?></h1>

            <?php $this->setup_wizard_sub_steps(); ?>

            <div class="seopress-tab active">

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
                                    href="<?php echo admin_url('admin.php?page=seopress-xml-sitemap'); ?>">
                                    <?php esc_html_e('Configure your XML sitemaps', 'wp-seopress'); ?>
                                </a>
                            </p>
                        </div>
                    </li>
                    <?php if (!method_exists(seopress_get_service('ToggleOption'), 'getToggleWhiteLabel') || '1' !== seopress_get_service('ToggleOption')->getToggleWhiteLabel()) {
                        $current_user = wp_get_current_user();
                        $user_email = $current_user->user_email ? esc_html( $current_user->user_email ) : '';
                        ?>
                        <li class="seopress-wizard-next-step-item">
                            <div class="seopress-wizard-next-step-description">
                                <p class="next-step-heading"><?php esc_html_e('Newsletter', 'wp-seopress'); ?>
                                </p>
                                <h3 class="next-step-description"><?php esc_html_e('SEO news in your inbox. Free.', 'wp-seopress'); ?>
                                </h3>
                                <ul class="next-step-extra-info">
                                    <li><span class="dashicons dashicons-minus"></span><?php esc_html_e('Be alerted to changes in Google’s algorithm', 'wp-seopress'); ?></li>
                                    <li><span class="dashicons dashicons-minus"></span><?php esc_html_e('The latest innovations of our products', 'wp-seopress'); ?></li>
                                    <li><span class="dashicons dashicons-minus"></span><?php esc_html_e('Improve your conversions and traffic with our new blog posts', 'wp-seopress'); ?></li>
                                </ul>
                            </div>
                            <div class="seopress-wizard-next-step-action">
                                <p class="seopress-setup-actions step">
                                    <a class="btn btnSecondary" target="_blank"
                                        href="<?php echo $docs['subscribe']; ?>&email=<?php echo $user_email; ?>">
                                        <?php esc_html_e('Subscribe', 'wp-seopress'); ?>
                                    </a>
                                </p>
                            </div>
                        </li>
                    <?php } ?>

                    <li class="seopress-wizard-additional-steps">
                        <div class="seopress-wizard-next-step-description">
                            <p class="next-step-heading"><?php esc_html_e('You can also:', 'wp-seopress'); ?>
                            </p>
                        </div>
                        <div class="seopress-wizard-next-step-action step">
                            <p class="seopress-setup-actions step">
                                <a class="btn btnTertiary"
                                    href="<?php echo esc_url(admin_url()); ?>">
                                    <?php esc_html_e('Visit Dashboard', 'wp-seopress'); ?>
                                </a>
                                <a class="btn btnTertiary"
                                    href="<?php echo esc_url(admin_url('admin.php?page=seopress-option')); ?>">
                                    <?php esc_html_e('Review Settings', 'wp-seopress'); ?>
                                </a>
                                <?php if (!method_exists(seopress_get_service('ToggleOption'), 'getToggleWhiteLabel') || '1' !== seopress_get_service('ToggleOption')->getToggleWhiteLabel()) { ?>
                                    <a class="btn btnTertiary"
                                        href="<?php echo $docs['support']; ?>"
                                        target="_blank">
                                        <?php esc_html_e('Knowledge base', 'wp-seopress'); ?>
                                    </a>
                                <?php } ?>
                            </p>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
<?php
	}
}

new SEOPRESS_Admin_Setup_Wizard();
