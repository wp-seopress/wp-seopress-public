<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

$data_attr = seopress_metaboxes_init();
?>

<div id="seopress-ca-tabs" class="wrap-seopress-analysis"
    data-home-id="<?php echo $data_attr['isHomeId']; ?>"
    data-term-id="<?php echo $data_attr['termId']; ?>"
    data_id="<?php echo $data_attr['current_id']; ?>"
    data_origin="<?php echo $data_attr['origin']; ?>"
    data_tax="<?php echo $data_attr['data_tax']; ?>">

    <?php do_action('seopress_ca_tab_before'); ?>

    <div id="seopress-ca-tabs-2">
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

            <button id="seopress_launch_analysis" type="button" class="<?php echo seopress_btn_secondary_classes(); ?>" data_id="<?php echo get_the_ID(); ?>" data_post_type="<?php echo get_current_screen()->post_type; ?>"><?php _e('Refresh analysis', 'wp-seopress'); ?></button>

            <?php do_action('seopress_ca_after_resfresh_analysis'); ?>

            <p><span class="description"><?php _e('To get the most accurate analysis, save your post first. We analyze all of your source code as a search engine would.', 'wp-seopress'); ?></span></p>
        </div>
            <?php do_action('seopress_ca_before'); ?>

            <div id="seopress-wrap-notice-target-kw" style="clear:both">
                <?php
                    $html = '';
                    $i = 0;
                    if (!empty($seopress_analysis_data['target_kws_count'])) {
                        foreach($seopress_analysis_data['target_kws_count'] as $kw => $item) {
                            if(!is_array($item)){
                                continue;
                            }

                            if(count($item['rows']) === 0){
                                continue;
                            }
                            $html .= '<li>
                                    <span class="dashicons dashicons-minus"></span>
                                    <strong>' . $item['key'] . '</strong>
                                    ' . sprintf(_n('is already used %d time', 'is already used %d times', count($item['rows']), 'wp-seopress'), count($item['rows'])). '
                                </li>';
                            $i++;
                        }
                    }
                ?>

                <?php if (!empty($html)) { ?>
                    <div id="seopress-notice-target-kw" class="seopress-notice is-warning">
                        <p><?php printf(_n('The keyword:','These keywords:', $i, 'wp-seopress'), number_format_i18n($i)); ?></p>
                        <ul>
                            <?php echo $html; ?>
                        </ul>
                        <p><?php _e('You should avoid using multiple times the same keyword for different pages. Try to consolidate your content into one single page.','wp-seopress'); ?></p>
                    </div>
                <?php } ?>
            </div>
        <?php
        if (function_exists('seopress_get_service')) {
            $analyzes = seopress_get_service('GetContentAnalysis')->getAnalyzes($post);
            seopress_get_service('RenderContentAnalysis')->render($analyzes, $seopress_analysis_data);
        } ?>
    </div>
    <?php do_action('seopress_ca_tab_after', $data_attr['current_id']); ?>
</div>
