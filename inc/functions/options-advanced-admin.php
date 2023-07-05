<?php
defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

//MANDATORY for using is_plugin_active
include_once ABSPATH . 'wp-admin/includes/plugin.php';

//Advanced
//=================================================================================================
/**
 * Cleaning attachments filename
 *
 * @since 5.8.0
 *
 * @param string $filename
 *
 * @return string $filename
 *
 * @author Benjamin
 */
add_filter('sanitize_file_name', 'seopress_image_seo_cleaning_filename', 10);
function seopress_image_seo_cleaning_filename($filename) {
    if (seopress_get_service('AdvancedOption')->getAdvancedCleaningFileName() === '1') {
        $filename = apply_filters( 'seopress_image_seo_before_cleaning', $filename );

        /* Force the file name in UTF-8 (encoding Windows / OS X / Linux) */
        $filename = mb_convert_encoding($filename, "UTF-8");

        $char_not_clean = ['/•/','/·/','/À/','/Á/','/Â/','/Ã/','/Ä/','/Å/','/Ç/','/È/','/É/','/Ê/','/Ë/','/Ì/','/Í/','/Î/','/Ï/','/Ò/','/Ó/','/Ô/','/Õ/','/Ö/','/Ù/','/Ú/','/Û/','/Ü/','/Ý/','/à/','/á/','/â/','/ã/','/ä/','/å/','/ç/','/è/','/é/','/ê/','/ë/','/ì/','/í/','/î/','/ï/','/ð/','/ò/','/ó/','/ô/','/õ/','/ö/','/ù/','/ú/','/û/','/ü/','/ý/','/ÿ/', '/©/'];

        $char_not_clean = apply_filters( 'seopress_image_seo_clean_input', $char_not_clean );

        $clean = ['-','-','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','o','o','o','o','o','u','u','u','u','y','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','o','o','o','o','o','o','u','u','u','u','y','y','copy'];

        $clean = apply_filters( 'seopress_image_seo_clean_output', $clean );

        $friendly_filename = preg_replace($char_not_clean, $clean, $filename);

        /* After replacement, we destroy the last residues */
        $friendly_filename = preg_replace('/\?/', '', $friendly_filename);

        /* Remove uppercase */
        $friendly_filename = strtolower($friendly_filename);

        $friendly_filename = apply_filters( 'seopress_image_seo_after_cleaning', $friendly_filename );

        $filename = $friendly_filename;
    }

    return $filename;
}

/**
 * Automatic image attributes
 *
 * @since 5.8.0
 *
 * @param string $post_ID
 *
 * @return string $post_ID || void
 *
 * @author Benjamin
 */
add_action('add_attachment', 'seopress_auto_image_attr');
function seopress_auto_image_attr($post_ID) {
    if ('1' === seopress_get_service('AdvancedOption')->getImageAutoTitleEditor() ||
    '1' === seopress_get_service('AdvancedOption')->getImageAutoAltEditor() ||
    '1' === seopress_get_service('AdvancedOption')->getImageAutoCaptionEditor() ||
    '1' === seopress_get_service('AdvancedOption')->getImageAutoDescriptionEditor()) {

        if (wp_attachment_is_image($post_ID)) {

            $parent = get_post($post_ID)->post_parent ? get_post($post_ID)->post_parent : null;
            $cpt = get_post_type($parent) ?  get_post_type($parent) : null;

            if (isset($cpt) && isset($parent) && $cpt === 'product') { //use the product title for WC products
                $img_attr = get_post($parent)->post_title;
            } else {
                $img_attr = get_post($post_ID)->post_title;
            }

            // Sanitize the title: remove hyphens, underscores & extra spaces:
            $img_attr = preg_replace('%\s*[-_\s]+\s*%', ' ', $img_attr);

            // Lowercase attributes
            $img_attr = strtolower($img_attr);

            $img_attr = apply_filters('seopress_auto_image_title', $img_attr, $cpt, $parent);

            // Create an array with the image meta (Title, Caption, Description) to be updated
            $img_attr_array = ['ID'=>$post_ID]; // Image (ID) to be updated

            if ('1' === seopress_get_service('AdvancedOption')->getImageAutoTitleEditor()) {
                $img_attr_array['post_title'] = $img_attr; // Set image Title
            }

            if ('1' === seopress_get_service('AdvancedOption')->getImageAutoCaptionEditor()) {
                $img_attr_array['post_excerpt'] = $img_attr; // Set image Caption
            }

            if ('1' === seopress_get_service('AdvancedOption')->getImageAutoDescriptionEditor()) {
                $img_attr_array['post_content'] = $img_attr; // Set image Desc
            }

            $img_attr_array = apply_filters('seopress_auto_image_attr', $img_attr_array);

            // Set the image Alt-Text
            if ('1' === seopress_get_service('AdvancedOption')->getImageAutoAltEditor()) {
                update_post_meta($post_ID, '_wp_attachment_image_alt', $img_attr);
            }

            // Set the image meta (e.g. Title, Excerpt, Content)
            if ('1' === seopress_get_service('AdvancedOption')->getImageAutoTitleEditor() || '1' === seopress_get_service('AdvancedOption')->getImageAutoCaptionEditor() || '1' === seopress_get_service('AdvancedOption')->getImageAutoDescriptionEditor()) {
                wp_update_post($img_attr_array);
            }
        }
    }
}

//Remove Content Analysis Metaboxe
if (seopress_get_service('AdvancedOption')->getAppearanceCaMetaboxe()==='1') {
    function seopress_advanced_appearance_ca_metaboxe_hook()
    {
        add_filter('seopress_metaboxe_content_analysis', '__return_false');
    }
    add_action('init', 'seopress_advanced_appearance_ca_metaboxe_hook', 999);
}

//Remove Genesis SEO Metaboxe
if ('1' === seopress_get_service('AdvancedOption')->getAppearanceGenesisSeoMetaboxe()) {
    function seopress_advanced_appearance_genesis_seo_metaboxe_hook()
    {
        remove_action('admin_menu', 'genesis_add_inpost_seo_box');
    }
    add_action('init', 'seopress_advanced_appearance_genesis_seo_metaboxe_hook', 999);
}

//Remove Genesis SEO Menu Link
if ('1' === seopress_get_service('AdvancedOption')->getAppearanceGenesisSeoMenu()) {
    function seopress_advanced_appearance_genesis_seo_menu_hook()
    {
        remove_theme_support('genesis-seo-settings-menu');
    }
    add_action('init', 'seopress_advanced_appearance_genesis_seo_menu_hook', 999);
}

//Bulk actions
$postTypes = seopress_get_service('WordPressData')->getPostTypes();

if (!empty($postTypes)) {
    foreach ($postTypes as $key => $value) {
        add_filter('bulk_actions-edit-' . $key, 'seopress_bulk_actions_noindex');
        add_filter('handle_bulk_actions-edit-' . $key, 'seopress_bulk_action_noindex_handler', 10, 3);

        add_filter('bulk_actions-edit-' . $key, 'seopress_bulk_actions_index');
        add_filter('handle_bulk_actions-edit-' . $key, 'seopress_bulk_action_index_handler', 10, 3);

        add_filter('bulk_actions-edit-' . $key, 'seopress_bulk_actions_nofollow');
        add_filter('handle_bulk_actions-edit-' . $key, 'seopress_bulk_action_nofollow_handler', 10, 3);

        add_filter('bulk_actions-edit-' . $key, 'seopress_bulk_actions_follow');
        add_filter('handle_bulk_actions-edit-' . $key, 'seopress_bulk_action_follow_handler', 10, 3);

        add_filter('bulk_actions-edit-' . $key, 'seopress_bulk_actions_redirect_enable');
        add_filter('handle_bulk_actions-edit-' . $key, 'seopress_bulk_action_redirect_enable_handler', 10, 3);

        add_filter('bulk_actions-edit-' . $key, 'seopress_bulk_actions_redirect_disable');
        add_filter('handle_bulk_actions-edit-' . $key, 'seopress_bulk_action_redirect_disable_handler', 10, 3);

        add_filter('bulk_actions-edit-' . $key, 'seopress_bulk_actions_add_instant_indexing');
        add_filter('handle_bulk_actions-edit-' . $key, 'seopress_bulk_action_add_instant_indexing_handler', 10, 3);
    }
}

$taxonomies = seopress_get_service('WordPressData')->getTaxonomies();

if (!empty($taxonomies)) {
    foreach ($taxonomies as $key => $value) {
        add_filter('bulk_actions-edit-' . $key, 'seopress_bulk_actions_noindex');
        add_filter('handle_bulk_actions-edit-' . $key, 'seopress_bulk_action_noindex_handler', 10, 3);

        add_filter('bulk_actions-edit-' . $key, 'seopress_bulk_actions_index');
        add_filter('handle_bulk_actions-edit-' . $key, 'seopress_bulk_action_index_handler', 10, 3);

        add_filter('bulk_actions-edit-' . $key, 'seopress_bulk_actions_nofollow');
        add_filter('handle_bulk_actions-edit-' . $key, 'seopress_bulk_action_nofollow_handler', 10, 3);

        add_filter('bulk_actions-edit-' . $key, 'seopress_bulk_actions_follow');
        add_filter('handle_bulk_actions-edit-' . $key, 'seopress_bulk_action_follow_handler', 10, 3);
    }
}

if (is_plugin_active('woocommerce/woocommerce.php')) {
    add_filter('bulk_actions-edit-product', 'seopress_bulk_actions_noindex');
    add_filter('handle_bulk_actions-edit-product', 'seopress_bulk_action_noindex_handler', 10, 3);

    add_filter('bulk_actions-edit-product', 'seopress_bulk_actions_index');
    add_filter('handle_bulk_actions-edit-product', 'seopress_bulk_action_index_handler', 10, 3);

    add_filter('bulk_actions-edit-product', 'seopress_bulk_actions_nofollow');
    add_filter('handle_bulk_actions-edit-product', 'seopress_bulk_action_nofollow_handler', 10, 3);

    add_filter('bulk_actions-edit-product', 'seopress_bulk_actions_follow');
    add_filter('handle_bulk_actions-edit-product', 'seopress_bulk_action_follow_handler', 10, 3);
}

//noindex
function seopress_bulk_actions_noindex($bulk_actions)
{
    $bulk_actions['seopress_noindex'] = __('Enable noindex', 'wp-seopress');

    return $bulk_actions;
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

//index
function seopress_bulk_actions_index($bulk_actions) {
    $bulk_actions['seopress_index'] = __('Enable index', 'wp-seopress');

    return $bulk_actions;
}

function seopress_bulk_action_index_handler($redirect_to, $doaction, $post_ids) {
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

function seopress_bulk_action_index_admin_notice() {
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
function seopress_bulk_actions_nofollow($bulk_actions) {
    $bulk_actions['seopress_nofollow'] = __('Enable nofollow', 'wp-seopress');

    return $bulk_actions;
}

function seopress_bulk_action_nofollow_handler($redirect_to, $doaction, $post_ids) {
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

function seopress_bulk_action_nofollow_admin_notice() {
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
function seopress_bulk_actions_follow($bulk_actions) {
    $bulk_actions['seopress_follow'] = __('Enable follow', 'wp-seopress');

    return $bulk_actions;
}

function seopress_bulk_action_follow_handler($redirect_to, $doaction, $post_ids) {
    if ('seopress_follow' !== $doaction) {
        return $redirect_to;
    }
    foreach ($post_ids as $post_id) {
        // Perform action for each post.
        delete_post_meta($post_id, '_seopress_robots_follow');
        delete_term_meta($post_id, '_seopress_robots_follow');
    }
    $redirect_to = add_query_arg('bulk_follow_posts', count($post_ids), $redirect_to);

    return $redirect_to;
}

add_action('admin_notices', 'seopress_bulk_action_follow_admin_notice');

function seopress_bulk_action_follow_admin_notice() {
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
function seopress_bulk_actions_redirect_enable($bulk_actions) {
    $bulk_actions['seopress_enable'] = __('Enable redirection', 'wp-seopress');

    return $bulk_actions;
}

function seopress_bulk_action_redirect_enable_handler($redirect_to, $doaction, $post_ids) {
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

function seopress_bulk_action_redirect_enable_admin_notice() {
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
function seopress_bulk_actions_redirect_disable($bulk_actions) {
    $bulk_actions['seopress_disable'] = __('Disable redirection', 'wp-seopress');

    return $bulk_actions;
}

function seopress_bulk_action_redirect_disable_handler($redirect_to, $doaction, $post_ids) {
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
function seopress_bulk_action_redirect_disable_admin_notice() {
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

//add to instant indexing
function seopress_bulk_actions_add_instant_indexing($bulk_actions) {
    $bulk_actions['seopress_instant_indexing'] = __('Add to instant indexing queue', 'wp-seopress');

    return $bulk_actions;
}

function seopress_bulk_action_add_instant_indexing_handler($redirect_to, $doaction, $post_ids) {
    if ('seopress_instant_indexing' !== $doaction) {
        return $redirect_to;
    }

    if (!empty($post_ids)) {
        $urls = '';
        $options    = get_option('seopress_instant_indexing_option_name');
        $check      = isset($options['seopress_instant_indexing_manual_batch']) ? esc_attr($options['seopress_instant_indexing_manual_batch']) : null;

        foreach ($post_ids as $post_id) {
            // Perform action for each post.
            $urls .= esc_url(get_the_permalink( $post_id ))."\n";
        }

        $urls = $check . "\n". $urls;


        $urls = implode("\n", array_unique(explode("\n", $urls)));
        $options['seopress_instant_indexing_manual_batch'] = $urls;


        update_option( 'seopress_instant_indexing_option_name', $options );
    }
    $redirect_to = add_query_arg('bulk_add_instant_indexing', count($post_ids), $redirect_to);

    return $redirect_to;
}

add_action('admin_notices', 'seopress_bulk_action_add_instant_indexing_admin_notice');
function seopress_bulk_action_add_instant_indexing_admin_notice() {
    if (! empty($_REQUEST['bulk_add_instant_indexing'])) {
        $queue_count = intval($_REQUEST['bulk_add_instant_indexing']);
        printf('<div id="message" class="updated fade"><p>' .
                _n(
                    '%s post added to instant indexing queue.',
                    '%s posts added to instant indexing queue.',
                    $queue_count,
                    'wp-seopress'
                ) . '</p></div>', $queue_count);
    }
}

//Quick Edit
add_action('quick_edit_custom_box', 'seopress_bulk_quick_edit_custom_box', 10, 2);
function seopress_bulk_quick_edit_custom_box($column_name) {
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
                case 'seopress_redirect_enable':
                    ?>
        <label class="alignleft">
            <input type="checkbox" name="seopress_redirections_enabled" value="yes">
            <span class="checkbox-title"><?php _e('Enable redirection?', 'wp-seopress'); ?></span>
        </label>
        <?php
                break;
                case 'seopress_redirect_url';
                ?>
        <label class="inline-edit-group">
            <span class="title"><?php _e('New URL', 'wp-seopress'); ?></span>
            <span class="input-text-wrap">
                <input type="text" name="seopress_redirections_value" />
            </span>
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
function seopress_bulk_quick_edit_save_post($post_id) {
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
    if (seopress_get_service('AdvancedOption')->getAppearanceNoIndexCol() ==='1') {
        if (isset($_REQUEST['seopress_noindex'])) {
            update_post_meta($post_id, '_seopress_robots_index', 'yes');
        } else {
            delete_post_meta($post_id, '_seopress_robots_index');
        }
    }
    if (seopress_get_service('AdvancedOption')->getAppearanceNoFollowCol() ==='1') {
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
if (seopress_get_service('AdvancedOption')->getAdvancedTaxDescEditor() ==='1' && current_user_can('publish_posts')) {
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
    if (!empty($taxonomies)) {
        foreach ($taxonomies as $key => $value) {
            add_action($key . '_edit_form_fields', 'seopress_tax_desc_wp_editor', 9, 1);
        }
    }
}
