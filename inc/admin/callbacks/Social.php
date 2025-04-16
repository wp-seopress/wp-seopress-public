<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

function seopress_social_knowledge_type_callback()
{
    $options = get_option('seopress_social_option_name');

    $selected = isset($options['seopress_social_knowledge_type']) ? $options['seopress_social_knowledge_type'] : null; ?>

<select id="seopress_social_knowledge_type" name="seopress_social_option_name[seopress_social_knowledge_type]">
    <option <?php if ('none' == $selected) { ?>
        selected="selected"
        <?php } ?>
        value="none"><?php esc_attr_e('None (will disable this feature)', 'wp-seopress'); ?>
    </option>
    <option <?php if ('Person' == $selected) { ?>
        selected="selected"
        <?php } ?>
        value="Person"><?php esc_attr_e('Person', 'wp-seopress'); ?>
    </option>
    <option <?php if ('Organization' == $selected) { ?>
        selected="selected"
        <?php } ?>
        value="Organization"><?php esc_attr_e('Organization', 'wp-seopress'); ?>
    </option>
</select>

<?php if (isset($options['seopress_social_knowledge_type'])) {
        esc_attr($options['seopress_social_knowledge_type']);
    }
}

function seopress_social_knowledge_name_callback()
{
    $options = get_option('seopress_social_option_name');
    $check   = isset($options['seopress_social_knowledge_name']) ? $options['seopress_social_knowledge_name'] : null;

    printf(
        '<input type="text" name="seopress_social_option_name[seopress_social_knowledge_name]" placeholder="' . esc_html__('e.g. My Local Business', 'wp-seopress') . '" aria-label="' . esc_attr__('Your name/organization', 'wp-seopress') . '" value="%s"/>',
        esc_html($check)
    );
}

function seopress_social_knowledge_img_callback()
{
    $options = get_option('seopress_social_option_name');

    $options_set = isset($options['seopress_social_knowledge_img']) ? esc_attr($options['seopress_social_knowledge_img']) : null;

    $check = isset($options['seopress_social_knowledge_img']); ?>

<input id="seopress_social_knowledge_img_meta" type="text"
    value="<?php echo esc_attr($options_set); ?>"
    name="seopress_social_option_name[seopress_social_knowledge_img]"
    aria-label="<?php esc_attr_e('Your photo/organization logo', 'wp-seopress'); ?>"
    placeholder="<?php esc_html_e('Select your logo', 'wp-seopress'); ?>" />

<input id="seopress_social_knowledge_img_upload" class="btn btnSecondary" type="button" value="<?php esc_attr_e('Upload an Image', 'wp-seopress'); ?>" />
<input id="seopress_social_knowledge_img_remove" class="btn btnLink is-deletable" type="button" value="<?php esc_attr_e('Remove', 'wp-seopress'); ?>" />

<p class="description">
    <?php esc_attr_e('JPG, PNG, WebP and GIF allowed. The minimum allowed image dimension is 200 x 200 pixels.', 'wp-seopress'); ?><br>
    <?php esc_attr_e('The size of the image file must not exceed 8 MB. Use images that are at least 1200 x 630 pixels for the best display on high resolution devices.', 'wp-seopress'); ?><br>
    <?php esc_attr_e('At the minimum, you should use images that are 600 x 315 pixels to display link page posts with larger images.', 'wp-seopress'); ?>
</p>

<div id="seopress_social_knowledge_img_placeholder_upload" class="seopress-img-placeholder" data_caption="<?php esc_attr_e('Click to select an image', 'wp-seopress'); ?>">
    <img id="seopress_social_knowledge_img_placeholder_src" src="<?php echo esc_attr(seopress_get_service('SocialOption')->getSocialKnowledgeImage()); ?>" />
</div>

    <?php if (isset($options['seopress_social_knowledge_img'])) {
        esc_attr($options['seopress_social_knowledge_img']);
    }
}

function seopress_social_knowledge_desc_callback()
{
    $options = get_option('seopress_social_option_name');
    $check   = isset($options['seopress_social_knowledge_desc']) ? $options['seopress_social_knowledge_desc'] : null;

    printf(
        '<input type="text" name="seopress_social_option_name[seopress_social_knowledge_desc]" placeholder="' . esc_html__('e.g. The example corporation is well-known for producing high-quality widgets', 'wp-seopress') . '" aria-label="' . esc_attr__('Description', 'wp-seopress') . '" value="%s"/>',
        esc_html($check)
    ); ?>
        <p class="description">
            <?php esc_html_e('Only for Organizations', 'wp-seopress'); ?>
        </p>
    <?php
}

function seopress_social_knowledge_email_callback()
{
    $options = get_option('seopress_social_option_name');
    $check   = isset($options['seopress_social_knowledge_email']) ? $options['seopress_social_knowledge_email'] : null;

    printf(
        '<input type="text" name="seopress_social_option_name[seopress_social_knowledge_email]" placeholder="' . esc_html__('e.g. contact@example.com', 'wp-seopress') . '" aria-label="' . esc_attr__('Email', 'wp-seopress') . '" value="%s"/>',
        esc_html($check)
    ); ?>
        <p class="description">
            <?php esc_html_e('Only for Organizations', 'wp-seopress'); ?>
        </p>
    <?php
}

function seopress_social_knowledge_phone_callback()
{
    $options = get_option('seopress_social_option_name');
    $check   = isset($options['seopress_social_knowledge_phone']) ? $options['seopress_social_knowledge_phone'] : null;

    printf(
        '<input type="text" name="seopress_social_option_name[seopress_social_knowledge_phone]" placeholder="' . esc_html__('e.g. +33123456789 (internationalized version required)', 'wp-seopress') . '" aria-label="' . esc_attr__('Organization\'s phone number', 'wp-seopress') . '" value="%s"/>',
        esc_html($check)
    ); ?>
    <p class="description">
        <?php esc_html_e('Only for Organizations', 'wp-seopress'); ?>
    </p>
<?php
}

function seopress_social_knowledge_contact_type_callback()
{
    $options = get_option('seopress_social_option_name');

    $selected = isset($options['seopress_social_knowledge_contact_type']) ? $options['seopress_social_knowledge_contact_type'] : null; ?>

<select id="seopress_social_knowledge_contact_type"
    name="seopress_social_option_name[seopress_social_knowledge_contact_type]">
    <option <?php if ('customer support' == $selected) { ?>
        selected="selected"
        <?php } ?>
        value="customer support"><?php esc_attr_e('Customer support', 'wp-seopress'); ?>
    </option>
    <option <?php if ('technical support' == $selected) { ?>
        selected="selected"
        <?php } ?>
        value="technical support"><?php esc_attr_e('Technical support', 'wp-seopress'); ?>
    </option>
    <option <?php if ('billing support' == $selected) { ?>
        selected="selected"
        <?php } ?>
        value="billing support"><?php esc_attr_e('Billing support', 'wp-seopress'); ?>
    </option>
    <option <?php if ('bill payment' == $selected) { ?>
        selected="selected"
        <?php } ?>
        value="bill payment"><?php esc_attr_e('Bill payment', 'wp-seopress'); ?>
    </option>
    <option <?php if ('sales' == $selected) { ?>
        selected="selected"
        <?php } ?>
        value="sales"><?php esc_attr_e('Sales', 'wp-seopress'); ?>
    </option>
    <option <?php if ('credit card support' == $selected) { ?>
        selected="selected"
        <?php } ?>
        value="credit card support"><?php esc_attr_e('Credit card support', 'wp-seopress'); ?>
    </option>
    <option <?php if ('emergency' == $selected) { ?>
        selected="selected"
        <?php } ?>
        value="emergency"><?php esc_attr_e('Emergency', 'wp-seopress'); ?>
    </option>
    <option <?php if ('baggage tracking' == $selected) { ?>
        selected="selected"
        <?php } ?>
        value="baggage tracking"><?php esc_attr_e('Baggage tracking', 'wp-seopress'); ?>
    </option>
    <option <?php if ('roadside assistance' == $selected) { ?>
        selected="selected"
        <?php } ?>
        value="roadside assistance"><?php esc_attr_e('Roadside assistance', 'wp-seopress'); ?>
    </option>
    <option <?php if ('package tracking' == $selected) { ?>
        selected="selected"
        <?php } ?>
        value="package tracking"><?php esc_attr_e('Package tracking', 'wp-seopress'); ?>
    </option>
</select>

<p class="description">
    <?php esc_html_e('Only for Organizations', 'wp-seopress'); ?>
</p>

<?php if (isset($options['seopress_social_knowledge_contact_type'])) {
        esc_attr($options['seopress_social_knowledge_contact_type']);
    }
}

function seopress_social_knowledge_contact_option_callback()
{
    $options = get_option('seopress_social_option_name');

    $selected = isset($options['seopress_social_knowledge_contact_option']) ? $options['seopress_social_knowledge_contact_option'] : null; ?>

<select id="seopress_social_knowledge_contact_option"
    name="seopress_social_option_name[seopress_social_knowledge_contact_option]">
    <option <?php if ('None' == $selected) { ?>
        selected="selected"
        <?php } ?>
        value="None"><?php esc_attr_e('None', 'wp-seopress'); ?>
    </option>
    <option <?php if ('TollFree' == $selected) { ?>
        selected="selected"
        <?php } ?>
        value="TollFree"><?php esc_attr_e('Toll Free', 'wp-seopress'); ?>
    </option>
    <option <?php if ('HearingImpairedSupported' == $selected) { ?>
        selected="selected"
        <?php } ?>
        value="HearingImpairedSupported"><?php esc_attr_e('Hearing impaired supported', 'wp-seopress'); ?>
    </option>
</select>

<p class="description">
    <?php esc_html_e('Only for Organizations', 'wp-seopress'); ?>
</p>

<?php if (isset($options['seopress_social_knowledge_contact_option'])) {
        esc_attr($options['seopress_social_knowledge_contact_option']);
    }
}

function seopress_social_knowledge_tax_id_callback()
{
    $options = get_option('seopress_social_option_name');
    $check   = isset($options['seopress_social_knowledge_tax_id']) ? $options['seopress_social_knowledge_tax_id'] : null;

    printf(
        '<input type="text" name="seopress_social_option_name[seopress_social_knowledge_tax_id]" placeholder="' . esc_html__('e.g. FR12345678901', 'wp-seopress') . '" aria-label="' . esc_attr__('VAT ID', 'wp-seopress') . '" value="%s"/>',
        esc_html($check)
    ); ?>
    <p class="description">
        <?php esc_html_e('Only for Organizations', 'wp-seopress'); ?>
    </p>
<?php
}

function seopress_social_accounts_facebook_callback()
{
    $options = get_option('seopress_social_option_name');
    $check   = isset($options['seopress_social_accounts_facebook']) ? $options['seopress_social_accounts_facebook'] : null;

    printf(
        '<input type="text" name="seopress_social_option_name[seopress_social_accounts_facebook]" placeholder="' . esc_html__('e.g. https://facebook.com/my-page-url', 'wp-seopress') . '" aria-label="' . esc_attr__('Facebook Page URL', 'wp-seopress') . '" value="%s"/>',
        esc_html($check)
    );
}

function seopress_social_accounts_twitter_callback()
{
    $options = get_option('seopress_social_option_name');
    $check   = isset($options['seopress_social_accounts_twitter']) ? $options['seopress_social_accounts_twitter'] : null;

    printf(
        '<input type="text" name="seopress_social_option_name[seopress_social_accounts_twitter]" placeholder="' . esc_html__('e.g. @my_x_account', 'wp-seopress') . '" aria-label="' . esc_attr__('X Page URL', 'wp-seopress') . '" value="%s"/>',
        esc_html($check)
    );
}

function seopress_social_accounts_pinterest_callback()
{
    $options = get_option('seopress_social_option_name');
    $check   = isset($options['seopress_social_accounts_pinterest']) ? $options['seopress_social_accounts_pinterest'] : null;

    printf(
        '<input type="text" name="seopress_social_option_name[seopress_social_accounts_pinterest]" placeholder="' . esc_html__('e.g. https://pinterest.com/my-page-url/', 'wp-seopress') . '" aria-label="' . esc_attr__('Pinterest URL', 'wp-seopress') . '" value="%s"/>',
        esc_html($check)
    );
}

function seopress_social_accounts_instagram_callback()
{
    $options = get_option('seopress_social_option_name');
    $check   = isset($options['seopress_social_accounts_instagram']) ? $options['seopress_social_accounts_instagram'] : null;

    printf(
        '<input type="text" name="seopress_social_option_name[seopress_social_accounts_instagram]" placeholder="' . esc_html__('e.g. https://www.instagram.com/my-page-url/', 'wp-seopress') . '" aria-label="' . esc_attr__('Instagram URL', 'wp-seopress') . '" value="%s"/>',
        esc_html($check)
    );
}

function seopress_social_accounts_youtube_callback()
{
    $options = get_option('seopress_social_option_name');
    $check   = isset($options['seopress_social_accounts_youtube']) ? $options['seopress_social_accounts_youtube'] : null;

    printf(
        '<input type="text" name="seopress_social_option_name[seopress_social_accounts_youtube]" placeholder="' . esc_html__('e.g. https://www.youtube.com/my-channel-url', 'wp-seopress') . '" aria-label="' . esc_attr__('YouTube URL', 'wp-seopress') . '" value="%s"/>',
        esc_html($check)
    );
}

function seopress_social_accounts_linkedin_callback()
{
    $options = get_option('seopress_social_option_name');
    $check   = isset($options['seopress_social_accounts_linkedin']) ? $options['seopress_social_accounts_linkedin'] : null;

    printf(
        '<input type="text" name="seopress_social_option_name[seopress_social_accounts_linkedin]" placeholder="' . esc_html__('e.g. http://linkedin.com/company/my-company-url/', 'wp-seopress') . '" aria-label="' . esc_attr__('LinkedIn URL', 'wp-seopress') . '" value="%s"/>',
        esc_html($check)
    );
}

function seopress_social_accounts_extra_callback() {
    $docs = function_exists('seopress_get_docs_links') ? seopress_get_docs_links() : '';
    $options = get_option('seopress_social_option_name');
    $check   = isset($options['seopress_social_accounts_extra']) ? esc_attr($options['seopress_social_accounts_extra']) : null;

    printf(
'<textarea id="seopress_social_accounts_extra" name="seopress_social_option_name[seopress_social_accounts_extra]" rows="8" placeholder="' . esc_html__('Enter 1 URL per line (e.g. https://example.com/my-profile)', 'wp-seopress') . '" aria-label="' . esc_attr__('Enter 1 URL per line (e.g. https://example.com/my-profile)', 'wp-seopress') . '">%s</textarea>',
esc_html($check)); ?>

<p class="seopress-help description"><?php esc_attr_e('Enter 1 URL per line (e.g. https://example.com/my-profile)', 'wp-seopress'); ?></p>

<?php
}

function seopress_social_facebook_og_callback()
{
    $options = get_option('seopress_social_option_name');

    $check = isset($options['seopress_social_facebook_og']); ?>

<label for="seopress_social_facebook_og">
    <input id="seopress_social_facebook_og" name="seopress_social_option_name[seopress_social_facebook_og]"
        type="checkbox" <?php if ('1' == $check) { ?>
    checked="yes"
    <?php } ?>
    value="1"/>

    <?php esc_attr_e('Enable OG data', 'wp-seopress'); ?>
</label>

<?php if (isset($options['seopress_social_facebook_og'])) {
        esc_attr($options['seopress_social_facebook_og']);
    }
}

function seopress_social_facebook_img_callback()
{
    $options = get_option('seopress_social_option_name');

    $options_set = isset($options['seopress_social_facebook_img']) ? $options['seopress_social_facebook_img'] : null;
    $options_set_attachment_id = isset($options['seopress_social_facebook_img_attachment_id']) ? esc_attr($options['seopress_social_facebook_img_attachment_id']) : null;
    $options_set_width = isset($options['seopress_social_facebook_img_width']) ? esc_attr($options['seopress_social_facebook_img_width']) : null;
    $options_set_height = isset($options['seopress_social_facebook_img_height']) ? esc_attr($options['seopress_social_facebook_img_height']) : null;



    ?>

<input id="seopress_social_fb_img_meta" type="text"
    value="<?php echo esc_attr($options_set); ?>"
    name="seopress_social_option_name[seopress_social_facebook_img]"
    aria-label="<?php esc_attr_e('Select a default image', 'wp-seopress'); ?>"
    placeholder="<?php esc_html_e('Select your default thumbnail', 'wp-seopress'); ?>" />


<input type="hidden" name="seopress_social_facebook_img_width" id="seopress_social_fb_img_width" value="<?php echo esc_html($options_set_width); ?>">
<input type="hidden" name="seopress_social_facebook_img_height" id="seopress_social_fb_img_height" value="<?php echo esc_html($options_set_height); ?>">
<input type="hidden" name="seopress_social_facebook_img_attachment_id" id="seopress_social_fb_img_attachment_id" value="<?php echo esc_html($options_set_attachment_id); ?>">

<input id="seopress_social_fb_img_upload" class="btn btnSecondary" type="button" value="<?php esc_attr_e('Upload an Image', 'wp-seopress'); ?>" />
<input id="seopress_social_fb_img_remove" class="btn btnLink is-deletable" type="button" value="<?php esc_attr_e('Remove', 'wp-seopress'); ?>" />


<p class="description"><?php esc_attr_e('Minimum size: 200x200px, ideal ratio 1.91:1, 8Mb max. (e.g. 1640x856px or 3280x1712px for retina screens)', 'wp-seopress'); ?></p>
<p class="description"><?php esc_attr_e('If no default image is set, we‘ll use your site icon defined from the Customizer.', 'wp-seopress'); ?></p>

<div id="seopress_social_fb_img_placeholder_upload" class="seopress-img-placeholder" data_caption="<?php esc_attr_e('Click to select an image', 'wp-seopress'); ?>">
    <img id="seopress_social_fb_img_placeholder_src" style="width: 524px;height: 274px;" src="<?php echo esc_attr($options_set); ?>" />
</div>

<?php if (isset($options['seopress_social_facebook_img'])) {
        esc_attr($options['seopress_social_facebook_img']);
    }
}

function seopress_social_facebook_img_default_callback()
{
    $options = get_option('seopress_social_option_name');

    $check = isset($options['seopress_social_facebook_img_default']); ?>

<label for="seopress_social_facebook_img_default">
    <input id="seopress_social_facebook_img_default"
        name="seopress_social_option_name[seopress_social_facebook_img_default]" type="checkbox" <?php if ('1' == $check) { ?>
    checked="yes"
    <?php } ?>
    value="1"/>
    <?php echo wp_kses_post(__('Override every <strong>og:image</strong> tag with this default image (except if a custom og:image has already been set from the SEO metabox).', 'wp-seopress')); ?>
</label>

<?php $def_og_img = isset($options['seopress_social_facebook_img']) ? $options['seopress_social_facebook_img'] : '';

    if ('' == $def_og_img) { ?>
<div class="seopress-notice is-warning is-inline">
    <p>
        <?php echo wp_kses_post(__('Please define a <strong>default OG Image</strong> from the field above', 'wp-seopress')); ?>
    </p>
</div>
<?php }

    if (isset($options['seopress_social_facebook_img_default'])) {
        esc_attr($options['seopress_social_facebook_img_default']);
    }
}

function seopress_social_facebook_img_cpt_callback()
{
    $post_types = seopress_get_service('WordPressData')->getPostTypes(false, ['has_archive' => true]);
    if (! empty($post_types)) {
        unset($post_types['post'], $post_types['page']);

        if (! empty($post_types)) {
            foreach ($post_types as $seopress_cpt_key => $seopress_cpt_value) { ?>
<h3><?php echo esc_html($seopress_cpt_value->labels->name); ?>
    <em><small>[<?php echo esc_html($seopress_cpt_value->name); ?>]</small></em>
</h3>

<?php if ('product' === $seopress_cpt_value->name && is_plugin_active('woocommerce/woocommerce.php')) { ?>
<p>
    <?php esc_attr_e('WooCommerce Shop Page.', 'wp-seopress'); ?>
</p>
<?php }

    $options = get_option('seopress_social_option_name');

    $options_set = isset($options['seopress_social_facebook_img_cpt'][$seopress_cpt_key]['url']) ? $options['seopress_social_facebook_img_cpt'][$seopress_cpt_key]['url'] : '';
?>

<p>
    <input
        id="seopress_social_facebook_img_cpt_meta_<?php echo esc_attr($seopress_cpt_key); ?>"
        class="seopress_social_facebook_img_cpt_meta" type="text"
        value="<?php echo esc_url($options_set); ?>"
        name="seopress_social_option_name[seopress_social_facebook_img_cpt][<?php echo esc_attr($seopress_cpt_key); ?>][url]"
        aria-label="<?php esc_attr_e('Select a default image', 'wp-seopress'); ?>"
        placeholder="<?php esc_html_e('Select your default thumbnail', 'wp-seopress'); ?>" />

    <input
        id="seopress_social_facebook_img_upload"
        class="seopress_social_facebook_img_cpt seopress-btn-upload-media btn btnSecondary"
        data-input-value="#seopress_social_facebook_img_cpt_meta_<?php echo esc_attr($seopress_cpt_key); ?>"
        type="button"
        value="<?php esc_attr_e('Upload an Image', 'wp-seopress'); ?>" />

</p>

<?php if (isset($options['seopress_social_facebook_img_cpt'][$seopress_cpt_key]['url'])) {
                    esc_attr($options['seopress_social_facebook_img_cpt'][$seopress_cpt_key]['url']);
                }
            }
        }
    }
    else { ?>
        <p>
            <?php esc_attr_e('No custom post type to configure.', 'wp-seopress'); ?>
        </p>
        <?php
    }
}

function seopress_social_facebook_link_ownership_id_callback()
{
    $options = get_option('seopress_social_option_name');
    $check   = isset($options['seopress_social_facebook_link_ownership_id']) ? $options['seopress_social_facebook_link_ownership_id'] : null;

    printf(
        '<input type="text" placeholder="' . esc_attr__('1234567890','wp-seopress') . '" name="seopress_social_option_name[seopress_social_facebook_link_ownership_id]" value="%s"/>',
        esc_html($check)
    ); ?>

<p class="description">
    <?php esc_attr_e('One or more Facebook Page IDs that are associated with a URL in order to enable link editing and instant article publishing.', 'wp-seopress'); ?>
</p>

<pre>&lt;meta property="fb:pages" content="page ID"/&gt;</pre>

<p>
    <span class="seopress-help dashicons dashicons-external"></span>
    <a class="seopress-help" href="https://www.facebook.com/help/1503421039731588" target="_blank">
        <?php esc_attr_e('How do I find my Facebook Page ID?', 'wp-seopress'); ?>
    </a>
</p>
<?php
}

function seopress_social_facebook_admin_id_callback()
{
    $options = get_option('seopress_social_option_name');
    $check   = isset($options['seopress_social_facebook_admin_id']) ? $options['seopress_social_facebook_admin_id'] : null;

    printf(
        '<input type="text" placeholder="' . esc_attr__('1234567890','wp-seopress') . '" name="seopress_social_option_name[seopress_social_facebook_admin_id]" value="%s"/>',
        esc_html($check)
    ); ?>

<p class="description">
    <?php esc_attr_e('The ID (or comma-separated list for properties that can accept multiple IDs) of an app, person using the app, or Page Graph API object.', 'wp-seopress'); ?>
</p>

<pre>&lt;meta property="fb:admins" content="admins ID"/&gt;</pre>

<?php
}

function seopress_social_facebook_app_id_callback()
{
    $options = get_option('seopress_social_option_name');
    $check   = isset($options['seopress_social_facebook_app_id']) ? $options['seopress_social_facebook_app_id'] : null;

    printf(
        '<input type="text" placeholder="' . esc_attr__('1234567890','wp-seopress') . '" name="seopress_social_option_name[seopress_social_facebook_app_id]" value="%s"/>',
        esc_html($check)
    ); ?>

<p class="description">
    <?php echo wp_kses_post(__('The Facebook app ID of the site\'s app. In order to use Facebook Insights you must add the app ID to your page. Insights lets you view analytics for traffic to your site from Facebook. Find the app ID in your App Dashboard. <a class="seopress-help" href="https://developers.facebook.com/apps/redirect/dashboard" target="_blank">More info here</a> <span class="seopress-help dashicons dashicons-external"></span>', 'wp-seopress')); ?>
</p>

<pre>&lt;meta property="fb:app_id" content="app ID"/&gt;</pre>

<p>
    <span class="seopress-help dashicons dashicons-external"></span>
    <a class="seopress-help" href="https://developers.facebook.com/docs/apps/register" target="_blank">
        <?php esc_attr_e('How to create a Facebook App ID', 'wp-seopress'); ?>
    </a>
</p>
<?php
}

function seopress_social_twitter_card_callback()
{
    $options = get_option('seopress_social_option_name');

    $check = isset($options['seopress_social_twitter_card']); ?>

<label for="seopress_social_twitter_card">
    <input id="seopress_social_twitter_card" name="seopress_social_option_name[seopress_social_twitter_card]"
        type="checkbox" <?php if ('1' == $check) { ?>
    checked="yes"
    <?php } ?>
    value="1"/>

    <?php esc_attr_e('Enable X Cards', 'wp-seopress'); ?>
</label>

<?php if (isset($options['seopress_social_twitter_card'])) {
        esc_attr($options['seopress_social_twitter_card']);
    }
}

function seopress_social_twitter_card_og_callback()
{
    $options = get_option('seopress_social_option_name');

    $check = isset($options['seopress_social_twitter_card_og']); ?>

<label for="seopress_social_twitter_card_og">
    <input id="seopress_social_twitter_card_og" name="seopress_social_option_name[seopress_social_twitter_card_og]"
        type="checkbox" <?php if ('1' == $check) { ?>
    checked="yes"
    <?php } ?>
    value="1"/>

    <?php esc_attr_e('Use OG if no X Cards', 'wp-seopress'); ?>
</label>

<?php if (isset($options['seopress_social_twitter_card_og'])) {
        esc_attr($options['seopress_social_twitter_card_og']);
    }
}

function seopress_social_twitter_card_img_callback()
{
    $options = get_option('seopress_social_option_name');

    $options_set = isset($options['seopress_social_twitter_card_img']) ? $options['seopress_social_twitter_card_img'] : '';
?>

<input id="seopress_social_twitter_img_meta" type="text"
    value="<?php echo esc_url($options_set); ?>"
    name="seopress_social_option_name[seopress_social_twitter_card_img]"
    aria-label="<?php esc_attr_e('Default X Image', 'wp-seopress'); ?>"
    placeholder="<?php esc_html_e('Select your default thumbnail', 'wp-seopress'); ?>" />

<input id="seopress_social_twitter_img_upload" class="btn btnSecondary" type="button" value="<?php esc_attr_e('Upload an Image', 'wp-seopress'); ?>" />
<input id="seopress_social_twitter_img_remove" class="btn btnLink is-deletable" type="button" value="<?php esc_attr_e('Remove', 'wp-seopress'); ?>" />

<p class="description">
    <?php esc_attr_e('Minimum size: 144x144px (300x157px with large card enabled), ideal ratio 1:1 (2:1 with large card), 5Mb max.', 'wp-seopress'); ?>
</p>

<div id="seopress_social_twitter_img_placeholder_upload" class="seopress-img-placeholder" data_caption="<?php esc_attr_e('Click to select an image', 'wp-seopress'); ?>">
    <img id="seopress_social_twitter_img_placeholder_src" class="seopress-img-x-preview" src="<?php echo esc_url($options_set); ?>" />
</div>

<?php if (isset($options['seopress_social_twitter_card_img'])) {
        esc_attr($options['seopress_social_twitter_card_img']);
    }
}

function seopress_social_twitter_card_img_size_callback()
{
    $options = get_option('seopress_social_option_name');

    $selected = isset($options['seopress_social_twitter_card_img_size']) ? $options['seopress_social_twitter_card_img_size'] : null; ?>

<select id="seopress_social_twitter_card_img_size"
    name="seopress_social_option_name[seopress_social_twitter_card_img_size]">
    <option <?php if ('default' == $selected) { ?>
        selected="selected"
        <?php } ?>
        value="default"><?php esc_attr_e('Default', 'wp-seopress'); ?>
    </option>
    <option <?php if ('large' == $selected) { ?>
        selected="selected"
        <?php } ?>
        value="large"><?php esc_attr_e('Large', 'wp-seopress'); ?>
    </option>
</select>

<p class="description">
    <?php echo wp_kses_post(__('The Summary Card with <strong>Large Image</strong> features a large, full-width prominent image alongside a tweet. It is designed to give the reader a rich photo experience, and clicking on the image brings the user to your website.', 'wp-seopress')); ?>
</p>

<?php if (isset($options['seopress_social_twitter_card_img_size'])) {
        esc_attr($options['seopress_social_twitter_card_img_size']);
    }
}

function seopress_social_li_img_size_callback()
{
    $options = get_option('seopress_social_option_name');

    $selected = isset($options['seopress_social_li_img_size']) ? $options['seopress_social_li_img_size'] : 'full'; ?>
<?php
    $img_sizes = [];
    $img_sizes = get_intermediate_image_sizes();
    $img_sizes[] = 'full';
?>
<select id="seopress_social_li_img_size" name="seopress_social_option_name[seopress_social_li_img_size]">
    <?php
        foreach($img_sizes as $size) {
            ?>
                <option <?php if ($size == $selected) { ?>
                    selected="selected"
                    <?php } ?>
                    value="<?php echo esc_attr($size); ?>"><?php echo esc_html($size); ?>
                </option>
            <?php
        }
    ?>
</select>

<p class="description">
    <?php esc_attr_e('Default: "full"', 'wp-seopress'); ?>
</p>

<?php if (isset($options['seopress_social_li_img_size'])) {
        esc_attr($options['seopress_social_li_img_size']);
    }
}

function seopress_social_fv_creator_callback()
{
    $options = get_option('seopress_social_option_name');
    $check   = isset($options['seopress_social_fv_creator']) ? $options['seopress_social_fv_creator'] : null;

    printf(
        '<input type="text" name="seopress_social_option_name[seopress_social_fv_creator]" placeholder="' . esc_html__('Your fediverse account (Mastodon, Threads...), e.g. @Gargron@mastodon.social', 'wp-seopress') . '" aria-label="' . esc_attr__('Your fediverse account (Mastodon, Flipboard, Threads...)', 'wp-seopress') . '" value="%s"/>',
        esc_html($check)
    );
    ?>

    <p class="description">
        <?php esc_attr_e('Improve the sharing of your posts on Mastodon.', 'wp-seopress'); ?>
        <a href="https://blog.joinmastodon.org/2024/07/highlighting-journalism-on-mastodon/" target="_blank" class="seopress-help">
            <?php esc_html_e('Learn more', 'wp-seopress'); ?>
        </a>
        <span class="seopress-help dashicons dashicons-external"></span>
    </p>

    <?php
}
