<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

function print_section_info_xml_sitemap_general()
{
    $docs = seopress_get_docs_links(); ?>
<div class="sp-section-header">
    <h2>
        <?php _e('General', 'wp-seopress'); ?>
    </h2>
</div>

<?php if ('' == get_option('permalink_structure')) { ?>
<div class="seopress-notice is-error">
    <p>
        <?php _e('Your permalinks are not <strong>SEO Friendly</strong>! Enable <strong>pretty permalinks</strong> to fix this.', 'wp-seopress'); ?>
    </p>
    <p>
        <a href="<?php echo admin_url('options-permalink.php'); ?>"
            class="btn btnSecondary">
            <?php _e('Change this settings', 'wp-seopress'); ?>
        </a>
    </p>
</div>
<?php } ?>

<p>
    <?php _e('A sitemap is a file where you provide information about the <strong>pages, images, videos... and the relationships between them</strong>. Search engines like Google read this file to <strong>crawl your site more efficiently</strong>.', 'wp-seopress'); ?>
</p>

<p>
    <?php _e('The XML sitemap is an <strong>exploration aid</strong>. Not having a sitemap will absolutely <strong>NOT prevent engines from indexing your content</strong>. For this, opt for meta robots.', 'wp-seopress'); ?>
</p>

<p><?php _e('This is the URL of your index sitemaps to submit to search engines:','wp-seopress'); ?></p>

<p>
    <pre><span class="dashicons dashicons-external"></span><a href="<?php echo get_option('home'); ?>/sitemaps.xml" target="_blank"><?php echo get_option('home'); ?>/sitemaps.xml</a></pre>
</p>

<?php
if (is_plugin_active('sg-cachepress/sg-cachepress.php')) { ?>
    <div class="seopress-notice">
            <h3><?php _e('SiteGround Optimizer user?', 'wp-seopress'); ?></h3>
            <p><?php _e('We automatically sent your XML sitemap URL for the preheat caching feature.', 'wp-seopress'); ?></p>
        </div>
<?php } ?>

<div class="seopress-notice">
    <p>
        <?php _e('<strong>Noindex content</strong> will not be displayed in Sitemaps. Same for custom canonical URLs.', 'wp-seopress'); ?>
    </p>
    <p>
        <?php _e('If you disable globally this feature (using the blue toggle from above), the <strong>native WordPress XML sitemaps</strong> will be re-activated.', 'wp-seopress'); ?>
    </p>

    <p class="seopress-help">
        <span class="dashicons dashicons-external"></span>
        <a href="<?php echo $docs['sitemaps']['error']['blank']; ?>" target="_blank">
            <?php _e('Blank sitemap?', 'wp-seopress'); ?>
        </a>

        <span class="dashicons dashicons-external"></span>
        <a href="<?php echo $docs['sitemaps']['error']['404']; ?>" target="_blank">
            <?php _e('404 error?', 'wp-seopress'); ?>
        </a>

        <span class="dashicons dashicons-external"></span>
        <a href="<?php echo $docs['sitemaps']['error']['html']; ?>" target="_blank">
            <?php _e('HTML error? Exclude XML and XSL from caching plugins!', 'wp-seopress'); ?>
        </a>
        <span class="dashicons dashicons-external"></span>
        <a href="<?php echo $docs['sitemaps']['xml']; ?>" target="_blank">
            <?php _e('Add your XML sitemaps to Google Search Console (video)', 'wp-seopress'); ?>
        </a>
    </p>
</div>

<?php if (isset($_SERVER['SERVER_SOFTWARE'])) {
        $server_software = explode('/', $_SERVER['SERVER_SOFTWARE']);
        reset($server_software);

        if ('nginx' == current($server_software)) { //IF NGINX
            ?>
<div class="seopress-notice">

    <p>
        <?php _e('Your server uses NGINX. If XML Sitemaps doesn\'t work properly, you need to add this rule to your configuration:', 'wp-seopress'); ?>
    </p>

    <pre>location ~ (([^/]*)sitemap(.*)|news|author|video(.*))\.x(m|s)l$ {
	## SEOPress
	rewrite ^.*/sitemaps\.xml$ /index.php?seopress_sitemap=1 last;
	rewrite ^.*/news.xml$ /index.php?seopress_news=$1 last;
	rewrite ^.*/video.xml$ /index.php?seopress_video=$1 last;
	rewrite ^.*/author.xml$ /index.php?seopress_author=$1 last;
	rewrite ^.*/sitemaps_xsl\.xsl$ /index.php?seopress_sitemap_xsl=1 last;
    rewrite ^.*/sitemaps_video_xsl\.xsl$ /index.php?seopress_sitemap_video_xsl=1 last;
	rewrite ^.*/([^/]+?)-sitemap([0-9]+)?.xml$ /index.php?seopress_cpt=$1&seopress_paged=$2 last;
}</pre>
</div>
<?php }
    } ?>

<?php
}

function print_section_info_html_sitemap()
{
    $docs = seopress_get_docs_links(); ?>

<div class="sp-section-header">
    <h2>
        <?php _e('HTML Sitemap', 'wp-seopress'); ?>
    </h2>
</div>

<p>
    <?php _e('Create an HTML Sitemap for your visitors and boost your SEO.', 'wp-seopress'); ?>
</p>
<p>
    <?php _e('Limited to 1,000 posts per post type. You can change the order and sorting criteria below.', 'wp-seopress'); ?>

    <a class="seopress-doc"
        href="<?php echo $docs['sitemaps']['html']; ?>"
        target="_blank">
        <span class="dashicons dashicons-editor-help"></span>
        <span class="screen-reader-text">
            <?php _e('Guide to enable a HTML Sitemap - new window', 'wp-seopress'); ?>
        </span>
    </a>
</p>

<div class="seopress-notice">
        <h3><?php _e('How to use the HTML Sitemap?', 'wp-seopress'); ?></h3>

        <h4><?php _e('Block Editor', 'wp-seopress'); ?></h4>
        <p><?php _e('Add the HTML sitemap block using the <strong>Block Editor</strong>.', 'wp-seopress'); ?></p>

        <hr>
        <h4><?php _e('Shortcode', 'wp-seopress'); ?></h4>

        <p><?php _e('You can also use this shortcode in your content (post, page, post type...):', 'wp-seopress'); ?></p>
        <pre>[seopress_html_sitemap]</pre>

        <p><?php _e('To include specific custom post types, use the CPT attribute:', 'wp-seopress'); ?></p>
        <pre>[seopress_html_sitemap cpt="post,product"]</pre>

        <h4><?php _e('Other', 'wp-seopress'); ?></h4>
        <p><?php _e('Dynamically display the sitemap by entering an ID to the first field below.', 'wp-seopress'); ?></p>
    </div>
<?php
}

function print_section_info_xml_sitemap_post_types()
{
    ?>
<div class="sp-section-header">
    <h2>
        <?php _e('Post Types', 'wp-seopress'); ?>
    </h2>
</div>
<p>
    <?php _e('Include/Exclude Post Types.', 'wp-seopress'); ?>
</p>

<?php
}

function print_section_info_xml_sitemap_taxonomies()
{
    ?>
<div class="sp-section-header">
    <h2>
        <?php _e('Taxonomies', 'wp-seopress'); ?>
    </h2>
</div>
<p>
    <?php _e('Include/Exclude Taxonomies.', 'wp-seopress'); ?>
</p>

<?php
}
