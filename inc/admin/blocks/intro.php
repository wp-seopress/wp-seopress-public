<?php
    // To prevent calling the plugin directly
    if ( ! function_exists('add_action')) {
        echo 'Please don&rsquo;t call the plugin directly. Thanks :)';
        exit;
    }

    if (defined('SEOPRESS_WL_ADMIN_HEADER') && SEOPRESS_WL_ADMIN_HEADER === false) {
        //do nothing
    } else {
?>

<div id="seopress-intro" class="seopress-intro">
    <div>
        <img src="<?php echo SEOPRESS_ASSETS_DIR . '/img/logo-seopress-square-alt.svg'; ?>" width="72" height="72" alt=""/>
    </div>

    <div>
        <h1>
            <?php
                /* translators: %s displays the current version number */
                printf(__('Welcome to SEOPress %s!', 'wp-seopress'), '7.2');
            ?>
        </h1>
        <p><?php _e('Your control center for SEO.', 'wp-seopress'); ?></p>
    </div>
</div>

<?php }
