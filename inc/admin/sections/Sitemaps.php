<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

function seopress_print_section_info_xml_sitemap_general()
{
    $docs = seopress_get_docs_links(); ?>
    <div class="sp-section-header">
        <h2>
            <?php esc_attr_e('General', 'wp-seopress'); ?>
        </h2>
    </div>

    <?php if ('' == get_option('permalink_structure')) { ?>
        <div class="seopress-notice is-error">
            <p>
                <?php echo wp_kses_post(__('Your permalinks are not <strong>SEO Friendly</strong>! Enable <strong>pretty permalinks</strong> to fix this.', 'wp-seopress')); ?>
            </p>
            <p>
                <a href="<?php echo esc_url(admin_url('options-permalink.php')); ?>" class="btn btnSecondary">
                    <?php esc_attr_e('Change this settings', 'wp-seopress'); ?>
                </a>
            </p>
        </div>
    <?php } ?>

    <p>
        <?php echo wp_kses_post(__('A sitemap is a file where you provide information about the <strong>pages, images, videos... and the relationships between them</strong>. Search engines like Google read this file to <strong>crawl your site more efficiently</strong>.', 'wp-seopress')); ?>
    </p>

    <p>
        <?php echo wp_kses_post(__('The XML sitemap is an <strong>exploration aid</strong>. Not having a sitemap will absolutely <strong>NOT prevent engines from indexing your content</strong>. For this, opt for meta robots.', 'wp-seopress')); ?>
    </p>

    <p><?php esc_attr_e('This is the URL of your index sitemaps to submit to search engines:', 'wp-seopress'); ?></p>

    <p>
    <pre><span class="dashicons dashicons-external"></span><a href="<?php echo esc_url(get_option('home')); ?>/sitemaps.xml" target="_blank"><?php echo esc_url(get_option('home')); ?>/sitemaps.xml</a></pre>
    </p>

    <?php
    if (is_plugin_active('sg-cachepress/sg-cachepress.php')) { ?>
        <div class="seopress-notice">
            <h3><?php esc_attr_e('SiteGround Optimizer user?', 'wp-seopress'); ?></h3>
            <p><?php esc_attr_e('We automatically sent your XML sitemap URL for the preheat caching feature.', 'wp-seopress'); ?></p>
        </div>
    <?php } ?>

    <div class="seopress-notice">
        <p>
            <?php echo wp_kses_post(__('<strong>Noindex content</strong> will not be displayed in Sitemaps. Same for custom canonical URLs.', 'wp-seopress')); ?>
        </p>
        <p>
            <?php echo wp_kses_post(__('If you disable globally this feature (using the blue toggle from above), the <strong>native WordPress XML sitemaps</strong> will be re-activated.', 'wp-seopress')); ?>
        </p>

        <p class="seopress-help">
            <span class="dashicons dashicons-external"></span>
            <a href="<?php echo esc_url($docs['sitemaps']['error']['blank']); ?>" target="_blank">
                <?php esc_attr_e('Blank sitemap?', 'wp-seopress'); ?>
            </a>

            <span class="dashicons dashicons-external"></span>
            <a href="<?php echo esc_url($docs['sitemaps']['error']['404']); ?>" target="_blank">
                <?php esc_attr_e('404 error?', 'wp-seopress'); ?>
            </a>

            <span class="dashicons dashicons-external"></span>
            <a href="<?php echo esc_url($docs['sitemaps']['error']['html']); ?>" target="_blank">
                <?php esc_attr_e('HTML error? Exclude XML and XSL from caching plugins!', 'wp-seopress'); ?>
            </a>
            <span class="dashicons dashicons-external"></span>
            <a href="<?php echo esc_url($docs['sitemaps']['xml']); ?>" target="_blank">
                <?php esc_attr_e('Add your XML sitemaps to Google Search Console (video)', 'wp-seopress'); ?>
            </a>
        </p>
    </div>

    <?php if (isset($_SERVER['SERVER_SOFTWARE'])) {
        $server_software = explode('/', sanitize_text_field(wp_unslash($_SERVER['SERVER_SOFTWARE'])));
        reset($server_software);

        if ('nginx' == current($server_software)) { //IF NGINX
    ?>
            <div class="seopress-notice">

                <p>
                    <?php esc_attr_e('Your server uses NGINX. If XML Sitemaps doesn\'t work properly, you need to add this rule to your configuration:', 'wp-seopress'); ?>
                </p>

                <pre>location ~ (([^/]*)sitemap(.*)|news|author|video(.*))\.x(m|s)l$ {
## SEOPress
rewrite ^.*/sitemaps\.xml$ /index.php?seopress_sitemap=1 last;
rewrite ^.*/news.xml$ /index.php?seopress_news=1 last;
rewrite ^.*/video([0-9]+)?.xml$ /index.php?seopress_video=1&seopress_paged=$1 last;
rewrite ^.*/author.xml$ /index.php?seopress_author=1 last;
rewrite ^.*/sitemaps_xsl\.xsl$ /index.php?seopress_sitemap_xsl=1 last;
rewrite ^.*/sitemaps_video_xsl\.xsl$ /index.php?seopress_sitemap_video_xsl=1 last;
rewrite ^.*/([^/]+?)-sitemap([0-9]+)?.xml$ /index.php?seopress_cpt=$1&seopress_paged=$2 last;
}</pre>

                <p>
                    <?php esc_attr_e('Contact your webhost for help and send them these rules.', 'wp-seopress'); ?>
                </p>
            </div>
    <?php }
    } ?>

<?php
}

function seopress_print_section_info_html_sitemap()
{
    $docs = seopress_get_docs_links(); ?>

    <div class="sp-section-header">
        <h2>
            <?php esc_attr_e('HTML Sitemap', 'wp-seopress'); ?>
        </h2>
    </div>

    <p>
        <?php esc_attr_e('Create an HTML Sitemap for your visitors and boost your SEO.', 'wp-seopress'); ?>
    </p>
    <p>
        <?php esc_attr_e('Limited to 1,000 posts per post type. You can change the order and sorting criteria below.', 'wp-seopress'); ?>

        <a class="seopress-doc" href="<?php echo esc_url($docs['sitemaps']['html']); ?>" target="_blank">
            <span class="dashicons dashicons-editor-help"></span>
            <span class="screen-reader-text">
                <?php esc_attr_e('Guide to enable a HTML Sitemap - new window', 'wp-seopress'); ?>
            </span>
        </a>
    </p>

    <div class="seopress-notice">
        <h3><?php esc_attr_e('How to use the HTML Sitemap?', 'wp-seopress'); ?></h3>

        <h4><?php esc_attr_e('Block Editor', 'wp-seopress'); ?></h4>
        <p><?php echo wp_kses_post(__('Add the HTML sitemap block using the <strong>Block Editor</strong>.', 'wp-seopress')); ?></p>

        <hr>
        <h4><?php esc_attr_e('Shortcode', 'wp-seopress'); ?></h4>

        <p><?php esc_attr_e('You can also use this shortcode in your content (post, page, post type...):', 'wp-seopress'); ?></p>
        <pre>[seopress_html_sitemap]</pre>

        <p><?php esc_attr_e('To include specific custom post types, use the CPT attribute:', 'wp-seopress'); ?></p>
        <pre>[seopress_html_sitemap cpt="post,product"]</pre>

        <p><?php esc_attr_e('To display only categories and/or product categories, use the terms_only attribute:', 'wp-seopress'); ?></p>
        <pre>[seopress_html_sitemap terms_only="true"]</pre>

        <p class="seopress-help">
            <span class="dashicons dashicons-external"></span>
            <a href="<?php echo esc_url($docs['sitemaps']['hooks']); ?>" target="_blank">
                <?php esc_attr_e('For advanced usage, you can also use the filters listed in the documentation.', 'wp-seopress'); ?>
            </a>
        </p>

        <h4><?php esc_attr_e('Other', 'wp-seopress'); ?></h4>
        <p><?php esc_attr_e('Dynamically display the sitemap by entering an ID to the first field below.', 'wp-seopress'); ?></p>
    </div>
<?php
}

function seopress_print_section_info_xml_sitemap_post_types()
{
?>
    <div class="sp-section-header">
        <h2>
            <?php esc_attr_e('Post Types', 'wp-seopress'); ?>
        </h2>
    </div>
    <p>
        <?php esc_attr_e('Include/Exclude Post Types.', 'wp-seopress'); ?>
    </p>

    <div class="seopress-notice">
        <p>
            <?php
            /* translators: %1$s: <code>public => true</code>, %2$s: <code>show_ui => true</code>*/
            printf(esc_attr__('Only post types registered with the %1$s and %2$s arguments will be listed here.', 'wp-seopress'), '<code>public => true</code>', '<code>show_ui => true</code>');
            ?>
        </p>
    </div>

<?php
}

function seopress_print_section_info_xml_sitemap_taxonomies()
{
?>
    <div class="sp-section-header">
        <h2>
            <?php esc_attr_e('Taxonomies', 'wp-seopress'); ?>
        </h2>
    </div>

    <p>
        <?php esc_attr_e('Include/Exclude Taxonomies.', 'wp-seopress'); ?>
    </p>

    <div class="seopress-notice">
        <p>
            <?php
            /* translators: %1$s: <code>public => true</code>, %2$s: <code>show_ui => true</code>*/
            printf(esc_attr__('Only taxonomies registered with the %1$s and %2$s arguments will be listed here.', 'wp-seopress'), '<code>public => true</code>', '<code>show_ui => true</code>');
            ?>
        </p>
        <p><?php esc_attr_e('Terms without any associated posts will not be listed in your XML sitemaps to prevent 404 errors.', 'wp-seopress'); ?></p>
    </div>

<?php
}
