<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

// Set class property
$this->options = get_option('seopress_option_name');
$current_tab   ='';
if (function_exists('seopress_admin_header')) {
    echo seopress_admin_header();
} ?>
<div id="seopress-content" class="seopress-option">
    <!--Get started-->
    <?php
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
if ('1' != seopress_get_hidden_notices_get_started_option()) {
    if (function_exists('seopress_get_toggle_white_label_option') && '1' == seopress_get_toggle_white_label_option()) {
        //do nothing
    } else {
        include_once dirname(dirname(__FILE__)) . '/blocks/get-started.php';
    }
} ?>
    <div class="seopress-dashboard-columns">
        <div class="seopress-dashboard-column">
            <?php include_once dirname(dirname(__FILE__)) . '/blocks/intro.php'; ?>
            <?php include_once dirname(dirname(__FILE__)) . '/blocks/tasks.php'; ?>
            <?php include_once dirname(dirname(__FILE__)) . '/blocks/notifications-center.php'; ?>
        </div>
        <div class="seopress-dashboard-column">
            <?php
                if (is_plugin_active('wp-seopress-pro/seopress-pro.php')) {
                    include_once dirname(dirname(__FILE__)) . '/blocks/insights.php';
                } ?>
            <?php include_once dirname(dirname(__FILE__)) . '/blocks/news.php'; ?>
            <?php include_once dirname(dirname(__FILE__)) . '/admin-features-list.php'; ?>
        </div>
    </div>

    <?php echo $this->seopress_feature_save(); ?>
</div>
<?php
