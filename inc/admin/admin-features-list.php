<div class="seopress-page-list">
    <div id="seopress-notice-save" style="display: none"><span class="dashicons dashicons-yes"></span><span class="html"></span></div>
    <?php
        $seopress_feature = apply_filters('seopress_remove_feature_titles', true);
        if ($seopress_feature === true) { ?>
            <div class="seopress-feature">
                <span class="inner">
                    <div class="img-tool">
                        <span class="dashicons dashicons-editor-table"></span>
                    </div>
                    <h3><?php _e('Titles & metas','wp-seopress'); ?></h3>
                    <p><?php _e('Manage all your titles & metas for post types, taxonomies, archives...','wp-seopress'); ?></p>
                    <a class="button-secondary" href="<?php echo admin_url( 'admin.php?page=seopress-titles' ); ?>"><?php _e('Manage','wp-seopress'); ?></a>
                    <?php
                        if(seopress_get_toggle_option('titles')=='1') { 
                            $seopress_get_toggle_titles_option = '1';
                        } else { 
                            $seopress_get_toggle_titles_option = '0';
                        }
                    ?>
                    <input type="checkbox" name="toggle-titles" id="toggle-titles" class="toggle" data-toggle="<?php echo $seopress_get_toggle_titles_option; ?>">
                    <label for="toggle-titles"></label>
                    <?php
                        if($seopress_get_toggle_titles_option =='1') { 
                            echo '<span id="titles-state-default" class="feature-state"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to disable this feature','wp-seopress').'</span>';
                            echo '<span id="titles-state" class="feature-state feature-state-off"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to enable this feature','wp-seopress').'</span>';
                        } else { 
                            echo '<span id="titles-state-default" class="feature-state"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to enable this feature','wp-seopress').'</span>';
                            echo '<span id="titles-state" class="feature-state feature-state-off"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to disable this feature','wp-seopress').'</span>';
                        }
                    ?>
                    <?php
                        if (function_exists('seopress_get_locale')) {
                            if (seopress_get_locale() =='fr') {
                                $seopress_docs_link['support']['titles'] = 'https://www.seopress.org/fr/support/guides/gerez-vos-balises-titres-metas/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
                            } else {
                                $seopress_docs_link['support']['titles'] = 'https://www.seopress.org/support/guides/manage-titles-meta-descriptions/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
                            }
                        }
                    ?>
                    <a href="<?php echo $seopress_docs_link['support']['titles']; ?>" target="_blank" class="seopress-doc" title="<?php _e('Read our guide','wp-seopress'); ?>"><span class="dashicons dashicons-editor-help"></span><span class="screen-reader-text"><?php _e('Guide to manage your titles and meta descriptions - new window','wp-seopress'); ?></span></a>
                </span>
            </div>
        <?php 
        }
        $seopress_feature = apply_filters('seopress_remove_feature_xml_sitemap', true);
        if ($seopress_feature === true) { ?>
            <div class="seopress-feature">
                <span class="inner">
                    <div class="img-tool">
                        <span class="dashicons dashicons-media-spreadsheet"></span>
                    </div>
                    <h3><?php _e('XML / Image / Video / HTML Sitemap','wp-seopress'); ?></h3>
                    <p><?php _e('Manage your XML / Image / Video / HTML Sitemap','wp-seopress'); ?></p>
                    <a class="button-secondary" href="<?php echo admin_url( 'admin.php?page=seopress-xml-sitemap' ); ?>"><?php _e('Manage','wp-seopress'); ?></a>
                    <?php
                        if(seopress_get_toggle_option('xml-sitemap')=='1') { 
                            $seopress_get_toggle_xml_sitemap_option = '1';
                        } else { 
                            $seopress_get_toggle_xml_sitemap_option = '0';
                        }
                    ?>
                    <input type="checkbox" name="toggle-xml-sitemap" id="toggle-xml-sitemap" class="toggle" data-toggle="<?php echo $seopress_get_toggle_xml_sitemap_option; ?>">
                    <label for="toggle-xml-sitemap"></label>
                    <?php
                        if($seopress_get_toggle_xml_sitemap_option =='1') { 
                            echo '<span id="xml-sitemap-state-default" class="feature-state"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to disable this feature','wp-seopress').'</span>';
                            echo '<span id="xml-sitemap-state" class="feature-state feature-state-off"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to enable this feature','wp-seopress').'</span>';
                        } else { 
                            echo '<span id="xml-sitemap-state-default" class="feature-state"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to enable this feature','wp-seopress').'</span>';
                            echo '<span id="xml-sitemap-state" class="feature-state feature-state-off"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to disable this feature','wp-seopress').'</span>';
                        }
                    ?>
                    <?php
                        if (function_exists('seopress_get_locale')) {
                            if (seopress_get_locale() =='fr') {
                                $seopress_docs_link['support']['sitemaps'] = 'https://www.seopress.org/fr/support/guides/activer-sitemap-xml/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
                            } else {
                                $seopress_docs_link['support']['sitemaps'] = 'https://www.seopress.org/support/guides/enable-xml-sitemaps/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
                            }
                        }
                    ?>
                    <a href="<?php echo $seopress_docs_link['support']['sitemaps']; ?>" target="_blank" class="seopress-doc" title="<?php _e('Read our guide','wp-seopress'); ?>"><span class="dashicons dashicons-editor-help"></span><span class="screen-reader-text"><?php _e('Guide to enable your XML Sitemaps - new window','wp-seopress'); ?></span></a>
                </span>
            </div>
        <?php 
        }
        $seopress_feature = apply_filters('seopress_remove_feature_social', true);
        if ($seopress_feature === true) { ?>
            <div class="seopress-feature">
                <span class="inner">
                    <div class="img-tool">
                        <span class="dashicons dashicons-share"></span>
                    </div>
                    <h3><?php _e('Social Networks','wp-seopress'); ?></h3>
                    <p><?php _e('Open Graph, Twitter Card, Google Knowledge Graph and more...','wp-seopress'); ?></p>
                    <a class="button-secondary" href="<?php echo admin_url( 'admin.php?page=seopress-social' ); ?>"><?php _e('Manage','wp-seopress'); ?></a>
                    <?php
                        if(seopress_get_toggle_option('social')=='1') { 
                            $seopress_get_toggle_social_option = '1';
                        } else { 
                            $seopress_get_toggle_social_option = '0';
                        }
                    ?>
                    <input type="checkbox" name="toggle-social" id="toggle-social" class="toggle" data-toggle="<?php echo $seopress_get_toggle_social_option; ?>">
                    <label for="toggle-social"></label>
                    <?php
                        if($seopress_get_toggle_social_option =='1') { 
                            echo '<span id="social-state-default" class="feature-state"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to disable this feature','wp-seopress').'</span>';
                            echo '<span id="social-state" class="feature-state feature-state-off"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to enable this feature','wp-seopress').'</span>';
                        } else { 
                            echo '<span id="social-state-default" class="feature-state"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to enable this feature','wp-seopress').'</span>';
                            echo '<span id="social-state" class="feature-state feature-state-off"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to disable this feature','wp-seopress').'</span>';
                        }
                    ?>
                    <?php
                        if (function_exists('seopress_get_locale')) {
                            if (seopress_get_locale() =='fr') {
                                $seopress_docs_link['support']['knowledge'] = 'https://www.seopress.org/fr/support/guides/activer-google-knowledge-graph/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
                            } else {
                                $seopress_docs_link['support']['knowledge'] = 'https://www.seopress.org/support/guides/enable-google-knowledge-graph/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
                            }
                        }
                    ?>
                    <a href="<?php echo $seopress_docs_link['support']['knowledge']; ?>" target="_blank" class="seopress-doc" title="<?php _e('Read our guide','wp-seopress'); ?>"><span class="dashicons dashicons-editor-help"></span><span class="screen-reader-text"><?php _e('Guide to enable Google Knowledge Graph - new window','wp-seopress'); ?></span></a>
                </span>
            </div>
        <?php 
        }
        $seopress_feature = apply_filters('seopress_remove_feature_google_analytics', true);
        if ($seopress_feature === true) { ?>
            <div class="seopress-feature">
                <span class="inner">
                    <div class="img-tool">
                        <span class="dashicons dashicons-chart-area"></span>
                    </div>
                    <h3><?php _e('Google Analytics','wp-seopress'); ?></h3>
                    <p><?php _e('Track everything about your visitors with Google Analytics','wp-seopress'); ?></p>
                    <a class="button-secondary" href="<?php echo admin_url( 'admin.php?page=seopress-google-analytics' ); ?>"><?php _e('Manage','wp-seopress'); ?></a>
                    <?php
                        if(seopress_get_toggle_option('google-analytics')=='1') { 
                            $seopress_get_toggle_google_analytics_option = '1';
                        } else { 
                            $seopress_get_toggle_google_analytics_option = '0';
                        }
                    ?>
                    <input type="checkbox" name="toggle-google-analytics" id="toggle-google-analytics" class="toggle" data-toggle="<?php echo $seopress_get_toggle_google_analytics_option; ?>">
                    <label for="toggle-google-analytics"></label>
                    <?php
                        if($seopress_get_toggle_google_analytics_option =='1') { 
                            echo '<span id="google-analytics-state-default" class="feature-state"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to disable this feature','wp-seopress').'</span>';
                            echo '<span id="google-analytics-state" class="feature-state feature-state-off"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to enable this feature','wp-seopress').'</span>';
                        } else { 
                            echo '<span id="google-analytics-state-default" class="feature-state"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to enable this feature','wp-seopress').'</span>';
                            echo '<span id="google-analytics-state" class="feature-state feature-state-off"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to disable this feature','wp-seopress').'</span>';
                        }
                    ?>
                    <?php
                        if (function_exists('seopress_get_locale')) {
                            if (seopress_get_locale() =='fr') {
                                $seopress_docs_link['support']['analytics'] = 'https://www.seopress.org/fr/support/guides/debutez-google-analytics/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
                            } else {
                                $seopress_docs_link['support']['analytics'] = 'https://www.seopress.org/support/guides/google-analytics/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
                            }
                        }
                    ?>
                    <a href="<?php echo $seopress_docs_link['support']['analytics']; ?>" target="_blank" class="seopress-doc" title="<?php _e('Read our guide','wp-seopress'); ?>"><span class="dashicons dashicons-editor-help"></span><span class="screen-reader-text"><?php _e('Guide to getting started with Google Analytics - new window','wp-seopress'); ?></span></a>
                </span>
            </div>
        <?php 
        }
        $seopress_feature = apply_filters('seopress_remove_feature_advanced', true);
        if ($seopress_feature === true) { ?>
            <div class="seopress-feature">
                <span class="inner">
                    <div class="img-tool">
                        <span class="dashicons dashicons-admin-tools"></span>                                  
                    </div>
                    <h3><?php _e('Advanced','wp-seopress'); ?></h3>
                    <p><?php _e('Advanced SEO options for advanced users!','wp-seopress'); ?></p>
                    <a class="button-secondary" href="<?php echo admin_url( 'admin.php?page=seopress-advanced' ); ?>"><?php _e('Manage','wp-seopress'); ?></a>
                    <?php
                        if(seopress_get_toggle_option('advanced')=='1') { 
                            $seopress_get_toggle_advanced_option = '1';
                        } else { 
                            $seopress_get_toggle_advanced_option = '0';
                        }
                    ?>
                    <input type="checkbox" name="toggle-advanced" id="toggle-advanced" class="toggle" data-toggle="<?php echo $seopress_get_toggle_advanced_option; ?>">
                    <label for="toggle-advanced"></label>
                    <?php
                        if($seopress_get_toggle_advanced_option =='1') { 
                            echo '<span id="advanced-state-default" class="feature-state"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to disable this feature','wp-seopress').'</span>';
                            echo '<span id="advanced-state" class="feature-state feature-state-off"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to enable this feature','wp-seopress').'</span>';
                        } else { 
                            echo '<span id="advanced-state-default" class="feature-state"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to enable this feature','wp-seopress').'</span>';
                            echo '<span id="advanced-state" class="feature-state feature-state-off"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to disable this feature','wp-seopress').'</span>';
                        }
                    ?>
                </span>
            </div>
        <?php 
        }
    ?>
    <?php if (is_plugin_active('wp-seopress-pro/seopress-pro.php')) { 
        $seopress_feature = apply_filters('seopress_remove_feature_woocommerce', true);
        if ($seopress_feature === true) { ?>
            <div class="seopress-feature">
                <span class="inner">
                    <div class="img-tool">
                        <span class="dashicons dashicons-cart"></span>                                  
                    </div>
                    <h3><?php _e('WooCommerce','wp-seopress'); ?></h3>
                    <p><?php _e('Improve WooCommerce SEO','wp-seopress'); ?></p>
                    <a class="button-secondary" href="<?php echo admin_url( 'admin.php?page=seopress-pro-page#tab=tab_seopress_woocommerce$1' ); ?>"><?php _e('Manage','wp-seopress'); ?></a>
                    <?php
                        if(seopress_get_toggle_option('woocommerce')=='1') { 
                            $seopress_get_toggle_woocommerce_option = '1';
                        } else { 
                            $seopress_get_toggle_woocommerce_option = '0';
                        }
                    ?>
                    <input type="checkbox" name="toggle-woocommerce" id="toggle-woocommerce" class="toggle" data-toggle="<?php echo $seopress_get_toggle_woocommerce_option; ?>">
                    <label for="toggle-woocommerce"></label>
                    <?php
                        if($seopress_get_toggle_woocommerce_option =='1') { 
                            echo '<span id="woocommerce-state-default" class="feature-state"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to disable this feature','wp-seopress').'</span>';
                            echo '<span id="woocommerce-state" class="feature-state feature-state-off"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to enable this feature','wp-seopress').'</span>';
                        } else { 
                            echo '<span id="woocommerce-state-default" class="feature-state"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to enable this feature','wp-seopress').'</span>';
                            echo '<span id="woocommerce-state" class="feature-state feature-state-off"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to disable this feature','wp-seopress').'</span>';
                        }
                    ?>

                    <?php
                        if (function_exists('seopress_get_locale')) {
                            if (seopress_get_locale() =='fr') {
                                $seopress_docs_link['support']['wc'] = 'https://www.seopress.org/fr/blog/woocommerce-seo-seopress-le-guide/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
                            } else {
                                $seopress_docs_link['support']['wc'] = 'https://www.seopress.org/blog/woocommerce-seo-seopress/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
                            }
                        }
                    ?>
                    <a href="<?php echo $seopress_docs_link['support']['wc']; ?>" target="_blank" class="seopress-doc" title="<?php _e('Read our guide','wp-seopress'); ?>"><span class="dashicons dashicons-editor-help"></span><span class="screen-reader-text"><?php _e('Guide to optimize your WooCommerce SEO - new window','wp-seopress'); ?></span></a>
                </span>
            </div>
            <?php 
        } 
        $seopress_feature = apply_filters('seopress_remove_feature_edd', true);
        if ($seopress_feature === true) { ?>
            <div class="seopress-feature">
                <span class="inner">
                    <div class="img-tool">
                        <span class="dashicons dashicons-cart"></span>                                  
                    </div>
                    <h3><?php _e('Easy Digital Downloads','wp-seopress'); ?></h3>
                    <p><?php _e('Improve Easy Digital Downloads SEO','wp-seopress'); ?></p>
                    <a class="button-secondary" href="<?php echo admin_url( 'admin.php?page=seopress-pro-page#tab=tab_seopress_edd$13' ); ?>"><?php _e('Manage','wp-seopress'); ?></a>
                    <?php
                        if(seopress_get_toggle_option('edd')=='1') { 
                            $seopress_get_toggle_edd_option = '1';
                        } else { 
                            $seopress_get_toggle_edd_option = '0';
                        }
                    ?>
                    <input type="checkbox" name="toggle-edd" id="toggle-edd" class="toggle" data-toggle="<?php echo $seopress_get_toggle_edd_option; ?>">
                    <label for="toggle-edd"></label>
                    <?php
                        if($seopress_get_toggle_edd_option =='1') { 
                            echo '<span id="edd-state-default" class="feature-state"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to disable this feature','wp-seopress').'</span>';
                            echo '<span id="edd-state" class="feature-state feature-state-off"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to enable this feature','wp-seopress').'</span>';
                        } else { 
                            echo '<span id="edd-state-default" class="feature-state"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to enable this feature','wp-seopress').'</span>';
                            echo '<span id="edd-state" class="feature-state feature-state-off"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to disable this feature','wp-seopress').'</span>';
                        }
                    ?>
                </span>
            </div>
            <?php 
        } 
        $seopress_feature = apply_filters('seopress_remove_feature_local_business', true);
        if ($seopress_feature === true) { ?>
            <div class="seopress-feature">
                <span class="inner">
                    <div class="img-tool">
                        <span class="dashicons dashicons-store"></span>
                    </div>
                    <h3><?php _e('Local Business','wp-seopress'); ?></h3>
                    <p><?php _e('Add Google Local Business data type','wp-seopress'); ?></p>
                    <a class="button-secondary" href="<?php echo admin_url( 'admin.php?page=seopress-pro-page#tab=tab_seopress_local_business$10' ); ?>"><?php _e('Manage','wp-seopress'); ?></a>
                    <?php
                        if(seopress_get_toggle_option('local-business')=='1') { 
                            $seopress_get_toggle_local_business_option = '1';
                        } else { 
                            $seopress_get_toggle_local_business_option = '0';
                        }
                    ?>
                    <input type="checkbox" name="toggle-local-business" id="toggle-local-business" class="toggle" data-toggle="<?php echo $seopress_get_toggle_local_business_option; ?>">
                    <label for="toggle-local-business"></label>
                    <?php
                        if($seopress_get_toggle_local_business_option =='1') { 
                            echo '<span id="local-business-state-default" class="feature-state"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to disable this feature','wp-seopress').'</span>';
                            echo '<span id="local-business-state" class="feature-state feature-state-off"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to enable this feature','wp-seopress').'</span>';
                        } else { 
                            echo '<span id="local-business-state-default" class="feature-state"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to enable this feature','wp-seopress').'</span>';
                            echo '<span id="local-business-state" class="feature-state feature-state-off"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to disable this feature','wp-seopress').'</span>';
                        }
                    ?>
                </span>
            </div>
        <?php 
        } 
        $seopress_feature = apply_filters('seopress_remove_feature_dublin_core', true);
        if ($seopress_feature === true) { ?>
            <div class="seopress-feature">
                <span class="inner">
                    <div class="img-tool">
                        <span class="dashicons dashicons-welcome-learn-more"></span>
                    </div>
                    <h3><?php _e('Dublin Core','wp-seopress'); ?></h3>
                    <p><?php _e('Add Dublin Core meta tags','wp-seopress'); ?></p>
                    <a class="button-secondary" href="<?php echo admin_url( 'admin.php?page=seopress-pro-page#tab=tab_seopress_dublin_core$8' ); ?>"><?php _e('Manage','wp-seopress'); ?></a>
                    <?php
                        if(seopress_get_toggle_option('dublin-core')=='1') { 
                            $seopress_get_toggle_dublin_core_option = '1';
                        } else { 
                            $seopress_get_toggle_dublin_core_option = '0';
                        }
                    ?>
                    <input type="checkbox" name="toggle-dublin-core" id="toggle-dublin-core" class="toggle" data-toggle="<?php echo $seopress_get_toggle_dublin_core_option; ?>">
                    <label for="toggle-dublin-core"></label>
                    <?php
                        if($seopress_get_toggle_dublin_core_option =='1') { 
                            echo '<span id="dublin-core-state-default" class="feature-state"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to disable this feature','wp-seopress').'</span>';
                            echo '<span id="dublin-core-state" class="feature-state feature-state-off"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to enable this feature','wp-seopress').'</span>';
                        } else { 
                            echo '<span id="dublin-core-state-default" class="feature-state"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to enable this feature','wp-seopress').'</span>';
                            echo '<span id="dublin-core-state" class="feature-state feature-state-off"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to disable this feature','wp-seopress').'</span>';
                        }
                    ?>
                </span>
            </div>
        <?php 
        } 
        $seopress_feature = apply_filters('seopress_remove_feature_schemas', true);
        if ($seopress_feature === true) { ?>
            <div class="seopress-feature">
                <span class="inner">
                    <div class="img-tool">
                        <span class="dashicons dashicons-media-spreadsheet"></span>
                    </div>
                    <h3><?php _e('Structured Data Types','wp-seopress'); ?></h3>
                    <p><?php _e('Add data types to your content: articles, courses, recipes, videos, events, products and more.','wp-seopress'); ?></p>
                    <a class="button-secondary" href="<?php echo admin_url( 'admin.php?page=seopress-pro-page#tab=tab_seopress_rich_snippets$9' ); ?>"><?php _e('Manage','wp-seopress'); ?></a>
                    <a class="button-secondary view-redirects" href="<?php echo admin_url( 'edit.php?post_type=seopress_schemas' ); ?>"><?php _e('See schemas','wp-seopress'); ?></a>
                    <?php
                        if(seopress_get_toggle_option('rich-snippets')=='1') { 
                            $seopress_get_toggle_rich_snippets_option = '1';
                        } else { 
                            $seopress_get_toggle_rich_snippets_option = '0';
                        }
                    ?>
                    <input type="checkbox" name="toggle-rich-snippets" id="toggle-rich-snippets" class="toggle" data-toggle="<?php echo $seopress_get_toggle_rich_snippets_option; ?>">
                    <label for="toggle-rich-snippets"></label>
                    <?php
                        if($seopress_get_toggle_rich_snippets_option =='1') { 
                            echo '<span id="rich-snippets-state-default" class="feature-state"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to disable this feature','wp-seopress').'</span>';
                            echo '<span id="rich-snippets-state" class="feature-state feature-state-off"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to enable this feature','wp-seopress').'</span>';
                        } else { 
                            echo '<span id="rich-snippets-state-default" class="feature-state"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to enable this feature','wp-seopress').'</span>';
                            echo '<span id="rich-snippets-state" class="feature-state feature-state-off"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to disable this feature','wp-seopress').'</span>';
                        }
                    ?>
                    <?php
                        if (function_exists('seopress_get_locale')) {
                            if (seopress_get_locale() =='fr') {
                                $seopress_docs_link['support']['schema'] = 'https://www.seopress.org/fr/support/guides/#types-de-donnees-structurees?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
                            } else {
                                $seopress_docs_link['support']['schema'] = 'https://www.seopress.org/support/guides/#structured-data-types?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
                            }
                        }
                    ?>
                    <a href="<?php echo $seopress_docs_link['support']['schema']; ?>" target="_blank" class="seopress-doc" title="<?php _e('Read our guide','wp-seopress'); ?>"><span class="dashicons dashicons-editor-help"></span><span class="screen-reader-text"><?php _e('Guide to add schemas with SEOPress PRO - new window','wp-seopress'); ?></span></a>
                </span>
            </div>
        <?php 
        } 
        $seopress_feature = apply_filters('seopress_remove_feature_breadcrumbs', true);
        if ($seopress_feature === true) { ?>
            <div class="seopress-feature">
                <span class="inner">
                    <div class="img-tool">
                        <span class="dashicons dashicons-feedback"></span>                              
                    </div>
                    <h3><?php _e('Breadcrumbs','wp-seopress'); ?></h3>
                    <p><?php _e('Enable Breadcrumbs for your theme and improve your SEO in SERPs','wp-seopress'); ?></p>
                    <a class="button-secondary" href="<?php echo admin_url( 'admin.php?page=seopress-pro-page#tab=tab_seopress_breadcrumbs$2' ); ?>"><?php _e('Manage','wp-seopress'); ?></a>
                    <?php
                        if(seopress_get_toggle_option('breadcrumbs')=='1') { 
                            $seopress_get_toggle_breadcrumbs_option = '1';
                        } else { 
                            $seopress_get_toggle_breadcrumbs_option = '0';
                        }
                    ?>
                    <input type="checkbox" name="toggle-breadcrumbs" id="toggle-breadcrumbs" class="toggle" data-toggle="<?php echo $seopress_get_toggle_breadcrumbs_option; ?>">
                    <label for="toggle-breadcrumbs"></label>
                    <?php
                        if($seopress_get_toggle_breadcrumbs_option =='1') { 
                            echo '<span id="breadcrumbs-state-default" class="feature-state"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to disable this feature','wp-seopress').'</span>';
                            echo '<span id="breadcrumbs-state" class="feature-state feature-state-off"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to enable this feature','wp-seopress').'</span>';
                        } else { 
                            echo '<span id="breadcrumbs-state-default" class="feature-state"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to enable this feature','wp-seopress').'</span>';
                            echo '<span id="breadcrumbs-state" class="feature-state feature-state-off"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to disable this feature','wp-seopress').'</span>';
                        }
                    ?>
                    <?php
                    if (function_exists('seopress_get_locale')) {
                            if (seopress_get_locale() =='fr') {
                                $seopress_docs_link['support']['breadcrumbs'] = 'https://www.seopress.org/fr/support/guides/activer-fil-dariane/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
                            } else {
                                $seopress_docs_link['support']['breadcrumbs'] = 'https://www.seopress.org/support/guides/enable-breadcrumbs/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
                            }
                        }
                    ?>
                    <a href="<?php echo $seopress_docs_link['support']['breadcrumbs']; ?>" target="_blank" class="seopress-doc" title="<?php _e('Read our guide','wp-seopress'); ?>"><span class="dashicons dashicons-editor-help"></span><span class="screen-reader-text"><?php _e('Guide to enable Breadcrumbs - new window','wp-seopress'); ?></span></a>
                </span>
            </div>
        <?php 
        } 
        $seopress_feature = apply_filters('seopress_remove_feature_page_speed', true);
        if ($seopress_feature === true) { ?>
            <div class="seopress-feature">
                <span class="inner">
                    <div class="img-tool">
                        <span class="dashicons dashicons-performance"></span>                              
                    </div>
                    <h3><?php _e('Google Page Speed','wp-seopress'); ?></h3>
                    <p><?php _e('Track your website performance to improve SEO with Google Page Speed','wp-seopress'); ?></p>
                    <a class="button-secondary" href="<?php echo admin_url( 'admin.php?page=seopress-pro-page#tab=tab_seopress_page_speed$3' ); ?>"><?php _e('Manage','wp-seopress'); ?></a>
                    <?php
                    if (function_exists('seopress_get_locale')) {
                            if (seopress_get_locale() =='fr') {
                                $seopress_docs_link['support']['page_speed'] = 'https://www.seopress.org/fr/support/guides/analyser-site-google-page-speed/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
                            } else {
                                $seopress_docs_link['support']['page_speed'] = 'https://www.seopress.org/support/guides/analyse-site-google-page-speed/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
                            }
                        }
                    ?>
                    <a href="<?php echo $seopress_docs_link['support']['page_speed']; ?>" target="_blank" class="seopress-doc" title="<?php _e('Read our guide','wp-seopress'); ?>"><span class="dashicons dashicons-editor-help"></span><span class="screen-reader-text"><?php _e('Guide to analyze your site with Google Page Speed - new window','wp-seopress'); ?></span></a>
                </span>
            </div>
            <?php 
        } ?>
        <?php if (!is_multisite()) { ?>
            <?php
            $seopress_feature = apply_filters('seopress_remove_feature_robots', true);
            if ($seopress_feature === true) { ?>
                <div class="seopress-feature">
                    <span class="inner">
                        <div class="img-tool">
                            <span class="dashicons dashicons-media-text"></span>                              
                        </div>
                        <h3><?php _e('robots.txt','wp-seopress'); ?></h3>
                        <p><?php _e('Edit your robots.txt file','wp-seopress'); ?></p>
                        <a href="<?php get_home_url(); ?>/robots.txt" class="button-secondary view-redirects" target="_blank"><?php _e('View your robots.txt','wp-seopress-pro'); ?></a>
                        <a class="button-secondary" href="<?php echo admin_url( 'admin.php?page=seopress-pro-page#tab=tab_seopress_robots$4' ); ?>"><?php _e('Manage','wp-seopress'); ?></a>
                        <?php
                            if(seopress_get_toggle_option('robots')=='1') { 
                                $seopress_get_toggle_robots_option = '1';
                            } else {
                                $seopress_get_toggle_robots_option = '0';
                            }
                        ?>
                        
                        <input type="checkbox" name="toggle-robots" id="toggle-robots" class="toggle" data-toggle="<?php echo $seopress_get_toggle_robots_option; ?>">
                        <label for="toggle-robots"></label>
                        <?php
                            if($seopress_get_toggle_robots_option =='1') { 
                                echo '<span id="robots-state-default" class="feature-state"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to disable this feature','wp-seopress').'</span>';
                                echo '<span id="robots-state" class="feature-state feature-state-off"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to enable this feature','wp-seopress').'</span>';
                            } else { 
                                echo '<span id="robots-state-default" class="feature-state"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to enable this feature','wp-seopress').'</span>';
                                echo '<span id="robots-state" class="feature-state feature-state-off"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to disable this feature','wp-seopress').'</span>';
                            }
                        ?>
                        <?php
                        if (function_exists('seopress_get_locale')) {
                                if (seopress_get_locale() =='fr') {
                                    $seopress_docs_link['support']['robots'] = 'https://www.seopress.org/fr/support/guides/editer-fichier-robots-txt/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
                                } else {
                                    $seopress_docs_link['support']['robots'] = 'https://www.seopress.org/support/guides/edit-robots-txt-file/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
                                }
                            }
                        ?>
                        <a href="<?php echo $seopress_docs_link['support']['robots']; ?>" target="_blank" class="seopress-doc" title="<?php _e('Read our guide','wp-seopress'); ?>"><span class="dashicons dashicons-editor-help"></span><span class="screen-reader-text"><?php _e('Guide to edit your robots.txt file - new window','wp-seopress'); ?></span></a>
                    </span>
                </div>
            <?php 
            }
        }
        $seopress_feature = apply_filters('seopress_remove_feature_news', true);
        if ($seopress_feature === true) { ?>
            <div class="seopress-feature">
                <span class="inner">
                    <div class="img-tool">
                        <span class="dashicons dashicons-admin-post"></span>                              
                    </div>
                    <h3><?php _e('Google News Sitemap','wp-seopress'); ?></h3>
                    <p><?php _e('Optimize your site for Google News','wp-seopress'); ?></p>
                    <a class="button-secondary" href="<?php echo admin_url( 'admin.php?page=seopress-pro-page#tab=tab_seopress_news$5' ); ?>"><?php _e('Manage','wp-seopress'); ?></a>
                    <?php
                        if(seopress_get_toggle_option('news')=='1') { 
                            $seopress_get_toggle_news_option = '1';
                        } else { 
                            $seopress_get_toggle_news_option = '0';
                        }
                    ?>
                    <input type="checkbox" name="toggle-news" id="toggle-news" class="toggle" data-toggle="<?php echo $seopress_get_toggle_news_option; ?>">
                    <label for="toggle-news"></label>
                    <?php
                        if($seopress_get_toggle_news_option =='1') { 
                            echo '<span id="news-state-default" class="feature-state"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to disable this feature','wp-seopress').'</span>';
                            echo '<span id="news-state" class="feature-state feature-state-off"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to enable this feature','wp-seopress').'</span>';
                        } else { 
                            echo '<span id="news-state-default" class="feature-state"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to enable this feature','wp-seopress').'</span>';
                            echo '<span id="news-state" class="feature-state feature-state-off"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to disable this feature','wp-seopress').'</span>';
                        }
                    ?>
                    <?php
                        if (function_exists('seopress_get_locale')) {
                            if (seopress_get_locale() =='fr') {
                                $seopress_docs_link['support']['gnews'] = 'https://www.seopress.org/fr/support/guides/activer-plan-de-site-xml-google-news/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
                            } else {
                                $seopress_docs_link['support']['gnews'] = 'https://www.seopress.org/support/guides/enable-google-news-xml-sitemap/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
                            }
                        }
                    ?>
                    <a href="<?php echo $seopress_docs_link['support']['gnews']; ?>" target="_blank" class="seopress-doc" title="<?php _e('Read our guide','wp-seopress'); ?>"><span class="dashicons dashicons-editor-help"></span><span class="screen-reader-text"><?php _e('Guide to activate Google News XML sitemap with SEOPress PRO - new window','wp-seopress'); ?></span></a>
                </span>
            </div>
        <?php
        }
        $seopress_feature = apply_filters('seopress_remove_feature_schemas', true);
        if ($seopress_feature === true) { ?>
            <div class="seopress-feature">
                <span class="inner">
                    <div class="img-tool">
                        <span class="dashicons dashicons-media-spreadsheet"></span>                              
                    </div>
                    <h3><?php _e('Schemas','wp-seopress'); ?></h3>
                    <p><?php _e('Create / manage your schemas','wp-seopress'); ?></p>
                    <a class="button-secondary view-redirects" href="<?php echo admin_url( 'edit.php?post_type=seopress_schemas' ); ?>"><?php _e('See schemas','wp-seopress'); ?></a>
                    <a class="button-secondary" href="<?php echo admin_url( 'admin.php?page=seopress-pro-page#tab=tab_seopress_schemas$9' ); ?>"><?php _e('Manage','wp-seopress'); ?></a>
                    
                    <?php
                        if (function_exists('seopress_get_locale')) {
                            if (seopress_get_locale() =='fr') {
                                $seopress_docs_link['support']['schema'] = 'https://www.seopress.org/fr/support/guides/#types-de-donnees-structurees?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
                            } else {
                                $seopress_docs_link['support']['schema'] = 'https://www.seopress.org/support/guides/#structured-data-types?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
                            }
                        }
                    ?>
                    <a href="<?php echo $seopress_docs_link['support']['schema']; ?>" target="_blank" class="seopress-doc" title="<?php _e('Read our guide','wp-seopress'); ?>"><span class="dashicons dashicons-editor-help"></span><span class="screen-reader-text"><?php _e('Guide to add schemas with SEOPress PRO - new window','wp-seopress'); ?></span></a>
                </span>
            </div>
        <?php
        }
        $seopress_feature = apply_filters('seopress_remove_feature_redirects', true);
        if ($seopress_feature === true) { ?>
            <div class="seopress-feature">
                <span class="inner">
                    <div class="img-tool">
                        <span class="dashicons dashicons-admin-links"></span>                              
                    </div>
                    <h3><?php _e('Redirections','wp-seopress'); ?></h3>
                    <p><?php _e('Monitor 404, create 301, 302 and 307 redirections','wp-seopress'); ?></p>
                    <a class="button-secondary view-redirects" href="<?php echo admin_url( 'edit.php?post_type=seopress_404' ); ?>"><?php _e('See redirects','wp-seopress'); ?></a>
                    <a class="button-secondary" href="<?php echo admin_url( 'admin.php?page=seopress-pro-page#tab=tab_seopress_404$6' ); ?>"><?php _e('Manage','wp-seopress'); ?></a><br><br>
                    <?php
                        if(seopress_get_toggle_option('404')=='1') { 
                            $seopress_get_toggle_404_option = '1';
                        } else { 
                            $seopress_get_toggle_404_option = '0';
                        }
                    ?>
                    <input type="checkbox" name="toggle-404" id="toggle-404" class="toggle" data-toggle="<?php echo $seopress_get_toggle_404_option; ?>">
                    <label for="toggle-404"></label>
                    <?php
                        if($seopress_get_toggle_404_option =='1') { 
                            echo '<span id="404-state-default" class="feature-state"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to disable this feature','wp-seopress').'</span>';
                            echo '<span id="404-state" class="feature-state feature-state-off"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to enable this feature','wp-seopress').'</span>';
                        } else { 
                            echo '<span id="404-state-default" class="feature-state"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to enable this feature','wp-seopress').'</span>';
                            echo '<span id="404-state" class="feature-state feature-state-off"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to disable this feature','wp-seopress').'</span>';
                        }
                    ?>
                    <?php
                    if (function_exists('seopress_get_locale')) {
                            if (seopress_get_locale() =='fr') {
                                $seopress_docs_link['support']['redirections'] = 'https://www.seopress.org/fr/support/guides/activer-redirections-301-surveillance-404/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
                            } else {
                                $seopress_docs_link['support']['redirections'] = 'https://www.seopress.org/support/guides/redirections/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
                            }
                        }
                    ?>
                    <a href="<?php echo $seopress_docs_link['support']['redirections']; ?>" target="_blank" class="seopress-doc" title="<?php _e('Read our guide','wp-seopress'); ?>"><span class="dashicons dashicons-editor-help"></span><span class="screen-reader-text"><?php _e('Guide to enable 301 redirections and 404 monitoring - new window','wp-seopress'); ?></span></a>
                </span>
            </div>
        <?php
        }
        $seopress_feature = apply_filters('seopress_remove_feature_bot', true);
        if ($seopress_feature === true) { ?>
            <div class="seopress-feature">
                <span class="inner">
                    <div class="img-tool">
                        <span class="dashicons dashicons-admin-generic"></span>
                    </div>
                    <h3><?php _e('Broken links','wp-seopress'); ?></h3>
                    <p><?php _e('Scan your site to find SEO problems.','wp-seopress'); ?></p>
                    <a class="button-secondary view-redirects" href="<?php echo admin_url( 'edit.php?post_type=seopress_bot' ); ?>"><?php _e('See broken links','wp-seopress'); ?></a>
                    <a class="button-secondary" href="<?php echo admin_url( 'admin.php?page=seopress-bot-batch' ); ?>"><?php _e('Scan','wp-seopress'); ?></a>
                    <?php
                        if(seopress_get_toggle_option('bot')=='1') { 
                            $seopress_get_toggle_bot_option = '1';
                        } else { 
                            $seopress_get_toggle_bot_option = '0';
                        }
                    ?>
                    <input type="checkbox" name="toggle-bot" id="toggle-bot" class="toggle" data-toggle="<?php echo $seopress_get_toggle_bot_option; ?>">
                    <label for="toggle-bot"></label>
                    <?php
                        if($seopress_get_toggle_bot_option =='1') { 
                            echo '<span id="bot-state-default" class="feature-state"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to disable this feature','wp-seopress').'</span>';
                            echo '<span id="bot-state" class="feature-state feature-state-off"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to enable this feature','wp-seopress').'</span>';
                        } else { 
                            echo '<span id="bot-state-default" class="feature-state"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to enable this feature','wp-seopress').'</span>';
                            echo '<span id="bot-state" class="feature-state feature-state-off"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to disable this feature','wp-seopress').'</span>';
                        }
                    ?>
                    <?php
                        if (function_exists('seopress_get_locale')) {
                            if (seopress_get_locale() =='fr') {
                                $seopress_docs_link['support']['broken'] = 'https://www.seopress.org/fr/support/guides/detecter-liens-casses/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
                            } else {
                                $seopress_docs_link['support']['broken'] = 'https://www.seopress.org/support/guides/detect-broken-links/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
                            }
                        }
                    ?>
                    <a href="<?php echo $seopress_docs_link['support']['broken']; ?>" target="_blank" class="seopress-doc" title="<?php _e('Read our guide','wp-seopress'); ?>"><span class="dashicons dashicons-editor-help"></span><span class="screen-reader-text"><?php _e('Guide to find broken links with SEOPress PRO - new window','wp-seopress'); ?></span></a>
                </span>
            </div>
        <?php
        }
        $seopress_feature = apply_filters('seopress_remove_feature_backlinks', true);
        if ($seopress_feature === true) { ?>
            <div class="seopress-feature">
                <span class="inner">
                    <div class="img-tool">
                        <span class="dashicons dashicons-admin-links"></span>
                    </div>
                    <h3><?php _e('Backlinks','wp-seopress'); ?></h3>
                    <p><?php _e('Check your backlinks with Majestic API.','wp-seopress'); ?></p>
                    <a class="button-secondary view-redirects" href="<?php echo admin_url( 'edit.php?post_type=seopress_backlinks' ); ?>"><?php _e('See backlinks','wp-seopress'); ?></a>
                    <a class="button-secondary" href="<?php echo admin_url( 'admin.php?page=seopress-pro-page#tab=tab_seopress_backlinks$12' ); ?>"><?php _e('Manage','wp-seopress'); ?></a>
                </span>
            </div>
        <?php
        }
        $seopress_feature = apply_filters('seopress_remove_feature_rewrite', true);
        if ($seopress_feature === true) { ?>
            <div class="seopress-feature">
                <span class="inner">
                    <div class="img-tool">
                        <span class="dashicons dashicons-admin-links"></span>
                    </div>
                    <h3><?php _e('URL Rewriting','wp-seopress'); ?></h3>
                    <p><?php _e('Customize your permalinks.','wp-seopress'); ?></p>
                    <a class="button-secondary" href="<?php echo admin_url( 'admin.php?page=seopress-pro-page#tab=tab_seopress_rewrite$14' ); ?>"><?php _e('Manage','wp-seopress'); ?></a>
                    <?php         
                    if(function_exists('seopress_get_toggle_option') && seopress_get_toggle_option('rewrite')=='1') { 
                        $seopress_get_toggle_rewrite_option = '1';
                    } else { 
                        $seopress_get_toggle_rewrite_option = '0';
                    }
                    ?>

                        <input type="checkbox" name="toggle-rewrite" id="toggle-rewrite" class="toggle" data-toggle="<?php echo $seopress_get_toggle_rewrite_option; ?>">
                        <label for="toggle-rewrite"></label>
                        
                        <?php
                        if($seopress_get_toggle_rewrite_option =='1') { 
                            echo '<span id="rewrite-state-default" class="feature-state"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to disable this feature','wp-seopress').'</span>';
                            echo '<span id="rewrite-state" class="feature-state feature-state-off"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to enable this feature','wp-seopress').'</span>';
                        } else { 
                            echo '<span id="rewrite-state-default" class="feature-state"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to enable this feature','wp-seopress').'</span>';
                            echo '<span id="rewrite-state" class="feature-state feature-state-off"><span class="dashicons dashicons-arrow-left-alt"></span>'.__('Click to disable this feature','wp-seopress').'</span>';
                        }
                    ?>
                </span>
            </div>
        <?php 
        }
        if (!is_multisite()) { 
            $seopress_feature = apply_filters('seopress_remove_feature_htaccess', true);
            if ($seopress_feature === true) {
            ?>
                <div class="seopress-feature">
                    <span class="inner">
                        <div class="img-tool">
                            <span class="dashicons dashicons-media-text"></span>                             
                        </div>
                        <h3><?php _e('.htaccess','wp-seopress'); ?></h3>
                        <p><?php _e('Edit your htaccess file.','wp-seopress'); ?></p>
                        <a class="button-secondary" href="<?php echo admin_url( 'admin.php?page=seopress-pro-page#tab=tab_seopress_htaccess$7' ); ?>"><?php _e('Manage','wp-seopress'); ?>
                        </a>
                    </span>
                </div>
            <?php 
            }
        } 
        $seopress_feature = apply_filters('seopress_remove_feature_rss', true);
        if ($seopress_feature === true) { ?>
            <div class="seopress-feature">
                <span class="inner">
                    <div class="img-tool">
                        <span class="dashicons dashicons-rss"></span>                             
                    </div>
                    <h3><?php _e('RSS','wp-seopress'); ?></h3>
                    <p><?php _e('Configure default WordPress RSS.','wp-seopress'); ?></p>
                    <a class="button-secondary" href="<?php echo admin_url( 'admin.php?page=seopress-pro-page#tab=tab_seopress_rss$11' ); ?>"><?php _e('Manage','wp-seopress'); ?>
                    </a>
                </span>
            </div>
        <?php 
        }
    }
    $seopress_feature = apply_filters('seopress_remove_feature_tools', true);
    if ($seopress_feature === true) { ?>
        <div class="seopress-feature">
            <span class="inner">
                <div class="img-tool">
                    <span class="dashicons dashicons-admin-settings"></span>                                   
                </div>
                <h3><?php _e('Tools','wp-seopress'); ?></h3>
                <p><?php _e('Import/Export plugin settings from site to site.','wp-seopress'); ?></p>
                <a class="button-secondary" href="<?php echo admin_url( 'admin.php?page=seopress-import-export' ); ?>"><?php _e('Manage','wp-seopress'); ?></a>
                <?php
                    if (function_exists('seopress_get_locale')) {
                        if (seopress_get_locale() =='fr') {
                            $seopress_docs_link['support']['export'] = 'https://www.seopress.org/fr/support/guides/exporter-importer-remise-a-niveau-parametres/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
                        } else {
                            $seopress_docs_link['support']['export'] = 'https://www.seopress.org/support/guides/export-import-reset-settings/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
                        }
                    }
                ?>
                <a href="<?php echo $seopress_docs_link['support']['export']; ?>" target="_blank" class="seopress-doc" title="<?php _e('Read our guide','wp-seopress'); ?>"><span class="dashicons dashicons-editor-help"></span><span class="screen-reader-text"><?php _e('Guide to Export/Import/Reset settings - new window','wp-seopress'); ?></span></a>
            </span>
        </div>
    <?php 
    }
    if (is_plugin_active('wp-seopress-pro/seopress-pro.php')) {
        $seopress_feature = apply_filters('seopress_remove_feature_license', true);
        if ($seopress_feature === true) { ?>
            <div class="seopress-feature">
                <span class="inner">
                    <div class="img-tool">
                        <span class="dashicons dashicons-admin-network"></span>                                   
                    </div>
                    <h3><?php _e('License','wp-seopress'); ?></h3>
                    <p><?php _e('Edit your license key.','wp-seopress'); ?></p>
                    <a class="button-secondary" href="<?php echo admin_url( 'admin.php?page=seopress-license' ); ?>"><?php _e('Manage','wp-seopress'); ?></a>
                    <?php
                        if (function_exists('seopress_get_locale')) {
                            if (seopress_get_locale() =='fr') {
                                $seopress_docs_link['support']['license'] = 'https://www.seopress.org/fr/support/guides/activer-licence-seopress-pro/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
                            } else {
                                $seopress_docs_link['support']['license'] = 'https://www.seopress.org/support/guides/activate-seopress-pro-license/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress';
                            }
                        }
                    ?>
                    <a href="<?php echo $seopress_docs_link['support']['license']; ?>" target="_blank" class="seopress-doc" title="<?php _e('Read our guide','wp-seopress'); ?>"><span class="dashicons dashicons-editor-help"></span><span class="screen-reader-text"><?php _e('Guide to activate SEOPress PRO - new window','wp-seopress'); ?></span></a>
                </span>
            </div>
        <?php 
        }
    } ?>
</div>