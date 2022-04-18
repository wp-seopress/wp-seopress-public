<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

// Set class property
$this->options = get_option('seopress_option_name');
$current_tab   ='';
if (function_exists('seopress_admin_header')) {
    echo seopress_admin_header();
}

function seopress_get_hidden_notices_get_started_option()
{
    $seopress_get_hidden_notices_get_started_option = get_option('seopress_notices');
    if (! empty($seopress_get_hidden_notices_get_started_option)) {
        foreach ($seopress_get_hidden_notices_get_started_option as $key => $seopress_get_hidden_notices_get_started_value) {
            $options[$key] = $seopress_get_hidden_notices_get_started_value;
        }
        if (isset($seopress_get_hidden_notices_get_started_option['notice-get-started'])) {
            return $seopress_get_hidden_notices_get_started_option['notice-get-started'];
        }
    }
}

function seopress_dashboard_cards_order_option()
{
    $seopress_dashboard_cards_order_option = get_option('seopress_dashboard_option_name');
    if (! empty($seopress_dashboard_cards_order_option)) {
        foreach ($seopress_dashboard_cards_order_option as $key => $seopress_dashboard_cards_order_value) {
            $options[$key] = $seopress_dashboard_cards_order_value;
        }
        if (isset($seopress_dashboard_cards_order_option['cards_order'])) {
            return $seopress_dashboard_cards_order_option['cards_order'];
        }
    }
}
?>

<div id="seopress-content" class="seopress-option">
    <!--Get started-->
    <?php
if ('1' != seopress_get_hidden_notices_get_started_option()) {
    if (function_exists('seopress_get_toggle_white_label_option') && '1' == seopress_get_toggle_white_label_option()) {
        //do nothing
    } else {
        include_once dirname(dirname(__FILE__)) . '/blocks/get-started.php';
    }
} ?>
    <div class="seopress-dashboard-columns">
        <div class="seopress-dashboard-column">
            <?php
                include_once dirname(dirname(__FILE__)) . '/blocks/intro.php';
                if (defined('SEOPRESS_WL_ADMIN_HEADER') && SEOPRESS_WL_ADMIN_HEADER === false) {
                    //do nothing
                } else {
                    include_once dirname(dirname(__FILE__)) . '/blocks/tasks.php';
                    include_once dirname(dirname(__FILE__)) . '/blocks/notifications-center.php';
                }
            ?>
        </div>
        <div class="seopress-dashboard-column">
            <?php

                $cards = [
                    'notice-insights-alert' => '/blocks/insights.php',
                    'seopress-news-panel' => '/blocks/news.php',
                    'seopress-page-list' => '/admin-features-list.php'
                ];

                $order = seopress_dashboard_cards_order_option();

                if (!empty($order)) {
                    foreach($order as $key => $value) {
                        if (isset($cards[$value])) {
                            include_once dirname(dirname(__FILE__)) . $cards[$value];
                        }
                    }
                } else {
                    foreach($cards as $key => $value) {
                        include_once dirname(dirname(__FILE__)) . $value;
                    }
                }
            ?>
        </div>
    </div>

    <?php echo $this->seopress_feature_save(); ?>
</div>
<?php
