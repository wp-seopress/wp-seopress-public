<?php

defined( 'ABSPATH' ) or die( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

class seopress_options
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;
	
    /**
     * Start up
     */
    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ), 10 );
        add_action( 'admin_init', array( $this, 'page_init' ) );
    }
	
	public function activate() {
        update_option($this->seopress_options, $this->data);
    }

    public function deactivate() {
        delete_option($this->seopress_options);
    }

    /**
     * Add options page
     */
    public function add_plugin_page()
    {
        add_menu_page('SEOPress Option Page', 'SEOPress', 'manage_options', 'seopress-option', array( $this, 'create_admin_page' ), 'dashicons-admin-generic', 90);
        $seopress_titles_help_tab = add_submenu_page('seopress-option', __('Titles & Metas','wp-seopress'), __('Titles & Metas','wp-seopress'), 'manage_options', 'seopress-titles', array( $this, 'seopress_titles_page' ));
        add_submenu_page('seopress-option', __('XML Sitemap','wp-seopress'), __('XML Sitemap','wp-seopress'), 'manage_options', 'seopress-xml-sitemap', array( $this, 'seopress_xml_sitemap_page' ));
        add_submenu_page('seopress-option', __('Social','wp-seopress'), __('Social','wp-seopress'), 'manage_options', 'seopress-social', array( $this, 'seopress_social_page' ));
        add_submenu_page('seopress-option', __('Advanced','wp-seopress'), __('Advanced','wp-seopress'), 'manage_options', 'seopress-advanced', array( $this, 'seopress_advanced_page' ));
        add_submenu_page('seopress-option', __('Import / Export / Reset settings','wp-seopress'), __('Import / Export / Reset','wp-seopress'), 'manage_options', 'seopress-import-export', array( $this,'seopress_import_export_page'));

        function seopress_titles_help_tab() {
            $screen = get_current_screen();

            $seopress_titles_help_tab_content = '
                <ul>
                    <li><span>'.__('%%sitetitle%%','wp-seopress').'</span>'.__('Site Title','wp-seopress').'</li>
                    <li><span>'.__('%%tagline%%','wp-seopress').'</span>'.__('Tagline','wp-seopress').'</li>
                    <li><span>'.__('%%post_title%%','wp-seopress').'</span>'.__('Post Title (post, page, custom post type)','wp-seopress').'</li>
                    <li><span>'.__('%%post_excerpt%%','wp-seopress').'</span>'.__('Post excerpt','wp-seopress').'</li>
                    <li><span>'.__('%%post_date%%','wp-seopress').'</span>'.__('Post date','wp-seopress').'</li>
                    <li><span>'.__('%%post_author%%','wp-seopress').'</span>'.__('Post author','wp-seopress').'</li>
                    <li><span>'.__('%%_category_title%%','wp-seopress').'</span>'.__('Category title','wp-seopress').'</li>
                    <li><span>'.__('%%_category_description%%','wp-seopress').'</span>'.__('Category description','wp-seopress').'</li>
                    <li><span>'.__('%%tag_title%%','wp-seopress').'</span>'.__('Tag title','wp-seopress').'</li>
                    <li><span>'.__('%%tag_description%%','wp-seopress').'</span>'.__('Tag description','wp-seopress').'</li>
                    <li><span>'.__('%%term_title%%','wp-seopress').'</span>'.__('Term title','wp-seopress').'</li>
                    <li><span>'.__('%%term_description%%','wp-seopress').'</span>'.__('Term description','wp-seopress').'</li>
                    <li><span>'.__('%%search_keywords%%','wp-seopress').'</span>'.__('Search keywords','wp-seopress').'</li>
                    <li><span>'.__('%%current_pagination%%','wp-seopress').'</span>'.__('Current number page','wp-seopress').'</li>
                </ul>
            ';

            $screen->add_help_tab( array(
                'id'    => 'seopress_titles_help_tab',
                'title' => __('Templates variables'),
                'content'   => $seopress_titles_help_tab_content,
            ));
        }
        add_action('load-'.$seopress_titles_help_tab, 'seopress_titles_help_tab');
    }
    

    function seopress_titles_page(){
        $this->options = get_option( 'seopress_titles_option_name' );
        ?>
        <form method="post" action="options.php" class="seopress-option">
        <?php 
        if (get_option('blog_public') =='0') {
            echo '<div class="error notice is-dismissable">';
            echo '<p>'. __('Discourage search engines from indexing this site is <strong>ON!</strong> None of the following settings will be applied.','wp-seopress');
            echo ' <a href="'.admin_url("options-reading.php").'">'.__('Change this settings','wp-seopress').'</a></p>';
            echo '</div>';
        }
        global $wp_version, $title;
        $current_tab = '';
        $tag = version_compare( $wp_version, '4.4' ) >= 0 ? 'h1' : 'h2';
        echo '<'.$tag.'>'.$title.'</'.$tag.'>';
        settings_fields( 'seopress_titles_option_group' );
        ?>
        
         <div id="seopress-tabs" class="wrap">
         <?php 
            
            $plugin_settings_tabs = array(
                'tab_seopress_titles_home' => __( "Home", "wp-seopress" ), 
                'tab_seopress_titles_single' => __( "Single Post Types", "wp-seopress" ), 
                'tab_seopress_titles_tax' => __( "Taxonomies", "wp-seopress" ), 
                'tab_seopress_titles_archives' => __( "Archives", "wp-seopress" ), 
                'tab_seopress_titles_advanced' => __( "Advanced", "wp-seopress" ),
            );

            echo '<h2 class="nav-tab-wrapper">';
            foreach ( $plugin_settings_tabs as $tab_key => $tab_caption ) {
                echo '<a id="'. $tab_key .'-tab" class="nav-tab" href="?page=seopress-titles#tab=' . $tab_key . '">' . $tab_caption . '</a>'; 
            }
            echo '</h2>';
        ?>
            <div class="seopress-tab <?php if ($current_tab == 'tab_seopress_titles_home') { echo 'active'; } ?>" id="tab_seopress_titles_home"><?php do_settings_sections( 'seopress-settings-admin-titles-home' ); ?></div>
            <div class="seopress-tab <?php if ($current_tab == 'tab_seopress_titles_single') { echo 'active'; } ?>" id="tab_seopress_titles_single"><?php do_settings_sections( 'seopress-settings-admin-titles-single' ); ?></div>
            <div class="seopress-tab <?php if ($current_tab == 'tab_seopress_titles_tax') { echo 'active'; } ?>" id="tab_seopress_titles_tax"><?php do_settings_sections( 'seopress-settings-admin-titles-tax' ); ?></div>
            <div class="seopress-tab <?php if ($current_tab == 'tab_seopress_titles_archives') { echo 'active'; } ?>" id="tab_seopress_titles_archives"><?php do_settings_sections( 'seopress-settings-admin-titles-archives' ); ?></div>
            <div class="seopress-tab <?php if ($current_tab == 'tab_seopress_titles_advanced') { echo 'active'; } ?>" id="tab_seopress_titles_advanced"><?php do_settings_sections( 'seopress-settings-admin-titles-advanced' ); ?></div>
        </div>

        <?php submit_button(); ?>
        </form>
        <?php
    }

    function seopress_xml_sitemap_page(){
        $this->options = get_option( 'seopress_xml_sitemap_option_name' );
        ?>
        <form method="post" action="options.php" class="seopress-option">
        <?php 
        global $wp_version, $title;
        $current_tab = '';
        $tag = version_compare( $wp_version, '4.4' ) >= 0 ? 'h1' : 'h2';
        echo '<'.$tag.'>'.$title.'</'.$tag.'>';
        settings_fields( 'seopress_xml_sitemap_option_group' );
        ?>
        
        <div id="seopress-tabs" class="wrap">
         <?php 
            
            $plugin_settings_tabs = array(
                'tab_seopress_xml_sitemap_general' => __( "General", "wp-seopress" ), 
                'tab_seopress_xml_sitemap_post_types' => __( "Post Types", "wp-seopress" ), 
                'tab_seopress_xml_sitemap_taxonomies' => __( "Taxonomies", "wp-seopress" ), 
            );

            echo '<h2 class="nav-tab-wrapper">';
            foreach ( $plugin_settings_tabs as $tab_key => $tab_caption ) {
                echo '<a id="'. $tab_key .'-tab" class="nav-tab" href="?page=seopress-xml-sitemap#tab=' . $tab_key . '">' . $tab_caption . '</a>'; 
            }
            echo '</h2>';
        ?>
            <div class="seopress-tab <?php if ($current_tab == 'tab_seopress_xml_sitemap_general') { echo 'active'; } ?>" id="tab_seopress_xml_sitemap_general"><?php do_settings_sections( 'seopress-settings-admin-xml-sitemap-general' ); ?></div>
            <div class="seopress-tab <?php if ($current_tab == 'tab_seopress_xml_sitemap_post_types') { echo 'active'; } ?>" id="tab_seopress_xml_sitemap_post_types"><?php do_settings_sections( 'seopress-settings-admin-xml-sitemap-post-types' ); ?></div>
            <div class="seopress-tab <?php if ($current_tab == 'tab_seopress_xml_sitemap_taxonomies') { echo 'active'; } ?>" id="tab_seopress_xml_sitemap_taxonomies"><?php do_settings_sections( 'seopress-settings-admin-xml-sitemap-taxonomies' ); ?></div>
        </div>
        
        <?php submit_button(); ?>
        </form>
        <?php
    }

    function seopress_social_page(){
        $this->options = get_option( 'seopress_social_option_name' );
        ?>
        <form method="post" action="options.php" class="seopress-option">
        <?php 
        global $wp_version, $title;
        $current_tab = '';
        $tag = version_compare( $wp_version, '4.4' ) >= 0 ? 'h1' : 'h2';
        echo '<'.$tag.'>'.$title.'</'.$tag.'>';
        settings_fields( 'seopress_social_option_group' );
        ?>
    
         <div id="seopress-tabs" class="wrap">
         <?php 
            
            $plugin_settings_tabs = array(
                'tab_seopress_social_knowledge' => __( "Knowledge Graph", "wp-seopress" ), 
                'tab_seopress_social_accounts' => __( "Your social accounts", "wp-seopress" ), 
                'tab_seopress_social_facebook' => __( "Facebook", "wp-seopress" ), 
                'tab_seopress_social_twitter' => __( "Twitter", "wp-seopress" ), 
            );

            echo '<h2 class="nav-tab-wrapper">';
            foreach ( $plugin_settings_tabs as $tab_key => $tab_caption ) {
                echo '<a id="'. $tab_key .'-tab" class="nav-tab" href="?page=seopress-social#tab=' . $tab_key . '">' . $tab_caption . '</a>'; 
            }
            echo '</h2>';
        ?>
            <div class="seopress-tab <?php if ($current_tab == 'tab_seopress_social_knowledge') { echo 'active'; } ?>" id="tab_seopress_social_knowledge"><?php do_settings_sections( 'seopress-settings-admin-social-knowledge' ); ?></div>
            <div class="seopress-tab <?php if ($current_tab == 'tab_seopress_social_accounts') { echo 'active'; } ?>" id="tab_seopress_social_accounts"><?php do_settings_sections( 'seopress-settings-admin-social-accounts' ); ?></div>
            <div class="seopress-tab <?php if ($current_tab == 'tab_seopress_social_facebook') { echo 'active'; } ?>" id="tab_seopress_social_facebook"><?php do_settings_sections( 'seopress-settings-admin-social-facebook' ); ?></div>
            <div class="seopress-tab <?php if ($current_tab == 'tab_seopress_social_twitter') { echo 'active'; } ?>" id="tab_seopress_social_twitter"><?php do_settings_sections( 'seopress-settings-admin-social-twitter' ); ?></div>
        </div>

        <?php submit_button(); ?>
        </form>
        <?php
    }

    function seopress_advanced_page(){
        $this->options = get_option( 'seopress_advanced_option_name' );
        ?>
        <form method="post" action="options.php" class="seopress-option">
        <?php 
        
        global $wp_version, $title;
        $current_tab = '';
        $tag = version_compare( $wp_version, '4.4' ) >= 0 ? 'h1' : 'h2';
        echo '<'.$tag.'>'.$title.'</'.$tag.'>';
        settings_fields( 'seopress_advanced_option_group' );
        ?>
    
         <div id="seopress-tabs" class="wrap">
         <?php 
            
            $plugin_settings_tabs = array(
                'tab_seopress_advanced_advanced' => __( "Advanced", "wp-seopress" ), 
            );

            echo '<h2 class="nav-tab-wrapper">';
            foreach ( $plugin_settings_tabs as $tab_key => $tab_caption ) {
                echo '<a id="'. $tab_key .'-tab" class="nav-tab" href="?page=seopress-advanced#tab=' . $tab_key . '">' . $tab_caption . '</a>'; 
            }
            echo '</h2>';
        ?>
            <div class="seopress-tab <?php if ($current_tab == 'tab_seopress_advanced_advanced') { echo 'active'; } ?>" id="tab_seopress_advanced_advanced"><?php do_settings_sections( 'seopress-settings-admin-advanced-advanced' ); ?></div>
        </div>

        <?php submit_button(); ?>
        </form>
        <?php
    }

    function seopress_import_export_page(){
        $this->options = get_option( 'seopress_import_export_option_name' );
        ?>
        <?php global $wp_version, $title;
        $tag = version_compare( $wp_version, '4.4' ) >= 0 ? 'h1' : 'h2';
        echo '<'.$tag.'>'.$title.'</'.$tag.'>';
        ?>
        <h3><span><?php _e( 'Import / Export Settings', 'wp-seopress' ); ?></span></h3>
        <?php print __('Import / Export SEOPress settings from site to site', 'wp-seopress'); ?>
        <div class="metabox-holder">
            <div class="postbox">
                <h3><span><?php _e( 'Export Settings', 'wp-seopress' ); ?></span></h3>
                <div class="inside">
                    <p><?php _e( 'Export the plugin settings for this site as a .json file. This allows you to easily import the configuration into another site.', 'wp-seopress' ); ?></p>
                    <form method="post">
                        <p><input type="hidden" name="seopress_action" value="export_settings" /></p>
                        <p>
                            <?php wp_nonce_field( 'seopress_export_nonce', 'seopress_export_nonce' ); ?>
                            <?php submit_button( __( 'Export', 'wp-seopress' ), 'secondary', 'submit', false ); ?>
                        </p>
                    </form>
                </div><!-- .inside -->
            </div><!-- .postbox -->

            <div class="postbox">
                <h3><span><?php _e( 'Import Settings', 'wp-seopress' ); ?></span></h3>
                <div class="inside">
                    <p><?php _e( 'Import the plugin settings from a .json file. This file can be obtained by exporting the settings on another site using the form above.', 'wp-seopress' ); ?></p>
                    <form method="post" enctype="multipart/form-data">
                        <p>
                            <input type="file" name="import_file"/>
                        </p>
                        <p>
                            <input type="hidden" name="seopress_action" value="import_settings" />
                            <?php wp_nonce_field( 'seopress_import_nonce', 'seopress_import_nonce' ); ?>
                            <?php submit_button( __( 'Import', 'wp-seopress' ), 'secondary', 'submit', false ); ?>
                        </p>
                    </form>
                </div><!-- .inside -->
            </div><!-- .postbox -->

            <div class="postbox">
                <h3><span><?php _e( 'Reset All Settings', 'wp-seopress' ); ?></span></h3>
                <div class="inside">
                    <p style="color:red"><?php _e( '<strong>WARNING:</strong> Delete all options related to SEOPress in your database.', 'wp-seopress' ); ?></p>
                     <form method="post" enctype="multipart/form-data">
                        <p>
                            <input type="hidden" name="seopress_action" value="reset_settings" />
                            <?php wp_nonce_field( 'seopress_reset_nonce', 'seopress_reset_nonce' ); ?>
                            <?php submit_button( __( 'Reset settings', 'wp-seopress' ), 'secondary', 'submit', false ); ?>
                        </p>
                    </form>
                </div><!-- .inside -->
            </div><!-- .postbox -->
        </div><!-- .metabox-holder -->
    <?php
    }

    /**
     * Options page callback
     */
    public function create_admin_page()
    {
	
        // Set class property
        $this->options = get_option( 'seopress_option_name' );
        ?>
            <div id="seopress-header">
				<div id="seopress-admin">
                    <div id="seopress-navbar">
    					<h1>
    						<span class="screen-reader-text"><?php _e( 'SEOPress', 'wp-seopress' ); ?></span>
                            <?php if ( is_plugin_active( 'seopress-pro/seopress-pro.php' ) ) { ?>
                                <span class="seopress-info-version">
                                    <strong>
                                        <?php _e('PRO', 'wp-seopress'); ?>
                                        <?php echo SEOPRESSPRO_VERSION; ?>
                                    </strong>
                                </span>
                            <?php } else { ?>
                                <span class="seopress-info-version"><?php echo SEOPRESS_VERSION; ?></span>
                            <?php } ?>
    					</h1>
                        <div id="seopress-notice">
                            <div class="small">
                                <span class="dashicons dashicons-wordpress"></span>
                                <?php _e( 'You like SEOPress? Don\'t forget to rate it 5 stars!', 'wp-seopress' ); ?>

                                <div class="wporg-ratings rating-stars">
                                    <a href="//wordpress.org/support/view/plugin-reviews/seopress?rate=1#postform" data-rating="1" title="" target="_blank"><span class="dashicons dashicons-star-filled" style="color:#e6b800 !important;"></span></a>
                                    <a href="//wordpress.org/support/view/plugin-reviews/seopress?rate=2#postform" data-rating="2" title="" target="_blank"><span class="dashicons dashicons-star-filled" style="color:#e6b800 !important;"></span></a>
                                    <a href="//wordpress.org/support/view/plugin-reviews/seopress?rate=3#postform" data-rating="3" title="" target="_blank"><span class="dashicons dashicons-star-filled" style="color:#e6b800 !important;"></span></a>
                                    <a href="//wordpress.org/support/view/plugin-reviews/seopress?rate=4#postform" data-rating="4" title="" target="_blank"><span class="dashicons dashicons-star-filled" style="color:#e6b800 !important;"></span></a>
                                    <a href="//wordpress.org/support/view/plugin-reviews/seopress?rate=5#postform" data-rating="5" title="" target="_blank"><span class="dashicons dashicons-star-filled" style="color:#e6b800 !important;"></span></a>
                                </div>
                                <script>
                                    jQuery(document).ready( function($) {
                                        $('.rating-stars').find('a').hover(
                                            function() {
                                                $(this).nextAll('a').children('span').removeClass('dashicons-star-filled').addClass('dashicons-star-empty');
                                                $(this).prevAll('a').children('span').removeClass('dashicons-star-empty').addClass('dashicons-star-filled');
                                                $(this).children('span').removeClass('dashicons-star-empty').addClass('dashicons-star-filled');
                                            }, function() {
                                                var rating = $('input#rating').val();
                                                if (rating) {
                                                    var list = $('.rating-stars a');
                                                    list.children('span').removeClass('dashicons-star-filled').addClass('dashicons-star-empty');
                                                    list.slice(0, rating).children('span').removeClass('dashicons-star-empty').addClass('dashicons-star-filled');
                                                }
                                            }
                                        );
                                    });
                                </script>
                            </div>
                            <div class="small">
                                <a href="http://twitter.com/wpcloudy" target="_blank"><div class="dashicons dashicons-twitter"></div></a>
                                <a href="http://www.seopress.org/" target="_blank"><div class="dashicons dashicons-info"></div></a>
                                &nbsp;
                                <a href="http://www.seopress.org/support" target="_blank"><?php _e( 'Support', 'wp-seopress' ); ?></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="seopress-content">
                <?php if (get_option('blog_public') !='1' || seopress_xml_sitemap_general_enable_option() !='1') { ?>
                    <div id="seopress-notifications-center">
                        <h2><span class="dashicons dashicons-flag"></span><?php _e('Notifications center','wp-seopress'); ?></h2>
                        <?php if (get_option('blog_public') !='1') { ?>
                            <div class="seopress-alert">
                                <p><span class="dashicons dashicons-warning"></span><?php _e('Your site is not visible to Search Engines!','wp-seopress'); ?></p>
                                <a class="button-primary" href="<?php echo admin_url( 'options-reading.php' ); ?>"><?php _e('Fix this!','wp-seopress'); ?></a>
                            </div>
                        <?php } ?>
                        <?php if (seopress_xml_sitemap_general_enable_option() !='1') { ?>
                            <div class="seopress-alert">
                                <p><span class="dashicons dashicons-warning"></span><?php _e('You don\'t have an XML Sitemap!','wp-seopress'); ?></p>
                                <a class="button-primary" href="<?php echo admin_url( 'admin.php?page=seopress-xml-sitemap' ); ?>"><?php _e('Fix this!','wp-seopress'); ?></a>
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>
                <div class="seopress-page-list">
                    <div class="seopress-feature">
                        <div class="img-tool">
                            <span class="dashicons dashicons-editor-table"></span>
                        </div>
                        <span class="inner">
                            <h4><?php _e('Titles & metas','wp-seopress'); ?></h4>
                            <p><?php _e('Manage all your titles & metas','wp-seopress'); ?></p>
                            <a class="button-secondary" href="<?php echo admin_url( 'admin.php?page=seopress-titles' ); ?>"><?php _e('Manage','wp-seopress'); ?></a>
                        </span>
                    </div>
                    <div class="seopress-feature">
                        <div class="img-tool">
                            <span class="dashicons dashicons-media-spreadsheet"></span>
                        </div>
                        <span class="inner">
                            <h4><?php _e('XML Sitemap','wp-seopress'); ?></h4>
                            <p><?php _e('Manage your XML Sitemap','wp-seopress'); ?></p>
                            <a class="button-secondary" href="<?php echo admin_url( 'admin.php?page=seopress-xml-sitemap' ); ?>"><?php _e('Manage','wp-seopress'); ?></a>
                        </span>
                    </div>
                    <div class="seopress-feature">
                        <div class="img-tool">
                            <span class="dashicons dashicons-share"></span>
                        </div>
                        <span class="inner">
                            <h4><?php _e('Social','wp-seopress'); ?></h4>
                            <p><?php _e('Open Graph, Twitter Card, Google Knowledge Graph and more...','wp-seopress'); ?></p>
                            <a class="button-secondary" href="<?php echo admin_url( 'admin.php?page=seopress-social' ); ?>"><?php _e('Manage','wp-seopress'); ?></a>
                        </span>
                    </div>
                    <div class="seopress-feature">
                        <div class="img-tool">
                            <span class="dashicons dashicons-admin-tools"></span>                                  
                        </div>
                        <span class="inner">
                            <h4><?php _e('Advanced','wp-seopress'); ?></h4>
                            <p><?php _e('Advanced SEO options for advanced users!','wp-seopress'); ?></p>
                            <a class="button-secondary" href="<?php echo admin_url( 'admin.php?page=seopress-advanced' ); ?>"><?php _e('Manage','wp-seopress'); ?></a>
                        </span>
                    </div>
                    <div class="seopress-feature">
                        <div class="img-tool">
                            <span class="dashicons dashicons-admin-settings"></span>                                   
                        </div>
                        <span class="inner">
                            <h4><?php _e('Import / Export / Reset','wp-seopress'); ?></h4>
                            <p><?php _e('Import / export SEOPress settings from site to site.','wp-seopress'); ?></p>
                            <a class="button-secondary" href="<?php echo admin_url( 'admin.php?page=seopress-import-export' ); ?>"><?php _e('Manage','wp-seopress'); ?></a>
                        </span>
                    </div>
                </div>
			</div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function page_init()
    {        
        register_setting(
            'seopress_option_group', // Option group
            'seopress_option_name', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        register_setting(
            'seopress_titles_option_group', // Option group
            'seopress_titles_option_name', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        register_setting(
            'seopress_xml_sitemap_option_group', // Option group
            'seopress_xml_sitemap_option_name', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        register_setting(
            'seopress_social_option_group', // Option group
            'seopress_social_option_name', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        register_setting(
            'seopress_advanced_option_group', // Option group
            'seopress_advanced_option_name', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        register_setting(
            'seopress_import_export_option_group', // Option group
            'seopress_import_export_option_name', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

		//Titles & metas SECTION===================================================================
        add_settings_section( 
            'seopress_setting_section_titles_home', // ID
            '',
            //__("Home","wp-seopress"), // Title
            array( $this, 'print_section_info_titles' ), // Callback
            'seopress-settings-admin-titles-home' // Page
        );  

        add_settings_field(
            'seopress_titles_home_site_title', // ID
           __("Site title","wp-seopress"), // Title
            array( $this, 'seopress_titles_home_site_title_callback' ), // Callback
            'seopress-settings-admin-titles-home', // Page
            'seopress_setting_section_titles_home' // Section                  
        );

        add_settings_field(
            'seopress_titles_home_site_desc', // ID
           __("Meta description","wp-seopress"), // Title
            array( $this, 'seopress_titles_home_site_desc_callback' ), // Callback
            'seopress-settings-admin-titles-home', // Page
            'seopress_setting_section_titles_home' // Section                  
        );

        //Single Post Types SECTION================================================================
        add_settings_section( 
            'seopress_setting_section_titles_single', // ID
            '',
            //__("Single Post Types","wp-seopress"), // Title
            array( $this, 'print_section_info_single' ), // Callback
            'seopress-settings-admin-titles-single' // Page
        );  

        add_settings_field(
            'seopress_titles_single_titles', // ID
            '',
            array( $this, 'seopress_titles_single_titles_callback' ), // Callback
            'seopress-settings-admin-titles-single', // Page
            'seopress_setting_section_titles_single' // Section                  
        );

        //Taxonomies SECTION=======================================================================
        add_settings_section( 
            'seopress_setting_section_titles_tax', // ID
            '',
            //__("Taxonomies","wp-seopress"), // Title
            array( $this, 'print_section_info_tax' ), // Callback
            'seopress-settings-admin-titles-tax' // Page
        );  

        add_settings_field(
            'seopress_titles_tax_titles', // ID
            '',
            array( $this, 'seopress_titles_tax_titles_callback' ), // Callback
            'seopress-settings-admin-titles-tax', // Page
            'seopress_setting_section_titles_tax' // Section                  
        );

        //Archives SECTION=========================================================================
        add_settings_section( 
            'seopress_setting_section_titles_archives', // ID
            '',
            //__("Archives","wp-seopress"), // Title
            array( $this, 'print_section_info_archives' ), // Callback
            'seopress-settings-admin-titles-archives' // Page
        );  

        add_settings_field(
            'seopress_titles_archives_author_title', // ID
            '',
            //__('Title template','wp-seopress'),
            array( $this, 'seopress_titles_archives_author_title_callback' ), // Callback
            'seopress-settings-admin-titles-archives', // Page
            'seopress_setting_section_titles_archives' // Section                  
        );

        add_settings_field(
            'seopress_titles_archives_author_desc', // ID
            '',
            //__('Meta description template','wp-seopress'),
            array( $this, 'seopress_titles_archives_author_desc_callback' ), // Callback
            'seopress-settings-admin-titles-archives', // Page
            'seopress_setting_section_titles_archives' // Section                  
        );

        add_settings_field(
            'seopress_titles_archives_date_title', // ID
            '',
            //__('Title template','wp-seopress'),
            array( $this, 'seopress_titles_archives_date_title_callback' ), // Callback
            'seopress-settings-admin-titles-archives', // Page
            'seopress_setting_section_titles_archives' // Section                  
        );

        add_settings_field(
            'seopress_titles_archives_date_desc', // ID
            '',
            //__('Meta description template','wp-seopress'),
            array( $this, 'seopress_titles_archives_date_desc_callback' ), // Callback
            'seopress-settings-admin-titles-archives', // Page
            'seopress_setting_section_titles_archives' // Section                  
        );

        add_settings_field(
            'seopress_titles_archives_search_title', // ID
            '',
            //__('Title template','wp-seopress'),
            array( $this, 'seopress_titles_archives_search_title_callback' ), // Callback
            'seopress-settings-admin-titles-archives', // Page
            'seopress_setting_section_titles_archives' // Section                  
        );

        add_settings_field(
            'seopress_titles_archives_search_desc', // ID
            '',
            //__('Meta description template','wp-seopress'),
            array( $this, 'seopress_titles_archives_search_desc_callback' ), // Callback
            'seopress-settings-admin-titles-archives', // Page
            'seopress_setting_section_titles_archives' // Section                  
        );

        add_settings_field(
            'seopress_titles_archives_404_title', // ID
            '',
            //__('Title template','wp-seopress'),
            array( $this, 'seopress_titles_archives_404_title_callback' ), // Callback
            'seopress-settings-admin-titles-archives', // Page
            'seopress_setting_section_titles_archives' // Section                  
        );

        add_settings_field(
            'seopress_titles_archives_404_desc', // ID
            '',
            //__('Meta description template','wp-seopress'),
            array( $this, 'seopress_titles_archives_404_desc_callback' ), // Callback
            'seopress-settings-admin-titles-archives', // Page
            'seopress_setting_section_titles_archives' // Section                  
        );        

        add_settings_field(
            'seopress_titles_archives_paged_title', // ID
            '',
            //__('Title template','wp-seopress'),
            array( $this, 'seopress_titles_archives_paged_title_callback' ), // Callback
            'seopress-settings-admin-titles-archives', // Page
            'seopress_setting_section_titles_archives' // Section                  
        );

        add_settings_field(
            'seopress_titles_archives_paged_desc', // ID
            '',
            //__('Meta description template','wp-seopress'),
            array( $this, 'seopress_titles_archives_paged_desc_callback' ), // Callback
            'seopress-settings-admin-titles-archives', // Page
            'seopress_setting_section_titles_archives' // Section                  
        );

        //Advanced SECTION=========================================================================
        add_settings_section( 
            'seopress_setting_section_titles_advanced', // ID
            '',
            //__("Advanced","wp-seopress"), // Title
            array( $this, 'print_section_info_advanced' ), // Callback
            'seopress-settings-admin-titles-advanced' // Page
        ); 

        add_settings_field(
            'seopress_titles_noindex', // ID
           __("noindex","wp-seopress"), // Title
            array( $this, 'seopress_titles_noindex_callback' ), // Callback
            'seopress-settings-admin-titles-advanced', // Page
            'seopress_setting_section_titles_advanced' // Section                  
        );

        add_settings_field(
            'seopress_titles_nofollow', // ID
           __("nofollow","wp-seopress"), // Title
            array( $this, 'seopress_titles_nofollow_callback' ), // Callback
            'seopress-settings-admin-titles-advanced', // Page
            'seopress_setting_section_titles_advanced' // Section                  
        );

        add_settings_field(
            'seopress_titles_noodp', // ID
           __("noodp","wp-seopress"), // Title
            array( $this, 'seopress_titles_noodp_callback' ), // Callback
            'seopress-settings-admin-titles-advanced', // Page
            'seopress_setting_section_titles_advanced' // Section                  
        );

        add_settings_field(
            'seopress_titles_noimageindex', // ID
           __("noimageindex","wp-seopress"), // Title
            array( $this, 'seopress_titles_noimageindex_callback' ), // Callback
            'seopress-settings-admin-titles-advanced', // Page
            'seopress_setting_section_titles_advanced' // Section                  
        );

        add_settings_field(
            'seopress_titles_noarchive', // ID
           __("noarchive","wp-seopress"), // Title
            array( $this, 'seopress_titles_noarchive_callback' ), // Callback
            'seopress-settings-admin-titles-advanced', // Page
            'seopress_setting_section_titles_advanced' // Section                  
        );

        add_settings_field(
            'seopress_titles_nosnippet', // ID
           __("nosnippet","wp-seopress"), // Title
            array( $this, 'seopress_titles_nosnippet_callback' ), // Callback
            'seopress-settings-admin-titles-advanced', // Page
            'seopress_setting_section_titles_advanced' // Section                  
        );

        //XML Sitemap SECTION======================================================================
        add_settings_section( 
            'seopress_setting_section_xml_sitemap_general', // ID
            '',
            //__("General","wp-seopress"), // Title
            array( $this, 'print_section_info_xml_sitemap_general' ), // Callback
            'seopress-settings-admin-xml-sitemap-general' // Page
        ); 

        add_settings_field(
            'seopress_xml_sitemap_general_enable', // ID
           __("Enable XML Sitemap","wp-seopress"), // Title
            array( $this, 'seopress_xml_sitemap_general_enable_callback' ), // Callback
            'seopress-settings-admin-xml-sitemap-general', // Page
            'seopress_setting_section_xml_sitemap_general' // Section                  
        );

        add_settings_section( 
            'seopress_setting_section_xml_sitemap_post_types', // ID
            '',
            //__("Post Types","wp-seopress"), // Title
            array( $this, 'print_section_info_xml_sitemap_post_types' ), // Callback
            'seopress-settings-admin-xml-sitemap-post-types' // Page
        ); 

        add_settings_field(
            'seopress_xml_sitemap_post_types_list', // ID
           __("Check to INCLUDE Post Types","wp-seopress"), // Title
            array( $this, 'seopress_xml_sitemap_post_types_list_callback' ), // Callback
            'seopress-settings-admin-xml-sitemap-post-types', // Page
            'seopress_setting_section_xml_sitemap_post_types' // Section                  
        );

        add_settings_section( 
            'seopress_setting_section_xml_sitemap_taxonomies', // ID
            '',
            //__("Taxonomies","wp-seopress"), // Title
            array( $this, 'print_section_info_xml_sitemap_taxonomies' ), // Callback
            'seopress-settings-admin-xml-sitemap-taxonomies' // Page
        ); 

        add_settings_field(
            'seopress_xml_sitemap_taxonomies_list', // ID
           __("Check to INCLUDE Taxonomies","wp-seopress"), // Title
            array( $this, 'seopress_xml_sitemap_taxonomies_list_callback' ), // Callback
            'seopress-settings-admin-xml-sitemap-taxonomies', // Page
            'seopress_setting_section_xml_sitemap_taxonomies' // Section                  
        );

        //Knowledge graph SECTION======================================================================
        add_settings_section( 
            'seopress_setting_section_social_knowledge', // ID
            '',
            //__("Knowledge graph","wp-seopress"), // Title
            array( $this, 'print_section_info_social_knowledge' ), // Callback
            'seopress-settings-admin-social-knowledge' // Page
        ); 

        add_settings_field(
            'seopress_social_knowledge_type', // ID
           __("Person or organization","wp-seopress"), // Title
            array( $this, 'seopress_social_knowledge_type_callback' ), // Callback
            'seopress-settings-admin-social-knowledge', // Page
            'seopress_setting_section_social_knowledge' // Section                  
        );

        add_settings_field(
            'seopress_social_knowledge_name', // ID
           __("Your name/organization","wp-seopress"), // Title
            array( $this, 'seopress_social_knowledge_name_callback' ), // Callback
            'seopress-settings-admin-social-knowledge', // Page
            'seopress_setting_section_social_knowledge' // Section                  
        );

        add_settings_field(
            'seopress_social_knowledge_img', // ID
           __("Your photo / organization logo","wp-seopress"), // Title
            array( $this, 'seopress_social_knowledge_img_callback' ), // Callback
            'seopress-settings-admin-social-knowledge', // Page
            'seopress_setting_section_social_knowledge' // Section                  
        );

        //Social SECTION=====================================================================================
        add_settings_section( 
            'seopress_setting_section_social_accounts', // ID
            '',
            //__("Social","wp-seopress"), // Title
            array( $this, 'print_section_info_social_accounts' ), // Callback
            'seopress-settings-admin-social-accounts' // Page
        ); 

        add_settings_field(
            'seopress_social_accounts_facebook', // ID
           __("Facebook Page URL","wp-seopress"), // Title
            array( $this, 'seopress_social_accounts_facebook_callback' ), // Callback
            'seopress-settings-admin-social-accounts', // Page
            'seopress_setting_section_social_accounts' // Section                  
        );

        add_settings_field(
            'seopress_social_accounts_twitter', // ID
           __("Twitter Username","wp-seopress"), // Title
            array( $this, 'seopress_social_accounts_twitter_callback' ), // Callback
            'seopress-settings-admin-social-accounts', // Page
            'seopress_setting_section_social_accounts' // Section                  
        );

        add_settings_field(
            'seopress_social_accounts_google', // ID
           __("Google + URL","wp-seopress"), // Title
            array( $this, 'seopress_social_accounts_google_callback' ), // Callback
            'seopress-settings-admin-social-accounts', // Page
            'seopress_setting_section_social_accounts' // Section                  
        );

        add_settings_field(
            'seopress_social_accounts_pinterest', // ID
           __("Pinterest URL","wp-seopress"), // Title
            array( $this, 'seopress_social_accounts_pinterest_callback' ), // Callback
            'seopress-settings-admin-social-accounts', // Page
            'seopress_setting_section_social_accounts' // Section                  
        );

        add_settings_field(
            'seopress_social_accounts_instagram', // ID
           __("Instagram URL","wp-seopress"), // Title
            array( $this, 'seopress_social_accounts_instagram_callback' ), // Callback
            'seopress-settings-admin-social-accounts', // Page
            'seopress_setting_section_social_accounts' // Section                  
        );

        add_settings_field(
            'seopress_social_accounts_youtube', // ID
           __("YouTube URL","wp-seopress"), // Title
            array( $this, 'seopress_social_accounts_youtube_callback' ), // Callback
            'seopress-settings-admin-social-accounts', // Page
            'seopress_setting_section_social_accounts' // Section                  
        );

        add_settings_field(
            'seopress_social_accounts_linkedin', // ID
           __("LinkedIn URL","wp-seopress"), // Title
            array( $this, 'seopress_social_accounts_linkedin_callback' ), // Callback
            'seopress-settings-admin-social-accounts', // Page
            'seopress_setting_section_social_accounts' // Section                  
        );

        add_settings_field(
            'seopress_social_accounts_myspace', // ID
           __("MySpace URL","wp-seopress"), // Title
            array( $this, 'seopress_social_accounts_myspace_callback' ), // Callback
            'seopress-settings-admin-social-accounts', // Page
            'seopress_setting_section_social_accounts' // Section                  
        );

        add_settings_field(
            'seopress_social_accounts_soundcloud', // ID
           __("Soundcloud URL","wp-seopress"), // Title
            array( $this, 'seopress_social_accounts_soundcloud_callback' ), // Callback
            'seopress-settings-admin-social-accounts', // Page
            'seopress_setting_section_social_accounts' // Section                  
        );

        add_settings_field(
            'seopress_social_accounts_tumblr', // ID
           __("Tumblr URL","wp-seopress"), // Title
            array( $this, 'seopress_social_accounts_tumblr_callback' ), // Callback
            'seopress-settings-admin-social-accounts', // Page
            'seopress_setting_section_social_accounts' // Section                  
        );

        //Facebook SECTION=========================================================================
        add_settings_section( 
            'seopress_setting_section_social_facebook', // ID
            '',
            //__("Facebook","wp-seopress"), // Title
            array( $this, 'print_section_info_social_facebook' ), // Callback
            'seopress-settings-admin-social-facebook' // Page
        );

        add_settings_field(
            'seopress_social_facebook_og', // ID
           __("Enable Open Graph Data","wp-seopress"), // Title
            array( $this, 'seopress_social_facebook_og_callback' ), // Callback
            'seopress-settings-admin-social-facebook', // Page
            'seopress_setting_section_social_facebook' // Section                  
        );

        add_settings_field(
            'seopress_social_facebook_img', // ID
           __("Select a default image","wp-seopress"), // Title
            array( $this, 'seopress_social_facebook_img_callback' ), // Callback
            'seopress-settings-admin-social-facebook', // Page
            'seopress_setting_section_social_facebook' // Section                  
        );

        add_settings_field(
            'seopress_social_facebook_admin_id', // ID
           __("Facebook Admin ID","wp-seopress"), // Title
            array( $this, 'seopress_social_facebook_admin_id_callback' ), // Callback
            'seopress-settings-admin-social-facebook', // Page
            'seopress_setting_section_social_facebook' // Section                  
        );

        add_settings_field(
            'seopress_social_facebook_app_id', // ID
           __("Facebook App ID","wp-seopress"), // Title
            array( $this, 'seopress_social_facebook_app_id_callback' ), // Callback
            'seopress-settings-admin-social-facebook', // Page
            'seopress_setting_section_social_facebook' // Section                  
        );

        //Twitter SECTION==========================================================================
        add_settings_section( 
            'seopress_setting_section_social_twitter', // ID
            '',
            //__("Twitter","wp-seopress"), // Title
            array( $this, 'print_section_info_social_twitter' ), // Callback
            'seopress-settings-admin-social-twitter' // Page
        );

        add_settings_field(
            'seopress_social_twitter_card', // ID
           __("Enable Twitter Card","wp-seopress"), // Title
            array( $this, 'seopress_social_twitter_card_callback' ), // Callback
            'seopress-settings-admin-social-twitter', // Page
            'seopress_setting_section_social_twitter' // Section                  
        );     

        add_settings_field(
            'seopress_social_twitter_card_img', // ID
           __("Default Twitter Image","wp-seopress"), // Title
            array( $this, 'seopress_social_twitter_card_img_callback' ), // Callback
            'seopress-settings-admin-social-twitter', // Page
            'seopress_setting_section_social_twitter' // Section                  
        );   

        add_settings_field(
            'seopress_social_twitter_card_img_size', // ID
           __("Image size for Twitter Summary card","wp-seopress"), // Title
            array( $this, 'seopress_social_twitter_card_img_size_callback' ), // Callback
            'seopress-settings-admin-social-twitter', // Page
            'seopress_setting_section_social_twitter' // Section                  
        );
        //Advanced SECTION=========================================================================
        add_settings_section( 
            'seopress_setting_section_advanced_advanced', // ID
            '',
            //__("Advanced","wp-seopress"), // Title
            array( $this, 'print_section_info_advanced_advanced' ), // Callback
            'seopress-settings-admin-advanced-advanced' // Page
        ); 

        add_settings_field(
            'seopress_advanced_advanced_attachments', // ID
           __("Redirect attachment pages to post parent (if none, redirect to homepage)","wp-seopress"), // Title
            array( $this, 'seopress_advanced_advanced_attachments_callback' ), // Callback
            'seopress-settings-admin-advanced-advanced', // Page
            'seopress_setting_section_advanced_advanced' // Section                  
        );

        add_settings_field(
            'seopress_advanced_advanced_stop_words', // ID
           __("Remove stop words from URL","wp-seopress"), // Title
            array( $this, 'seopress_advanced_advanced_stop_words_callback' ), // Callback
            'seopress-settings-admin-advanced-advanced', // Page
            'seopress_setting_section_advanced_advanced' // Section                  
        );

        add_settings_field(
            'seopress_advanced_advanced_google', // ID
           __("Google site verification","wp-seopress"), // Title
            array( $this, 'seopress_advanced_advanced_google_callback' ), // Callback
            'seopress-settings-admin-advanced-advanced', // Page
            'seopress_setting_section_advanced_advanced' // Section                  
        );

        add_settings_field(
            'seopress_advanced_advanced_bing', // ID
           __("Bing site verification","wp-seopress"), // Title
            array( $this, 'seopress_advanced_advanced_bing_callback' ), // Callback
            'seopress-settings-admin-advanced-advanced', // Page
            'seopress_setting_section_advanced_advanced' // Section                  
        );        

        add_settings_field(
            'seopress_advanced_advanced_pinterest', // ID
           __("Pinterest site verification","wp-seopress"), // Title
            array( $this, 'seopress_advanced_advanced_pinterest_callback' ), // Callback
            'seopress-settings-admin-advanced-advanced', // Page
            'seopress_setting_section_advanced_advanced' // Section                  
        );

        add_settings_field(
            'seopress_advanced_advanced_yandex', // ID
           __("Yandex site verification","wp-seopress"), // Title
            array( $this, 'seopress_advanced_advanced_yandex_callback' ), // Callback
            'seopress-settings-admin-advanced-advanced', // Page
            'seopress_setting_section_advanced_advanced' // Section                  
        );         
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input )
    {	

        $seopress_sanitize_fields = array('seopress_titles_home_site_title', 'seopress_titles_home_site_desc', 'seopress_titles_archives_author_title', 'seopress_titles_archives_author_desc', 'seopress_titles_archives_date_title', 'seopress_titles_archives_date_desc', 'seopress_titles_archives_search_title', 'seopress_titles_archives_search_desc', 'seopress_titles_archives_404_title', 'seopress_titles_archives_404_desc', 'seopress_titles_archives_paged_title', 'seopress_titles_archives_paged_desc', 'seopress_social_knowledge_name', 'seopress_social_knowledge_img', 'seopress_social_accounts_facebook', 'seopress_social_accounts_twitter', 'seopress_social_accounts_google', 'seopress_social_accounts_pinterest', 'seopress_social_accounts_instagram', 'seopress_social_accounts_youtube', 'seopress_social_accounts_linkedin', 'seopress_social_accounts_myspace', 'seopress_social_accounts_soundcloud', 'seopress_social_accounts_tumblr', 'seopress_social_facebook_admin_id', 'seopress_social_facebook_app_id' );

        $seopress_sanitize_site_verification = array('seopress_advanced_advanced_google', 'seopress_advanced_advanced_bing', 'seopress_advanced_advanced_pinterest', 'seopress_advanced_advanced_yandex' );
        
        foreach ($seopress_sanitize_fields as $value) {
            if( !empty( $input[$value] ) )
                $input[$value] = sanitize_text_field( $input[$value] );
        }
        
        foreach ($seopress_sanitize_site_verification as $value) {
            if( !empty( $input[$value] ) ) {
                if (preg_match('#content=\'([^"]+)\'#', $input[$value], $m)) {
                    $input[$value] = $m[1];
                } elseif (preg_match('#content="([^"]+)"#', $input[$value], $m)) {
                    $input[$value] = $m[1];
                }
            }
                
        }

        return $input;
    }

    /** 
     * Print the Section text
     */
	 
    public function print_section_info_titles()
    {
        print __('<p>Customize your titles & metas for homepage</p>', 'wp-seopress');
    }	

    public function print_section_info_single()
    {
        print __('<p>Customize your titles & metas for Single Custom Post Types</p>', 'wp-seopress');
    }

    public function print_section_info_advanced()
    {
        print __('<p>Customize your metas for all pages</p>', 'wp-seopress');
    }    

    public function print_section_info_tax()
    {
        print __('<p>Customize your metas for all taxonomies archives</p>', 'wp-seopress');
    }    

    public function print_section_info_archives()
    {
        print __('<p>Customize your metas for all archives</p>', 'wp-seopress');
    }     

    public function print_section_info_xml_sitemap_general()
    {
        print __('<p>Enable your Sitemap</p>', 'wp-seopress');
        echo __('To view your sitemap, enable permalinks (not default one), and save settings to flush them.', 'wp-seopress');
        echo '<br>';
        echo __('Only the last 1000 items are listed in Sitemaps for performances issues.', 'wp-seopress');
        echo '<br>';
        echo '<br>';
        echo '<a href="'.home_url().'/sitemaps/" target="_blank" class="button">'.__('View your sitemap','wp-seopress').'</a>';
    } 

    public function print_section_info_xml_sitemap_post_types()
    {
        print __('<p>Include / Exclude Post Types</p>', 'wp-seopress');
    }

    public function print_section_info_xml_sitemap_taxonomies()
    {
        print __('<p>Include / Exclude Taxonomies</p>', 'wp-seopress');
    }    

    public function print_section_info_social_knowledge()
    {
        print __('<p>Configure Google Knowledge Graph</p>', 'wp-seopress');
    }     

    public function print_section_info_social_accounts()
    {
        print __('<p>Link your site with your social accounts</p>', 'wp-seopress');
    }    

    public function print_section_info_social_facebook()
    {
        print __('<p>Manage Open Graph datas</p>', 'wp-seopress');
    }    

    public function print_section_info_social_twitter()
    {
        print __('<p>Manage your Twitter card</p>', 'wp-seopress');
    }

    public function print_section_info_advanced_advanced()
    {
        print __('<p>Advanced SEO options</p>', 'wp-seopress');
    }    

    /** 
     * Get the settings option array and print one of its values
     */
	
    //Titles & metas
    public function seopress_titles_home_site_title_callback()
    {
        printf(
        '<input type="text" name="seopress_titles_option_name[seopress_titles_home_site_title]" placeholder="'.__('My awesome website','wp-seopress').'" value="%s"/>',
        esc_html( $this->options['seopress_titles_home_site_title'])
        
        );
        
    }

    public function seopress_titles_home_site_desc_callback()
    {
        printf(
        '<textarea name="seopress_titles_option_name[seopress_titles_home_site_desc]" placeholder="'.__('This is cool website about Wookiees','wp-seopress').'">%s</textarea>',
        esc_html( $this->options['seopress_titles_home_site_desc'])
        
        );
        
    } 

    //Single CPT
    public function seopress_titles_single_titles_callback()
    {
        foreach (seopress_get_post_types() as $seopress_cpt_key => $seopress_cpt_value) {
            
            echo '<h2>'.$seopress_cpt_value->labels->name.'</h2>';

            //Single Title CPT
            echo '<div class="seopress_wrap_single_cpt">';

                _e('Title template','wp-seopress');
                echo "<br/>";

                printf(
                '<input type="text" name="seopress_titles_option_name[seopress_titles_single_titles]['.$seopress_cpt_key.'][title]" value="%s"/>',
                esc_html( $this->options['seopress_titles_single_titles'][$seopress_cpt_key]['title'])   
                );

            echo '</div>';

            //Single Meta Description CPT
            echo '<div class="seopress_wrap_single_cpt">';
                
                _e('Meta description template','wp-seopress');
                echo "<br/>";

                printf(
                '<textarea name="seopress_titles_option_name[seopress_titles_single_titles]['.$seopress_cpt_key.'][description]">%s</textarea>',
                esc_html( $this->options['seopress_titles_single_titles'][$seopress_cpt_key]['description'])
                );

            echo '</div>';

            //Single No-Index CPT
            echo '<div class="seopress_wrap_single_cpt">';

                $options = get_option( 'seopress_titles_option_name' );  
            
                $check = isset($options['seopress_titles_single_titles'][$seopress_cpt_key]['noindex']);      
                
                echo '<input id="seopress_titles_single_cpt_noindex['.$seopress_cpt_key.']" name="seopress_titles_option_name[seopress_titles_single_titles]['.$seopress_cpt_key.'][noindex]" type="checkbox"';
                if ('1' == $check) echo 'checked="yes"'; 
                echo ' value="1"/>';
                
                echo '<label for="seopress_titles_single_cpt_noindex['.$seopress_cpt_key.']">'. __( 'noindex', 'wp-seopress' ) .'</label>';
                
                if (isset($this->options['seopress_titles_single_titles'][$seopress_cpt_key]['noindex'])) {
                    esc_attr( $this->options['seopress_titles_single_titles'][$seopress_cpt_key]['noindex']);
                }

            echo '</div>';

            //Single No-Follow CPT
            echo '<div class="seopress_wrap_single_cpt">';

                $options = get_option( 'seopress_titles_option_name' );  
            
                $check = isset($options['seopress_titles_single_titles'][$seopress_cpt_key]['nofollow']);      
                
                echo '<input id="seopress_titles_single_cpt_nofollow['.$seopress_cpt_key.']" name="seopress_titles_option_name[seopress_titles_single_titles]['.$seopress_cpt_key.'][nofollow]" type="checkbox"';
                if ('1' == $check) echo 'checked="yes"'; 
                echo ' value="1"/>';
                
                echo '<label for="seopress_titles_single_cpt_nofollow['.$seopress_cpt_key.']">'. __( 'nofollow', 'wp-seopress' ) .'</label>';
                
                if (isset($this->options['seopress_titles_single_titles'][$seopress_cpt_key]['nofollow'])) {
                    esc_attr( $this->options['seopress_titles_single_titles'][$seopress_cpt_key]['nofollow']);
                }

            echo '</div>';

            //Single Published / modified date CPT
            echo '<div class="seopress_wrap_single_cpt">';

                $options = get_option( 'seopress_titles_option_name' );  
            
                $check = isset($options['seopress_titles_single_titles'][$seopress_cpt_key]['date']);      
                
                echo '<input id="seopress_titles_single_cpt_date['.$seopress_cpt_key.']" name="seopress_titles_option_name[seopress_titles_single_titles]['.$seopress_cpt_key.'][date]" type="checkbox"';
                if ('1' == $check) echo 'checked="yes"'; 
                echo ' value="1"/>';
                
                echo '<label for="seopress_titles_single_cpt_date['.$seopress_cpt_key.']">'. __( 'Display date in Google search results?', 'wp-seopress' ) .'</label>';
                
                if (isset($this->options['seopress_titles_single_titles'][$seopress_cpt_key]['date'])) {
                    esc_attr( $this->options['seopress_titles_single_titles'][$seopress_cpt_key]['date']);
                }

            echo '</div>';
        }
    }

    //Taxonomies
    public function seopress_titles_tax_titles_callback()
    {
        foreach (seopress_get_taxonomies() as $seopress_tax_key => $seopress_tax_value) {
            
            echo '<h2>'.$seopress_tax_value->labels->name.'</h2>';

            //Tax Title
            echo '<div class="seopress_wrap_tax">';

                _e('Title template','wp-seopress');
                echo "<br/>";

                printf(
                '<input type="text" name="seopress_titles_option_name[seopress_titles_tax_titles]['.$seopress_tax_key.'][title]" value="%s"/>',
                esc_html( $this->options['seopress_titles_tax_titles'][$seopress_tax_key]['title'])   
                );

            echo '</div>';

            //Tax Meta Description
            echo '<div class="seopress_wrap_tax">';
                
                _e('Meta description template','wp-seopress');
                echo "<br/>";

                printf(
                '<textarea name="seopress_titles_option_name[seopress_titles_tax_titles]['.$seopress_tax_key.'][description]">%s</textarea>',
                esc_html( $this->options['seopress_titles_tax_titles'][$seopress_tax_key]['description'])
                );

            echo '</div>';

            //Tax No-Index
            echo '<div class="seopress_wrap_tax">';

                $options = get_option( 'seopress_titles_option_name' );  
            
                $check = isset($options['seopress_titles_tax_titles'][$seopress_tax_key]['noindex']);      
                
                echo '<input id="seopress_titles_tax_noindex['.$seopress_tax_key.']" name="seopress_titles_option_name[seopress_titles_tax_titles]['.$seopress_tax_key.'][noindex]" type="checkbox"';
                if ('1' == $check) echo 'checked="yes"'; 
                echo ' value="1"/>';
                
                echo '<label for="seopress_titles_tax_noindex['.$seopress_tax_key.']">'. __( 'noindex', 'wp-seopress' ) .'</label>';
                
                if (isset($this->options['seopress_titles_tax_titles'][$seopress_tax_key]['noindex'])) {
                    esc_attr( $this->options['seopress_titles_tax_titles'][$seopress_tax_key]['noindex']);
                }

            echo '</div>';

            //Tax No-Follow
            echo '<div class="seopress_wrap_tax">';

                $options = get_option( 'seopress_titles_option_name' );  
            
                $check = isset($options['seopress_titles_tax_titles'][$seopress_tax_key]['nofollow']);      
                
                echo '<input id="seopress_titles_tax_nofollow['.$seopress_tax_key.']" name="seopress_titles_option_name[seopress_titles_tax_titles]['.$seopress_tax_key.'][nofollow]" type="checkbox"';
                if ('1' == $check) echo 'checked="yes"'; 
                echo ' value="1"/>';
                
                echo '<label for="seopress_titles_tax_nofollow['.$seopress_tax_key.']">'. __( 'nofollow', 'wp-seopress' ) .'</label>';
                
                if (isset($this->options['seopress_titles_tax_titles'][$seopress_tax_key]['nofollow'])) {
                    esc_attr( $this->options['seopress_titles_tax_titles'][$seopress_tax_key]['nofollow']);
                }

            echo '</div>';
        }
    }

    //Archives
    public function seopress_titles_archives_author_title_callback()
    {
        echo '<h2>'.__('Author archives','wp-seopress').'</h2>';
        
        _e('Title template','wp-seopress');
        echo "<br/>";
        
        printf(
        '<input type="text" name="seopress_titles_option_name[seopress_titles_archives_author_title]" value="%s"/>',
        esc_html( $this->options['seopress_titles_archives_author_title'])
        
        );
        
    }

    public function seopress_titles_archives_author_desc_callback()
    {
        _e('Meta description template','wp-seopress');
        echo "<br/>";

        printf(
        '<textarea name="seopress_titles_option_name[seopress_titles_archives_author_desc]">%s</textarea>',
        esc_html( $this->options['seopress_titles_archives_author_desc'])
        
        );
        
    }

    public function seopress_titles_archives_date_title_callback()
    {
        echo '<h2>'.__('Date archives','wp-seopress').'</h2>';
        
        _e('Title template','wp-seopress');
        echo "<br/>";

        printf(
        '<input type="text" name="seopress_titles_option_name[seopress_titles_archives_date_title]" value="%s"/>',
        esc_html( $this->options['seopress_titles_archives_date_title'])
        
        );
        
    }

    public function seopress_titles_archives_date_desc_callback()
    {        
        _e('Meta description template','wp-seopress');
        echo "<br/>";

        printf(
        '<textarea name="seopress_titles_option_name[seopress_titles_archives_date_desc]">%s</textarea>',
        esc_html( $this->options['seopress_titles_archives_date_desc'])
        
        );
        
    }

    public function seopress_titles_archives_search_title_callback()
    {
        echo '<h2>'.__('Search archives','wp-seopress').'</h2>';
        
        _e('Title template','wp-seopress');
        echo "<br/>";

        printf(
        '<input type="text" name="seopress_titles_option_name[seopress_titles_archives_search_title]" value="%s"/>',
        esc_html( $this->options['seopress_titles_archives_search_title'])
        
        );
        
    }

    public function seopress_titles_archives_search_desc_callback()
    {        
        _e('Meta description template','wp-seopress');
        echo "<br/>";

        printf(
        '<textarea name="seopress_titles_option_name[seopress_titles_archives_search_desc]">%s</textarea>',
        esc_html( $this->options['seopress_titles_archives_search_desc'])
        
        );
        
    }

    public function seopress_titles_archives_404_title_callback()
    {
        echo '<h2>'.__('404 archives','wp-seopress').'</h2>';
        
        _e('Title template','wp-seopress');
        echo "<br/>";

        printf(
        '<input type="text" name="seopress_titles_option_name[seopress_titles_archives_404_title]" value="%s"/>',
        esc_html( $this->options['seopress_titles_archives_404_title'])
        
        );
        
    }

    public function seopress_titles_archives_404_desc_callback()
    {        
        _e('Meta description template','wp-seopress');
        echo "<br/>";

        printf(
        '<textarea name="seopress_titles_option_name[seopress_titles_archives_404_desc]">%s</textarea>',
        esc_html( $this->options['seopress_titles_archives_404_desc'])
        
        );
        
    }

    public function seopress_titles_archives_paged_title_callback()
    {
        echo '<h2>'.__('Paged archives','wp-seopress').'</h2>';
        
        _e('Title template','wp-seopress');
        echo "<br/>";

        printf(
        '<input type="text" name="seopress_titles_option_name[seopress_titles_archives_paged_title]" value="%s"/>',
        esc_html( $this->options['seopress_titles_archives_paged_title'])
        
        );
        
    }

    public function seopress_titles_archives_paged_desc_callback()
    {        
        _e('Meta description template','wp-seopress');
        echo "<br/>";

        printf(
        '<textarea name="seopress_titles_option_name[seopress_titles_archives_paged_desc]">%s</textarea>',
        esc_html( $this->options['seopress_titles_archives_paged_desc'])
        
        );
        
    }

    //Advanced
    public function seopress_titles_noindex_callback()
    {
        $options = get_option( 'seopress_titles_option_name' );  
        
        $check = isset($options['seopress_titles_noindex']);      
        
        echo '<input id="seopress_titles_noindex" name="seopress_titles_option_name[seopress_titles_noindex]" type="checkbox"';
        if ('1' == $check) echo 'checked="yes"'; 
        echo ' value="1"/>';
        
        echo '<label for="seopress_titles_noindex">'. __( 'noindex', 'wp-seopress' ) .'</label>';
        
        if (isset($this->options['seopress_titles_noindex'])) {
            esc_attr( $this->options['seopress_titles_noindex']);
        }
    }

    public function seopress_titles_nofollow_callback()
    {
        $options = get_option( 'seopress_titles_option_name' );  
        
        $check = isset($options['seopress_titles_nofollow']);      
        
        echo '<input id="seopress_titles_nofollow" name="seopress_titles_option_name[seopress_titles_nofollow]" type="checkbox"';
        if ('1' == $check) echo 'checked="yes"'; 
        echo ' value="1"/>';
        
        echo '<label for="seopress_titles_nofollow">'. __( 'nofollow', 'wp-seopress' ) .'</label>';
        
        if (isset($this->options['seopress_titles_nofollow'])) {
            esc_attr( $this->options['seopress_titles_nofollow']);
        }
    }

    public function seopress_titles_noodp_callback()
    {
        $options = get_option( 'seopress_titles_option_name' );  
        
        $check = isset($options['seopress_titles_noodp']);      
        
        echo '<input id="seopress_titles_noodp" name="seopress_titles_option_name[seopress_titles_noodp]" type="checkbox"';
        if ('1' == $check) echo 'checked="yes"'; 
        echo ' value="1"/>';
        
        echo '<label for="seopress_titles_noodp">'. __( 'noodp', 'wp-seopress' ) .'</label>';
        
        if (isset($this->options['seopress_titles_noodp'])) {
            esc_attr( $this->options['seopress_titles_noodp']);
        }
    }

    public function seopress_titles_noimageindex_callback()
    {
        $options = get_option( 'seopress_titles_option_name' );  
        
        $check = isset($options['seopress_titles_noimageindex']);      
        
        echo '<input id="seopress_titles_noimageindex" name="seopress_titles_option_name[seopress_titles_noimageindex]" type="checkbox"';
        if ('1' == $check) echo 'checked="yes"'; 
        echo ' value="1"/>';
        
        echo '<label for="seopress_titles_noimageindex">'. __( 'noimageindex', 'wp-seopress' ) .'</label>';
        
        if (isset($this->options['seopress_titles_noimageindex'])) {
            esc_attr( $this->options['seopress_titles_noimageindex']);
        }
    }

    public function seopress_titles_noarchive_callback()
    {
        $options = get_option( 'seopress_titles_option_name' );  
        
        $check = isset($options['seopress_titles_noarchive']);      
        
        echo '<input id="seopress_titles_noarchive" name="seopress_titles_option_name[seopress_titles_noarchive]" type="checkbox"';
        if ('1' == $check) echo 'checked="yes"'; 
        echo ' value="1"/>';
        
        echo '<label for="seopress_titles_noarchive">'. __( 'noarchive', 'wp-seopress' ) .'</label>';
        
        if (isset($this->options['seopress_titles_noarchive'])) {
            esc_attr( $this->options['seopress_titles_noarchive']);
        }
    }

    public function seopress_titles_nosnippet_callback()
    {
        $options = get_option( 'seopress_titles_option_name' );  
        
        $check = isset($options['seopress_titles_nosnippet']);      
        
        echo '<input id="seopress_titles_nosnippet" name="seopress_titles_option_name[seopress_titles_nosnippet]" type="checkbox"';
        if ('1' == $check) echo 'checked="yes"'; 
        echo ' value="1"/>';
        
        echo '<label for="seopress_titles_nosnippet">'. __( 'nosnippet', 'wp-seopress' ) .'</label>';
        
        if (isset($this->options['seopress_titles_nosnippet'])) {
            esc_attr( $this->options['seopress_titles_nosnippet']);
        }
    }

    public function seopress_xml_sitemap_general_enable_callback()
    {
        $options = get_option( 'seopress_xml_sitemap_option_name' );  
        
        $check = isset($options['seopress_xml_sitemap_general_enable']);      
        
        echo '<input id="seopress_xml_sitemap_general_enable" name="seopress_xml_sitemap_option_name[seopress_xml_sitemap_general_enable]" type="checkbox"';
        if ('1' == $check) echo 'checked="yes"'; 
        echo ' value="1"/>';
        
        echo '<label for="seopress_xml_sitemap_general_enable">'. __( 'Enable XML Sitemap', 'wp-seopress' ) .'</label>';
        
        if (isset($this->options['seopress_xml_sitemap_general_enable'])) {
            esc_attr( $this->options['seopress_xml_sitemap_general_enable']);
        }
    }

    public function seopress_xml_sitemap_post_types_list_callback()
    {
        $options = get_option( 'seopress_xml_sitemap_option_name' );  
        
        $check = isset($options['seopress_xml_sitemap_post_types_list']);      
        
        global $wp_post_types;

        $args = array(
            'show_ui' => true,
        );

        $output = 'objects'; // names or objects, note names is the default
        $operator = 'and'; // 'and' or 'or'

        $post_types = get_post_types( $args, $output, $operator ); 

        foreach ($post_types as $seopress_cpt_key => $seopress_cpt_value) {
            
            echo '<h2>'.$seopress_cpt_value->labels->name.'</h2>';

            //List all post types
            echo '<div class="seopress_wrap_single_cpt">';

                $options = get_option( 'seopress_xml_sitemap_option_name' );  
                
                $check = isset($options['seopress_xml_sitemap_post_types_list'][$seopress_cpt_key]['include']);      
                
                echo '<input id="seopress_xml_sitemap_post_types_list_include['.$seopress_cpt_key.']" name="seopress_xml_sitemap_option_name[seopress_xml_sitemap_post_types_list]['.$seopress_cpt_key.'][include]" type="checkbox"';
                if ('1' == $check) echo 'checked="yes"'; 
                echo ' value="1"/>';
                
                echo '<label for="seopress_xml_sitemap_post_types_list_include['.$seopress_cpt_key.']">'. __( 'Include', 'wp-seopress' ) .'</label>';
                
                if (isset($this->options['seopress_xml_sitemap_post_types_list'][$seopress_cpt_key]['include'])) {
                    esc_attr( $this->options['seopress_xml_sitemap_post_types_list'][$seopress_cpt_key]['include']);
                }

            echo '</div>';
        }
    }

    public function seopress_xml_sitemap_taxonomies_list_callback()
    {
        $options = get_option( 'seopress_xml_sitemap_option_name' );  
        
        $check = isset($options['seopress_xml_sitemap_taxonomies_list']);      
        
        $args = array(
            'show_ui' => true,
            'public' => true,
          
        ); 
        $output = 'objects'; // or objects
        $operator = 'and'; // 'and' or 'or'
        $taxonomies = get_taxonomies( $args, $output, $operator );  

        foreach ($taxonomies as $seopress_tax_key => $seopress_tax_value) {
            
            echo '<h2>'.$seopress_tax_value->labels->name.'</h2>';

            //List all taxonomies
            echo '<div class="seopress_wrap_single_tax">';

                $options = get_option( 'seopress_xml_sitemap_option_name' );  
            
                $check = isset($options['seopress_xml_sitemap_taxonomies_list'][$seopress_tax_key]['include']);      
                
                echo '<input id="seopress_xml_sitemap_taxonomies_list_include['.$seopress_tax_key.']" name="seopress_xml_sitemap_option_name[seopress_xml_sitemap_taxonomies_list]['.$seopress_tax_key.'][include]" type="checkbox"';
                if ('1' == $check) echo 'checked="yes"'; 
                echo ' value="1"/>';
                
                echo '<label for="seopress_xml_sitemap_taxonomies_list_include['.$seopress_tax_key.']">'. __( 'Include', 'wp-seopress' ) .'</label>';
                
                if (isset($this->options['seopress_xml_sitemap_taxonomies_list'][$seopress_tax_key]['include'])) {
                    esc_attr( $this->options['seopress_xml_sitemap_taxonomies_list'][$seopress_tax_key]['include']);
                }

            echo '</div>';
        }
    }

    public function seopress_social_knowledge_type_callback()
    {
        $options = get_option( 'seopress_social_option_name' );    
        
        $selected = $options['seopress_social_knowledge_type'];
        
        echo '<select id="seopress_social_knowledge_type" name="seopress_social_option_name[seopress_social_knowledge_type]">';
            echo ' <option '; 
                if ('person' == $selected) echo 'selected="selected"'; 
                echo ' value="person">'. __("Person","wp-seopress") .'</option>';
            echo '<option '; 
                if ('organization' == $selected) echo 'selected="selected"'; 
                echo ' value="organization">'. __("Organization","wp-seopress") .'</option>';
        echo '</select>';

        if (isset($this->options['seopress_social_knowledge_type'])) {
            esc_attr( $this->options['seopress_social_knowledge_type']);
        }
    }

    public function seopress_social_knowledge_name_callback()
    {
        printf(
        '<input type="text" name="seopress_social_option_name[seopress_social_knowledge_name]" placeholder="'.__('eg: Apple','wp-seopress').'" value="%s"/>',
        esc_html( $this->options['seopress_social_knowledge_name'])
        );
        
    }    

    public function seopress_social_knowledge_img_callback() {
        $options = get_option( 'seopress_social_option_name' );  
        
        $check = isset($options['seopress_social_knowledge_img']);      

        echo '<input id="seopress_social_knowledge_img_meta" type="text" value="'.$options['seopress_social_knowledge_img'].'" name="seopress_social_option_name[seopress_social_knowledge_img]" placeholder="'.__('Select your logo','wp-seopress').'"  />
        
        <input id="seopress_social_knowledge_img_upload" class="button" type="button" value="'.__('Upload an Image','wp-seopress').'" />';
        
        if (isset($this->options['seopress_social_knowledge_img'])) {
            esc_attr( $this->options['seopress_social_knowledge_img']);
        }
    }

    public function seopress_social_accounts_facebook_callback()
    {
        printf(
        '<input type="text" name="seopress_social_option_name[seopress_social_accounts_facebook]" placeholder="'.__('eg: https://www.facebook.com/your-page','wp-seopress').'" value="%s"/>',
        esc_html( $this->options['seopress_social_accounts_facebook'])
        
        );
        
    }

    public function seopress_social_accounts_twitter_callback()
    {
        printf(
        '<input type="text" name="seopress_social_option_name[seopress_social_accounts_twitter]" placeholder="'.__('eg: @wpcloudy','wp-seopress').'" value="%s"/>',
        esc_html( $this->options['seopress_social_accounts_twitter'])
        
        );
        
    }

    public function seopress_social_accounts_google_callback()
    {
        printf(
        '<input type="text" name="seopress_social_option_name[seopress_social_accounts_google]" placeholder="'.__('eg: https://plus.google.com/+BenjaminDenis','wp-seopress').'" value="%s"/>',
        esc_html( $this->options['seopress_social_accounts_google'])
        
        );
        
    }    

    public function seopress_social_accounts_pinterest_callback()
    {
        printf(
        '<input type="text" name="seopress_social_option_name[seopress_social_accounts_pinterest]" placeholder="'.__('eg: https://pinterest.com/wpbuy/','wp-seopress').'" value="%s"/>',
        esc_html( $this->options['seopress_social_accounts_pinterest'])
        
        );
        
    }

    public function seopress_social_accounts_instagram_callback()
    {
        printf(
        '<input type="text" name="seopress_social_option_name[seopress_social_accounts_instagram]" placeholder="'.__('eg: https://www.instagram.com/your-name/','wp-seopress').'" value="%s"/>',
        esc_html( $this->options['seopress_social_accounts_instagram'])
        
        );
        
    }

    public function seopress_social_accounts_youtube_callback()
    {
        printf(
        '<input type="text" name="seopress_social_option_name[seopress_social_accounts_youtube]" placeholder="'.__('eg: https://www.youtube.com/channel/UCpQzarWu55UzCIH7-OW6pwA','wp-seopress').'" value="%s"/>',
        esc_html( $this->options['seopress_social_accounts_youtube'])
        
        );
        
    }

    public function seopress_social_accounts_linkedin_callback()
    {
        printf(
        '<input type="text" name="seopress_social_option_name[seopress_social_accounts_linkedin]" placeholder="'.__('eg: https://www.linkedin.com/in/benjamin-denis-70672b3b','wp-seopress').'" value="%s"/>',
        esc_html( $this->options['seopress_social_accounts_linkedin'])
        
        );
        
    }

    public function seopress_social_accounts_myspace_callback()
    {
        printf(
        '<input type="text" name="seopress_social_option_name[seopress_social_accounts_myspace]" placeholder="'.__('eg: https://myspace.com/your-page','wp-seopress').'" value="%s"/>',
        esc_html( $this->options['seopress_social_accounts_myspace'])
        
        );
        
    }

    public function seopress_social_accounts_soundcloud_callback()
    {
        printf(
        '<input type="text" name="seopress_social_option_name[seopress_social_accounts_soundcloud]" placeholder="'.__('eg: https://soundcloud.com/michaelmccannmusic','wp-seopress').'" value="%s"/>',
        esc_html( $this->options['seopress_social_accounts_soundcloud'])
        
        );
        
    }

    public function seopress_social_accounts_tumblr_callback()
    {
        printf(
        '<input type="text" name="seopress_social_option_name[seopress_social_accounts_tumblr]" placeholder="'.__('eg: https://your-site.tumblr.com','wp-seopress').'" value="%s"/>',
        esc_html( $this->options['seopress_social_accounts_tumblr'])
        
        );
        
    }

    public function seopress_social_facebook_og_callback()
    {
        $options = get_option( 'seopress_social_option_name' );  
        
        $check = isset($options['seopress_social_facebook_og']);      
        
        echo '<input id="seopress_social_facebook_og" name="seopress_social_option_name[seopress_social_facebook_og]" type="checkbox"';
        if ('1' == $check) echo 'checked="yes"'; 
        echo ' value="1"/>';
        
        echo '<label for="seopress_social_facebook_og">'. __( 'Enable OG data', 'wp-seopress' ) .'</label>';
        
        if (isset($this->options['seopress_social_facebook_og'])) {
            esc_attr( $this->options['seopress_social_facebook_og']);
        }
    }    

    public function seopress_social_facebook_img_callback()
    {
        $options = get_option( 'seopress_social_option_name' );  
        
        $check = isset($options['seopress_social_facebook_img']);      

        echo '<input id="seopress_social_fb_img_meta" type="text" value="'.$options['seopress_social_facebook_img'].'" name="seopress_social_option_name[seopress_social_facebook_img]" placeholder="'.__('Select your default thumbnail','wp-seopress').'"  />
        
        <input id="seopress_social_fb_img_upload" class="button" type="button" value="'.__('Upload an Image','wp-seopress').'" />';
        
        if (isset($this->options['seopress_social_facebook_img'])) {
            esc_attr( $this->options['seopress_social_facebook_img']);
        }
    }

    public function seopress_social_facebook_admin_id_callback()
    {
        printf(
        '<input type="text" name="seopress_social_option_name[seopress_social_facebook_admin_id]" value="%s"/>',
        esc_html( $this->options['seopress_social_facebook_admin_id'])
        
        );
        
    }

    public function seopress_social_facebook_app_id_callback()
    {
        printf(
        '<input type="text" name="seopress_social_option_name[seopress_social_facebook_app_id]" value="%s"/>',
        esc_html( $this->options['seopress_social_facebook_app_id'])
        
        );
        
    }

    public function seopress_social_twitter_card_callback()
    {
        $options = get_option( 'seopress_social_option_name' );  
        
        $check = isset($options['seopress_social_twitter_card']);      
        
        echo '<input id="seopress_social_twitter_card" name="seopress_social_option_name[seopress_social_twitter_card]" type="checkbox"';
        if ('1' == $check) echo 'checked="yes"'; 
        echo ' value="1"/>';
        
        echo '<label for="seopress_social_twitter_card">'. __( 'Enable Twitter card', 'wp-seopress' ) .'</label>';
        
        if (isset($this->options['seopress_social_twitter_card'])) {
            esc_attr( $this->options['seopress_social_twitter_card']);
        }
    }

    public function seopress_social_twitter_card_img_callback()
    {      
        $options = get_option( 'seopress_social_option_name' );  
        
        $check = isset($options['seopress_social_twitter_card_img']);      

        echo '<input id="seopress_social_twitter_img_meta" type="text" value="'.$options['seopress_social_twitter_card_img'].'" name="seopress_social_option_name[seopress_social_twitter_card_img]" placeholder="'.__('Select your default thumbnail','wp-seopress').'"  />
        
        <input id="seopress_social_twitter_img_upload" class="button" type="button" value="'.__('Upload an Image','wp-seopress').'" />';
        
        if (isset($this->options['seopress_social_twitter_card_img'])) {
            esc_attr( $this->options['seopress_social_twitter_card_img']);
        }
    }

    public function seopress_social_twitter_card_img_size_callback()
    {      
        $options = get_option( 'seopress_social_option_name' );    
        
        $selected = $options['seopress_social_twitter_card_img_size'];
        
        echo '<select id="seopress_social_twitter_card_img_size" name="seopress_social_option_name[seopress_social_twitter_card_img_size]">';
            echo ' <option '; 
                if ('default' == $selected) echo 'selected="selected"'; 
                echo ' value="default">'. __("Default","wp-seopress") .'</option>';
            echo '<option '; 
                if ('large' == $selected) echo 'selected="selected"'; 
                echo ' value="large">'. __("Large","wp-seopress") .'</option>';
        echo '</select>';

        if (isset($this->options['seopress_social_twitter_card_img_size'])) {
            esc_attr( $this->options['seopress_social_twitter_card_img_size']);
        }
    }

    public function seopress_advanced_advanced_attachments_callback()
    {
        $options = get_option( 'seopress_advanced_option_name' );  
        
        $check = isset($options['seopress_advanced_advanced_attachments']);      
        
        echo '<input id="seopress_advanced_advanced_attachments" name="seopress_advanced_option_name[seopress_advanced_advanced_attachments]" type="checkbox"';
        if ('1' == $check) echo 'checked="yes"'; 
        echo ' value="1"/>';
        
        echo '<label for="seopress_advanced_advanced_attachments">'. __( 'Redirect attachment pages to post parent (or homepage if none)', 'wp-seopress' ) .'</label>';
        
        if (isset($this->options['seopress_advanced_advanced_attachments'])) {
            esc_attr( $this->options['seopress_advanced_advanced_attachments']);
        }
    }

    public function seopress_advanced_advanced_stop_words_callback()
    {
        $options = get_option( 'seopress_advanced_option_name' );  
        
        $check = isset($options['seopress_advanced_advanced_stop_words']);      
        
        echo '<input id="seopress_advanced_advanced_stop_words" name="seopress_advanced_option_name[seopress_advanced_advanced_stop_words]" type="checkbox"';
        if ('1' == $check) echo 'checked="yes"'; 
        echo ' value="1"/>';
        
        echo '<label for="seopress_advanced_advanced_stop_words">'. __( 'Remove stop words in permalinks', 'wp-seopress' ) .'</label><span class="dashicons dashicons-info" title="'.__('Setting based on site language. Supported language EN, FR, ES, DE, IT, PT.','wp-seopress').'"></span>';

        if (isset($this->options['seopress_advanced_advanced_stop_words'])) {
            esc_attr( $this->options['seopress_advanced_advanced_stop_words']);
        }
    }

    public function seopress_advanced_advanced_google_callback()
    {
        printf(
        '<input type="text" name="seopress_advanced_option_name[seopress_advanced_advanced_google]" placeholder="'.__('Enter Google meta value site verification','wp-seopress').'" value="%s"/>',
        esc_html( $this->options['seopress_advanced_advanced_google'])
        
        );
        
    }

    public function seopress_advanced_advanced_bing_callback()
    {
        printf(
        '<input type="text" name="seopress_advanced_option_name[seopress_advanced_advanced_bing]" placeholder="'.__('Enter Bing meta value site verification','wp-seopress').'" value="%s"/>',
        esc_html( $this->options['seopress_advanced_advanced_bing'])
        
        );
        
    }

    public function seopress_advanced_advanced_pinterest_callback()
    {
        printf(
        '<input type="text" name="seopress_advanced_option_name[seopress_advanced_advanced_pinterest]" placeholder="'.__('Enter Pinterest meta value site verification','wp-seopress').'" value="%s"/>',
        esc_html( $this->options['seopress_advanced_advanced_pinterest'])
        
        );
        
    }

    public function seopress_advanced_advanced_yandex_callback()
    {
        printf(
        '<input type="text" name="seopress_advanced_option_name[seopress_advanced_advanced_yandex]" placeholder="'.__('Enter Yandex meta value site verification','wp-seopress').'" value="%s"/>',
        esc_html( $this->options['seopress_advanced_advanced_yandex'])
        
        );
        
    }
}
	
if( is_admin() )
    $my_settings_page = new seopress_options();
	
?>