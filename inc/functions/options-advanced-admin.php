<?php
defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

//MANDATORY for using is_plugin_active
include_once ABSPATH . 'wp-admin/includes/plugin.php';

global $pagenow;

//Admin notices
//=================================================================================================
//License notice
if (current_user_can(seopress_capability('manage_options', 'notice')) && is_seopress_page()) {
    //PRO
    if ('valid' != get_option('seopress_pro_license_status') && is_plugin_active('wp-seopress-pro/seopress-pro.php') && ! is_multisite()) {
        function seopress_notice_license()
        {
            $screen_id = get_current_screen();
            if ('seopress-option' === $screen_id->parent_base && 'seo_page_seopress-license' !== $screen_id->base) {
                $docs = seopress_get_docs_links();

                $class   = 'seopress-notice is-error';

                $message = '<p><strong>' . __('Welcome to SEOPress PRO!', 'wp-seopress') . '</strong></p>';

                $message .= '<p>' . __('Please activate your license to receive automatic updates and get premium support.', 'wp-seopress') . '</p>';

                $message .= '<p><a class="btn btnPrimary" href="' . admin_url('admin.php?page=seopress-license') . '">' . __('Activate License', 'wp-seopress') . '</a></p>';

                printf('<div class="%1$s">%2$s</div>', esc_attr($class), $message);
            }
        }
        add_action('seopress_admin_notices', 'seopress_notice_license');
    }
}

//Permalinks notice
if (isset($pagenow) && 'options-permalink.php' == $pagenow) {
    function seopress_notice_permalinks()
    {
        $class   = 'notice notice-warning';
        $message = '<strong>' . __('WARNING', 'wp-seopress') . '</strong>';
        $message .= '<p>' . __('Do NOT change your permalink structure on a production site. Changing URLs can severely damage your SEO.', 'wp-seopress') . '</p>';

        printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), $message);
    }
    add_action('admin_notices', 'seopress_notice_permalinks');

    if ('' == get_option('permalink_structure')) { //If default permalink
        function seopress_notice_no_rewrite_url()
        {
            $class   = 'notice notice-warning';
            $message = '<strong>' . __('WARNING', 'wp-seopress') . '</strong>';
            $message .= '<p>' . __('URL rewriting is NOT enabled on your site. Select a permalink structure that is optimized for SEO (NOT Plain).', 'wp-seopress') . '</p>';

            printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), $message);
        }
        add_action('admin_notices', 'seopress_notice_no_rewrite_url');
    }
}

//Advanced
//=================================================================================================
//Automatic title on media file
function seopress_advanced_advanced_image_auto_title_editor_option()
{
    return seopress_get_service('AdvancedOption')->getImageAutoTitleEditor();

}

//Automatic alt text on media file
function seopress_advanced_advanced_image_auto_alt_editor_option()
{
    return seopress_get_service('AdvancedOption')->getImageAutoAltEditor();
}

//Automatic caption on media file
function seopress_advanced_advanced_image_auto_caption_editor_option()
{
    return seopress_get_service('AdvancedOption')->getImageAutoCaptionEditor();

}

//Automatic desc on media file
function seopress_advanced_advanced_image_auto_desc_editor_option()
{
    return seopress_get_service('AdvancedOption')->getImageAutoDescriptionEditor();
}

if ('' != seopress_advanced_advanced_image_auto_title_editor_option() ||
'' != seopress_advanced_advanced_image_auto_alt_editor_option() ||
'' != seopress_advanced_advanced_image_auto_caption_editor_option() ||
'' != seopress_advanced_advanced_image_auto_desc_editor_option()) {
    add_action('add_attachment', 'seopress_auto_image_attr');
    function seopress_auto_image_attr($post_ID)
    {
        if (wp_attachment_is_image($post_ID)) {
            $img_attr = get_post($post_ID)->post_title;

            // Sanitize the title: remove hyphens, underscores & extra spaces:
            $img_attr = preg_replace('%\s*[-_\s]+\s*%', ' ', $img_attr);

            // Lowercase attributes
            $img_attr = strtolower($img_attr);

            $img_attr = apply_filters('seopress_auto_image_title', $img_attr);

            // Create an array with the image meta (Title, Caption, Description) to be updated
            $img_attr_array = ['ID'=>$post_ID]; // Image (ID) to be updated

            if ('' != seopress_advanced_advanced_image_auto_title_editor_option()) {
                $img_attr_array['post_title'] = $img_attr; // Set image Title
            }

            if ('' != seopress_advanced_advanced_image_auto_caption_editor_option()) {
                $img_attr_array['post_excerpt'] = $img_attr; // Set image Caption
            }

            if ('' != seopress_advanced_advanced_image_auto_desc_editor_option()) {
                $img_attr_array['post_content'] = $img_attr; // Set image Desc
            }

            $img_attr_array = apply_filters('seopress_auto_image_attr', $img_attr_array);

            // Set the image Alt-Text
            if ('' != seopress_advanced_advanced_image_auto_alt_editor_option()) {
                update_post_meta($post_ID, '_wp_attachment_image_alt', $img_attr);
            }

            // Set the image meta (e.g. Title, Excerpt, Content)
            if ('' != seopress_advanced_advanced_image_auto_title_editor_option() || '' != seopress_advanced_advanced_image_auto_caption_editor_option() || '' != seopress_advanced_advanced_image_auto_desc_editor_option()) {
                wp_update_post($img_attr_array);
            }
        }
    }
}

//Metaboxe position
/**
 * @deprecated 5.4.0
 */
function seopress_advanced_appearance_metaboxe_position_option()
{
    return seopress_get_service('AdvancedOption')->getAppearanceMetaboxePosition();

}

//Set default tab in schema metabox
/**
 * @deprecated 5.4.0
 */
function seopress_advanced_appearance_schema_default_tab_option()
{
    return seopress_get_service('AdvancedOption')->getAppearanceSchemaDefaultTab();

}

//Columns in post types
/**
 * @deprecated 5.4.0
 */
function seopress_advanced_appearance_title_col_option()
{
    return seopress_get_service('AdvancedOption')->getAppearanceTitleCol();

}
/**
 * @deprecated 5.4.0
 */
function seopress_advanced_appearance_meta_desc_col_option()
{
    return seopress_get_service('AdvancedOption')->getAppearanceMetaDescriptionCol();

}
/**
 * @deprecated 5.4.0
 */
function seopress_advanced_appearance_redirect_url_col_option()
{
    return seopress_get_service('AdvancedOption')->getAppearanceRedirectUrlCol();

}
/**
 * @deprecated 5.4.0
 */
function seopress_advanced_appearance_redirect_enable_col_option()
{
    return seopress_get_service('AdvancedOption')->getAppearanceRedirectEnableCol();

}
/**
 * @deprecated 5.4.0
 */
function seopress_advanced_appearance_canonical_option()
{
    return seopress_get_service('AdvancedOption')->getAppearanceCanonical();

}
/**
 * @deprecated 5.4.0
 */
function seopress_advanced_appearance_target_kw_col_option()
{
    return seopress_get_service('AdvancedOption')->getAppearanceTargetKwCol();

}
/**
 * @deprecated 5.4.0
 */
function seopress_advanced_appearance_noindex_col_option()
{
    return seopress_get_service('AdvancedOption')->getAppearanceNoIndexCol();

}
/**
 * @deprecated 5.4.0
 */
function seopress_advanced_appearance_nofollow_col_option()
{
    return seopress_get_service('AdvancedOption')->getAppearanceNoFollowCol();
}
/**
 * @deprecated 5.4.0
 */
function seopress_advanced_appearance_words_col_option()
{
    return seopress_get_service('AdvancedOption')->getAppearanceWordsCol();

}
/**
 * @deprecated 5.4.0
 */
function seopress_advanced_appearance_ps_col_option()
{
    return seopress_get_service('AdvancedOption')->getAppearancePsCol();

}
/**
 * @deprecated 5.4.0
 */
function seopress_advanced_appearance_insights_col_option()
{
    return seopress_get_service('AdvancedOption')->getAppearanceInsightsCol();

}
/**
 * @deprecated 5.4.0
 */
function seopress_advanced_appearance_score_col_option()
{
    return seopress_get_service('AdvancedOption')->getAppearanceScoreCol();
}

function seopress_insights_query_all_rankings()
{
    if (!is_plugin_active('wp-seopress-insights/seopress-insights.php')) {
        return;
    }

    //Init
    $rankings = [];

    //Args
    $args = [
        'order'         	 => 'ASC',
        'post_type'     	 => 'seopress_rankings',
        'posts_per_page' 	=> -1,
        'fields'          => 'ids',
    ];

    $args = apply_filters('seopress_insights_dashboard_rankings_list_query', $args);

    //Query rankings
    $kw_query = new WP_Query($args);

    // The Loop
    if ($kw_query->have_posts()) {
        while ($kw_query->have_posts()) {
            $kw_query->the_post();
            $data = get_post_meta(get_the_ID(), 'seopress_insights_data', false);

            if (! empty($data[0])) {
                array_multisort($data[0], SORT_DESC, SORT_REGULAR);
            }
        }
    }
    /* Restore original Post Data */
    wp_reset_postdata();

    return $data[0];
}

if ('' != seopress_advanced_appearance_title_col_option()
|| '' != seopress_advanced_appearance_meta_desc_col_option()
|| '' != seopress_advanced_appearance_redirect_enable_col_option()
|| '' != seopress_advanced_appearance_redirect_url_col_option()
|| '' != seopress_advanced_appearance_canonical_option()
|| '' != seopress_advanced_appearance_target_kw_col_option()
|| '' != seopress_advanced_appearance_noindex_col_option()
|| '' != seopress_advanced_appearance_nofollow_col_option()
|| '' != seopress_advanced_appearance_words_col_option()
|| '' != seopress_advanced_appearance_ps_col_option()
|| '' != seopress_advanced_appearance_insights_col_option()
|| '' != seopress_advanced_appearance_score_col_option()) {
    function seopress_add_columns()
    {
        if (!isset(get_current_screen()->post_type)) {
            return;
        }

        $key = get_current_screen()->post_type;
        if (null === seopress_titles_single_cpt_enable_option($key) && '' != $key) {
            $postTypes = seopress_get_service('WordPressData')->getPostTypes();
            if (array_key_exists($key, $postTypes)) {
                add_filter('manage_' . $key . '_posts_columns', 'seopress_title_columns');
                add_action('manage_' . $key . '_posts_custom_column', 'seopress_title_display_column', 10, 2);
                if (is_plugin_active('easy-digital-downloads/easy-digital-downloads.php')) {
                    add_filter('manage_edit-' . $key . '_columns', 'seopress_title_columns');
                }
            }
        }
    }

    $postTypes = seopress_get_service('WordPressData')->getPostTypes();
    //Sortable columns
    foreach ($postTypes as $key => $value) {
        add_filter('manage_edit-' . $key . '_sortable_columns', 'seopress_admin_sortable_columns');
    }

    function seopress_admin_sortable_columns($columns)
    {
        $columns['seopress_noindex']  = 'seopress_noindex';
        $columns['seopress_nofollow'] = 'seopress_nofollow';

        return $columns;
    }

    add_filter('pre_get_posts', 'seopress_admin_sort_columns_by');
    function seopress_admin_sort_columns_by($query)
    {
        if (! is_admin()) {
            return;
        } else {
            $orderby = $query->get('orderby');
            if ('seopress_noindex' == $orderby) {
                $query->set('meta_key', '_seopress_robots_index');
                $query->set('orderby', 'meta_value');
            }
            if ('seopress_nofollow' == $orderby) {
                $query->set('meta_key', '_seopress_robots_follow');
                $query->set('orderby', 'meta_value');
            }
        }
    }
}

//Remove Content Analysis Metaboxe
/**
 * @deprecated 5.4.0
 */
function seopress_advanced_appearance_ca_metaboxe_hook_option()
{
    return seopress_get_service('AdvancedOption')->getAppearanceCaMetaboxe();

}

if ('' != seopress_advanced_appearance_ca_metaboxe_hook_option()) {
    function seopress_advanced_appearance_ca_metaboxe_hook()
    {
        add_filter('seopress_metaboxe_content_analysis', '__return_false');
    }
    add_action('init', 'seopress_advanced_appearance_ca_metaboxe_hook', 999);
}

//Remove Genesis SEO Metaboxe
function seopress_advanced_appearance_genesis_seo_metaboxe_hook_option()
{
    return seopress_get_service('AdvancedOption')->getAppearanceGenesisSeoMetaboxe();

}

if ('' != seopress_advanced_appearance_genesis_seo_metaboxe_hook_option()) {
    function seopress_advanced_appearance_genesis_seo_metaboxe_hook()
    {
        remove_action('admin_menu', 'genesis_add_inpost_seo_box');
    }
    add_action('init', 'seopress_advanced_appearance_genesis_seo_metaboxe_hook', 999);
}

//Remove Genesis SEO Menu Link
function seopress_advanced_appearance_genesis_seo_menu_option()
{
    return seopress_get_service('AdvancedOption')->getAppearanceGenesisSeoMenu();
}

if ('' != seopress_advanced_appearance_genesis_seo_menu_option()) {
    function seopress_advanced_appearance_genesis_seo_menu_hook()
    {
        remove_theme_support('genesis-seo-settings-menu');
    }
    add_action('init', 'seopress_advanced_appearance_genesis_seo_menu_hook', 999);
}

$postTypes = seopress_get_service('WordPressData')->getPostTypes();

//Bulk actions
//noindex
foreach ($postTypes as $key => $value) {
    add_filter('bulk_actions-edit-' . $key, 'seopress_bulk_actions_noindex');
}
foreach (seopress_get_taxonomies() as $key => $value) {
    add_filter('bulk_actions-edit-' . $key, 'seopress_bulk_actions_noindex');
}

if (is_plugin_active('woocommerce/woocommerce.php')) {
    add_filter('bulk_actions-edit-product', 'seopress_bulk_actions_noindex');
}

function seopress_bulk_actions_noindex($bulk_actions)
{
    $bulk_actions['seopress_noindex'] = __('Enable noindex', 'wp-seopress');

    return $bulk_actions;
}

foreach ($postTypes as $key => $value) {
    add_filter('handle_bulk_actions-edit-' . $key, 'seopress_bulk_action_noindex_handler', 10, 3);
}
foreach (seopress_get_taxonomies() as $key => $value) {
    add_filter('handle_bulk_actions-edit-' . $key, 'seopress_bulk_action_noindex_handler', 10, 3);
}
if (is_plugin_active('woocommerce/woocommerce.php')) {
    add_filter('handle_bulk_actions-edit-product', 'seopress_bulk_action_noindex_handler', 10, 3);
}

function seopress_bulk_action_noindex_handler($redirect_to, $doaction, $post_ids)
{
    if ('seopress_noindex' !== $doaction) {
        return $redirect_to;
    }
    foreach ($post_ids as $post_id) {
        // Perform action for each post/term
        update_post_meta($post_id, '_seopress_robots_index', 'yes');
        update_term_meta($post_id, '_seopress_robots_index', 'yes');
    }
    $redirect_to = add_query_arg('bulk_noindex_posts', count($post_ids), $redirect_to);

    return $redirect_to;
}

add_action('admin_notices', 'seopress_bulk_action_noindex_admin_notice');

function seopress_bulk_action_noindex_admin_notice()
{
    if (! empty($_REQUEST['bulk_noindex_posts'])) {
        $noindex_count = intval($_REQUEST['bulk_noindex_posts']);
        printf('<div id="message" class="updated fade"><p>' .
                _n(
                    '%s post to noindex.',
                    '%s posts to noindex.',
                    $noindex_count,
                    'wp-seopress'
                ) . '</p></div>', $noindex_count);
    }
}


$postTypes = seopress_get_service('WordPressData')->getPostTypes();
//index
foreach ($postTypes as $key => $value) {
    add_filter('bulk_actions-edit-' . $key, 'seopress_bulk_actions_index');
}
foreach (seopress_get_taxonomies() as $key => $value) {
    add_filter('bulk_actions-edit-' . $key, 'seopress_bulk_actions_index');
}
if (is_plugin_active('woocommerce/woocommerce.php')) {
    add_filter('bulk_actions-edit-product', 'seopress_bulk_actions_index');
}

function seopress_bulk_actions_index($bulk_actions)
{
    $bulk_actions['seopress_index'] = __('Enable index', 'wp-seopress');

    return $bulk_actions;
}

foreach ($postTypes as $key => $value) {
    add_filter('handle_bulk_actions-edit-' . $key, 'seopress_bulk_action_index_handler', 10, 3);
}
foreach (seopress_get_taxonomies() as $key => $value) {
    add_filter('handle_bulk_actions-edit-' . $key, 'seopress_bulk_action_index_handler', 10, 3);
}
if (is_plugin_active('woocommerce/woocommerce.php')) {
    add_filter('handle_bulk_actions-edit-product', 'seopress_bulk_action_index_handler', 10, 3);
}

function seopress_bulk_action_index_handler($redirect_to, $doaction, $post_ids)
{
    if ('seopress_index' !== $doaction) {
        return $redirect_to;
    }
    foreach ($post_ids as $post_id) {
        // Perform action for each post.
        delete_post_meta($post_id, '_seopress_robots_index', '');
        delete_term_meta($post_id, '_seopress_robots_index', '');
    }
    $redirect_to = add_query_arg('bulk_index_posts', count($post_ids), $redirect_to);

    return $redirect_to;
}

add_action('admin_notices', 'seopress_bulk_action_index_admin_notice');

function seopress_bulk_action_index_admin_notice()
{
    if (! empty($_REQUEST['bulk_index_posts'])) {
        $index_count = intval($_REQUEST['bulk_index_posts']);
        printf('<div id="message" class="updated fade"><p>' .
                _n(
                    '%s post to index.',
                    '%s posts to index.',
                    $index_count,
                    'wp-seopress'
                ) . '</p></div>', $index_count);
    }
}

//nofollow
foreach ($postTypes as $key => $value) {
    add_filter('bulk_actions-edit-' . $key, 'seopress_bulk_actions_nofollow');
}
foreach (seopress_get_taxonomies() as $key => $value) {
    add_filter('bulk_actions-edit-' . $key, 'seopress_bulk_actions_nofollow');
}
if (is_plugin_active('woocommerce/woocommerce.php')) {
    add_filter('bulk_actions-edit-product', 'seopress_bulk_actions_nofollow');
}

function seopress_bulk_actions_nofollow($bulk_actions)
{
    $bulk_actions['seopress_nofollow'] = __('Enable nofollow', 'wp-seopress');

    return $bulk_actions;
}
foreach ($postTypes as $key => $value) {
    add_filter('handle_bulk_actions-edit-' . $key, 'seopress_bulk_action_nofollow_handler', 10, 3);
}
foreach (seopress_get_taxonomies() as $key => $value) {
    add_filter('handle_bulk_actions-edit-' . $key, 'seopress_bulk_action_nofollow_handler', 10, 3);
}
if (is_plugin_active('woocommerce/woocommerce.php')) {
    add_filter('handle_bulk_actions-edit-product', 'seopress_bulk_action_nofollow_handler', 10, 3);
}

function seopress_bulk_action_nofollow_handler($redirect_to, $doaction, $post_ids)
{
    if ('seopress_nofollow' !== $doaction) {
        return $redirect_to;
    }
    foreach ($post_ids as $post_id) {
        // Perform action for each post.
        update_post_meta($post_id, '_seopress_robots_follow', 'yes');
        update_term_meta($post_id, '_seopress_robots_follow', 'yes');
    }
    $redirect_to = add_query_arg('bulk_nofollow_posts', count($post_ids), $redirect_to);

    return $redirect_to;
}

add_action('admin_notices', 'seopress_bulk_action_nofollow_admin_notice');

function seopress_bulk_action_nofollow_admin_notice()
{
    if (! empty($_REQUEST['bulk_nofollow_posts'])) {
        $nofollow_count = intval($_REQUEST['bulk_nofollow_posts']);
        printf('<div id="message" class="updated fade"><p>' .
                _n(
                    '%s post to nofollow.',
                    '%s posts to nofollow.',
                    $nofollow_count,
                    'wp-seopress'
                ) . '</p></div>', $nofollow_count);
    }
}

//follow
foreach ($postTypes as $key => $value) {
    add_filter('bulk_actions-edit-' . $key, 'seopress_bulk_actions_follow');
}
foreach (seopress_get_taxonomies() as $key => $value) {
    add_filter('bulk_actions-edit-' . $key, 'seopress_bulk_actions_follow');
}
if (is_plugin_active('woocommerce/woocommerce.php')) {
    add_filter('bulk_actions-edit-product', 'seopress_bulk_actions_follow');
}

function seopress_bulk_actions_follow($bulk_actions)
{
    $bulk_actions['seopress_follow'] = __('Enable follow', 'wp-seopress');

    return $bulk_actions;
}

foreach ($postTypes as $key => $value) {
    add_filter('handle_bulk_actions-edit-' . $key, 'seopress_bulk_action_follow_handler', 10, 3);
}
foreach (seopress_get_taxonomies() as $key => $value) {
    add_filter('handle_bulk_actions-edit-' . $key, 'seopress_bulk_action_follow_handler', 10, 3);
}
if (is_plugin_active('woocommerce/woocommerce.php')) {
    add_filter('handle_bulk_actions-edit-product', 'seopress_bulk_action_follow_handler', 10, 3);
}

function seopress_bulk_action_follow_handler($redirect_to, $doaction, $post_ids)
{
    if ('seopress_follow' !== $doaction) {
        return $redirect_to;
    }
    foreach ($post_ids as $post_id) {
        // Perform action for each post.
        delete_post_meta($post_id, '_seopress_robots_follow', '');
        delete_term_meta($post_id, '_seopress_robots_follow', '');
    }
    $redirect_to = add_query_arg('bulk_follow_posts', count($post_ids), $redirect_to);

    return $redirect_to;
}

add_action('admin_notices', 'seopress_bulk_action_follow_admin_notice');

function seopress_bulk_action_follow_admin_notice()
{
    if (! empty($_REQUEST['bulk_follow_posts'])) {
        $follow_count = intval($_REQUEST['bulk_follow_posts']);
        printf('<div id="message" class="updated fade"><p>' .
                _n(
                    '%s post to follow.',
                    '%s posts to follow.',
                    $follow_count,
                    'wp-seopress'
                ) . '</p></div>', $follow_count);
    }
}

//enable 301
foreach ($postTypes as $key => $value) {
    add_filter('bulk_actions-edit-' . $key, 'seopress_bulk_actions_redirect_enable');
}

function seopress_bulk_actions_redirect_enable($bulk_actions)
{
    $bulk_actions['seopress_enable'] = __('Enable redirection', 'wp-seopress');

    return $bulk_actions;
}
foreach ($postTypes as $key => $value) {
    add_filter('handle_bulk_actions-edit-' . $key, 'seopress_bulk_action_redirect_enable_handler', 10, 3);
}

function seopress_bulk_action_redirect_enable_handler($redirect_to, $doaction, $post_ids)
{
    if ('seopress_enable' !== $doaction) {
        return $redirect_to;
    }
    foreach ($post_ids as $post_id) {
        // Perform action for each post.
        update_post_meta($post_id, '_seopress_redirections_enabled', 'yes');
    }
    $redirect_to = add_query_arg('bulk_enable_redirects_posts', count($post_ids), $redirect_to);

    return $redirect_to;
}

add_action('admin_notices', 'seopress_bulk_action_redirect_enable_admin_notice');

function seopress_bulk_action_redirect_enable_admin_notice()
{
    if (! empty($_REQUEST['bulk_enable_redirects_posts'])) {
        $enable_count = intval($_REQUEST['bulk_enable_redirects_posts']);
        printf('<div id="message" class="updated fade"><p>' .
                _n(
                    '%s redirections enabled.',
                    '%s redirections enabled.',
                    $enable_count,
                    'wp-seopress'
                ) . '</p></div>', $enable_count);
    }
}

//disable 301
foreach ($postTypes as $key => $value) {
    add_filter('bulk_actions-edit-' . $key, 'seopress_bulk_actions_redirect_disable');
}

function seopress_bulk_actions_redirect_disable($bulk_actions)
{
    $bulk_actions['seopress_disable'] = __('Disable redirection', 'wp-seopress');

    return $bulk_actions;
}
foreach ($postTypes as $key => $value) {
    add_filter('handle_bulk_actions-edit-' . $key, 'seopress_bulk_action_redirect_disable_handler', 10, 3);
}

function seopress_bulk_action_redirect_disable_handler($redirect_to, $doaction, $post_ids)
{
    if ('seopress_disable' !== $doaction) {
        return $redirect_to;
    }
    foreach ($post_ids as $post_id) {
        // Perform action for each post.
        update_post_meta($post_id, '_seopress_redirections_enabled', '');
    }
    $redirect_to = add_query_arg('bulk_disable_redirects_posts', count($post_ids), $redirect_to);

    return $redirect_to;
}

add_action('admin_notices', 'seopress_bulk_action_redirect_disable_admin_notice');
function seopress_bulk_action_redirect_disable_admin_notice()
{
    if (! empty($_REQUEST['bulk_disable_redirects_posts'])) {
        $enable_count = intval($_REQUEST['bulk_disable_redirects_posts']);
        printf('<div id="message" class="updated fade"><p>' .
                _n(
                    '%s redirection disabled.',
                    '%s redirections disabled.',
                    $enable_count,
                    'wp-seopress'
                ) . '</p></div>', $enable_count);
    }
}

//Quick Edit
add_action('quick_edit_custom_box', 'seopress_bulk_quick_edit_custom_box', 10, 2);
function seopress_bulk_quick_edit_custom_box($column_name)
{
    static $printNonce = true;
    if ($printNonce) {
        $printNonce = false;
        wp_nonce_field(plugin_basename(__FILE__), 'seopress_title_edit_nonce');
    } ?>
<div class="wp-clearfix"></div>
<fieldset class="inline-edit-col-left">
    <div class="inline-edit-col column-<?php echo $column_name; ?>">

        <?php
                switch ($column_name) {
                case 'seopress_title':
                ?>
        <h4><?php _e('SEO', 'wp-seopress'); ?>
        </h4>
        <label class="inline-edit-group">
            <span class="title"><?php _e('Title tag', 'wp-seopress'); ?></span>
            <span class="input-text-wrap"><input type="text" name="seopress_title" /></span>
        </label>
        <?php
                break;
                case 'seopress_desc':
                ?>
        <label class="inline-edit-group">
            <span class="title"><?php _e('Meta description', 'wp-seopress'); ?></span>
            <span class="input-text-wrap"><textarea cols="18" rows="1" name="seopress_desc" autocomplete="off"
                    role="combobox" aria-autocomplete="list" aria-expanded="false"></textarea></span>
        </label>
        <?php
                break;
                case 'seopress_tkw':
                ?>
        <label class="inline-edit-group">
            <span class="title"><?php _e('Target keywords', 'wp-seopress'); ?></span>
            <span class="input-text-wrap"><input type="text" name="seopress_tkw" /></span>
        </label>
        <?php
                break;
                case 'seopress_canonical':
                ?>
        <label class="inline-edit-group">
            <span class="title"><?php _e('Canonical', 'wp-seopress'); ?></span>
            <span class="input-text-wrap"><input type="text" name="seopress_canonical" /></span>
        </label>
        <?php
                break;
                case 'seopress_noindex':
                ?>
        <label class="alignleft">
            <input type="checkbox" name="seopress_noindex" value="yes">
            <span class="checkbox-title"><?php _e('Do not display this page in search engine results / XML - HTML sitemaps <strong>(noindex)</strong>', 'wp-seopress'); ?></span>
        </label>
        <?php
                break;
                case 'seopress_nofollow':
                ?>
        <label class="alignleft">
            <input type="checkbox" name="seopress_nofollow" value="yes">
            <span class="checkbox-title"><?php _e('Do not follow links for this page <strong>(nofollow)</strong>', 'wp-seopress'); ?></span>
        </label>
        <?php
                break;
                default:
                break;
                } ?>
    </div>
</fieldset>
<?php
}

add_action('save_post', 'seopress_bulk_quick_edit_save_post', 10, 2);
function seopress_bulk_quick_edit_save_post($post_id)
{
    // don't save if Elementor library
    if (isset($_REQUEST['post_type']) && 'elementor_library' == $_REQUEST['post_type']) {
        return $post_id;
    }

    // don't save for autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }

    // dont save for revisions
    if (isset($_REQUEST['post_type']) && 'revision' == $_REQUEST['post_type']) {
        return $post_id;
    }

    if (! current_user_can('edit_posts', $post_id)) {
        return;
    }

    $_REQUEST += ['seopress_title_edit_nonce' => ''];

    if (! wp_verify_nonce($_REQUEST['seopress_title_edit_nonce'], plugin_basename(__FILE__))) {
        return;
    }
    if (isset($_REQUEST['seopress_title'])) {
        update_post_meta($post_id, '_seopress_titles_title', esc_html($_REQUEST['seopress_title']));
    }
    if (isset($_REQUEST['seopress_desc'])) {
        update_post_meta($post_id, '_seopress_titles_desc', esc_html($_REQUEST['seopress_desc']));
    }
    if (isset($_REQUEST['seopress_tkw'])) {
        update_post_meta($post_id, '_seopress_analysis_target_kw', esc_html($_REQUEST['seopress_tkw']));
    }
    if (isset($_REQUEST['seopress_canonical'])) {
        update_post_meta($post_id, '_seopress_robots_canonical', esc_html($_REQUEST['seopress_canonical']));
    }
    if ('' != seopress_advanced_appearance_noindex_col_option()) {
        if (isset($_REQUEST['seopress_noindex'])) {
            update_post_meta($post_id, '_seopress_robots_index', 'yes');
        } else {
            delete_post_meta($post_id, '_seopress_robots_index');
        }
    }
    if ('' != seopress_advanced_appearance_nofollow_col_option()) {
        if (isset($_REQUEST['seopress_nofollow'])) {
            update_post_meta($post_id, '_seopress_robots_follow', 'yes');
        } else {
            delete_post_meta($post_id, '_seopress_robots_follow');
        }
    }

    //Elementor sync
    if (did_action('elementor/loaded')) {
        $elementor = get_post_meta($post_id, '_elementor_page_settings', true);

        if (! empty($elementor)) {
            if (isset($_REQUEST['seopress_title'])) {
                $elementor['_seopress_titles_title'] = esc_html($_REQUEST['seopress_title']);
            }
            if (isset($_REQUEST['seopress_desc'])) {
                $elementor['_seopress_titles_desc'] = esc_html($_REQUEST['seopress_desc']);
            }
            if (isset($_REQUEST['seopress_noindex'])) {
                $elementor['_seopress_robots_index'] = 'yes';
            } else {
                $elementor['_seopress_robots_index'] = '';
            }
            if (isset($_REQUEST['seopress_nofollow'])) {
                $elementor['_seopress_robots_follow'] = 'yes';
            } else {
                $elementor['_seopress_robots_follow'] = '';
            }
            if (isset($_REQUEST['seopress_canonical'])) {
                $elementor['_seopress_robots_canonical'] = esc_html($_REQUEST['seopress_canonical']);
            }
            if (isset($_REQUEST['seopress_tkw'])) {
                $elementor['_seopress_analysis_target_kw'] = esc_html($_REQUEST['seopress_tkw']);
            }
            update_post_meta($post_id, '_elementor_page_settings', $elementor);
        }
    }
}

//WP Editor on taxonomy description field
function seopress_advanced_advanced_tax_desc_editor_option()
{
    $seopress_advanced_advanced_tax_desc_editor_option = get_option('seopress_advanced_option_name');
    if (! empty($seopress_advanced_advanced_tax_desc_editor_option)) {
        foreach ($seopress_advanced_advanced_tax_desc_editor_option as $key => $seopress_advanced_advanced_tax_desc_editor_value) {
            $options[$key] = $seopress_advanced_advanced_tax_desc_editor_value;
        }
        if (isset($seopress_advanced_advanced_tax_desc_editor_option['seopress_advanced_advanced_tax_desc_editor'])) {
            return $seopress_advanced_advanced_tax_desc_editor_option['seopress_advanced_advanced_tax_desc_editor'];
        }
    }
}
if ('' != seopress_advanced_advanced_tax_desc_editor_option() && current_user_can('publish_posts')) {
    function seopress_tax_desc_wp_editor_init()
    {
        global $pagenow;
        if ('term.php' == $pagenow || 'edit-tags.php' == $pagenow) {
            remove_filter('pre_term_description', 'wp_filter_kses');
            remove_filter('term_description', 'wp_kses_data');

            //Disallow HTML Tags
            if (! current_user_can('unfiltered_html')) {
                add_filter('pre_term_description', 'wp_kses_post');
                add_filter('term_description', 'wp_kses_post');
            }

            //Allow HTML Tags
            add_filter('term_description', 'wptexturize');
            add_filter('term_description', 'convert_smilies');
            add_filter('term_description', 'convert_chars');
            add_filter('term_description', 'wpautop');
        }
    }
    add_action('init', 'seopress_tax_desc_wp_editor_init', 100);

    function seopress_tax_desc_wp_editor($tag)
    {
        global $pagenow;
        if ('term.php' == $pagenow || 'edit-tags.php' == $pagenow) {
            $content = '';

            if ('term.php' == $pagenow) {
                $editor_id = 'description';
            } elseif ('edit-tags.php' == $pagenow) {
                $editor_id = 'tag-description';
            } ?>

<tr class="form-field term-description-wrap">
    <th scope="row"><label for="description"><?php _e('Description'); ?></label></th>
    <td>
        <?php
                    $settings = [
                        'textarea_name' => 'description',
                        'textarea_rows' => 10,
                    ];
            wp_editor(htmlspecialchars_decode($tag->description), 'html-tag-description', $settings); ?>
        <p class="description"><?php _e('The description is not prominent by default; however, some themes may show it.'); ?>
        </p>
    </td>
    <script type="text/javascript">
        // Remove default description field
        jQuery('textarea#description').closest('.form-field').remove();
    </script>
</tr>

<?php
        }
    }
    $seopress_get_taxonomies = seopress_get_taxonomies();
    foreach ($seopress_get_taxonomies as $key => $value) {
        add_action($key . '_edit_form_fields', 'seopress_tax_desc_wp_editor', 9, 1);
    }
}
