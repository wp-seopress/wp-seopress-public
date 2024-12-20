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
    <?php esc_attr_e('Redirect attachment pages to post parent (or homepage if none)', 'wp-seopress'); ?>
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

    <?php esc_attr_e('Redirect attachment pages to their file URL (https://www.example.com/my-image-file.jpg)', 'wp-seopress'); ?>
</label>

<p class="description">
    <?php esc_attr_e('If this option is checked, it will take precedence over the redirection of attachments to the post\'s parent.', 'wp-seopress'); ?>
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

    <?php esc_attr_e('When upload a media, remove accents, spaces, capital letters... and force UTF-8 encoding', 'wp-seopress'); ?>
</label>

<p class="description">
    <?php echo wp_kses_post(__('e.g. <code>ExãMple 1 cópy!.jpg</code> => <code>example-1-copy.jpg</code>', 'wp-seopress')); ?>
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

    <?php esc_attr_e('When uploading an image file, automatically set the title based on the filename', 'wp-seopress'); ?>
</label>

<p class="description">
    <?php esc_attr_e('We use the product title for WooCommerce products.', 'wp-seopress'); ?>
</p>

<?php if (isset($options['seopress_advanced_advanced_image_auto_title_editor'])) {
        esc_attr($options['seopress_advanced_advanced_image_auto_title_editor']);
    }
}

function seopress_advanced_advanced_image_auto_alt_editor_callback() {
    $docs = seopress_get_docs_links();
    $options = get_option('seopress_advanced_option_name');

    $check = isset($options['seopress_advanced_advanced_image_auto_alt_editor']); ?>

<label for="seopress_advanced_advanced_image_auto_alt_editor">
    <input id="seopress_advanced_advanced_image_auto_alt_editor"
        name="seopress_advanced_option_name[seopress_advanced_advanced_image_auto_alt_editor]" type="checkbox" <?php if ('1' == $check) { ?>
    checked="yes"
    <?php } ?>
    value="1"/>

    <?php esc_attr_e('When uploading an image file, automatically set the alternative text based on the filename', 'wp-seopress'); ?>
</label>

<?php
    if ( ! is_plugin_active('wp-seopress-pro/seopress-pro.php')) {
        if (defined('SEOPRESS_WL_ADMIN_HEADER') && SEOPRESS_WL_ADMIN_HEADER === false) {
        } else {
            echo '<p class="seopress-help description"><a href="'.esc_url($docs['ai']['introduction']).'" target="_blank">' . esc_attr__('Our PRO version can optimize your image ALT texts for Search Engines using AI and Machine Learning.', 'wp-seopress') . '</a><span class="dashicons dashicons-external"></span></p>';
        }
    } else {
        echo '<p class="seopress-help description"><a href="'.esc_url(admin_url('admin.php?page=seopress-pro-page#tab=tab_seopress_ai')).'">' . esc_attr__('Use AI to automatically describe your image files.', 'wp-seopress') . '</a></p>';
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

    <?php esc_attr_e('Use the target keywords if not alternative text set for the image', 'wp-seopress'); ?>
</label>

<p class="description">
    <?php esc_attr_e('This setting will be applied to images without any alt text only on frontend. This setting is retroactive. If you turn it off, alt texts that were previously empty will be empty again.', 'wp-seopress'); ?>
</p>

<?php if (isset($options['seopress_advanced_advanced_image_auto_alt_target_kw'])) {
        esc_attr($options['seopress_advanced_advanced_image_auto_alt_target_kw']);
    }
}

function seopress_advanced_advanced_image_auto_alt_txt_callback() {
    $options = get_option('seopress_advanced_option_name');

    $check = isset($options['seopress_advanced_advanced_image_auto_alt_txt']); ?>

<label for="seopress_advanced_advanced_image_auto_alt_txt">
    <input id="seopress_advanced_advanced_image_auto_alt_txt"
        name="seopress_advanced_option_name[seopress_advanced_advanced_image_auto_alt_txt]" type="checkbox"
        <?php if ('1' == $check) { ?>
    checked="yes"
    <?php } ?>
    value="1"/>

    <?php esc_attr_e('Apply the alt text defined in your media library for already inserted images', 'wp-seopress'); ?>
</label>

<p class="description">
    <?php esc_attr_e('By default, WordPress does not update image alt texts entered from the media library after they are inserted into the content of a post, page, or post type. By checking this box, this will be done when the page loads on the fly as long as this option remains active.', 'wp-seopress'); ?>
</p>

<?php if (isset($options['seopress_advanced_advanced_image_auto_alt_txt'])) {
        esc_attr($options['seopress_advanced_advanced_image_auto_alt_txt']);
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

    <?php esc_attr_e('When uploading an image file, automatically set the caption based on the filename', 'wp-seopress'); ?>
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

    <?php esc_attr_e('When uploading an image file, automatically set the description based on the filename', 'wp-seopress'); ?>
</label>

<?php if (isset($options['seopress_advanced_advanced_image_auto_desc_editor'])) {
        esc_attr($options['seopress_advanced_advanced_image_auto_desc_editor']);
    }
}
