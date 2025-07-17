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
                $seo_title = 'SEOPress';
                if (is_plugin_active('wp-seopress-pro/seopress-pro.php')) {
                    if (method_exists(seopress_get_service('ToggleOption'), 'getToggleWhiteLabel') && '1' === seopress_get_service('ToggleOption')->getToggleWhiteLabel()) {
                        $seo_title = function_exists('seopress_pro_get_service') && method_exists(seopress_pro_get_service('OptionPro'), 'getWhiteLabelListTitle') && seopress_pro_get_service('OptionPro')->getWhiteLabelListTitle() ? seopress_pro_get_service('OptionPro')->getWhiteLabelListTitle() : 'SEOPress';
                    }
                }

				/* translators: %1$s plugin name, default: SEOPress, %2$s displays the current version number */
				printf(esc_html__('Welcome to %1$s %2$s!', 'wp-seopress'), esc_html($seo_title), '8.9.0.1');
			?>
        </h1>
        <p><?php esc_attr_e('Your control center for SEO.', 'wp-seopress'); ?></p>
    </div>
</div>

<?php }
