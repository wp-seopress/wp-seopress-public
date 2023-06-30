<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

function seopress_advanced_advanced_attachments_callback() {
    $options = get_option('seopress_advanced_option_name');

    $check = isset($options['seopress_advanced_advanced_attachments']); ?>

<label for="seopress_advanced_advanced_attachments">
    <input id="seopress_advanced_advanced_attachments"
        name="seopress_advanced_option_name[seopress_advanced_advanced_attachments]" type="checkbox" <?php if ('1' == $check) { ?>
    checked="yes"
    <?php } ?>
    value="1"/>
    <?php _e('Redirect attachment pages to post parent (or homepage if none)', 'wp-seopress'); ?>
</label>

<?php if (isset($options['seopress_advanced_advanced_attachments'])) {
        esc_attr($options['seopress_advanced_advanced_attachments']);
    }
}

function seopress_advanced_advanced_attachments_file_callback() {
    $options = get_option('seopress_advanced_option_name');

    $check = isset($options['seopress_advanced_advanced_attachments_file']); ?>

<label for="seopress_advanced_advanced_attachments_file">
    <input id="seopress_advanced_advanced_attachments_file"
        name="seopress_advanced_option_name[seopress_advanced_advanced_attachments_file]" type="checkbox" <?php if ('1' == $check) { ?>
    checked="yes"
    <?php } ?>
    value="1"/>

    <?php _e('Redirect attachment pages to their file URL (https://www.example.com/my-image-file.jpg)', 'wp-seopress'); ?>
</label>

<p class="description">
    <?php _e('If this option is checked, it will take precedence over the redirection of attachments to the post\'s parent.', 'wp-seopress'); ?>
</p>

<?php if (isset($options['seopress_advanced_advanced_attachments_file'])) {
        esc_attr($options['seopress_advanced_advanced_attachments_file']);
    }
}

function seopress_advanced_advanced_clean_filename_callback() {
    $options = get_option('seopress_advanced_option_name');

    $check = isset($options['seopress_advanced_advanced_clean_filename']); ?>

<label for="seopress_advanced_advanced_clean_filename">
    <input id="seopress_advanced_advanced_clean_filename"
        name="seopress_advanced_option_name[seopress_advanced_advanced_clean_filename]" type="checkbox" <?php if ('1' == $check) { ?>
    checked="yes"
    <?php } ?>
    value="1"/>

    <?php _e('When upload a media, remove accents, spaces, capital letters... and force UTF-8 encoding', 'wp-seopress'); ?>
</label>

<p class="description">
    <?php _e('e.g. <code>ExãMple 1 cópy!.jpg</code> => <code>example-1-copy.jpg</code>', 'wp-seopress'); ?>
</p>

<?php if (isset($options['seopress_advanced_advanced_clean_filename'])) {
        esc_attr($options['seopress_advanced_advanced_clean_filename']);
    }
}

function seopress_advanced_advanced_image_auto_title_editor_callback() {
    $options = get_option('seopress_advanced_option_name');

    $check = isset($options['seopress_advanced_advanced_image_auto_title_editor']); ?>

<label for="seopress_advanced_advanced_image_auto_title_editor">
    <input id="seopress_advanced_advanced_image_auto_title_editor"
        name="seopress_advanced_option_name[seopress_advanced_advanced_image_auto_title_editor]" type="checkbox" <?php if ('1' == $check) { ?>
    checked="yes"
    <?php } ?>
    value="1"/>

    <?php _e('When uploading an image file, automatically set the title based on the filename', 'wp-seopress'); ?>
</label>

<p class="description">
    <?php _e('We use the product title for WooCommerce products.', 'wp-seopress'); ?>
</p>

<?php if (isset($options['seopress_advanced_advanced_image_auto_title_editor'])) {
        esc_attr($options['seopress_advanced_advanced_image_auto_title_editor']);
    }
}

function seopress_advanced_advanced_image_auto_alt_editor_callback() {
    $options = get_option('seopress_advanced_option_name');

    $check = isset($options['seopress_advanced_advanced_image_auto_alt_editor']); ?>

<label for="seopress_advanced_advanced_image_auto_alt_editor">
    <input id="seopress_advanced_advanced_image_auto_alt_editor"
        name="seopress_advanced_option_name[seopress_advanced_advanced_image_auto_alt_editor]" type="checkbox" <?php if ('1' == $check) { ?>
    checked="yes"
    <?php } ?>
    value="1"/>

    <?php _e('When uploading an image file, automatically set the alternative text based on the filename', 'wp-seopress'); ?>
</label>

<?php if ( ! is_plugin_active('imageseo/imageseo.php')) {
        if (method_exists(seopress_get_service('ToggleOption'), 'getToggleWhiteLabel') && '1' !== seopress_get_service('ToggleOption')->getToggleWhiteLabel()) {
            echo '<p class="seopress-help description"><a href="https://www.seopress.org/go/image-seo" target="_blank">' . __('We recommend Image SEO plugin to optimize your image ALT texts and names for Search Engines using AI and Machine Learning. Starting from just €4.99.', 'wp-seopress') . '</a><span class="dashicons dashicons-external"></span></p>';
        }
    }

    if (isset($options['seopress_advanced_advanced_image_auto_alt_editor'])) {
        esc_attr($options['seopress_advanced_advanced_image_auto_alt_editor']);
    }
}

function seopress_advanced_advanced_image_auto_alt_target_kw_callback() {
    $options = get_option('seopress_advanced_option_name');

    $check = isset($options['seopress_advanced_advanced_image_auto_alt_target_kw']); ?>

<label for="seopress_advanced_advanced_image_auto_alt_target_kw">
    <input id="seopress_advanced_advanced_image_auto_alt_target_kw"
        name="seopress_advanced_option_name[seopress_advanced_advanced_image_auto_alt_target_kw]" type="checkbox"
        <?php if ('1' == $check) { ?>
    checked="yes"
    <?php } ?>
    value="1"/>

    <?php _e('Use the target keywords if not alternative text set for the image', 'wp-seopress'); ?>
</label>

<p class="description">
    <?php _e('This setting will be applied to images without any alt text only on frontend. This setting is retroactive. If you turn it off, alt texts that were previously empty will be empty again.', 'wp-seopress'); ?>
</p>

<?php if (isset($options['seopress_advanced_advanced_image_auto_alt_target_kw'])) {
        esc_attr($options['seopress_advanced_advanced_image_auto_alt_target_kw']);
    }
}

function seopress_advanced_advanced_image_auto_caption_editor_callback() {
    $options = get_option('seopress_advanced_option_name');

    $check = isset($options['seopress_advanced_advanced_image_auto_caption_editor']); ?>

<label for="seopress_advanced_advanced_image_auto_caption_editor">
    <input id="seopress_advanced_advanced_image_auto_caption_editor"
        name="seopress_advanced_option_name[seopress_advanced_advanced_image_auto_caption_editor]" type="checkbox"
        <?php if ('1' == $check) { ?>
    checked="yes"
    <?php } ?>
    value="1"/>

    <?php _e('When uploading an image file, automatically set the caption based on the filename', 'wp-seopress'); ?>
</label>

<?php if (isset($options['seopress_advanced_advanced_image_auto_caption_editor'])) {
        esc_attr($options['seopress_advanced_advanced_image_auto_caption_editor']);
    }
}

function seopress_advanced_advanced_image_auto_desc_editor_callback() {
    $options = get_option('seopress_advanced_option_name');

    $check = isset($options['seopress_advanced_advanced_image_auto_desc_editor']); ?>
<label for="seopress_advanced_advanced_image_auto_desc_editor">
    <input id="seopress_advanced_advanced_image_auto_desc_editor"
        name="seopress_advanced_option_name[seopress_advanced_advanced_image_auto_desc_editor]" type="checkbox" <?php if ('1' == $check) { ?>
    checked="yes"
    <?php } ?>
    value="1"/>

    <?php _e('When uploading an image file, automatically set the description based on the filename', 'wp-seopress'); ?>
</label>

<?php if (isset($options['seopress_advanced_advanced_image_auto_desc_editor'])) {
        esc_attr($options['seopress_advanced_advanced_image_auto_desc_editor']);
    }
}
