<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

$data_attr = seopress_metaboxes_init();

?>

<div class="wrap-seopress-analysis"
    data-home-id="<?php echo $data_attr['isHomeId']; ?>"
    data-term-id="<?php echo $data_attr['termId']; ?>"
    data_id="<?php echo $data_attr['current_id']; ?>"
    data_origin="<?php echo $data_attr['origin']; ?>"
    data_tax="<?php echo $data_attr['data_tax']; ?>">
    <p>
        <?php _e('Enter a few keywords for analysis to help you write optimized content.', 'wp-seopress'); ?>
    </p>
    <p class="description-alt">
        <svg width="24" height="24" viewBox="0 0 24 24" role="img" aria-hidden="true" focusable="false">
            <path
                d="M12 15.8c-3.7 0-6.8-3-6.8-6.8s3-6.8 6.8-6.8c3.7 0 6.8 3 6.8 6.8s-3.1 6.8-6.8 6.8zm0-12C9.1 3.8 6.8 6.1 6.8 9s2.4 5.2 5.2 5.2c2.9 0 5.2-2.4 5.2-5.2S14.9 3.8 12 3.8zM8 17.5h8V19H8zM10 20.5h4V22h-4z">
            </path>
        </svg>
        <?php _e('Writing content for your users is the most important thing! If it doesn‘t feel natural, your visitors will leave your site, Google will know it and your ranking will be affected.', 'wp-seopress'); ?>
    </p>
    <div class="col-left">
        <p>
            <label for="seopress_analysis_target_kw_meta"><?php _e('Target keywords', 'wp-seopress'); ?>
                <?php echo seopress_tooltip(__('Target keywords', 'wp-seopress'), __('Separate target keywords with commas. Do not use spaces after the commas, unless you want to include them', 'wp-seopress'), esc_html('my super keyword,another keyword,keyword')); ?>
            </label>
            <input id="seopress_analysis_target_kw_meta" type="text" name="seopress_analysis_target_kw"
                placeholder="<?php esc_html_e('Enter your target keywords', 'wp-seopress'); ?>"
                aria-label="<?php _e('Target keywords', 'wp-seopress'); ?>"
                value="<?php esc_attr_e($seopress_analysis_target_kw); ?>" />
        </p>
        <?php if (empty($seopress_analysis_data)) { ?>
        <button id="seopress_launch_analysis" type="button" class="<?php echo seopress_btn_secondary_classes(); ?>"
            data_id="<?php echo get_the_ID(); ?>"
            data_post_type="<?php echo get_current_screen()->post_type; ?>"><?php _e('Analyze my content', 'wp-seopress'); ?></button>
        <?php } else { ?>
        <button id="seopress_launch_analysis" type="button"
            class="<?php echo seopress_btn_secondary_classes(); ?>"
            data_id="<?php echo get_the_ID(); ?>"
            data_post_type="<?php echo get_current_screen()->post_type; ?>"><?php _e('Refresh analysis', 'wp-seopress'); ?></button>
        <?php }

            if (is_plugin_active('wp-seopress-insights/seopress-insights.php')) { ?>
        <button id="seopress_add_to_insights" type="button"
            class="<?php echo seopress_btn_secondary_classes(); ?>"
            data_id="<?php echo get_the_ID(); ?>"
            data_post_type="<?php echo get_current_screen()->post_type; ?>">
            <?php _e('Track with Insights', 'wp-seopress'); ?>
        </button>
        <div id="seopress_add_to_insights_status"></div>
        <span class="spinner"></span>
        <?php } ?>

        <p><span class="description"><?php _e('To get the most accurate analysis, save your post first. We analyze all of your source code as a search engine would.', 'wp-seopress'); ?></span>
        </p>
    </div>
    <?php if (is_plugin_active('wp-seopress-pro/seopress-pro.php')) { ?>
    <div class="col-right">
        <p>
            <label for="seopress_google_suggest_kw_meta">
                <?php _e('Google suggestions', 'wp-seopress'); ?>
                <?php echo seopress_tooltip(__('Google suggestions', 'wp-seopress'), __('Enter a keyword, or a phrase, to find the top 10 Google suggestions instantly. This is useful if you want to work with the long tail technique.', 'wp-seopress'), esc_html('my super keyword,another keyword,keyword')); ?>
            </label>
            <input id="seopress_google_suggest_kw_meta" type="text" name="seopress_google_suggest_kw"
                placeholder="<?php _e('Get suggestions from Google', 'wp-seopress'); ?>"
                aria-label="Google suggestions" value="">
            <span class="description"><?php _e('Click on a suggestion below to add it as a target keyword.', 'wp-seopress'); ?></span>
        </p>
        <button id="seopress_get_suggestions" type="button"
            class="<?php echo seopress_btn_secondary_classes(); ?>">
            <?php _e('Get suggestions!', 'wp-seopress'); ?>
        </button>

        <ul id='seopress_suggestions'></ul>
        <?php if ('' != get_locale()) {
                $locale       = substr(get_locale(), 0, 2);
                $country_code = substr(get_locale(), -2);
            } else {
                $locale       = 'en';
                $country_code = 'US';
            } ?>
        <script>
            jQuery('#seopress_get_suggestions').on('click', function(data) {
                data.preventDefault();

                document.getElementById('seopress_suggestions').innerHTML = '';

                var kws = jQuery('#seopress_google_suggest_kw_meta').val();

                if (kws) {
                    var script = document.createElement('script');
                    script.src =
                        'https://www.google.com/complete/search?client=firefox&format=rich&hl=<?php echo $locale; ?>&q=' +
                        kws +
                        '&gl=<?php echo $country_code; ?>&callback=seopress_google_suggest';
                    document.body.appendChild(script);
                }
            });
        </script>
    </div>
    <?php }

if (function_exists('seopress_get_service')) {
    $analyzes = seopress_get_service('GetContentAnalysis')->getAnalyzes($post);
    seopress_get_service('RenderContentAnalysis')->render($analyzes, $seopress_analysis_data);
}
