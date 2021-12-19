<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

function seopress_admin_header($context = "") {
    $docs = seopress_get_docs_links(); ?>

<div id="seopress-header" class="seopress-option">
    <div id="seopress-navbar">
        <ul>
            <li>
                <a
                    href="<?php echo admin_url('admin.php?page=seopress-option'); ?>">
                    <?php _e('Home', 'wp-seopress'); ?>
                </a>
            </li>
            <?php if (get_admin_page_title()) { ?>
            <li>
                <?php echo get_admin_page_title(); ?>
            </li>
            <?php } ?>
        </ul>
    </div>
    <aside id="seopress-activity-panel" class="seopress-activity-panel">
        <div role="tablist" aria-orientation="horizontal" class="seopress-activity-panel-tabs">
            <button type="button" role="tab" aria-selected="true" id="activity-panel-tab-display" data-panel="display"
                class="btn">
                <span class="dashicons dashicons-layout"></span>
                <?php _e('Display', 'wp-seopress'); ?>
            </button>
            <button type="button" role="tab" aria-selected="true" id="activity-panel-tab-help" data-panel="help"
                class="btn">
                <span class="dashicons dashicons-editor-help"></span>
                <?php _e('Documentation', 'wp-seopress'); ?>
            </button>
        </div>
        <div id="seopress-activity-panel-help" class="seopress-activity-panel-wrapper" tabindex="0" role="tabpanel"
            aria-label="Help">
            <div id="activity-panel-true">
                <div class="seopress-activity-panel-header">
                    <div class="seopress-inbox-title">
                        <p><?php _e('Documentation', 'wp-seopress'); ?>
                        </p>
                    </div>
                </div>
                <div>
                    <form
                        action="<?php echo $docs['website']; ?>"
                        method="get" class="seopress-search" target="_blank">
                        <input class="adminbar-input" id="seopress-search" name="s" type="text" value=""
                            placeholder="<?php _e('Search our documentation', 'wp-seopress'); ?>"
                            maxlength="150">
                        <label for="seopress-search" class="screen-reader-text"><?php _e('Search', 'wp-seopress'); ?></label>
                    </form>
                    <ul class="seopress-list-items" role="menu">
                        <?php
                        $docs_started = $docs['get_started'];
                        if ($context ==='insights') {
                            $docs_started = $docs['get_started_insights'];
                        }
                        foreach ($docs_started as $key => $value) {
        foreach ($value as $_key => $_value) {
            ?>
                        <li class="seopress-item">
                            <a href="<?php echo $_value; ?>"
                                class="seopress-item-inner has-action" aria-disabled="false" tabindex="0"
                                role="menuitem" target="_blank" data-link-type="external">
                                <div class="seopress-item-before"></div>
                                <div class="seopress-item-text">
                                    <span class="seopress-item-title">
                                        <?php echo $_key; ?>
                                    </span>
                                </div>
                                <div class="seopress-item-after"></div>
                            </a>
                        </li>
                        <?php
        }
    } ?>
                    </ul>
                </div>
            </div>
        </div>
        <div id="seopress-activity-panel-display" class="seopress-activity-panel-wrapper" tabindex="0" role="popover"
            aria-label="Display">
            <div id="activity-panel-true">
                <div class="seopress-activity-panel-header">
                    <div class="seopress-inbox-title">
                        <p><?php _e('Choose the way it looks', 'wp-seopress'); ?>
                        </p>
                    </div>
                </div>
                <div class="seopress-activity-panel-content">
                    <?php
                            $options = get_option('seopress_advanced_option_name');
    $check                           = isset($options['seopress_advanced_appearance_notifications']); ?>

                    <p>
                        <input id="notifications_center" class="toggle" data-toggle=<?php if ('1' == $check) {
        echo '1';
    } else {
        echo '0';
    } ?> name="seopress_advanced_option_name[seopress_advanced_appearance_notifications]"
                        type="checkbox" <?php if ('1' == $check) {
        echo 'checked="yes"';
    } ?>/>
                        <label for="notifications_center"></label>
                        <label for="seopress_advanced_option_name[seopress_advanced_appearance_notifications]">
                            <?php _e('Hide Notifications Center?', 'wp-seopress'); ?>
                        </label>
                    </p>

                    <?php
    $check = isset($options['seopress_advanced_appearance_news']); ?>

                    <p>
                        <input id="seopress_news" class="toggle" data-toggle=<?php if ('1' == $check) {
        echo '1';
    } else {
        echo '0';
    } ?> name="seopress_advanced_option_name[seopress_advanced_appearance_news]" type="checkbox"
                        <?php if ('1' == $check) {
        echo 'checked="yes"';
    } ?>/>
                        <label for="seopress_news"></label>
                        <label for="seopress_advanced_option_name[seopress_advanced_appearance_news]">
                            <?php _e('Hide SEO News?', 'wp-seopress'); ?>
                        </label>
                    </p>

                    <?php
    $check = isset($options['seopress_advanced_appearance_seo_tools']); ?>

                    <p>
                        <input id="seopress_tools" class="toggle" data-toggle=<?php if ('1' == $check) {
        echo '1';
    } else {
        echo '0';
    } ?> name="seopress_advanced_option_name[seopress_advanced_appearance_seo_tools]" type="checkbox"
                        <?php if ('1' == $check) {
        echo 'checked="yes"';
    } ?>/>
                        <label for="seopress_tools"></label>
                        <label for="seopress_advanced_option_name[seopress_advanced_appearance_seo_tools]">
                            <?php _e('Hide Site Overview?', 'wp-seopress'); ?>
                        </label>
                    </p>
                </div>
            </div>
        </div>
    </aside>
</div>
<?php
}
