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
        include_once dirname(dirname(__FILE__)) . '/blocks/intro.php';
        include_once dirname(dirname(__FILE__)) . '/blocks/notifications.php';
    ?>

    <div class="seopress-dashboard-columns">
        <div class="seopress-dashboard-column">
            <?php
                include_once dirname(dirname(__FILE__)) . '/blocks/get-started.php';
                include_once dirname(dirname(__FILE__)) . '/blocks/tasks.php';
            ?>
        </div>
        <?php
            include_once dirname(dirname(__FILE__)) . '/blocks/insights.php';
        ?>
    </div>
    <?php
        include_once dirname(dirname(__FILE__)) . '/blocks/features-list.php';
        include_once dirname(dirname(__FILE__)) . '/blocks/ebooks.php';
        include_once dirname(dirname(__FILE__)) . '/blocks/integrations.php';
        include_once dirname(dirname(__FILE__)) . '/blocks/news.php';
        $this->feature_save();
    ?>
</div>
<?php echo $this->feature_save(); ?>
<?php
