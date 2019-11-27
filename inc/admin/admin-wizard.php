<?php
/**
 * Setup Wizard Class
 *
 * Takes new users through some basic steps to setup SEOPress.
 *
 * @package     SEOPress/inc/admin
 * @version     3.5.8
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * SEOPRESS_Admin_Setup_Wizard class.
 */
class SEOPRESS_Admin_Setup_Wizard {

	/**
	 * Current step
	 *
	 * @var string
	 */
	private $step = '';

	/**
	 * Steps for the setup wizard
	 *
	 * @var array
	 */
	private $steps = array();

	/**
	 * Hook in tabs.
	 */
	public function __construct() {
		if ( apply_filters( 'seopress_enable_setup_wizard', true ) && current_user_can( 'manage_options' ) ) {
			add_action( 'admin_menu', array( $this, 'admin_menus' ) );
			add_action( 'admin_init', array( $this, 'setup_wizard' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		}
	}

	/**
	 * Add admin menus/screens.
	 */
	public function admin_menus() {
		add_dashboard_page( '', '', 'manage_options', 'seopress-setup', '' );
	}

	/**
	 * Register/enqueue scripts and styles for the Setup Wizard.
	 *
	 * Hooked onto 'admin_enqueue_scripts'.
	 */
	public function enqueue_scripts() {
		wp_enqueue_style( 'seopress-setup', plugins_url( 'assets/css/seopress-setup.min.css', dirname(dirname(__FILE__))), array( 'dashicons', 'install' ), SEOPRESS_VERSION );
		wp_register_script( 'seopress-migrate-ajax', plugins_url( 'assets/js/seopress-migrate.js', dirname(dirname(__FILE__))), array( 'jquery' ), SEOPRESS_VERSION, true );

        $seopress_migrate = array( 
            'seopress_aio_migrate' => array(
                'seopress_nonce' => wp_create_nonce('seopress_aio_migrate_nonce'),
                'seopress_aio_migration' => admin_url( 'admin-ajax.php'),
            ),
            'seopress_yoast_migrate' => array(
                'seopress_nonce' => wp_create_nonce('seopress_yoast_migrate_nonce'),
                'seopress_yoast_migration' => admin_url( 'admin-ajax.php'),
            ),
            'seopress_seo_framework_migrate' => array(
                'seopress_nonce' => wp_create_nonce('seopress_seo_framework_migrate_nonce'),
                'seopress_seo_framework_migration' => admin_url( 'admin-ajax.php'),
            ),
            'seopress_rk_migrate' => array(
                'seopress_nonce' => wp_create_nonce('seopress_rk_migrate_nonce'),
                'seopress_rk_migration' => admin_url( 'admin-ajax.php'),
            ),
        );
        wp_localize_script( 'seopress-migrate-ajax', 'seopressAjaxMigrate', $seopress_migrate );
	}

	/**
	 * Show the setup wizard.
	 */
	public function setup_wizard() {
		if ( empty( $_GET['page'] ) || 'seopress-setup' !== $_GET['page'] ) {
			return;
		}
		$default_steps = array(
			'import_settings' => array(
				'name'    => __( 'Import SEO settings', 'wp-seopress' ),
				'view'    => array( $this, 'seopress_setup_import_settings' ),
				'handler' => array( $this, 'seopress_setup_import_settings_save' ),
			),
			'site'     => array(
				'name'    => __( 'Your site', 'wp-seopress' ),
				'view'    => array( $this, 'seopress_setup_site' ),
				'handler' => array( $this, 'seopress_setup_site_save' ),
			),
			'indexing'    => array(
				'name'    => __( 'Indexing', 'wp-seopress' ),
				'view'    => array( $this, 'seopress_setup_indexing' ),
				'handler' => array( $this, 'seopress_setup_indexing_save' ),
			),
			'advanced' => array(
				'name'    => __( 'Advanced options', 'wp-seopress' ),
				'view'    => array( $this, 'seopress_setup_advanced' ),
				'handler' => array( $this, 'seopress_setup_advanced_save' ),
			),
			'ready'  => array(
				'name'    => __( 'Ready!', 'wp-seopress' ),
				'view'    => array( $this, 'seopress_setup_ready' ),
				'handler' => '',
			),
		);

		$this->steps = apply_filters( 'seopress_setup_wizard_steps', $default_steps );
		$this->step  = isset( $_GET['step'] ) ? sanitize_key( $_GET['step'] ) : current( array_keys( $this->steps ) );

		if ( ! empty( $_POST['save_step'] ) && isset( $this->steps[ $this->step ]['handler'] ) ) {
			call_user_func( $this->steps[ $this->step ]['handler'], $this );
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
	 * @param string $step  slug (default: current step).
	 * @return string       URL for next step if a next step exists.
	 *                      Admin URL if it's the last step.
	 *                      Empty string on failure.
	 * @since 3.5.8
	 */
	public function get_next_step_link( $step = '' ) {
		if ( ! $step ) {
			$step = $this->step;
		}

		$keys = array_keys( $this->steps );
		if ( end( $keys ) === $step ) {
			return admin_url();
		}

		$step_index = array_search( $step, $keys, true );
		if ( false === $step_index ) {
			return '';
		}

		return add_query_arg( 'step', $keys[ $step_index + 1 ], remove_query_arg( 'activate_error' ) );
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
			<title><?php esc_html_e( 'SEOPress &rsaquo; Setup Wizard', 'wp-seopress' ); ?></title>
			<?php do_action( 'admin_enqueue_scripts' ); ?>
			<?php wp_print_scripts( 'seopress-migrate-ajax' ); ?>
			<?php do_action( 'admin_print_styles' ); ?>
			<?php do_action( 'admin_head' ); ?>
		</head>
		<body class="seopress-setup wp-core-ui">
			<h1 id="seopress-logo"><a href="https://www.seopress.org/" target="_blank"><img src="<?php echo plugins_url('assets/img/logo-seopress.svg', dirname(dirname(__FILE__))); ?>" alt="SEOPress" /></a></h1>
		<?php
	}

	/**
	 * Setup Wizard Footer.
	 */
	public function setup_wizard_footer() {
		?>
			<?php if ( 'import_settings' === $this->step ) : ?>
				<a class="seopress-setup-footer-links" href="<?php echo esc_url( admin_url() ); ?>"><?php esc_html_e( 'Not right now', 'wp-seopress' ); ?></a>
			<?php elseif ( 'site' === $this->step || 'indexing' === $this->step || 'advanced' === $this->step ) : ?>
				<a class="seopress-setup-footer-links" href="<?php echo esc_url( $this->get_next_step_link() ); ?>"><?php esc_html_e( 'Skip this step', 'wp-seopress' ); ?></a>
			<?php endif; ?>
			<?php do_action( 'seopress_setup_footer' ); ?>
			</body>
		</html>
		<?php
	}

	/**
	 * Output the steps.
	 */
	public function setup_wizard_steps() {
		$output_steps      = $this->steps;
		?>
		<ol class="seopress-setup-steps">
			<?php
			foreach ( $output_steps as $step_key => $step ) {
				$is_completed = array_search( $this->step, array_keys( $this->steps ), true ) > array_search( $step_key, array_keys( $this->steps ), true );

				if ( $step_key === $this->step ) {
					?>
					<li class="active"><?php echo esc_html( $step['name'] ); ?></li>
					<?php
				} elseif ( $is_completed ) {
					?>
					<li class="done">
						<a href="<?php echo esc_url( add_query_arg( 'step', $step_key, remove_query_arg( 'activate_error' ) ) ); ?>"><?php echo esc_html( $step['name'] ); ?></a>
					</li>
					<?php
				} else {
					?>
					<li><?php echo esc_html( $step['name'] ); ?></li>
					<?php
				}
			}
			?>
		</ol>
		<?php
	}

	/**
	 * Output the content for the current step.
	 */
	public function setup_wizard_content() {
		echo '<div class="seopress-setup-content">';
		if ( ! empty( $this->steps[ $this->step ]['view'] ) ) {
			call_user_func( $this->steps[ $this->step ]['view'], $this );
		}
		echo '</div>';
	}

	/**
	 * Init "Step 1: Import SEO settings".
	 */
	public function seopress_setup_import_settings() {
		?>
		<form method="post" class="address-step">
			<?php wp_nonce_field( 'seopress-setup' ); ?>
			<p class="store-setup"><?php esc_html_e( 'The following wizard will help you configure SEOPress and get you started quickly.', 'wp-seopress' ); ?></p>

			<div class="store-address-container">
                <!-- Yoast import tool --> 
                <div id="yoast-migration-tool" class="postbox section-tool seopress-wizard-services">
                    <h3><span><?php _e( 'Import posts and terms metadata from Yoast', 'wp-seopress' ); ?></span></h3>
                    <p><?php _e( 'By clicking Migrate, we\'ll import:', 'wp-seopress' ); ?></p>
                    <ul>
                        <li><?php _e('Title tags','wp-seopress'); ?></li>
                        <li><?php _e('Meta description','wp-seopress'); ?></li>
                        <li><?php _e('Facebook Open Graph tags (title, description and image thumbnail)','wp-seopress'); ?></li>
                        <li><?php _e('Twitter tags (title, description and image thumbnail)','wp-seopress'); ?></li>
                        <li><?php _e('Meta Robots (noindex, nofollow...)','wp-seopress'); ?></li>
                        <li><?php _e('Canonical URL','wp-seopress'); ?></li>
                        <li><?php _e('Focus keywords','wp-seopress'); ?></li>
                    </ul>
                    <p style="color:red"><span class="dashicons dashicons-warning"></span> <?php _e( '<strong>WARNING:</strong> Migration will delete / update all SEOPress posts and terms metadata. Some dynamic variables will not be interpreted. We do NOT delete any Yoast data.', 'wp-seopress' ); ?></p>
                    <button id="seopress-yoast-migrate" class="button"><?php _e('Migrate now','wp-seopress'); ?></button>
                    <span class="spinner"></span>
                    <div class="log"></div>
                </div><!-- .postbox -->                

                <!-- All In One import tool --> 
                <div id="aio-migration-tool" class="postbox section-tool seopress-wizard-services">
                    <h3><span><?php _e( 'Import posts and terms metadata from All In One SEO', 'wp-seopress' ); ?></span></h3>
                    <p><?php _e( 'By clicking Migrate, we\'ll import:', 'wp-seopress' ); ?></p>
                    <ul>
                        <li><?php _e('Title tags','wp-seopress'); ?></li>
                        <li><?php _e('Meta description','wp-seopress'); ?></li>
                        <li><?php _e('Facebook Open Graph tags (title, description and image thumbnail)','wp-seopress'); ?></li>
                        <li><?php _e('Twitter image thumbnail','wp-seopress'); ?></li>
                        <li><?php _e('Meta Robots (noindex, nofollow)','wp-seopress'); ?></li>
                    </ul>
                    <p style="color:red"><span class="dashicons dashicons-warning"></span> <?php _e( '<strong>WARNING:</strong> Migration will update / delete all SEOPress posts and terms metadata. Some dynamic variables will not be interpreted. We do NOT delete any AIO data.', 'wp-seopress' ); ?></p>
                    <button id="seopress-aio-migrate" class="button"><?php _e('Migrate now','wp-seopress'); ?></button>
                    <span class="spinner"></span>
                    <div class="log"></div>
                </div><!-- .postbox -->

                <!-- SEO Framework import tool --> 
                <div id="seo-framework-migration-tool" class="postbox section-tool seopress-wizard-services">
                    <h3><span><?php _e( 'Import posts and terms metadata from The SEO Framework', 'wp-seopress' ); ?></span></h3>
                    <p><?php _e( 'By clicking Migrate, we\'ll import:', 'wp-seopress' ); ?></p>
                    <ul>
                        <li><?php _e('Title tags','wp-seopress'); ?></li>
                        <li><?php _e('Meta description','wp-seopress'); ?></li>
                        <li><?php _e('Facebook Open Graph tags (title, description and image thumbnail)','wp-seopress'); ?></li>
                        <li><?php _e('Twitter tags (title, description and image thumbnail)','wp-seopress'); ?></li>
                        <li><?php _e('Meta Robots (noindex, nofollow, noarchive)','wp-seopress'); ?></li>
                        <li><?php _e('Canonical URL','wp-seopress'); ?></li>
                        <li><?php _e('Redirect URL','wp-seopress'); ?></li>
                    </ul>
                    <p style="color:red"><span class="dashicons dashicons-warning"></span> <?php _e( '<strong>WARNING:</strong> Migration will update / delete all SEOPress posts and terms metadata. Some dynamic variables will not be interpreted. We do NOT delete any SEO Framework data.', 'wp-seopress' ); ?></p>
                    <button id="seopress-seo-framework-migrate" class="button"><?php _e('Migrate now','wp-seopress'); ?></button>
                    <span class="spinner"></span>
                    <div class="log"></div>
                </div><!-- .postbox -->

                <!-- RK import tool --> 
                <div id="rk-migration-tool" class="postbox section-tool seopress-wizard-services">
                    <h3><span><?php _e( 'Import posts and terms metadata from Rank Math', 'wp-seopress' ); ?></span></h3>
                    <p><?php _e( 'By clicking Migrate, we\'ll import:', 'wp-seopress' ); ?></p>
                    <ul>
                        <li><?php _e('Title tags','wp-seopress'); ?></li>
                        <li><?php _e('Meta description','wp-seopress'); ?></li>
                        <li><?php _e('Facebook Open Graph tags (title, description and image thumbnail)','wp-seopress'); ?></li>
                        <li><?php _e('Twitter tags (title, description and image thumbnail)','wp-seopress'); ?></li>
                        <li><?php _e('Meta Robots (noindex, nofollow, noarchive, noimageindex)','wp-seopress'); ?></li>
                        <li><?php _e('Canonical URL','wp-seopress'); ?></li>
                        <li><?php _e('Focus keywords','wp-seopress'); ?></li>
                    </ul>
                    <p style="color:red"><span class="dashicons dashicons-warning"></span> <?php _e( '<strong>WARNING:</strong> Migration will update / delete all SEOPress posts and terms metadata. Some dynamic variables will not be interpreted. We do NOT delete any Rank Math data.', 'wp-seopress' ); ?></p>
                    <button id="seopress-rk-migrate" class="button"><?php _e('Migrate now','wp-seopress'); ?></button>
                    <span class="spinner"></span>
                    <div class="log"></div>
                </div><!-- .postbox -->
            </div>
			
			<p class="seopress-setup-actions step">
				<button type="submit" class="button-primary button button-large button-next" value="<?php esc_attr_e( "Next step", 'wp-seopress' ); ?>" name="save_step"><?php esc_html_e( "Next step", 'wp-seopress' ); ?></button>
				<?php wp_nonce_field( 'seopress-setup' ); ?>
			</p>
		</form>
		<?php
	}

	/**
	 * Save step 1 settings.
	 */
	public function seopress_setup_import_settings_save() {
		check_admin_referer( 'seopress-setup' );
		wp_safe_redirect( esc_url_raw( $this->get_next_step_link() ) );
		exit;
	}

	/**
	 * Init "Step 2: Your site".
	 */
	public function seopress_setup_site() {
			$seopress_titles_option = get_option( 'seopress_titles_option_name' );
			$seopress_social_option = get_option( 'seopress_social_option_name' );
			
			$site_sep = isset($seopress_titles_option['seopress_titles_sep']) ? $seopress_titles_option['seopress_titles_sep'] : NULL;
			$site_title = isset($seopress_titles_option['seopress_titles_home_site_title']) ? $seopress_titles_option['seopress_titles_home_site_title'] : NULL;
			$knowledge_type = isset($seopress_social_option['seopress_social_knowledge_type']) ? $seopress_social_option['seopress_social_knowledge_type'] : NULL;
			$knowledge_name = isset($seopress_social_option['seopress_social_knowledge_name']) ? $seopress_social_option['seopress_social_knowledge_name'] : NULL;
			$knowledge_img = isset($seopress_social_option['seopress_social_knowledge_img']) ? $seopress_social_option['seopress_social_knowledge_img'] : NULL;
			$knowledge_fb = isset($seopress_social_option['seopress_social_accounts_facebook']) ? $seopress_social_option['seopress_social_accounts_facebook'] : NULL;
			$knowledge_tw = isset($seopress_social_option['seopress_social_accounts_twitter']) ? $seopress_social_option['seopress_social_accounts_twitter'] : NULL;
			$knowledge_pin = isset($seopress_social_option['seopress_social_accounts_pinterest']) ? $seopress_social_option['seopress_social_accounts_pinterest'] : NULL;
			$knowledge_insta = isset($seopress_social_option['seopress_social_accounts_instagram']) ? $seopress_social_option['seopress_social_accounts_instagram'] : NULL;
			$knowledge_yt = isset($seopress_social_option['seopress_social_accounts_youtube']) ? $seopress_social_option['seopress_social_accounts_youtube'] : NULL;
			$knowledge_li = isset($seopress_social_option['seopress_social_accounts_linkedin']) ? $seopress_social_option['seopress_social_accounts_linkedin'] : NULL;
			$knowledge_mys = isset($seopress_social_option['seopress_social_accounts_myspace']) ? $seopress_social_option['seopress_social_accounts_myspace'] : NULL;
			$knowledge_sound = isset($seopress_social_option['seopress_social_accounts_soundcloud']) ? $seopress_social_option['seopress_social_accounts_soundcloud'] : NULL;
			$knowledge_tu = isset($seopress_social_option['seopress_social_accounts_tumblr']) ? $seopress_social_option['seopress_social_accounts_tumblr'] : NULL;
		?>

		<h1><?php esc_html_e( 'Your site', 'wp-seopress' ); ?></h1>
		<form method="post">
			<p><?php esc_html_e( 'To build title tags and knowledge graph for Google, you need to fill out the fields below to configure the general settings. ', 'wp-seopress' ); ?></p>

			<label class="location-prompt" for="site_sep"><?php esc_html_e( 'Separator', 'wp-seopress' ); ?></label>
			<input type="text" id="site_sep" class="location-input" name="site_sep" placeholder="<?php esc_html_e('eg: |', 'wp-seopress'); ?>" required value="<?php echo $site_sep; ?>" />

			<label class="location-prompt" for="site_title"><?php esc_html_e( 'Site title', 'wp-seopress' ); ?></label>
			<input type="text" id="site_title" class="location-input" name="site_title" placeholder="<?php esc_html_e('eg: My super website', 'wp-seopress'); ?>" required value="<?php echo $site_title; ?>" />

			<label class="location-prompt" for="knowledge_type"><?php esc_html_e('Person or organization','wp-seopress'); ?></label>
			<?php
			    echo '<select id="knowledge_type" name="knowledge_type" data-placeholder="'.esc_attr__( 'Choose a knowledge type', 'wp-seopress' ).'"	class="location-input wc-enhanced-select dropdown">';
			        echo ' <option '; 
			            if ('None' == $knowledge_type) echo 'selected="selected"'; 
			            echo ' value="none">'. __("None (will disable this feature)","wp-seopress") .'</option>';
			        echo ' <option '; 
			            if ('Person' == $knowledge_type) echo 'selected="selected"'; 
			            echo ' value="Person">'. __("Person","wp-seopress") .'</option>';
			        echo '<option '; 
			            if ('Organization' == $knowledge_type) echo 'selected="selected"'; 
			            echo ' value="Organization">'. __("Organization","wp-seopress") .'</option>';
			    echo '</select>';
			?>

			<label class="location-prompt" for="knowledge_name"><?php esc_html_e( 'Your name/organization', 'wp-seopress' ); ?></label>
			<input type="text" id="knowledge_name" class="location-input" name="knowledge_name" placeholder="<?php esc_html_e('eg: SEOPress', 'wp-seopress'); ?>" value="<?php echo $knowledge_name; ?>" />

			<label class="location-prompt" for="knowledge_img"><?php esc_html_e( 'Your photo/organization logo', 'wp-seopress' ); ?></label>
			<input type="text" id="knowledge_img" class="location-input" name="knowledge_img" placeholder="<?php esc_html_e('eg: https://www.example.com/logo.png', 'wp-seopress'); ?>" value="<?php echo $knowledge_img; ?>" />

			<label class="location-prompt" for="knowledge_fb"><?php esc_html_e( 'Facebook page URL', 'wp-seopress' ); ?></label>
			<input type="text" id="knowledge_fb" class="location-input" name="knowledge_fb" placeholder="<?php esc_html_e('eg: https://www.facebook.com/your-page','wp-seopress'); ?>" value="<?php echo $knowledge_fb; ?>" />

			<label class="location-prompt" for="knowledge_tw"><?php esc_html_e( 'Twitter Username', 'wp-seopress' ); ?></label>
			<input type="text" id="knowledge_tw" class="location-input" name="knowledge_tw" placeholder="<?php esc_html_e('eg: @wp_seopress', 'wp-seopress'); ?>" value="<?php echo $knowledge_tw; ?>" />

			<label class="location-prompt" for="knowledge_pin"><?php esc_html_e( 'Pinterest URL', 'wp-seopress' ); ?></label>
			<input type="text" id="knowledge_pin" class="location-input" name="knowledge_pin" placeholder="<?php esc_html_e('eg: https://pinterest.com/wpbuy/', 'wp-seopress'); ?>" value="<?php echo $knowledge_pin; ?>" />

			<label class="location-prompt" for="knowledge_insta"><?php esc_html_e( 'Instagram URL', 'wp-seopress' ); ?></label>
			<input type="text" id="knowledge_insta" class="location-input" name="knowledge_insta" placeholder="<?php esc_html_e('eg: https://www.instagram.com/wp_seopress/', 'wp-seopress'); ?>" value="<?php echo $knowledge_insta; ?>" />

			<label class="location-prompt" for="knowledge_yt"><?php esc_html_e( 'YouTube URL', 'wp-seopress' ); ?></label>
			<input type="text" id="knowledge_yt" class="location-input" name="knowledge_yt" placeholder="<?php esc_html_e('eg: https://www.youtube.com/SEOPress', 'wp-seopress'); ?>" value="<?php echo $knowledge_yt; ?>" />

			<label class="location-prompt" for="knowledge_li"><?php esc_html_e( 'LinkedIn URL', 'wp-seopress' ); ?></label>
			<input type="text" id="knowledge_li" class="location-input" name="knowledge_li" placeholder="<?php esc_html_e('eg: http://linkedin.com/company/seopress/', 'wp-seopress'); ?>" value="<?php echo $knowledge_li; ?>" />

			<label class="location-prompt" for="knowledge_mys"><?php esc_html_e( 'MySpace URL', 'wp-seopress' ); ?></label>
			<input type="text" id="knowledge_mys" class="location-input" name="knowledge_mys" placeholder="<?php esc_html_e('eg: https://myspace.com/your-page', 'wp-seopress'); ?>" value="<?php echo $knowledge_mys; ?>" />

			<label class="location-prompt" for="knowledge_sound"><?php esc_html_e( 'Soundcloud URL', 'wp-seopress' ); ?></label>
			<input type="text" id="knowledge_sound" class="location-input" name="knowledge_sound" placeholder="<?php esc_html_e('eg: https://soundcloud.com/michaelmccannmusic', 'wp-seopress'); ?>" value="<?php echo $knowledge_sound; ?>" />

			<label class="location-prompt" for="knowledge_tu"><?php esc_html_e( 'Tumblr URL', 'wp-seopress' ); ?></label>
			<input type="text" id="knowledge_tu" class="location-input" name="knowledge_tu" placeholder="<?php esc_html_e('eg: https://your-site.tumblr.com', 'wp-seopress'); ?>" value="<?php echo $knowledge_tu; ?>" />

			<p class="seopress-setup-actions step">
				<button type="submit" class="button-primary button button-large button-next" value="<?php esc_attr_e( 'Continue', 'wp-seopress' ); ?>" name="save_step"><?php esc_html_e( 'Continue', 'wp-seopress' ); ?></button>
				<?php wp_nonce_field( 'seopress-setup' ); ?>
			</p>
		</form>
		<?php
	}

	/**
	 * Save step 2 settings.
	 */
	public function seopress_setup_site_save() {
		check_admin_referer( 'seopress-setup' );
		
		//Get options
		$seopress_titles_option = get_option("seopress_titles_option_name");
		$seopress_social_option = get_option("seopress_social_option_name");

		//Titles
		$seopress_titles_option['seopress_titles_sep'] = isset( $_POST['site_sep'] ) ? sanitize_text_field( wp_unslash( $_POST['site_sep'] ) ) : '';
		$seopress_titles_option['seopress_titles_home_site_title'] = isset( $_POST['site_title'] ) ? sanitize_text_field( wp_unslash( $_POST['site_title'] ) ) : '';

		//Social
		$seopress_social_option['seopress_social_knowledge_type'] = isset( $_POST['knowledge_type'] ) ? esc_attr( wp_unslash( $_POST['knowledge_type'] ) ) : '';
		$seopress_social_option['seopress_social_knowledge_name'] = isset( $_POST['knowledge_name'] ) ? sanitize_text_field( wp_unslash( $_POST['knowledge_name'] ) ) : '';
		$seopress_social_option['seopress_social_knowledge_img'] = isset( $_POST['knowledge_img'] ) ? sanitize_text_field( wp_unslash( $_POST['knowledge_img'] ) ) : '';

		//Social accounts
		$seopress_social_option['seopress_social_accounts_facebook'] = isset( $_POST['knowledge_fb'] ) ? sanitize_text_field( wp_unslash( $_POST['knowledge_fb'] ) ) : '';
		$seopress_social_option['seopress_social_accounts_twitter'] = isset( $_POST['knowledge_tw'] ) ? sanitize_text_field( wp_unslash( $_POST['knowledge_tw'] ) ) : '';
		$seopress_social_option['seopress_social_accounts_pinterest'] = isset( $_POST['knowledge_pin'] ) ? sanitize_text_field( wp_unslash( $_POST['knowledge_pin'] ) ) : '';
		$seopress_social_option['seopress_social_accounts_instagram'] = isset( $_POST['knowledge_insta'] ) ? sanitize_text_field( wp_unslash( $_POST['knowledge_insta'] ) ) : '';
		$seopress_social_option['seopress_social_accounts_youtube'] = isset( $_POST['knowledge_yt'] ) ? sanitize_text_field( wp_unslash( $_POST['knowledge_yt'] ) ) : '';
		$seopress_social_option['seopress_social_accounts_linkedin'] = isset( $_POST['knowledge_li'] ) ? sanitize_text_field( wp_unslash( $_POST['knowledge_li'] ) ) : '';
		$seopress_social_option['seopress_social_accounts_myspace'] = isset( $_POST['knowledge_mys'] ) ? sanitize_text_field( wp_unslash( $_POST['knowledge_mys'] ) ) : '';
		$seopress_social_option['seopress_social_accounts_soundcloud'] = isset( $_POST['knowledge_sound'] ) ? sanitize_text_field( wp_unslash( $_POST['knowledge_sound'] ) ) : '';
		$seopress_social_option['seopress_social_accounts_tumblr'] = isset( $_POST['knowledge_tu'] ) ? sanitize_text_field( wp_unslash( $_POST['knowledge_tu'] ) ) : '';

		//Save options
		update_option( 'seopress_titles_option_name', $seopress_titles_option );
		update_option( 'seopress_social_option_name', $seopress_social_option );

		wp_safe_redirect( esc_url_raw( $this->get_next_step_link() ) );
		exit;
	}

	/**
	 *	Init "Step 3: Indexing Step".
	 */
	public function seopress_setup_indexing() {
			$seopress_titles_option = get_option( 'seopress_titles_option_name' );
		?>
		<h1><?php esc_html_e( 'Indexing', 'wp-seopress' ); ?></h1>
		<p><?php esc_html_e( 'Specify to the search engines what you want to be indexed or not. Default: index', 'wp-seopress' ); ?></p>
		<form method="post" class="seopress-wizard-indexing-form">
			<?php if(!empty(seopress_get_post_types())) { ?>
				<div class="seopress-wizard-services">
					<p>
						<?php _e('For which single post types, should indexing be disabled?','wp-seopress'); ?>
					</p>
					
					<ul>
						<?php
							//Post Types
							foreach (seopress_get_post_types() as $seopress_cpt_key => $seopress_cpt_value) {
								$seopress_titles_single_titles = isset($seopress_titles_option['seopress_titles_single_titles'][$seopress_cpt_key]['noindex']);

					            echo '<h3>'.$seopress_cpt_value->labels->name.' <em><small>['.$seopress_cpt_value->name.']</small></em></h3>';

					            //Single No-Index CPT
					            echo '<li class="recommended-item checkbox">';
					                echo '<input id="seopress_titles_single_cpt_noindex['.$seopress_cpt_key.']" name="seopress_titles_option_name[seopress_titles_single_titles]['.$seopress_cpt_key.'][noindex]" type="checkbox"';
					                if ('1' == $seopress_titles_single_titles) echo 'checked="yes"'; 
					                echo ' value="1"/>';
					                
					                echo '<label for="seopress_titles_single_cpt_noindex['.$seopress_cpt_key.']">'. __( 'Do not display this single post type in search engine results <strong>(noindex)</strong>', 'wp-seopress' ) .'</label>';
					            echo '</li>';
					        }
					    ?>
					</ul>
				</div>
			<?php } ?>

			<?php if(!empty(seopress_get_post_types())) { ?>
				<div class="seopress-wizard-services">
				    <p>
						<?php _e('For which post type archives, should indexing be disabled?','wp-seopress'); ?>
					</p>

					<ul>
						<?php
							foreach (seopress_get_post_types() as $seopress_cpt_key => $seopress_cpt_value) {
					            if (!in_array($seopress_cpt_key, array('post','page'))) {

					            	echo '<h3>'.$seopress_cpt_value->labels->name.' <em><small>['.$seopress_cpt_value->name.']</small></em></h2>';

					                //Archive No-Index CPT
				                    $seopress_titles_archive_titles = isset($seopress_titles_option['seopress_titles_archive_titles'][$seopress_cpt_key]['noindex']);      
				                    
				                    echo '<li class="recommended-item checkbox">';
					                    echo '<input id="seopress_titles_archive_cpt_noindex['.$seopress_cpt_key.']" name="seopress_titles_option_name[seopress_titles_archive_titles]['.$seopress_cpt_key.'][noindex]" type="checkbox"';
					                    if ('1' == $seopress_titles_archive_titles) echo 'checked="yes"'; 
					                    echo ' value="1"/>';
					                    
					                    echo '<label for="seopress_titles_archive_cpt_noindex['.$seopress_cpt_key.']">'. __( 'Do not display this post type archive in search engine results <strong>(noindex)</strong>', 'wp-seopress' ) .'</label>';
					                echo '</li>';
					            }
					        }
				        ?>
			    	</ul>
			    </div>
			<?php } ?>

			<?php if(!empty(seopress_get_taxonomies())) { ?>
			    <div class="seopress-wizard-services">
				    <p>
						<?php _e('For which taxonomy archives, should indexing be disabled?','wp-seopress'); ?>
					</p>

					<ul>
						<?php
					        //Archives
					        foreach (seopress_get_taxonomies() as $seopress_tax_key => $seopress_tax_value) {
					            $seopress_titles_tax_titles = isset($seopress_titles_option['seopress_titles_tax_titles'][$seopress_tax_key]['noindex']);

					            echo '<h3>'.$seopress_tax_value->labels->name.' <em><small>['.$seopress_tax_value->name.']</small></em></h2>';

					            //Tax No-Index
					            echo '<li class="recommended-item checkbox">';
					                echo '<input id="seopress_titles_tax_noindex['.$seopress_tax_key.']" name="seopress_titles_option_name[seopress_titles_tax_titles]['.$seopress_tax_key.'][noindex]" type="checkbox"';
					                if ('1' == $seopress_titles_tax_titles) echo 'checked="yes"'; 
					                echo ' value="1"/>';
					                
					                echo '<label for="seopress_titles_tax_noindex['.$seopress_tax_key.']">'. __( 'Do not display this taxonomy archive in search engine results <strong>(noindex)</strong>', 'wp-seopress' ) .'</label>';
					            echo '</li>';
					        }
			        	?>
		        	</ul>
		        </div>
		    <?php } ?>	

			<p class="seopress-setup-actions step">
				<button type="submit" class="button-primary button button-large button-next" value="<?php esc_attr_e( 'Continue', 'wp-seopress' ); ?>" name="save_step"><?php esc_html_e( 'Continue', 'wp-seopress' ); ?></button>
				<?php wp_nonce_field( 'seopress-setup' ); ?>
			</p>
		</form>
		<?php
	}

	/**
	 * Save Step 3 settings.
	 */
	public function seopress_setup_indexing_save() {
		check_admin_referer( 'seopress-setup' );

		//Get options
		$seopress_titles_option = get_option("seopress_titles_option_name");

		//Post Types noindex
		foreach (seopress_get_post_types() as $seopress_cpt_key => $seopress_cpt_value) {
			if (isset($_POST['seopress_titles_option_name']['seopress_titles_single_titles'][$seopress_cpt_key]['noindex'])) {
				$noindex = esc_attr(wp_unslash($_POST['seopress_titles_option_name']['seopress_titles_single_titles'][$seopress_cpt_key]['noindex']));
			} else {
				$noindex = NULL;
			}
			$seopress_titles_option['seopress_titles_single_titles'][$seopress_cpt_key]['noindex'] = $noindex;			
		}

		//Post Type archives noindex
		foreach (seopress_get_post_types() as $seopress_cpt_key => $seopress_cpt_value) {
			if (isset($_POST['seopress_titles_option_name']['seopress_titles_archive_titles'][$seopress_cpt_key]['noindex'])) {
				$noindex = esc_attr(wp_unslash($_POST['seopress_titles_option_name']['seopress_titles_archive_titles'][$seopress_cpt_key]['noindex']));
			} else {
				$noindex = NULL;
			}
			$seopress_titles_option['seopress_titles_archive_titles'][$seopress_cpt_key]['noindex'] = $noindex;
		}

		//Archives noindex
		foreach (seopress_get_taxonomies() as $seopress_tax_key => $seopress_tax_value) {
			if (isset($_POST['seopress_titles_option_name']['seopress_titles_tax_titles'][$seopress_tax_key]['noindex'])) {
				$noindex = esc_attr(wp_unslash($_POST['seopress_titles_option_name']['seopress_titles_tax_titles'][$seopress_tax_key]['noindex']));
			} else {
				$noindex = NULL;
			}
			$seopress_titles_option['seopress_titles_tax_titles'][$seopress_tax_key]['noindex'] = $noindex;
		}

		//Save options
		update_option( 'seopress_titles_option_name', $seopress_titles_option );

		wp_redirect( esc_url_raw( $this->get_next_step_link() ) );
		exit;
	}

	/**
	 *	Init "Step 4: Advanced Step".
	 */
	public function seopress_setup_advanced() {
			$seopress_titles_option = get_option( 'seopress_titles_option_name' );
			$author_noindex = isset($seopress_titles_option['seopress_titles_archives_author_noindex']);

			$seopress_advanced_option = get_option( 'seopress_advanced_option_name' );
        	$attachments_file = isset($seopress_advanced_option['seopress_advanced_advanced_attachments_file']);
        	$category_url = isset($seopress_advanced_option['seopress_advanced_advanced_category_url']);
		?>
		<h1><?php esc_html_e( 'Advanced options', 'wp-seopress' ); ?></h1>
		<form method="post">
			<ul class="seopress-wizard-services">
				<?php
					//Noindex on author archives
					echo '<li class="seopress-wizard-service-item checkbox">';
				        echo '<input id="author_noindex" class="location-input" name="author_noindex" type="checkbox"';
				        if ('1' == $author_noindex) echo 'checked="yes"'; 
				        echo ' value="1"/>';
				        
				        echo '<label for="author_noindex" class="location-prompt">'. __( 'Do not display author archives in search engine results <strong>(noindex)</strong>', 'wp-seopress' ) .'</label>';
				    echo '</li>';
				    echo '<li class="seopress-wizard-service-info">';
				    	_e('You only have one author on your site? Check this option to avoid duplicate content.','wp-seopress');
			        echo '</li>';

			        //Redirect attachment pages to URL
					echo '<li class="seopress-wizard-service-item checkbox">';
				        echo '<input id="attachments_file" class="location-input" name="attachments_file" type="checkbox"';
				        if ('1' == $attachments_file) echo 'checked="yes"'; 
				        echo ' value="1"/>';
				        
				        echo '<label for="attachments_file" class="location-prompt">'. __( 'Redirect attachment pages to their file URL (https://www.example.com/my-image-file.jpg)', 'wp-seopress' ) .'</label>';
				    echo '</li>';
				    echo '<li class="seopress-wizard-service-info">';
				    	_e('By default, SEOPress redirects your Attachment pages to the parent post. Optimize this by redirecting the user directly to the URL of the media file.','wp-seopress');
			        echo '</li>';

			        //Remove /category/ in URLs
					echo '<li class="seopress-wizard-service-item checkbox">';
				        echo '<input id="category_url" name="category_url" type="checkbox" class="location-input"';
				        if ('1' == $category_url) echo 'checked="yes"'; 
				        echo ' value="1"/>';
				        
				        echo '<label for="category_url" class="location-prompt">'. __( 'Remove /category/ in your permalinks', 'wp-seopress' ) .'</label>';
				    echo '</li>';
				    echo '<li class="seopress-wizard-service-info">';
				    	_e('Shorten your URLs by removing /category/ and improve your SEO.','wp-seopress');
			        echo '</li>';
		        ?>
	    	</ul>

			<p class="seopress-setup-actions step">
				<button type="submit" class="button-primary button button-large button-next" value="<?php esc_attr_e( 'Continue', 'wp-seopress' ); ?>" name="save_step"><?php esc_html_e( 'Continue', 'wp-seopress' ); ?></button>
				<?php wp_nonce_field( 'seopress-setup' ); ?>
			</p>
		</form>
		<?php
	}

	/**
	 * Save step 4 settings.
	 */
	public function seopress_setup_advanced_save() {
		check_admin_referer( 'seopress-setup' );

		//Get options
		$seopress_titles_option = get_option("seopress_titles_option_name");
		$seopress_advanced_option = get_option("seopress_advanced_option_name");

		//Author indexing
		$seopress_titles_option['seopress_titles_archives_author_noindex'] = isset( $_POST['author_noindex'] ) ? esc_attr(wp_unslash($_POST['author_noindex'])) : NULL;

		//Advanced
		$seopress_advanced_option['seopress_advanced_advanced_attachments_file'] = isset( $_POST['attachments_file'] ) ? esc_attr(wp_unslash($_POST['attachments_file'])) : NULL;
		$seopress_advanced_option['seopress_advanced_advanced_category_url'] = isset( $_POST['category_url'] ) ? esc_attr(wp_unslash($_POST['category_url'])) : NULL;

		//Save options
		update_option( 'seopress_titles_option_name', $seopress_titles_option );
		update_option( 'seopress_advanced_option_name', $seopress_advanced_option );

		wp_redirect( esc_url_raw( $this->get_next_step_link() ) );
		exit;
	}	

	/**
	 * Final step.
	 */
	public function seopress_setup_ready() {
		//Remove SEOPress notice
		$seopress_notices = get_option('seopress_notices');
		$seopress_notices['notice-wizard'] = "1";
		update_option('seopress_notices',$seopress_notices);
		
		//Flush permalinks
		flush_rewrite_rules();
		?>
		<h1><?php esc_html_e( "Your site is now ready for search engines!", 'wp-seopress' ); ?></h1>

		<?php if (get_option( 'seopress_pro_license_status' ) !='valid' && is_plugin_active('wp-seopress-pro/seopress-pro.php') && !is_multisite()) { ?>
			<div class="seopress-message seopress-newsletter">
				<h3 class="seopress-setup-actions step">
					<?php esc_html_e( "Welcome to SEOPress PRO!","wp-seopress"); ?>
				</h3>
				<p class="seopress-setup-actions step">
					<?php esc_html_e( "Please activate your license to receive automatic updates and get premium support.", 'wp-seopress' ); ?>
				</p>
				<p class="seopress-setup-actions step">
					<a class="button button-primary button-large" href="<?php echo admin_url( 'admin.php?page=seopress-license' ); ?>">
						<span class="dashicons dashicons-admin-network"></span>
						<?php _e('Activate License', 'wp-seopress'); ?>
					</a>
				</p>
			</div>
		<?php } else { ?>
			<div class="seopress-message seopress-newsletter">
				<h3 class="seopress-setup-actions step">
					<?php esc_html_e( "Go PRO with SEOPress PRO!","wp-seopress"); ?>
				</h3>
				<p class="seopress-setup-actions step">
					<?php esc_html_e( "When you upgrade to the PRO version, you get a lot of additional features, like automatic and manual schemas, Video Sitemap, WooCommerce enhancements, Analytics statistics in your Dashboard, breadcrumbs, redirections, and more.", 'wp-seopress' ); ?>
				</p>
				<p class="seopress-setup-actions step">
					<a class="button button-primary button-large" href="https://www.seopress.org/" target="_blank">
						<span class="dashicons dashicons-cart"></span>
						<?php _e('Buy SEOPress PRO - $39 / unlimited sites', 'wp-seopress'); ?>
					</a>
				</p>
			</div>
		<?php } ?>

		<ul class="seopress-wizard-next-steps">
			<li class="seopress-wizard-next-step-item">
				<div class="seopress-wizard-next-step-description">
					<p class="next-step-heading"><?php esc_html_e("Next step","wp-seopress"); ?></p>
					<h3 class="next-step-description"><?php esc_html_e("Create your XML sitemaps","wp-seopress"); ?></h3>
					<p class="next-step-extra-info"><?php esc_html_e("Build custom XML sitemaps to improve Google's crawling of your site.","wp-seopress"); ?></p>
				</div>
				<div class="seopress-wizard-next-step-action">
					<p class="seopress-setup-actions step">
						<a class="button button-primary button-large" href="<?php echo admin_url( 'admin.php?page=seopress-xml-sitemap' ); ?>">
							<?php esc_html_e("Configure your XML sitemaps","wp-seopress"); ?>
						</a>
					</p>
				</div>
			</li>
			<li class="seopress-wizard-additional-steps">
				<div class="seopress-wizard-next-step-description">
					<p class="next-step-heading"><?php esc_html_e( 'Follow us:', 'wp-seopress' ); ?></p>
				</div>
				<div class="seopress-wizard-next-step-action step">
					<ul class="recommended-step">
						<li class="recommended-item">
							<a href="https://www.facebook.com/seopresspro/" target="_blank">
								<span class="dashicons dashicons-facebook"></span>
								<?php _e('Like our Facebook page','wp-seopress'); ?>
							</a>
						</li>
						<li class="recommended-item">
							<a href="https://twitter.com/wp_seopress" target="_blank">
								<span class="dashicons dashicons-twitter"></span>
								<?php _e('Follow us on Twitter','wp-seopress'); ?>
							</a>
						</li>
						<li class="recommended-item">
							<a href="https://www.youtube.com/seopress" target="_blank">
								<span class="dashicons dashicons-video-alt3"></span>
								<?php _e('Watch our guided tour videos to learn more about SEOPress','wp-seopress'); ?>
							</a>
						</li>
						<li class="recommended-item">
							<a href="https://www.instagram.com/wp_seopress/" target="_blank">
								<span class="dashicons dashicons-instagram"></span>
								<?php _e('The off side of SEOPress','wp-seopress'); ?>
							</a>
						</li>
					</ul>
				</div>
			</li>
			<li class="seopress-wizard-additional-steps">
				<div class="seopress-wizard-next-step-description">
					<p class="next-step-heading"><?php esc_html_e( 'You can also:', 'wp-seopress' ); ?></p>
				</div>
				<div class="seopress-wizard-next-step-action step">
					<p class="seopress-setup-actions step">
						<a class="button button-large" href="<?php echo esc_url( admin_url() ); ?>">
							<?php esc_html_e( 'Visit Dashboard', 'wp-seopress' ); ?>
						</a>
						<a class="button button-large" href="<?php echo esc_url( admin_url( 'admin.php?page=seopress-option' ) ); ?>">
							<?php esc_html_e( 'Review Settings', 'wp-seopress' ); ?>
						</a>
						<a class="button button-large" href="<?php echo esc_url('https://www.seopress.org/support/?utm_source=plugin&utm_medium=wizard&utm_campaign=seopress'); ?>" target="_blank">
							<?php esc_html_e( 'Knowledge base', 'wp-seopress' ); ?>
						</a>
					</p>
				</div>
			</li>
		</ul>
		<?php
	}
}

new SEOPRESS_Admin_Setup_Wizard();