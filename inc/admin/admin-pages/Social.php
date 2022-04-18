<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

$this->options = get_option('seopress_social_option_name');
if (function_exists('seopress_admin_header')) {
    echo seopress_admin_header();
} ?>
<form method="post" action="<?php echo admin_url('options.php'); ?>" class="seopress-option">
    <?php
        echo $this->seopress_feature_title('social');
settings_fields('seopress_social_option_group'); ?>

    <div id="seopress-tabs" class="wrap">
        <?php
            $current_tab = '';
$plugin_settings_tabs    = [
                'tab_seopress_social_knowledge' => __('Knowledge Graph', 'wp-seopress'),
                'tab_seopress_social_accounts'  => __('Your social accounts', 'wp-seopress'),
                'tab_seopress_social_facebook'  => __('Facebook (Open Graph)', 'wp-seopress'),
                'tab_seopress_social_twitter'   => __('Twitter (Twitter card)', 'wp-seopress'),
            ];

echo '<div class="nav-tab-wrapper">';
foreach ($plugin_settings_tabs as $tab_key => $tab_caption) {
    echo '<a id="' . $tab_key . '-tab" class="nav-tab" href="?page=seopress-social#tab=' . $tab_key . '">' . $tab_caption . '</a>';
}
echo '</div>'; ?>
                <div class="seopress-tab <?php if ('tab_seopress_social_knowledge' == $current_tab) {
    echo 'active';
} ?>" id="tab_seopress_social_knowledge"><?php do_settings_sections('seopress-settings-admin-social-knowledge'); ?></div>
                <div class="seopress-tab <?php if ('tab_seopress_social_accounts' == $current_tab) {
    echo 'active';
} ?>" id="tab_seopress_social_accounts"><?php do_settings_sections('seopress-settings-admin-social-accounts'); ?></div>
                <div class="seopress-tab <?php if ('tab_seopress_social_facebook' == $current_tab) {
    echo 'active';
} ?>" id="tab_seopress_social_facebook"><?php do_settings_sections('seopress-settings-admin-social-facebook'); ?></div>
                <div class="seopress-tab <?php if ('tab_seopress_social_twitter' == $current_tab) {
    echo 'active';
} ?>" id="tab_seopress_social_twitter"><?php do_settings_sections('seopress-settings-admin-social-twitter'); ?></div>
        </div>

        <?php sp_submit_button(__('Save changes', 'wp-seopress')); ?>
    </form>
<?php
