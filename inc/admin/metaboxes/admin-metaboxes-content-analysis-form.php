<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

$data_attr = seopress_metaboxes_init();
?>

<div id="seopress-ca-tabs" class="wrap-seopress-analysis"
    data-home-id="<?php echo esc_attr($data_attr['isHomeId']); ?>"
    data-term-id="<?php echo esc_attr($data_attr['termId']); ?>"
    data_id="<?php echo esc_attr($data_attr['current_id']); ?>"
    data_origin="<?php echo esc_attr($data_attr['origin']); ?>"
    data_tax="<?php echo esc_attr($data_attr['data_tax']); ?>">

    <?php do_action('seopress_ca_tab_before'); ?>

    <div id="seopress-ca-tabs-2">
        <p>
            <?php esc_html_e('Enter a few keywords for analysis to help you write optimized content.', 'wp-seopress'); ?>
        </p>
        <div class="col-left">
            <p>
                <label for="seopress_analysis_target_kw_meta"><?php esc_html_e('Target keywords', 'wp-seopress'); ?></label>
                <span class="description"><?php esc_html_e('Separate target keywords with commas. Do not use spaces after the commas, unless you want to include them.', 'wp-seopress'); ?></span>
                <input id="seopress_analysis_target_kw_meta" type="text" name="seopress_analysis_target_kw"
                    placeholder="<?php esc_html_e('Enter your target keywords', 'wp-seopress'); ?>"
                    aria-label="<?php esc_attr_e('Target keywords', 'wp-seopress'); ?>"
                    value="<?php echo esc_attr($seopress_analysis_target_kw); ?>" />
            </p>

            <button id="seopress_launch_analysis" type="button" class="<?php echo esc_attr(seopress_btn_secondary_classes()); ?>" data_id="<?php echo absint(get_the_ID()); ?>" data_post_type="<?php echo esc_attr(get_current_screen()->post_type); ?>"><?php esc_html_e('Refresh analysis', 'wp-seopress'); ?></button>

            <?php do_action('seopress_ca_after_resfresh_analysis'); ?>

            <p><span class="description"><?php esc_attr_e('To get the most accurate analysis, save your post first. We analyze all of your source code as a search engine would.', 'wp-seopress'); ?></span></p>
        </div>
            <?php do_action('seopress_ca_before'); ?>

            <div id="seopress-wrap-notice-target-kw" style="clear:both">
                <?php
                    $html = '';
                    $i = 0;
                    $itemData = seopress_get_service('ContentAnalysisDatabase')->getData($post->ID , ["keywords"]);

                    if (isset($itemData['keywords']) && !empty($itemData['keywords'])) {
                        $kwsCount = seopress_get_service('CountTargetKeywordsUse')->getCountByKeywords($itemData['keywords'], $post->ID);

                        foreach($kwsCount as $kw => $item) {
                            if(count($item['rows']) <= 1){
                                continue;
                            }
                            $html .= '<li>
                                    <span class="dashicons dashicons-minus"></span>
                                    <strong>' . $item['key'] . '</strong>
                                    ' . /* translators: %d number of times the target keyword is used */ sprintf(_n('is already used %d time', 'is already used %d times', count($item['rows']) - 1, 'wp-seopress'), count($item['rows']) - 1). '
                                </li>';
                            if (!empty($item['rows'])) {
                                $html .= '<details><summary>' . __('(URL using this keyword)', 'wp-seopress') . '</summary><ul>';
                                foreach($item['rows'] as $row) {
                                    if ($row['post_id'] == $post->ID) {
                                        continue;
                                    }
                                    $html .= '<li><span class="dashicons dashicons-edit-page"></span><a href="' . $row['edit_link'] . '">' . $row['title'] . '</a></li>';
                                }
                                $html .= '</ul></details>';
                            }
                            $i++;
                        }
                    }
                ?>

                <?php if (!empty($html)) { ?>
                    <div id="seopress-notice-target-kw" class="seopress-notice is-warning">
                        <p><?php echo esc_html(sprintf(_n('The keyword:','These keywords:', $i, 'wp-seopress'), absint(number_format_i18n($i)))); ?></p>
                        <ul>
                            <?php echo $html; ?>
                        </ul>
                        <p><?php esc_html_e('You should avoid using multiple times the same keyword for different pages. Try to consolidate your content into one single page.','wp-seopress'); ?></p>
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
