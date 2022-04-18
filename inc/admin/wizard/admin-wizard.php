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
				'seopress_nonce'					      => wp_create_nonce('seopress_aio_migrate_nonce'),
				'seopress_aio_migration'			=> admin_url('admin-ajax.php'),
			],
			'seopress_yoast_migrate'			=> [
				'seopress_nonce'					        => wp_create_nonce('seopress_yoast_migrate_nonce'),
				'seopress_yoast_migration'			=> admin_url('admin-ajax.php'),
			],
			'seopress_seo_framework_migrate'	=> [
				'seopress_nonce'					               => wp_create_nonce('seopress_seo_framework_migrate_nonce'),
				'seopress_seo_framework_migration' 	=> admin_url('admin-ajax.php'),
			],
			'seopress_rk_migrate'				=> [
				'seopress_nonce'					      => wp_create_nonce('seopress_rk_migrate_nonce'),
				'seopress_rk_migration'				=> admin_url('admin-ajax.php'),
			],
			'seopress_squirrly_migrate' 		=> [
				'seopress_nonce' 					         => wp_create_nonce('seopress_squirrly_migrate_nonce'),
				'seopress_squirrly_migration'		=> admin_url('admin-ajax.php'),
			],
			'seopress_seo_ultimate_migrate' 	=> [
				'seopress_nonce' 					            => wp_create_nonce('seopress_seo_ultimate_migrate_nonce'),
				'seopress_seo_ultimate_migration'	=> admin_url('admin-ajax.php'),
			],
			'seopress_wp_meta_seo_migrate'		=> [
				'seopress_nonce' 					           => wp_create_nonce('seopress_meta_seo_migrate_nonce'),
				'seopress_wp_meta_seo_migration'	=> admin_url('admin-ajax.php'),
			],
			'seopress_premium_seo_pack_migrate'	=> [
				'seopress_nonce'						                => wp_create_nonce('seopress_premium_seo_pack_migrate_nonce'),
				'seopress_premium_seo_pack_migration'	=> admin_url('admin-ajax.php'),
			],
			'seopress_wpseo_migrate'			=> [
				'seopress_nonce'						        => wp_create_nonce('seopress_wpseo_migrate_nonce'),
				'seopress_wpseo_migration'				=> admin_url('admin-ajax.php'),
			],
			'seopress_platinum_seo_migrate'			=> [
				'seopress_nonce'						               => wp_create_nonce('seopress_platinum_seo_migrate_nonce'),
				'seopress_platinum_seo_migration'				=> admin_url('admin-ajax.php'),
			],
			'seopress_smart_crawl_migrate'			=> [
				'seopress_nonce'						              => wp_create_nonce('seopress_smart_crawl_migrate_nonce'),
				'seopress_smart_crawl_migration'				=> admin_url('admin-ajax.php'),
			],
			'seopress_seopressor_migrate'			=> [
				'seopress_nonce'						             => wp_create_nonce('seopress_seopressor_migrate_nonce'),
				'seopress_seopressor_migration'				=> admin_url('admin-ajax.php'),
			],
			'seopress_metadata_csv'				=> [
				'seopress_nonce'					        => wp_create_nonce('seopress_export_csv_metadata_nonce'),
				'seopress_metadata_export'			=> admin_url('admin-ajax.php'),
			],
			'i18n'								=> [
				'migration'							=> __('Migration completed!', 'wp-seopress'),
				'export'							   => __('Export completed!', 'wp-seopress'),
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

        $seo_title = 'SEOPress';
        if (function_exists('seopress_get_toggle_white_label_option') && '1' == seopress_get_toggle_white_label_option()) {
            $seo_title = seopress_white_label_plugin_list_title_option() ? seopress_white_label_plugin_list_title_option() : 'SEOPress';
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

        if (function_exists('seopress_get_toggle_white_label_option') && '1' !== seopress_get_toggle_white_label_option()) {
            if (
                (
                    ! is_plugin_active('wp-seopress-insights/seopress-insights.php') && ! is_multisite()
                )
                ||
                (
                    ! is_plugin_active('wp-seopress-pro/seopress-pro.php')
                )
            ) {

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
                        'name'    => sprintf(__('Extend %s', 'wp-seopress'), $seo_title),
                        'view'    => [$this, 'seopress_setup_pro'],
                        'handler' => '',
                        'sub_steps' => $sub_steps,
                        'parent' => 'pro'
                    ];
                }

                if (! is_plugin_active('wp-seopress-insights/seopress-insights.php') && ! is_multisite()) {
                    $default_steps['insights'] = [
                        'name'    => sprintf(__('Extend %s', 'wp-seopress'), $seo_title),
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
		set_current_screen(); ?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
	<meta name="viewport" content="width=device-width" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php esc_html_e('SEOPress &rsaquo; Setup Wizard', 'wp-seopress'); ?>
	</title>
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
        $seo_title = 'SEOPress';
        if (function_exists('seopress_get_toggle_white_label_option') && '1' == seopress_get_toggle_white_label_option()) {
            $seo_title = seopress_white_label_plugin_list_title_option() ? seopress_white_label_plugin_list_title_option() : 'SEOPress';
        }
		?>
    <div class="seopress-setup-content">
        <h1><?php printf(esc_html__('Welcome to %s!', 'wp-seopress'), $seo_title); ?><hr role="presentation"></h1>

        <?php $this->setup_wizard_sub_steps(); ?>

        <div class="seopress-tab active">
            <form method="post">
                <?php wp_nonce_field('seopress-setup'); ?>
                <h2><?php printf(esc_html__('Configure %s with the best settings for your site','wp-seopress'), $seo_title); ?></h2>
                <p class="store-setup intro"><?php printf(esc_html__('The following wizard will help you configure %s and get you started quickly.', 'wp-seopress'), $seo_title); ?>
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
		$seo_title = 'SEOPress';
		if (function_exists('seopress_get_toggle_white_label_option') && '1' == seopress_get_toggle_white_label_option()) {
			$seo_title = seopress_white_label_plugin_list_title_option() ? seopress_white_label_plugin_list_title_option() : 'SEOPress';
		} ?>
        <div class="seopress-setup-content">
            <h1><?php printf(esc_html__('Migrate your SEO metadata to %s!', 'wp-seopress'), $seo_title); ?><hr role="presentation"></h1>

            <?php $this->setup_wizard_sub_steps(); ?>

            <div class="seopress-tab active">
	            <form method="post">
		            <?php wp_nonce_field('seopress-setup'); ?>

                    <p class="store-setup intro"><?php esc_html_e('The first step is to import your previous post and term metadata from other plugins to keep your SEO.', 'wp-seopress'); ?>
                    </p>

                    <?php
                        $plugins = [
                            'yoast'            => 'Yoast SEO',
                            'aio'              => 'All In One SEO',
                            'seo-framework'    => 'The SEO Framework',
                            'rk'               => 'Rank Math',
                            'squirrly'         => 'Squirrly SEO',
                            'seo-ultimate'     => 'SEO Ultimate',
                            'wp-meta-seo'      => 'WP Meta SEO',
                            'premium-seo-pack' => 'Premium SEO Pack',
                            'wpseo'            => 'wpSEO',
                            'platinum-seo'     => 'Platinum SEO Pack',
                            'smart-crawl'      => 'SmartCrawl',
                            'seopressor'       => 'SEOPressor',
                        ];

                    echo '<p>
                                            <select id="select-wizard-import" name="select-wizard-import">
                                                <option value="none">' . __('Select an option', 'wp-seopress') . '</option>';

                    foreach ($plugins as $plugin => $name) {
                        echo '<option value="' . $plugin . '-migration-tool">' . $name . '</option>';
                    }
                    echo '</select>
                                        </p>

                                    <p class="description">' . __('You don\'t have to enable the selected SEO plugin to run the import.', 'wp-seopress') . '</p>';

                    foreach ($plugins as $plugin => $name) {
                        echo seopress_migration_tool($plugin, $name);
                    } ?>


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
		$seopress_titles_option = get_option('seopress_titles_option_name');
		$seopress_social_option = get_option('seopress_social_option_name');

		$site_sep        = isset($seopress_titles_option['seopress_titles_sep']) ? $seopress_titles_option['seopress_titles_sep'] : null;
		$site_title      = isset($seopress_titles_option['seopress_titles_home_site_title']) ? $seopress_titles_option['seopress_titles_home_site_title'] : null;
		$knowledge_type  = isset($seopress_social_option['seopress_social_knowledge_type']) ? $seopress_social_option['seopress_social_knowledge_type'] : null;
		$knowledge_name  = isset($seopress_social_option['seopress_social_knowledge_name']) ? $seopress_social_option['seopress_social_knowledge_name'] : null;
		$knowledge_img   = isset($seopress_social_option['seopress_social_knowledge_img']) ? $seopress_social_option['seopress_social_knowledge_img'] : null; ?>

        <div class="seopress-setup-content">
            <h1><?php esc_html_e('Your site', 'wp-seopress'); ?><hr role="presentation"></h1>

            <?php $this->setup_wizard_sub_steps(); ?>

            <div class="seopress-tab active">

                <form method="post">
                    <h2><?php _e('Tell us more about your site','wp-seopress'); ?></h2>
                    <p><?php esc_html_e('To build title tags and knowledge graph for Google, you need to fill out the fields below to configure the general settings.', 'wp-seopress'); ?>
                    </p>

                    <p>
                        <label for="site_sep"><?php esc_html_e('Separator', 'wp-seopress'); ?></label>
                        <input type="text" id="site_sep" class="location-input" name="site_sep"
                            placeholder="<?php esc_html_e('eg: |', 'wp-seopress'); ?>"
                            required value="<?php echo $site_sep; ?>" />
                    </p>

                    <p class="description">
                        <?php _e('This separator will be used by the dynamic variable <strong>%%sep%%</strong> in your title and meta description templates.', 'wp-seopress'); ?>
                    </p>

                    <p>
                        <label for="site_title"><?php esc_html_e('Home site title', 'wp-seopress'); ?></label>
                        <input type="text" id="site_title" class="location-input" name="site_title"
                            placeholder="<?php esc_html_e('eg: My super website', 'wp-seopress'); ?>"
                            required value="<?php echo $site_title; ?>" />
                    </p>

                    <p class="description">
                        <?php _e('The site title will be used by the dynamic variable <strong>%%sitetitle%%</strong> in your title and meta description templates.', 'wp-seopress'); ?>
                    </p>

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
                            placeholder="<?php esc_html_e('eg: My Company Name', 'wp-seopress'); ?>"
                            value="<?php echo $knowledge_name; ?>" />
                    </p>

                    <p>
                        <label for="knowledge_img_meta"><?php esc_html_e('Your photo/organization logo', 'wp-seopress'); ?></label>
                        <input type="text" id="knowledge_img_meta" class="location-input" name="knowledge_img"
                            placeholder="<?php esc_html_e('eg: https://www.example.com/logo.png', 'wp-seopress'); ?>"
                            value="<?php echo $knowledge_img; ?>" />

                        <input id="knowledge_img_upload" class="btn btnSecondary" type="button" value="<?php _e('Upload an Image', 'wp-seopress'); ?>" />
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
		$seopress_titles_option['seopress_titles_sep']             = isset($_POST['site_sep']) ? esc_attr(wp_unslash($_POST['site_sep'])) : '';
		$seopress_titles_option['seopress_titles_home_site_title'] = isset($_POST['site_title']) ? sanitize_text_field(wp_unslash($_POST['site_title'])) : '';

		//Social
		$seopress_social_option['seopress_social_knowledge_type'] = isset($_POST['knowledge_type']) ? esc_attr(wp_unslash($_POST['knowledge_type'])) : '';
		$seopress_social_option['seopress_social_knowledge_name'] = isset($_POST['knowledge_name']) ? sanitize_text_field(wp_unslash($_POST['knowledge_name'])) : '';
		$seopress_social_option['seopress_social_knowledge_img']  = isset($_POST['knowledge_img']) ? sanitize_text_field(wp_unslash($_POST['knowledge_img'])) : '';

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

		$knowledge_fb    = isset($seopress_social_option['seopress_social_accounts_facebook']) ? $seopress_social_option['seopress_social_accounts_facebook'] : null;
		$knowledge_tw    = isset($seopress_social_option['seopress_social_accounts_twitter']) ? $seopress_social_option['seopress_social_accounts_twitter'] : null;
		$knowledge_pin   = isset($seopress_social_option['seopress_social_accounts_pinterest']) ? $seopress_social_option['seopress_social_accounts_pinterest'] : null;
		$knowledge_insta = isset($seopress_social_option['seopress_social_accounts_instagram']) ? $seopress_social_option['seopress_social_accounts_instagram'] : null;
		$knowledge_yt    = isset($seopress_social_option['seopress_social_accounts_youtube']) ? $seopress_social_option['seopress_social_accounts_youtube'] : null;
		$knowledge_li    = isset($seopress_social_option['seopress_social_accounts_linkedin']) ? $seopress_social_option['seopress_social_accounts_linkedin'] : null; ?>

        <div class="seopress-setup-content">
            <h1><?php esc_html_e('Your site', 'wp-seopress'); ?><hr role="presentation"></h1>

            <?php $this->setup_wizard_sub_steps(); ?>

            <div class="seopress-tab active">

                <form method="post">
                    <h2><?php _e('Link your site to your social networks','wp-seopress'); ?></h2>

                    <p><?php esc_html_e('Fill in your social accounts for search engines.', 'wp-seopress'); ?>
                    </p>

                    <p>
                        <label for="knowledge_fb"><?php esc_html_e('Facebook page URL', 'wp-seopress'); ?></label>
                        <input type="text" id="knowledge_fb" class="location-input" name="knowledge_fb"
                            placeholder="<?php esc_html_e('eg: https://facebook.com/my-page-url', 'wp-seopress'); ?>"
                            value="<?php echo $knowledge_fb; ?>" />
                    </p>

                    <p>
                        <label for="knowledge_tw"><?php esc_html_e('Twitter Username', 'wp-seopress'); ?></label>
                        <input type="text" id="knowledge_tw" class="location-input" name="knowledge_tw"
                            placeholder="<?php esc_html_e('eg: @my_twitter_account', 'wp-seopress'); ?>"
                            value="<?php echo $knowledge_tw; ?>" />
                    </p>

                    <p>
                        <label for="knowledge_pin"><?php esc_html_e('Pinterest URL', 'wp-seopress'); ?></label>
                        <input type="text" id="knowledge_pin" class="location-input" name="knowledge_pin"
                            placeholder="<?php esc_html_e('eg: https://pinterest.com/my-page-url/', 'wp-seopress'); ?>"
                            value="<?php echo $knowledge_pin; ?>" />
                    </p>

                    <p>
                        <label for="knowledge_insta"><?php esc_html_e('Instagram URL', 'wp-seopress'); ?></label>
                        <input type="text" id="knowledge_insta" class="location-input" name="knowledge_insta"
                            placeholder="<?php esc_html_e('eg: https://www.instagram.com/my-page-url/', 'wp-seopress'); ?>"
                            value="<?php echo $knowledge_insta; ?>" />
                    </p>

                    <p>
                        <label for="knowledge_yt"><?php esc_html_e('YouTube URL', 'wp-seopress'); ?></label>
                        <input type="text" id="knowledge_yt" class="location-input" name="knowledge_yt"
                            placeholder="<?php esc_html_e('eg: https://www.youtube.com/my-channel-url', 'wp-seopress'); ?>"
                            value="<?php echo $knowledge_yt; ?>" />
                    </p>

                    <p>
                        <label for="knowledge_li"><?php esc_html_e('LinkedIn URL', 'wp-seopress'); ?></label>
                        <input type="text" id="knowledge_li" class="location-input" name="knowledge_li"
                            placeholder="<?php esc_html_e('eg: http://linkedin.com/company/my-company-url/', 'wp-seopress'); ?>"
                            value="<?php echo $knowledge_li; ?>" />
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
            <h1><?php esc_html_e('Indexing', 'wp-seopress'); ?><hr role="presentation"></h1>

            <?php $this->setup_wizard_sub_steps(); ?>

            <div class="seopress-tab active">

                <form method="post" class="seopress-wizard-indexing-form">
                    <?php
                    $postTypes = seopress_get_service('WordPressData')->getPostTypes();
                    if ( ! empty($postTypes)) { ?>
                    <h2>
                        <?php _e('For which single post types, should indexing be disabled?', 'wp-seopress'); ?>
                    </h2>

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
            <h1><?php esc_html_e('Indexing', 'wp-seopress'); ?><hr role="presentation"></h1>

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
            <h1><?php esc_html_e('Indexing', 'wp-seopress'); ?><hr role="presentation"></h1>

            <?php $this->setup_wizard_sub_steps(); ?>

            <div class="seopress-tab active">

                <form method="post" class="seopress-wizard-indexing-form">
                    <?php if ( ! empty(seopress_get_taxonomies())) { ?>
                    <h2>
                        <?php _e('For which taxonomy archives, should indexing be disabled?', 'wp-seopress'); ?>
                    </h2>

                    <ul>
                        <?php
                        //Archives
                        foreach (seopress_get_taxonomies() as $seopress_tax_key => $seopress_tax_value) {
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
		foreach (seopress_get_taxonomies() as $seopress_tax_key => $seopress_tax_value) {
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

            <h1><?php esc_html_e('Advanced options', 'wp-seopress'); ?><hr role="presentation"></h1>

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
                            <?php _e('By default, SEOPress redirects your Attachment pages to the parent post. Optimize this by redirecting the user directly to the URL of the media file.', 'wp-seopress'); ?>
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
		$universal_seo_block_editor       = isset($seopress_advanced_option['seopress_advanced_appearance_universal_metabox']);
		$universal_seo_metabox            = isset($seopress_advanced_option['seopress_advanced_appearance_universal_metabox_disable']); ?>

        <div class="seopress-setup-content">

            <h1><?php esc_html_e('Advanced options', 'wp-seopress'); ?><hr role="presentation"></h1>

            <?php $this->setup_wizard_sub_steps(); ?>

            <div class="seopress-tab active">

                <form method="post">
                    <h2>
                        <?php _e('Improve your workflow with the Universal SEO metabox', 'wp-seopress'); ?>
                    </h2>

                    <p><?php _e('Edit your SEO metadata directly from your page or theme builder.', 'wp-seopress'); ?></p>
                    <ul>
                        <!-- Universal SEO metabox overview -->
                        <?php if ((function_exists('seopress_get_toggle_white_label_option') && '1' !== seopress_get_toggle_white_label_option())) { ?>
                            <li class="description">
                                <?php echo wp_oembed_get('https://www.youtube.com/watch?v=sf0ocG7vQMM'); ?>
                            </li>
                        <?php } ?>

                        <!-- Universal SEO metabox for page builers -->
                        <li class="seopress-wizard-service-item checkbox">
                            <label for="universal_seo_metabox">
                                <input id="universal_seo_metabox" name="universal_seo_metabox" type="checkbox" class="location-input" <?php if ('1' == $universal_seo_metabox) {
                            echo 'checked="yes"';
                        } ?> value="1"/>
                                <?php _e('No, I prefer to use the good old one SEO metabox', 'wp-seopress'); ?>
                            </label>
                        </li>
                        <li class="description">
                            <?php _e('You can change this setting at anytime from SEO, Advanced settings page, Appearance tab.', 'wp-seopress'); ?>
                        </li>
                        <!-- Universal SEO metabox for Block Editor -->
                        <li class="seopress-wizard-service-item checkbox">
                            <label for="universal_seo_block_editor">
                                <input id="universal_seo_block_editor" name="universal_seo_block_editor" type="checkbox" class="location-input" <?php if ('1' == $universal_seo_block_editor) {
                            echo 'checked="yes"';
                        } ?> value="1"/>
                                <?php _e('Yes, enable the universal SEO metabox for the Block Editor too', 'wp-seopress'); ?>
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
		$seopress_advanced_option['seopress_advanced_appearance_universal_metabox_disable']         = isset($_POST['universal_seo_metabox']) ? esc_attr(wp_unslash($_POST['universal_seo_metabox'])) : null;
		$seopress_advanced_option['seopress_advanced_appearance_universal_metabox']                 = isset($_POST['universal_seo_block_editor']) ? esc_attr(wp_unslash($_POST['universal_seo_block_editor'])) : null;

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
                <?php _e('SEOPress PRO','wp-seopress'); ?><hr role="presentation">
            </h1>

            <?php $this->setup_wizard_sub_steps(); ?>

            <div class="seopress-tab active">
                <h2><?php _e('Premium SEO features to increase your rankings', 'wp-seopress'); ?></h2>

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
                    <span class="dashicons dashicons-minus"></span><?php _e('Observe the evolution of your site via <strong>Google Analytics stats</strong> directly from your WordPress Dashboard.', 'wp-seopress'); ?>
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
            <?php _e('SEOPress Insights','wp-seopress'); ?><hr role="presentation">
            </h1>

            <?php $this->setup_wizard_sub_steps(); ?>

            <div class="seopress-tab active">
                <h2><?php _e('Start monitoring your rankings and backlinks directly from your WordPress admin', 'wp-seopress'); ?></h2>

                <p class="seopress-setup-actions step">
                    <span class="dashicons dashicons-minus"></span><?php esc_html_e('Track your keyword positions from Google Search results daily.', 'wp-seopress'); ?>
                </p>
                <p class="seopress-setup-actions step">
                    <span class="dashicons dashicons-minus"></span><?php esc_html_e('Monitor and analyse your top 1,000 Backlinks weekly.', 'wp-seopress'); ?>
                </p>
                <p class="seopress-setup-actions step">
                    <span class="dashicons dashicons-minus"></span><?php esc_html_e('Export your data to CSV, PDF, Excel.', 'wp-seopress'); ?>
                </p>
                <p class="seopress-setup-actions step">
                    <span class="dashicons dashicons-minus"></span><?php esc_html_e('Receive your rankings in your inbox.', 'wp-seopress'); ?>
                </p>

                <?php echo wp_oembed_get('https://www.youtube.com/watch?v=n-a2U4_anWA'); ?>

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
		$seopress_notices                  = get_option('seopress_notices');
		$seopress_notices['notice-wizard'] = '1';
		update_option('seopress_notices', $seopress_notices, false);

		$docs = seopress_get_docs_links();

        $seo_title = 'SEOPress PRO';
        if (function_exists('seopress_get_toggle_white_label_option') && '1' == seopress_get_toggle_white_label_option()) {
            $seo_title = seopress_white_label_plugin_list_title_pro_option() ? seopress_white_label_plugin_list_title_pro_option() : 'SEOPress PRO';
        }

		//Flush permalinks
		flush_rewrite_rules(false); ?>

        <div class="seopress-setup-content">

            <h1><?php esc_html_e('Your site is now ready for search engines!', 'wp-seopress'); ?><hr role="presentation"></h1>

            <?php $this->setup_wizard_sub_steps(); ?>

            <div class="seopress-tab active">

                <ul class="seopress-wizard-next-steps">
                    <li class="seopress-wizard-next-step-item">
                        <!-- SEOPress PRO -->
                        <?php if ('valid' != get_option('seopress_pro_license_status') && is_plugin_active('wp-seopress-pro/seopress-pro.php') && ! is_multisite()) { ?>
                        <div class="seopress-wizard-next-step-description">
                            <p class="next-step-heading"><?php esc_html_e('Next step', 'wp-seopress'); ?>
                            </p>
                            <h3 class="next-step-description">
                                <?php printf(esc_html__('Welcome to %s!', 'wp-seopress'), $seo_title); ?>
                            </h3>
                            <p class="next-step-extra-info">
                                <?php esc_html_e('Please activate your license to receive automatic updates and get premium support.', 'wp-seopress'); ?>
                            </p>
                        </div>
                        <div class="seopress-wizard-next-step-action">
                            <p class="seopress-setup-actions step">
                                <a class="btn btnPrimary"
                                    href="<?php echo admin_url('admin.php?page=seopress-license'); ?>">
                                    <?php _e('Activate License', 'wp-seopress'); ?>
                                </a>
                            </p>
                        </div>
                        <?php } ?>
                    </li>

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
                                <a class="btn btnSecondary"
                                    href="<?php echo admin_url('admin.php?page=seopress-xml-sitemap'); ?>">
                                    <?php esc_html_e('Configure your XML sitemaps', 'wp-seopress'); ?>
                                </a>
                            </p>
                        </div>
                    </li>

                    <?php if (function_exists('seopress_get_toggle_white_label_option') && '1' !== seopress_get_toggle_white_label_option()) { ?>
                        <li class="seopress-wizard-additional-steps">
                            <div class="seopress-wizard-next-step-description">
                                <p class="next-step-heading"><?php esc_html_e('Follow us:', 'wp-seopress'); ?>
                                </p>
                            </div>
                            <div class="seopress-wizard-next-step-action step">
                                <ul class="recommended-step">
                                    <li class="seopress-wizard-service-item">
                                        <a href="<?php echo $docs['external']['facebook']; ?>"
                                            target="_blank">
                                            <span class="dashicons dashicons-facebook"></span>
                                            <?php _e('Like our Facebook page', 'wp-seopress'); ?>
                                        </a>
                                    </li>
                                    <li class="seopress-wizard-service-item">
                                        <a href="<?php echo $docs['external']['facebook_gr']; ?>"
                                            target="_blank">
                                            <span class="dashicons dashicons-facebook"></span>
                                            <?php _e('Join our Facebook Community group', 'wp-seopress'); ?>
                                        </a>
                                    </li>
                                    <li class="seopress-wizard-service-item">
                                        <a href="<?php echo $docs['external']['youtube']; ?>"
                                            target="_blank">
                                            <span class="dashicons dashicons-video-alt3"></span>
                                            <?php _e('Watch our guided tour videos to learn more about SEOPress', 'wp-seopress'); ?>
                                        </a>
                                    </li>
                                    <li class="seopress-wizard-service-item">
                                        <a href="<?php echo $docs['blog']; ?>"
                                            target="_blank">
                                            <span class="dashicons dashicons-format-aside"></span>
                                            <?php _e('Read our blog posts about SEO concepts, tutorials and more', 'wp-seopress'); ?>
                                        </a>
                                    </li>
                                    <li class="seopress-wizard-service-item">
                                        <a href="<?php echo $docs['external']['twitter']; ?>"
                                            target="_blank">
                                            <span class="dashicons dashicons-twitter"></span>
                                            <?php _e('Follow us on Twitter', 'wp-seopress'); ?>
                                        </a>
                                    </li>
                                    <li class="seopress-wizard-service-item">
                                        <a href="<?php echo $docs['external']['instagram']; ?>"
                                            target="_blank">
                                            <span class="dashicons dashicons-instagram"></span>
                                            <?php _e('The off side of SEOPress', 'wp-seopress'); ?>
                                        </a>
                                    </li>
                                </ul>
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
                                <a class="btn btnSecondary"
                                    href="<?php echo esc_url(admin_url()); ?>">
                                    <?php esc_html_e('Visit Dashboard', 'wp-seopress'); ?>
                                </a>
                                <a class="btn btnSecondary"
                                    href="<?php echo esc_url(admin_url('admin.php?page=seopress-option')); ?>">
                                    <?php esc_html_e('Review Settings', 'wp-seopress'); ?>
                                </a>
                                <?php if (function_exists('seopress_get_toggle_white_label_option') && '1' !== seopress_get_toggle_white_label_option()) { ?>
                                    <a class="btn btnSecondary"
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
