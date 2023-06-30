<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

// Set class property
$this->options = get_option('seopress_option_name');
$current_tab   ='';
if (function_exists('seopress_admin_header')) {
    echo seopress_admin_header();
}
?>

<div id="seopress-content" class="seopress-option">
    <!--Get started-->
    <?php
if ('1' !== seopress_get_service('NoticeOption')->getNoticeGetStarted()) {
    if (method_exists(seopress_get_service('ToggleOption'), 'getToggleWhiteLabel') && '1' === seopress_get_service('ToggleOption')->getToggleWhiteLabel()) {
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
                    'seopress-page-list' => '/blocks/features-list.php'
                ];

                $order = seopress_get_service('DashboardOption')->getDashboardCardsOrder();

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
