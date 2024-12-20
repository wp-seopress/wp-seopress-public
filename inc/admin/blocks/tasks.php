<?php
    defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

    if (is_plugin_active('wp-seopress-pro/seopress-pro.php')) {
        if (method_exists(seopress_get_service('ToggleOption'), 'getToggleWhiteLabel') && '1' === seopress_get_service('ToggleOption')->getToggleWhiteLabel()) {
            return;
        }
    }

    $docs = seopress_get_docs_links();
    $status_pro = get_option('seopress_pro_license_status');
    if (false !== $status_pro && 'valid' == $status_pro) {
        $status_pro = true;
    } else {
        $status_pro = false;
    }
    $status_insights = get_option('seopress_insights_license_status');
    if (false !== $status_insights && 'valid' == $status_insights) {
        $status_insights = true;
    } else {
        $status_insights = false;
    }

    if (defined('SEOPRESS_WL_ADMIN_HEADER') && SEOPRESS_WL_ADMIN_HEADER === false) {
        //do nothing
    } else {
        $class = '1' !== seopress_get_service('NoticeOption')->getNoticeTasks() ? 'is-active' : '';
    ?>

    <div id="notice-tasks-alert" class="seopress-card <?php echo esc_attr($class); ?>" style="display: none">
        <div class="seopress-card-title">
            <h2><?php esc_attr_e('SEOPress Suite', 'wp-seopress'); ?></h2>
            <p><?php esc_html_e('From on-site to off-site SEO, our SEO plugins cover all your needs to rank higher in search engines.','wp-seopress'); ?></p>
        </div>
        <div class="seopress-card-content">
            <?php
                $products = [
                    'wp-seopress/seopress.php' => [
                        'title' => 'SEOPress Free',
                        'logo' => SEOPRESS_URL_ASSETS . '/img/logo-seopress-free.svg',
                        'url' => $docs['pricing']
                    ],
                    'wp-seopress-pro/seopress-pro.php' => [
                        'title' => 'SEOPress PRO',
                        'logo' => SEOPRESS_URL_ASSETS . '/img/logo-seopress-pro.svg',
                        'url' => $docs['addons']['pro'],
                        'status' => $status_pro,
                    ],
                    'wp-seopress-insights/seopress-insights.php' => [
                        'title' => 'SEOPress Insights',
                        'logo' => SEOPRESS_URL_ASSETS . '/img/logo-seopress-insights.svg',
                        'url' => $docs['addons']['insights'],
                        'status' => $status_insights,
                    ],
                ];
            ?>
            <div class="seopress-integrations seopress-suite">
                <?php
                    foreach($products as $key => $product) {
                        $title = $product['title'];
                        $logo = $product['logo'];
                        $url = $product['url'];
                        $upgrade = false;
                        ?>
                        <div class="seopress-integration">
                            <img src="<?php echo esc_url($logo); ?>" width="32" height="32" alt="<?php echo esc_attr( $title ); ?>"/>
                            <div class="details">
                                <h3 class="name"><?php echo esc_html($title); ?></h3>
                                <?php 
                                // Plugin status
                                if (is_plugin_active( $key )) {
                                    $status = 'status-active';
                                    $label = esc_attr__( 'Active', 'wp-seopress' );
                                } else {
                                    $status = 'status-inactive';
                                    $label = esc_attr__( 'Inactive', 'wp-seopress' );
                                    $upgrade = true;
                                } 
                                // License status
                                if (is_plugin_active( $key ) && isset($product['status'])) {
                                    if ($product['status'] === true) {
                                        $label = esc_attr__( 'License valid', 'wp-seopress' );
                                    } else {
                                        $status = 'status-expired';
                                        $label = esc_attr__( 'License invalid', 'wp-seopress' );
                                    }
                                }
                                ?>
                                <div class="seopress-d-flex seopress-wrap-details">
                                    <div class="status">
                                        <span class="badge <?php echo esc_attr( $status ); ?>"></span>
                                        <span class="label"><?php echo esc_attr( $label ); ?></span>
                                    </div>
                                    <?php if ($upgrade === true) { ?>
                                        <div class="status upgrade">
                                            <a href="<?php echo esc_url($url); ?>" target="_blank">
                                                <?php esc_html_e('Upgrade', 'wp-seopress'); ?>
                                                <span class="seopress-help dashicons dashicons-external"></span>
                                            </a>
                                        </div>
                                    <?php } elseif (isset($product['status']) && $product['status'] === false) { ?>
                                        <div class="status upgrade">
                                            <a href="<?php echo esc_url(admin_url( 'admin.php?page=seopress-license' )); ?>">
                                                <?php esc_html_e('Check license', 'wp-seopress'); ?>
                                            </a>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                ?>
            </div>
        </div>
    </div>

<?php
}
