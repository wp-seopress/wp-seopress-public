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
        $default_steps = [
            'import_settings' => [
                'name'    => __('Import SEO settings', 'wp-seopress'),
                'view'    => [$this, 'seopress_setup_import_settings'],
                'handler' => [$this, 'seopress_setup_import_settings_save'],
            ],
            'site'     => [
                'name'    => __('Your site', 'wp-seopress'),
                'view'    => [$this, 'seopress_setup_site'],
                'handler' => [$this, 'seopress_setup_site_save'],
            ],
            'indexing'    => [
                'name'    => __('Indexing', 'wp-seopress'),
                'view'    => [$this, 'seopress_setup_indexing'],
                'handler' => [$this, 'seopress_setup_indexing_save'],
            ],
            'advanced'    => [
                'name'    => __('Advanced options', 'wp-seopress'),
                'view'    => [$this, 'seopress_setup_advanced'],
                'handler' => [$this, 'seopress_setup_advanced_save'],
            ],
        ];

        if ( ! is_plugin_active('wp-seopress-insights/seopress-insights.php') && ! is_multisite()) {
            $default_steps['insights'] = [
                    'name'    => __('Insights', 'wp-seopress'),
                    'view'    => [$this, 'seopress_setup_insights'],
                    'handler' => '',
            ];
        }
        $default_steps['ready']  = [
                'name'    => __('Ready!', 'wp-seopress'),
                'view'    => [$this, 'seopress_setup_ready'],
                'handler' => '',
        ];

        $this->steps = apply_filters('seopress_setup_wizard_steps', $default_steps);
        $this->step  = isset($_GET['step']) ? sanitize_key($_GET['step']) : current(array_keys($this->steps));

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

        return add_query_arg('step', $keys[$step_index + 1], remove_query_arg('activate_error'));
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
    <?php do_action('admin_enqueue_scripts'); ?>
    <?php wp_print_scripts('seopress-migrate-ajax'); ?>
    <?php do_action('admin_print_styles'); ?>
    <?php do_action('admin_head'); ?>
</head>

<?php
        $class = '';
        if ('insights' === $this->step) {
            $class = ' no_nav_menu';
        } ?>

<body
    class="seopress-setup seopress-option wp-core-ui<?php echo $class; ?>">
    <?php
    }

    /**
     * Setup Wizard Footer.
     */
    public function setup_wizard_footer() {
        ?>
    <div class="seopress-setup-footer">
        <?php if ('import_settings' === $this->step) { ?>
        <a class="seopress-setup-footer-links"
            href="<?php echo esc_url(admin_url()); ?>"><?php esc_html_e('Not right now', 'wp-seopress'); ?></a>
        <?php } elseif ('site' === $this->step || 'indexing' === $this->step || 'advanced' === $this->step || 'insights' === $this->step) { ?>
        <a class="seopress-setup-footer-links"
            href="<?php echo esc_url($this->get_next_step_link()); ?>"><?php esc_html_e('Skip this step', 'wp-seopress'); ?></a>
        <?php } ?>
        <?php do_action('seopress_setup_footer'); ?>
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
        $output_steps      = $this->steps; ?>
<div id="wpcontent" class="seopress-option">
    <ol class="seopress-setup-steps">
        <?php
                $i = 1;
        foreach ($output_steps as $step_key => $step) {
            $is_completed = array_search($this->step, array_keys($this->steps), true) > array_search($step_key, array_keys($this->steps), true);

            if ($step_key === $this->step) {
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
                href="<?php echo esc_url(add_query_arg('step', $step_key, remove_query_arg('activate_error'))); ?>">
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
     * Output the content for the current step.
     */
    public function setup_wizard_content() {
        echo '<div class="seopress-setup-content">';
        if ( ! empty($this->steps[$this->step]['view'])) {
            call_user_func($this->steps[$this->step]['view'], $this);
        }
        echo '</div>';
    }

    /**
     * Init "Step 1: Import SEO settings".
     */
    public function seopress_setup_import_settings() {
        ?>
    <form method="post">
        <?php wp_nonce_field('seopress-setup'); ?>
        <h1 class="text-center"><?php _e('Welcome to SEOPress!', 'wp-seopress'); ?>
        </h1>
        <p class="store-setup"><?php esc_html_e('The following wizard will help you configure SEOPress and get you started quickly.', 'wp-seopress'); ?>
        </p>
        <p class="store-setup"><?php esc_html_e('The first step is to import your previous settings from other plugins to keep your SEO.', 'wp-seopress'); ?>
        </p>
        <p class="store-setup"><?php esc_html_e('No data to migrate? Click "Next step" button!', 'wp-seopress'); ?>
        </p>

        <h2><?php _e('Import posts and terms metadata from', 'wp-seopress'); ?>
        </h2>
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
</div>

<p class="seopress-setup-actions step">
    <button type="submit" class="btnPrimary btn btnNext"
        value="<?php esc_attr_e('Next step', 'wp-seopress'); ?>"
        name="save_step">
        <?php esc_html_e('Next step', 'wp-seopress'); ?>
    </button>

    <?php wp_nonce_field('seopress-setup'); ?>
</p>
</form>
<?php
    }

    /**
     * Save step 1 settings.
     */
    public function seopress_setup_import_settings_save() {
        check_admin_referer('seopress-setup');
        wp_safe_redirect(esc_url_raw($this->get_next_step_link()));
        exit;
    }

    /**
     * Init "Step 2: Your site".
     */
    public function seopress_setup_site() {
        $seopress_titles_option = get_option('seopress_titles_option_name');
        $seopress_social_option = get_option('seopress_social_option_name');

        $site_sep        = isset($seopress_titles_option['seopress_titles_sep']) ? $seopress_titles_option['seopress_titles_sep'] : null;
        $site_title      = isset($seopress_titles_option['seopress_titles_home_site_title']) ? $seopress_titles_option['seopress_titles_home_site_title'] : null;
        $knowledge_type  = isset($seopress_social_option['seopress_social_knowledge_type']) ? $seopress_social_option['seopress_social_knowledge_type'] : null;
        $knowledge_name  = isset($seopress_social_option['seopress_social_knowledge_name']) ? $seopress_social_option['seopress_social_knowledge_name'] : null;
        $knowledge_img   = isset($seopress_social_option['seopress_social_knowledge_img']) ? $seopress_social_option['seopress_social_knowledge_img'] : null;
        $knowledge_fb    = isset($seopress_social_option['seopress_social_accounts_facebook']) ? $seopress_social_option['seopress_social_accounts_facebook'] : null;
        $knowledge_tw    = isset($seopress_social_option['seopress_social_accounts_twitter']) ? $seopress_social_option['seopress_social_accounts_twitter'] : null;
        $knowledge_pin   = isset($seopress_social_option['seopress_social_accounts_pinterest']) ? $seopress_social_option['seopress_social_accounts_pinterest'] : null;
        $knowledge_insta = isset($seopress_social_option['seopress_social_accounts_instagram']) ? $seopress_social_option['seopress_social_accounts_instagram'] : null;
        $knowledge_yt    = isset($seopress_social_option['seopress_social_accounts_youtube']) ? $seopress_social_option['seopress_social_accounts_youtube'] : null;
        $knowledge_li    = isset($seopress_social_option['seopress_social_accounts_linkedin']) ? $seopress_social_option['seopress_social_accounts_linkedin'] : null; ?>

<h1><?php esc_html_e('Your site', 'wp-seopress'); ?>
</h1>
<form method="post">
    <p><?php esc_html_e('To build title tags and knowledge graph for Google, you need to fill out the fields below to configure the general settings. ', 'wp-seopress'); ?>
    </p>

    <p>
        <label for="site_sep"><?php esc_html_e('Separator', 'wp-seopress'); ?></label>
        <input type="text" id="site_sep" class="location-input" name="site_sep"
            placeholder="<?php esc_html_e('eg: |', 'wp-seopress'); ?>"
            required value="<?php echo $site_sep; ?>" />
    </p>

    <p class="description seopress-wizard-services description">
        <?php _e('This separator will be used by the dynamic variable <strong>%%sep%%</strong> in your title and meta description templates.', 'wp-seopress'); ?>
    </p>

    <p>
        <label for="site_title"><?php esc_html_e('Home site title', 'wp-seopress'); ?></label>
        <input type="text" id="site_title" class="location-input" name="site_title"
            placeholder="<?php esc_html_e('eg: My super website', 'wp-seopress'); ?>"
            required value="<?php echo $site_title; ?>" />
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

    <p>
        <label for="knowledge_name"><?php esc_html_e('Your name/organization', 'wp-seopress'); ?></label>
        <input type="text" id="knowledge_name" class="location-input" name="knowledge_name"
            placeholder="<?php esc_html_e('eg: My Company Name', 'wp-seopress'); ?>"
            value="<?php echo $knowledge_name; ?>" />
    </p>

    <p>
        <label for="knowledge_img"><?php esc_html_e('Your photo/organization logo', 'wp-seopress'); ?></label>
        <input type="text" id="knowledge_img" class="location-input" name="knowledge_img"
            placeholder="<?php esc_html_e('eg: https://www.example.com/logo.png', 'wp-seopress'); ?>"
            value="<?php echo $knowledge_img; ?>" />
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
            value="<?php esc_attr_e('Continue', 'wp-seopress'); ?>"
            name="save_step">
            <?php esc_html_e('Continue', 'wp-seopress'); ?>
        </button>
        <?php wp_nonce_field('seopress-setup'); ?>
    </p>
</form>
<?php
    }

    /**
     * Save step 2 settings.
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

        //Social accounts
        $seopress_social_option['seopress_social_accounts_facebook']   = isset($_POST['knowledge_fb']) ? sanitize_text_field(wp_unslash($_POST['knowledge_fb'])) : '';
        $seopress_social_option['seopress_social_accounts_twitter']    = isset($_POST['knowledge_tw']) ? sanitize_text_field(wp_unslash($_POST['knowledge_tw'])) : '';
        $seopress_social_option['seopress_social_accounts_pinterest']  = isset($_POST['knowledge_pin']) ? sanitize_text_field(wp_unslash($_POST['knowledge_pin'])) : '';
        $seopress_social_option['seopress_social_accounts_instagram']  = isset($_POST['knowledge_insta']) ? sanitize_text_field(wp_unslash($_POST['knowledge_insta'])) : '';
        $seopress_social_option['seopress_social_accounts_youtube']    = isset($_POST['knowledge_yt']) ? sanitize_text_field(wp_unslash($_POST['knowledge_yt'])) : '';
        $seopress_social_option['seopress_social_accounts_linkedin']   = isset($_POST['knowledge_li']) ? sanitize_text_field(wp_unslash($_POST['knowledge_li'])) : '';

        //Save options
        update_option('seopress_titles_option_name', $seopress_titles_option);
        update_option('seopress_social_option_name', $seopress_social_option);

        wp_safe_redirect(esc_url_raw($this->get_next_step_link()));
        exit;
    }

    /**
     *	Init "Step 3: Indexing Step".
     */
    public function seopress_setup_indexing() {
        $seopress_titles_option = get_option('seopress_titles_option_name'); ?>
<h1><?php esc_html_e('Indexing', 'wp-seopress'); ?>
</h1>
<p><?php esc_html_e('Specify to the search engines what you want to be indexed or not.', 'wp-seopress'); ?>
</p>
<p><?php esc_html_e('Avoid indexing duplicate or poor quality content.', 'wp-seopress'); ?>
</p>
<p><?php esc_html_e('Default: index', 'wp-seopress'); ?>
</p>

<form method="post" class="seopress-wizard-indexing-form">
    <?php if ( ! empty(seopress_get_post_types())) { ?>
    <div class="seopress-wizard-services">
        <h2>
            <?php _e('For which single post types, should indexing be disabled?', 'wp-seopress'); ?>
        </h2>

        <ul>
            <?php
                            //Post Types
                            foreach (seopress_get_post_types() as $seopress_cpt_key => $seopress_cpt_value) {
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
    </div>
    <?php } ?>

    <?php if ( ! empty(seopress_get_post_types())) { ?>
    <div class="seopress-wizard-services">
        <h2>
            <?php _e('For which post type archives, should indexing be disabled?', 'wp-seopress'); ?>
        </h2>

        <ul>
            <?php
                            foreach (seopress_get_post_types() as $seopress_cpt_key => $seopress_cpt_value) {
                                if ( ! in_array($seopress_cpt_key, ['post', 'page'])) {
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
                        ?>
        </ul>
    </div>
    <?php } ?>

    <?php if ( ! empty(seopress_get_taxonomies())) { ?>
    <div class="seopress-wizard-services">
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
    </div>
    <?php } ?>

    <p class="seopress-setup-actions step">
        <button type="submit" class="btnPrimary btn btnNext"
            value="<?php esc_attr_e('Continue', 'wp-seopress'); ?>"
            name="save_step">
            <?php esc_html_e('Continue', 'wp-seopress'); ?>
        </button>

        <?php wp_nonce_field('seopress-setup'); ?>
    </p>
</form>
<?php
    }

    /**
     * Save Step 3 settings.
     */
    public function seopress_setup_indexing_save() {
        check_admin_referer('seopress-setup');

        //Get options
        $seopress_titles_option = get_option('seopress_titles_option_name');

        //Post Types noindex
        foreach (seopress_get_post_types() as $seopress_cpt_key => $seopress_cpt_value) {
            if (isset($_POST['seopress_titles_option_name']['seopress_titles_single_titles'][$seopress_cpt_key]['noindex'])) {
                $noindex = esc_attr(wp_unslash($_POST['seopress_titles_option_name']['seopress_titles_single_titles'][$seopress_cpt_key]['noindex']));
            } else {
                $noindex = null;
            }
            $seopress_titles_option['seopress_titles_single_titles'][$seopress_cpt_key]['noindex'] = $noindex;
        }

        //Post Type archives noindex
        foreach (seopress_get_post_types() as $seopress_cpt_key => $seopress_cpt_value) {
            if (isset($_POST['seopress_titles_option_name']['seopress_titles_archive_titles'][$seopress_cpt_key]['noindex'])) {
                $noindex = esc_attr(wp_unslash($_POST['seopress_titles_option_name']['seopress_titles_archive_titles'][$seopress_cpt_key]['noindex']));
            } else {
                $noindex = null;
            }
            $seopress_titles_option['seopress_titles_archive_titles'][$seopress_cpt_key]['noindex'] = $noindex;
        }

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
        $product_category_url             = isset($seopress_advanced_option['seopress_advanced_advanced_product_cat_url']);
        $meta_title                       = isset($seopress_advanced_option['seopress_advanced_appearance_title_col']);
        $universal_seo_metabox            = isset($seopress_advanced_option['seopress_advanced_appearance_universal_metabox_disable']);
        $meta_desc                        = isset($seopress_advanced_option['seopress_advanced_appearance_meta_desc_col']);
        $robots_noindex                   = isset($seopress_advanced_option['seopress_advanced_appearance_noindex_col']);
        $robots_nofollow                  = isset($seopress_advanced_option['seopress_advanced_appearance_nofollow_col']);
        $ca_score                         = isset($seopress_advanced_option['seopress_advanced_appearance_score_col']); ?>

<h1><?php esc_html_e('Advanced options', 'wp-seopress'); ?>
</h1>
<p><?php esc_html_e('Final step before being ready to rank on search engines!', 'wp-seopress'); ?>
</p>

<form method="post">
    <ul class="seopress-wizard-services">
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

    <h2>
        <?php _e('Universal SEO metabox', 'wp-seopress'); ?>
    </h2>

    <ul class="seopress-wizard-services">
        <li class="description">
            <?php _e('Edit your SEO metadata from your page or theme builder. Default: <strong>Enabled</strong>.', 'wp-seopress'); ?>
        </li>
        <!-- Show meta title -->
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
    </ul>

    <h2>
        <?php _e('Choose which SEO columns to display in post types list:', 'wp-seopress'); ?>
    </h2>

    <ul class="seopress-wizard-services">
        <!-- Show meta title -->
        <li class="seopress-wizard-service-item checkbox">
            <label for="meta_title">
                <input id="meta_title" name="meta_title" type="checkbox" class="location-input" <?php if ('1' == $meta_title) {
            echo 'checked="yes"';
        } ?> value="1"/>
                <?php _e('Show Title tag column in post types', 'wp-seopress'); ?>
            </label>
        </li>

        <!-- Show meta description -->
        <li class="seopress-wizard-service-item checkbox">
            <label for="meta_desc">
                <input id="meta_desc" name="meta_desc" type="checkbox" class="location-input" <?php if ('1' == $meta_desc) {
            echo 'checked="yes"';
        } ?> value="1"/>
                <?php _e('Show Meta description column in post types', 'wp-seopress'); ?>
            </label>
        </li>

        <!-- Show meta robots noindex -->
        <li class="seopress-wizard-service-item checkbox">
            <label for="robots_noindex">
                <input id="robots_noindex" name="robots_noindex" type="checkbox" class="location-input" <?php if ('1' == $robots_noindex) {
            echo 'checked="yes"';
        } ?> value="1"/>
                <?php _e('Show noindex column in post types', 'wp-seopress'); ?>
            </label>
        </li>
        <li class="description">
            <?php _e('Quickly know if a content is in noindex.', 'wp-seopress'); ?>
        </li>

        <!-- Show meta robots nofollow -->
        <li class="seopress-wizard-service-item checkbox">
            <label for="robots_nofollow">
                <input id="robots_nofollow" name="robots_nofollow" type="checkbox" class="location-input" <?php if ('1' == $robots_nofollow) {
            echo 'checked="yes"';
        } ?> value="1"/>
                <?php _e('Show nofollow column in post types', 'wp-seopress'); ?>
            </label>
        </li>
        <li class="description">
            <?php _e('Quickly know if a content is in nofollow.', 'wp-seopress'); ?>
        </li>

        <!-- Show meta content analysis score -->
        <li class="seopress-wizard-service-item checkbox">
            <label for="ca_score">
                <input id="ca_score" name="ca_score" type="checkbox" class="location-input" <?php if ('1' == $ca_score) {
            echo 'checked="yes"';
        } ?> value="1"/>
                <?php _e('Show content analysis score column in post types', 'wp-seopress'); ?>
            </label>
        </li>
        <li class="description">
            <?php _e('Quickly know if a content is optimized for search engines.', 'wp-seopress'); ?>
        </li>
    </ul>

    <p class="seopress-setup-actions step">
        <button type="submit" class="btn btnPrimary btnNext"
            value="<?php esc_attr_e('Continue', 'wp-seopress'); ?>"
            name="save_step">
            <?php esc_html_e('Continue', 'wp-seopress'); ?>
        </button>

        <?php wp_nonce_field('seopress-setup'); ?>
    </p>
</form>
<?php
    }

    /**
     * Save step 4 settings.
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

        $seopress_advanced_option['seopress_advanced_appearance_universal_metabox_disable']         = isset($_POST['universal_seo_metabox']) ? esc_attr(wp_unslash($_POST['universal_seo_metabox'])) : null;

        $seopress_advanced_option['seopress_advanced_appearance_title_col']         = isset($_POST['meta_title']) ? esc_attr(wp_unslash($_POST['meta_title'])) : null;
        $seopress_advanced_option['seopress_advanced_appearance_meta_desc_col']     = isset($_POST['meta_desc']) ? esc_attr(wp_unslash($_POST['meta_desc'])) : null;

        $seopress_advanced_option['seopress_advanced_appearance_noindex_col']       = isset($_POST['robots_noindex']) ? esc_attr(wp_unslash($_POST['robots_noindex'])) : null;
        $seopress_advanced_option['seopress_advanced_appearance_nofollow_col']      = isset($_POST['robots_nofollow']) ? esc_attr(wp_unslash($_POST['robots_nofollow'])) : null;
        $seopress_advanced_option['seopress_advanced_appearance_score_col']         = isset($_POST['ca_score']) ? esc_attr(wp_unslash($_POST['ca_score'])) : null;

        //Save options
        update_option('seopress_titles_option_name', $seopress_titles_option);
        update_option('seopress_advanced_option_name', $seopress_advanced_option);

        wp_redirect(esc_url_raw($this->get_next_step_link()));
        exit;
    }

    /**
     *	Init "Step 4: Advanced Step".
     */
    public function seopress_setup_insights() {
        $docs = seopress_get_docs_links(); ?>
<!-- SEOPress Insights -->
<div class="seopress-wizard-next-step-description text-center">
    <img src="<?php echo SEOPRESS_ASSETS_DIR . '/img/logo-seopress-insights-square-alt.svg'; ?>"
        width="40" height="40" alt="" />

    <h1 class="seopress-setup-actions step">
        <?php _e('Start monitoring your rankings and backlinks <br>directly from your WordPress admin', 'wp-seopress'); ?>
    </h1>
    <p class="seopress-setup-actions step">
        <?php esc_html_e('Track your keyword positions from Google Search results daily.', 'wp-seopress'); ?>
    </p>
    <p class="seopress-setup-actions step">
        <?php esc_html_e('Monitor and analyse your top 1,000 Backlinks weekly.', 'wp-seopress'); ?>
    </p>
    <p class="seopress-setup-actions step">
        <?php esc_html_e('Export your data to CSV, PDF, Excel.', 'wp-seopress'); ?>
    </p>

    <div class="wrap-iframe">
        <?php echo wp_oembed_get('https://www.youtube.com/watch?v=n-a2U4_anWA'); ?>
    </div>

    <p class="seopress-setup-actions step">
        <a class="btn btnPrimary"
            href="<?php echo $docs['insights']; ?>"
            target="_blank">
            <?php _e('Get SEOPress Insights', 'wp-seopress'); ?>
        </a>
    </p>
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
        update_option('seopress_notices', $seopress_notices);

        $docs = seopress_get_docs_links();

        //Flush permalinks
        flush_rewrite_rules(false); ?>
<h1><?php esc_html_e('Your site is now ready for search engines!', 'wp-seopress'); ?>
</h1>

<ul class="seopress-wizard-next-steps">
    <li class="seopress-wizard-next-step-item">
        <!-- SEOPress PRO -->
        <?php if ('valid' != get_option('seopress_pro_license_status') && is_plugin_active('wp-seopress-pro/seopress-pro.php') && ! is_multisite()) { ?>
        <div class="seopress-wizard-next-step-description">
            <p class="next-step-heading"><?php esc_html_e('Next step', 'wp-seopress'); ?>
            </p>
            <h3 class="next-step-description">
                <?php esc_html_e('Welcome to SEOPress PRO!', 'wp-seopress'); ?>
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
        <?php } elseif ( ! is_plugin_active('wp-seopress-pro/seopress-pro.php') && ! is_multisite()) { ?>
        <div class="seopress-wizard-next-step-description text-center">
            <h2 class="next-step-description">
                <?php esc_html_e('Go PRO with SEOPress PRO!', 'wp-seopress'); ?>
            </h2>
            <p class="next-step-extra-info">
                <?php esc_html_e('When you upgrade to the PRO version, you get a lot of additional features, like automatic and manual schemas, Video Sitemap, WooCommerce enhancements, Analytics statistics in your Dashboard, breadcrumbs, redirections, and more.', 'wp-seopress'); ?>
            </p>

            <p class="seopress-setup-actions step">
                <a class="btn btnPrimary"
                    href="<?php echo $docs['website']; ?>"
                    target="_blank">
                    <?php _e('Buy SEOPress PRO - $39 / unlimited sites', 'wp-seopress'); ?>
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
                <a class="btn btnSecondary"
                    href="<?php echo $docs['support']; ?>"
                    target="_blank">
                    <?php esc_html_e('Knowledge base', 'wp-seopress'); ?>
                </a>
            </p>
        </div>
    </li>
</ul>
<?php
    }
}

new SEOPRESS_Admin_Setup_Wizard();
