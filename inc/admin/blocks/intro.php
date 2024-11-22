<?php
    defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

    if (defined('SEOPRESS_WL_ADMIN_HEADER') && SEOPRESS_WL_ADMIN_HEADER === false) {
        //do nothing
    } else {
?>

<div id="seopress-intro" class="seopress-intro">
    <div>
        <img src="<?php echo esc_url(SEOPRESS_ASSETS_DIR . '/img/logo-seopress.svg'); ?>" width="72" height="72" alt=""/>
    </div>

    <div>
        <h1>
            <?php
                /* translators: %s displays the current version number */
                printf(esc_attr__('Welcome to SEOPress %s!', 'wp-seopress'), '8.2');
            ?>
        </h1>
        <p><?php esc_attr_e('Your control center for SEO.', 'wp-seopress'); ?></p>
    </div>
</div>

<?php }
