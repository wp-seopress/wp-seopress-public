<?php
    // To prevent calling the plugin directly
    if (! function_exists('add_action')) {
        echo 'Please don&rsquo;t call the plugin directly. Thanks :)';
        exit;
    }

    if (defined('SEOPRESS_WL_ADMIN_HEADER') && SEOPRESS_WL_ADMIN_HEADER === false) {
        //do nothing
    } else {
    ?>
    <div id="seopress-notifications-center" class="seopress-notifications-center">
        <div class="seopress-notifications-list-content seopress-notifications-active">
            <?php
                $args = seopress_get_service('Notifications')->generateAllNotifications();
                $args = seopress_get_service('Notifications')->orderByImpact($args);
                if (!empty($args)) {
                    foreach($args as $arg) {
                        if (isset($arg['status']) && $arg['status'] === true) {
                            echo seopress_get_service('Notifications')->renderNotification($arg);
                        }
                    }
                }
                if ($total === 0) { ?>
                    <div class="seopress-notifications-none">
                        <img src="<?php echo SEOPRESS_ASSETS_DIR . '/img/ico-notifications.svg'; ?>" width="56" height="56" alt=""/>
                        <h3>
                            <?php _e('You don‘t have any notifications yet!', 'wp-seopress'); ?>
                        </h3>
                    </div>
                <?php }
            ?>
        </div>
        <details class="seopress-notifications-hidden">
            <summary class="seopress-notifications-list-title">
                <img src="<?php echo SEOPRESS_ASSETS_DIR; ?>/img/ico-notifications-hidden.svg" alt='' width='32' height='32' />
                <h3 id="seopress-hidden-notifications"><?php _e('Hidden notifications','wp-seopress'); ?></h3>
            </summary>
            <div class="seopress-notifications-list-content">
                <?php
                    if (!empty($args)) {
                        $html = '';
                        foreach($args as $arg) {
                            if (isset($arg['status']) && $arg['status'] === false) {
                                $html .= seopress_get_service('Notifications')->renderNotification($arg);
                            }
                        }
                        if (!empty($html)) {
                            echo $html;
                        } else {
                            echo '<p>' . __('You currently have no hidden notifications.', 'wp-seopress') . '</p>';
                        }
                    }
                ?>
            </div>
        </details>
    </div>
    <!--#seopress-notifications-center-->
<?php
}
