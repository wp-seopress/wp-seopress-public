<?php
// To prevent calling the plugin directly
if (!function_exists('add_action')) {
    echo 'Please don&rsquo;t call the plugin directly. Thanks :)';
    exit;
}

if (defined('SEOPRESS_WL_ADMIN_HEADER') && SEOPRESS_WL_ADMIN_HEADER === false) {
    //do nothing
} else {

    $notifications = seopress_get_service('Notifications')->getSeverityNotification('all');

    $total = $alerts_high = $alerts_medium = $alerts_low = $alerts_info = 0;

    if (!empty($notifications['total'])) {
        $total = $notifications['total'];
        $alerts_high = !empty($notifications['severity']['high']) ? $notifications['severity']['high'] : 0;
        $alerts_medium = !empty($notifications['severity']['medium']) ? $notifications['severity']['medium'] : 0;
        $alerts_low = !empty($notifications['severity']['low']) ? $notifications['severity']['low'] : 0;
        $alerts_info = !empty($notifications['severity']['info']) ? $notifications['severity']['info'] : 0;
    }

    $class = '1' !== seopress_get_service('AdvancedOption')->getAppearanceNotification() ? 'is-active' : '';
?>

    <div id="seopress-notifications" class="seopress-notifications seopress-card <?php echo $class; ?>" style="display: none">
        <div class="seopress-card-title">
            <h2>
                <?php
                if ($total === 0) {
                    printf(__('Notifications', 'wp-seopress'), $total);
                } else {
                    printf(_n('You have %d notification', 'You have %d notifications', $total, 'wp-seopress'), $total);
                }
                ?>
            </h2>
        </div>
        <div class="seopress-card-content">
            <?php if ($total !== 0) : ?>
                <?php if ($alerts_high !== 0) : ?>
                    <div class="seopress-alert">
                        <div class="seopress-alert-title">
                            <span class="seopress-impact seopress-impact-high"></span>
                            <h3><?php printf(_n('%d Critical Notification', '%d Critical Notifications', $alerts_high, 'wp-seopress'), $alerts_high); ?></h3>
                        </div>
                        <div class="seopress-alert-content">
                            <p><?php _e('We strongly encourage you to resolve these serious issues to avoid any SEO damage.', 'wp-seopress'); ?></p>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($alerts_medium !== 0) : ?>
                    <div class="seopress-alert">
                        <div class="seopress-alert-title">
                            <span class="seopress-impact seopress-impact-medium"></span>
                            <h3><?php printf(_n('%d Important Notification', '%d Important Notifications', $alerts_medium, 'wp-seopress'), $alerts_medium); ?></h3>
                        </div>
                        <div class="seopress-alert-content">
                            <p><?php _e('These issues can affect your SEO. Please fix them.', 'wp-seopress'); ?></p>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($alerts_low !== 0) : ?>
                    <div class="seopress-alert">
                        <div class="seopress-alert-title">
                            <span class="seopress-impact seopress-impact-low"></span>
                            <h3><?php printf(_n('%d Low Impact Notification', '%d Low Impact Notifications', $alerts_low, 'wp-seopress'), $alerts_low); ?></h3>
                        </div>
                        <div class="seopress-alert-content">
                            <p><?php _e('These issues might have an impact on your SEO.', 'wp-seopress'); ?></p>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($alerts_info !== 0) : ?>
                    <div class="seopress-alert">
                        <div class="seopress-alert-title">
                            <span class="seopress-impact seopress-impact-info"></span>
                            <h3><?php printf(_n('%d Informative Notification', '%d Informative Notifications', $alerts_info, 'wp-seopress'), $alerts_info); ?></h3>
                        </div>
                        <div class="seopress-alert-content">
                            <p><?php _e('Useful notifications you might want to read to improve your SEO.', 'wp-seopress'); ?></p>
                        </div>
                    </div>
                <?php endif; ?>

            <?php else : ?>

                <div class="seopress-notifications-none">
                    <img src="<?php echo SEOPRESS_ASSETS_DIR . '/img/ico-notifications.svg'; ?>" width="56" height="56" alt=""/>
                    <h3>
                        <?php _e('You don‘t have any notifications yet!', 'wp-seopress'); ?>
                    </h3>
                </div>

            <?php endif; ?>

            <button id="seopress-see-notifications" type="button" role="tab" aria-selected="true" data-panel="notifications" class="btn btnSecondary">
                <?php _e('See all notifications', 'wp-seopress'); ?>
            </button>
        </div>
    </div>
    <?php
}
