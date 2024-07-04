<?php
defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

if (defined('SEOPRESS_WL_ADMIN_HEADER') && SEOPRESS_WL_ADMIN_HEADER === false) {
    //do nothing
} else {

    $notifications = seopress_get_service('Notifications')->getSeverityNotification('all');

    $total = $alerts_high = $alerts_medium = $alerts_low = $alerts_info = 0;

    if (!empty($notifications['total'])) {
        $total = $notifications['total'];
    }

    $class = '1' !== seopress_get_service('AdvancedOption')->getAppearanceNotification() ? 'is-active' : '';

    if ($total !== 0) {
?>
    <div id="seopress-notifications" class="seopress-notifications seopress-card <?php echo esc_attr($class); ?>" style="display: none">
        <div class="seopress-card-content">
            <p>
                <?php
                    /* translators: %s number of notifications */
                    printf(esc_attr(_n('You have %s notification. We strongly encourage you to resolve this issue to avoid any SEO damage.', 'You have %s notifications. We strongly encourage you to resolve these issues to avoid any SEO damage.', esc_attr($total), 'wp-seopress')), '<span>'.esc_attr($total).'</span>');
                ?>
            </p>
            <button id="seopress-see-notifications" type="button" role="tab" aria-selected="true" data-panel="notifications" class="btn btnSecondary">
                <?php esc_attr_e('See all notifications', 'wp-seopress'); ?>
            </button>
        </div>
    </div>
    <?php
    }
}
