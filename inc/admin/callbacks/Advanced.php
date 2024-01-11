<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

function seopress_advanced_advanced_replytocom_callback() {
	$options = get_option('seopress_advanced_option_name');

	$check = isset($options['seopress_advanced_advanced_replytocom']); ?>

<label for="seopress_advanced_advanced_replytocom">
	<input id="seopress_advanced_advanced_replytocom"
		name="seopress_advanced_option_name[seopress_advanced_advanced_replytocom]" type="checkbox" <?php if ('1' == $check) { ?>
	checked="yes"
	<?php } ?>
	value="1"/>

    <?php _e('Remove ?replytocom link in source code and replace it with a simple anchor', 'wp-seopress'); ?>
</label>

<p class="description">
    <?php _e( 'e.g. <code>https://www.example.com/my-blog-post/?replytocom=10#respond</code> => <code>#comment-10</code>', 'wp-seopress' ); ?>
</p>

<?php if (isset($options['seopress_advanced_advanced_replytocom'])) {
		esc_attr($options['seopress_advanced_advanced_replytocom']);
	}
}

function seopress_advanced_advanced_noreferrer_callback() {
	$options = get_option('seopress_advanced_option_name');

	$check = isset($options['seopress_advanced_advanced_noreferrer']); ?>

<label for="seopress_advanced_advanced_noreferrer">
	<input id="seopress_advanced_advanced_noreferrer"
		name="seopress_advanced_option_name[seopress_advanced_advanced_noreferrer]" type="checkbox" <?php if ('1' == $check) { ?>
	checked="yes"
	<?php } ?>
	value="1"/>
	<?php _e('Remove noreferrer link attribute in source code', 'wp-seopress'); ?>
</label>

<p class="description">
	<?php _e('Useful for affiliate links (e.g. Amazon).','wp-seopress'); ?>
</p>

<?php if (isset($options['seopress_advanced_advanced_noreferrer'])) {
		esc_attr($options['seopress_advanced_advanced_noreferrer']);
	}
}

function seopress_advanced_advanced_tax_desc_editor_callback() {
	$options = get_option('seopress_advanced_option_name');

	$check = isset($options['seopress_advanced_advanced_tax_desc_editor']); ?>

<label for="seopress_advanced_advanced_tax_desc_editor">
	<input id="seopress_advanced_advanced_tax_desc_editor"
		name="seopress_advanced_option_name[seopress_advanced_advanced_tax_desc_editor]" type="checkbox" <?php if ('1' == $check) { ?>
	checked="yes"
	<?php } ?>
	value="1"/>
	<?php _e('Add TINYMCE editor to term description', 'wp-seopress'); ?>
</label>

<?php if (isset($options['seopress_advanced_advanced_tax_desc_editor'])) {
		esc_attr($options['seopress_advanced_advanced_tax_desc_editor']);
	}
}

function seopress_advanced_advanced_category_url_callback() {
	$options = get_option('seopress_advanced_option_name');

	$check = isset($options['seopress_advanced_advanced_category_url']); ?>

<label for="seopress_advanced_advanced_category_url">
	<input id="seopress_advanced_advanced_category_url"
		name="seopress_advanced_option_name[seopress_advanced_advanced_category_url]" type="checkbox" <?php if ('1' == $check) { ?>
	checked="yes"
	<?php } ?>
	value="1"/>
	<?php
	$category_base = '/category/';
	if (get_option('category_base')) {
		$category_base = '/' . get_option('category_base');
	}
    /* translators: %s category base, eg: /category/ */
	printf(__('Remove <strong>%s</strong> in your permalinks', 'wp-seopress'), $category_base); ?>
</label>

<p class="description">
	<?php _e('e.g. <code>https://example.com/category/my-post-category/</code> => <code>https://example.com/my-post-category/</code>','wp-seopress'); ?>
</p>

<div class="seopress-notice">
	<p>
		<?php _e('You have to flush your permalinks each time you change this setting.', 'wp-seopress'); ?>
	</p>
</div>

<?php
	if (isset($options['seopress_advanced_advanced_category_url'])) {
		esc_attr($options['seopress_advanced_advanced_category_url']);
	}
}

function seopress_advanced_advanced_product_cat_url_callback() {
	if (is_plugin_active('woocommerce/woocommerce.php')) {
		$options = get_option('seopress_advanced_option_name');

		$check = isset($options['seopress_advanced_advanced_product_cat_url']);

		?>

	<label for="seopress_advanced_advanced_product_cat_url">
		<input id="seopress_advanced_advanced_product_cat_url"
			name="seopress_advanced_option_name[seopress_advanced_advanced_product_cat_url]" type="checkbox" <?php if ('1' == $check) { ?>
		checked="yes"
		<?php } ?>
		value="1"/>

		<?php
		$category_base = get_option('woocommerce_permalinks');
		$category_base = $category_base['category_base'];

		if ('' != $category_base) {
			$category_base = '/' . $category_base . '/';
		} else {
			$category_base = '/product-category/';
		}

		printf(__('Remove <strong>%s</strong> in your permalinks', 'wp-seopress'), $category_base); ?>

	</label>

	<p class="description">
		<?php _e('e.g. <code>https://example.com/product-category/my-product-category/</code> => <code>https://example.com/my-product-category/</code>','wp-seopress'); ?>
	</p>

	<div class="seopress-notice">
		<p>
			<?php _e('You have to flush your permalinks each time you change this setting.', 'wp-seopress'); ?>
		</p>
		<p>
			<?php _e('Make sure you don\'t have identical URLs after activating this option to prevent conflicts.', 'wp-seopress'); ?>
		</p>
	</div>

	<?php
		if (isset($options['seopress_advanced_advanced_product_cat_url'])) {
			esc_attr($options['seopress_advanced_advanced_product_cat_url']);
		}
	} else { ?>
		<div class="seopress-notice is-warning">
			<p>
				<?php _e('You need to enable <strong>WooCommerce</strong> to apply these settings.', 'wp-seopress'); ?>
			</p>
		</div>
		<?php
	}
}

function seopress_advanced_advanced_wp_generator_callback() {
	$options = get_option('seopress_advanced_option_name');

	$check = isset($options['seopress_advanced_advanced_wp_generator']); ?>

<label for="seopress_advanced_advanced_wp_generator">
	<input id="seopress_advanced_advanced_wp_generator"
		name="seopress_advanced_option_name[seopress_advanced_advanced_wp_generator]" type="checkbox" <?php if ('1' == $check) { ?>
	checked="yes"
	<?php } ?>
	value="1"/>
	<?php _e('Remove WordPress meta generator in source code', 'wp-seopress'); ?>
</label>

<pre><?php echo esc_attr('<meta name="generator" content="WordPress 6.2" />'); ?></pre>

<?php if (isset($options['seopress_advanced_advanced_wp_generator'])) {
		esc_attr($options['seopress_advanced_advanced_wp_generator']);
	}
}

function seopress_advanced_advanced_hentry_callback() {
	$options = get_option('seopress_advanced_option_name');

	$check = isset($options['seopress_advanced_advanced_hentry']); ?>

<label for="seopress_advanced_advanced_hentry">
	<input id="seopress_advanced_advanced_hentry"
		name="seopress_advanced_option_name[seopress_advanced_advanced_hentry]" type="checkbox" <?php if ('1' == $check) { ?>
	checked="yes"
	<?php } ?>
	value="1"/>

	<?php _e('Remove hentry post class to prevent Google from seeing this as structured data (schema)', 'wp-seopress'); ?>
</label>

<?php if (isset($options['seopress_advanced_advanced_hentry'])) {
		esc_attr($options['seopress_advanced_advanced_hentry']);
	}
}

function seopress_advanced_advanced_comments_author_url_callback() {
	$options = get_option('seopress_advanced_option_name');

	$check = isset($options['seopress_advanced_advanced_comments_author_url']); ?>

<label for="seopress_advanced_advanced_comments_author_url">
	<input id="seopress_advanced_advanced_comments_author_url"
		name="seopress_advanced_option_name[seopress_advanced_advanced_comments_author_url]" type="checkbox" <?php if ('1' == $check) { ?>
	checked="yes"
	<?php } ?>
	value="1"/>

	<?php _e('Remove comment author URL in comments if the website is filled from profile page', 'wp-seopress'); ?>
</label>

<?php if (isset($options['seopress_advanced_advanced_comments_author_url'])) {
		esc_attr($options['seopress_advanced_advanced_comments_author_url']);
	}
}

function seopress_advanced_advanced_comments_website_callback() {
	$options = get_option('seopress_advanced_option_name');

	$check = isset($options['seopress_advanced_advanced_comments_website']); ?>

<label for="seopress_advanced_advanced_comments_website">
	<input id="seopress_advanced_advanced_comments_website"
		name="seopress_advanced_option_name[seopress_advanced_advanced_comments_website]" type="checkbox" <?php if ('1' == $check) { ?>
	checked="yes"
	<?php } ?>
	value="1"/>

	<?php _e('Remove website field from comment form to reduce spam', 'wp-seopress'); ?>
</label>

<?php if (isset($options['seopress_advanced_advanced_comments_website'])) {
		esc_attr($options['seopress_advanced_advanced_comments_website']);
	}
}

function seopress_advanced_advanced_comments_form_link_callback() {
	$options = get_option('seopress_advanced_option_name');

	$check = isset($options['seopress_advanced_advanced_comments_form_link']); ?>

<label for="seopress_advanced_advanced_comments_form_link">
	<input id="seopress_advanced_advanced_comments_form_link"
		name="seopress_advanced_option_name[seopress_advanced_advanced_comments_form_link]" type="checkbox" <?php if ('1' == $check) { ?>
	checked="yes"
	<?php } ?>
	value="1"/>

    <?php _e('Prevent search engines to follow / index the link to the comments form', 'wp-seopress'); ?>

</label>

<pre>https://www.example.com/my-blog-post/#respond</pre>

<?php if (isset($options['seopress_advanced_advanced_comments_form_link'])) {
		esc_attr($options['seopress_advanced_advanced_comments_form_link']);
	}
}

function seopress_advanced_advanced_wp_shortlink_callback() {
	$options = get_option('seopress_advanced_option_name');

	$check = isset($options['seopress_advanced_advanced_wp_shortlink']); ?>

<label for="seopress_advanced_advanced_wp_shortlink">
	<input id="seopress_advanced_advanced_wp_shortlink"
		name="seopress_advanced_option_name[seopress_advanced_advanced_wp_shortlink]" type="checkbox" <?php if ('1' == $check) { ?>
	checked="yes"
	<?php } ?>
	value="1"/>

	<?php _e('Remove WordPress shortlink meta tag in source code', 'wp-seopress'); ?>
</label>

<pre><?php esc_attr_e('<link rel="shortlink" href="https://www.example.com/"/>'); ?></pre>

<?php if (isset($options['seopress_advanced_advanced_wp_shortlink'])) {
		esc_attr($options['seopress_advanced_advanced_wp_shortlink']);
	}
}

function seopress_advanced_advanced_wp_wlw_callback() {
	$options = get_option('seopress_advanced_option_name');

	$check = isset($options['seopress_advanced_advanced_wp_wlw']); ?>

<label for="seopress_advanced_advanced_wp_wlw">
	<input id="seopress_advanced_advanced_wp_wlw"
		name="seopress_advanced_option_name[seopress_advanced_advanced_wp_wlw]" type="checkbox" <?php if ('1' == $check) { ?>
	checked="yes"
	<?php } ?>
	value="1"/>

	<?php _e('Remove Windows Live Writer meta tag in source code', 'wp-seopress'); ?>
</label>

<pre><?php esc_attr_e('<link rel="wlwmanifest" type="application/wlwmanifest+xml" href="https://www.example.com/wp-includes/wlwmanifest.xml" />'); ?></pre>

<?php if (isset($options['seopress_advanced_advanced_wp_wlw'])) {
		esc_attr($options['seopress_advanced_advanced_wp_wlw']);
	}
}

function seopress_advanced_advanced_wp_rsd_callback() {
	$options = get_option('seopress_advanced_option_name');

	$check = isset($options['seopress_advanced_advanced_wp_rsd']); ?>

<label for="seopress_advanced_advanced_wp_rsd">
	<input id="seopress_advanced_advanced_wp_rsd"
		name="seopress_advanced_option_name[seopress_advanced_advanced_wp_rsd]" type="checkbox" <?php if ('1' == $check) { ?>
	checked="yes"
	<?php } ?>
	value="1"/>

	<?php _e('Remove Really Simple Discovery meta tag in source code', 'wp-seopress'); ?>
</label>

<p class="description">
	<?php _e('WordPress Site Health feature will return a HTTPS warning if you enable this option. This is a false positive of course.', 'wp-seopress'); ?>
</p>

<pre><?php esc_attr_e('<link rel="EditURI" type="application/rsd+xml" title="RSD" href="https://www.example.com/xmlrpc.php?rsd" />'); ?></pre>

<?php if (isset($options['seopress_advanced_advanced_wp_rsd'])) {
		esc_attr($options['seopress_advanced_advanced_wp_rsd']);
	}
}

function seopress_advanced_advanced_wp_oembed_callback() {
	$options = get_option('seopress_advanced_option_name');

	$check = isset($options['seopress_advanced_advanced_wp_oembed']); ?>

<label for="seopress_advanced_advanced_wp_oembed">
	<input id="seopress_advanced_advanced_wp_oembed"
		name="seopress_advanced_option_name[seopress_advanced_advanced_wp_oembed]" type="checkbox" <?php if ('1' == $check) { ?>
	checked="yes"
	<?php } ?>
	value="1"/>

	<?php _e('Remove oEmbed links in source code', 'wp-seopress'); ?>
</label>

<p class="description">
	<?php _e('This will prevent other blogs to embed one of your posts on their site.', 'wp-seopress'); ?>
</p>

<pre><?php esc_attr_e('<link rel="alternate" type="application/json+oembed" href="https://www.example.com/wp-json/oembed/1.0/embed?url=https://www.example.com/my-blog-post/" />'); ?></pre>

<pre><?php esc_attr_e('<link rel="alternate" type="text/xml+oembed" href="https://www.example.com/wp-json/oembed/1.0/embed?url=https://www.example.com/my-blog-post/&format=xml" />'); ?></pre>

<?php if (isset($options['seopress_advanced_advanced_wp_oembed'])) {
		esc_attr($options['seopress_advanced_advanced_wp_oembed']);
	}
}

function seopress_advanced_advanced_wp_x_pingback_callback() {
	$options = get_option('seopress_advanced_option_name');

	$check = isset($options['seopress_advanced_advanced_wp_x_pingback']); ?>

<label for="seopress_advanced_advanced_wp_x_pingback">
	<input id="seopress_advanced_advanced_wp_x_pingback"
		name="seopress_advanced_option_name[seopress_advanced_advanced_wp_x_pingback]" type="checkbox" <?php if ('1' == $check) { ?>
	checked="yes"
	<?php } ?>
	value="1"/>

	<?php _e('Remove X-Pingback from HTTP headers', 'wp-seopress'); ?>
</label>

<p class="description">
	<?php _e('This will disable pingbacks/trackbacks and increase security (DDOS).', 'wp-seopress'); ?>
</p>

<pre><?php esc_attr_e('X-Pingback: https://www.example.com/xmlrpc.php'); ?></pre>

<?php if (isset($options['seopress_advanced_advanced_wp_x_pingback'])) {
		esc_attr($options['seopress_advanced_advanced_wp_x_pingback']);
	}
}

function seopress_advanced_advanced_wp_x_powered_by_callback() {
	$options = get_option('seopress_advanced_option_name');

	$check = isset($options['seopress_advanced_advanced_wp_x_powered_by']); ?>

<label for="seopress_advanced_advanced_wp_x_powered_by">
	<input id="seopress_advanced_advanced_wp_x_powered_by"
		name="seopress_advanced_option_name[seopress_advanced_advanced_wp_x_powered_by]" type="checkbox" <?php if ('1' == $check) { ?>
	checked="yes"
	<?php } ?>
	value="1"/>

	<?php _e('Remove X-Powered-By from HTTP headers', 'wp-seopress'); ?>
</label>

<p class="description">
	<?php _e('By default, WordPress uses this to display your PHP version.', 'wp-seopress'); ?>
</p>

<pre><?php esc_attr_e('X-Powered-By: PHP/8.1.9'); ?></pre>

<?php if (isset($options['seopress_advanced_advanced_wp_x_powered_by'])) {
		esc_attr($options['seopress_advanced_advanced_wp_x_powered_by']);
	}
}

function seopress_advanced_advanced_google_callback() {
	$options = get_option('seopress_advanced_option_name');
	$check   = isset($options['seopress_advanced_advanced_google']) ? $options['seopress_advanced_advanced_google'] : null;

	printf(
'<input type="text" name="seopress_advanced_option_name[seopress_advanced_advanced_google]" placeholder="' . esc_html__('Enter Google meta value site verification', 'wp-seopress') . '" aria-label="' . __('Google site verification', 'wp-seopress') . '" value="%s"/>',
esc_html($check)
); ?>
<p class="description">
	<?php _e('If your site is already verified in <strong>Google Search Console</strong>, you can leave this field empty.', 'wp-seopress'); ?>
</p>

<?php
}

function seopress_advanced_advanced_bing_callback() {
	$options = get_option('seopress_advanced_option_name');
	$check   = isset($options['seopress_advanced_advanced_bing']) ? $options['seopress_advanced_advanced_bing'] : null;

	printf(
'<input type="text" name="seopress_advanced_option_name[seopress_advanced_advanced_bing]" placeholder="' . esc_html__('Enter Bing meta value site verification', 'wp-seopress') . '" aria-label="' . __('Bing site verification', 'wp-seopress') . '" value="%s"/>',
esc_html($check)
); ?>
<p class="description">
	<?php _e('If your site is already verified in <strong>Bing Webmaster tools</strong>, you can leave this field empty.', 'wp-seopress'); ?>
</p>

<?php
}

function seopress_advanced_advanced_pinterest_callback() {
	$options = get_option('seopress_advanced_option_name');
	$check   = isset($options['seopress_advanced_advanced_pinterest']) ? $options['seopress_advanced_advanced_pinterest'] : null;

	printf(
'<input type="text" name="seopress_advanced_option_name[seopress_advanced_advanced_pinterest]" placeholder="' . esc_html__('Enter Pinterest meta value site verification', 'wp-seopress') . '" aria-label="' . __('Pinterest site verification', 'wp-seopress') . '" value="%s"/>',
esc_html($check)
);
}

function seopress_advanced_advanced_yandex_callback() {
	$options = get_option('seopress_advanced_option_name');
	$check   = isset($options['seopress_advanced_advanced_yandex']) ? $options['seopress_advanced_advanced_yandex'] : null;

	printf(
'<input type="text" name="seopress_advanced_option_name[seopress_advanced_advanced_yandex]" aria-label="' . __('Yandex site verification', 'wp-seopress') . '" placeholder="' . esc_html__('Enter Yandex meta value site verification', 'wp-seopress') . '" value="%s"/>',
esc_html($check)
);
}

function seopress_advanced_appearance_adminbar_callback() {
	$options = get_option('seopress_advanced_option_name');

	$check = isset($options['seopress_advanced_appearance_adminbar']); ?>

<label for="seopress_advanced_appearance_adminbar">
	<input id="seopress_advanced_appearance_adminbar"
		name="seopress_advanced_option_name[seopress_advanced_appearance_adminbar]" type="checkbox" <?php if ('1' == $check) { ?>
	checked="yes"
	<?php } ?>
	value="1"/>

	<?php _e('Remove SEO from Admin Bar in backend and frontend', 'wp-seopress'); ?>
</label>

<?php if (isset($options['seopress_advanced_appearance_adminbar'])) {
		esc_attr($options['seopress_advanced_appearance_adminbar']);
	}
}

function seopress_advanced_appearance_universal_metabox_disable_callback() {
	$docs = function_exists('seopress_get_docs_links') ? seopress_get_docs_links() : '';
	$options = get_option('seopress_advanced_option_name');

	if(!$options){
		$check = "1";
	} else {
		$check = isset($options['seopress_advanced_appearance_universal_metabox_disable']) && $options['seopress_advanced_appearance_universal_metabox_disable'] === '1' ? true : false;
	}
?>

<label for="seopress_advanced_appearance_universal_metabox_disable">
	<input id="seopress_advanced_appearance_universal_metabox_disable"
		name="seopress_advanced_option_name[seopress_advanced_appearance_universal_metabox_disable]"
		type="checkbox"
		<?php checked($check, "1"); ?>
		value="1"/>

	<?php _e('Disable the universal SEO metabox', 'wp-seopress'); ?>
</label>

<p class="description">
    <?php _e('Uncheck this option to edit your SEO directly from your page builder UI.','wp-seopress'); ?>
</p>
<p class="description">
	<a class="seopress-help" href="<?php echo $docs['universal']['introduction']; ?>" target="_blank">
		<?php _e('Learn more about how we interface with all page builders to optimize your productivity','wp-seopress'); ?>
		<span class="seopress-help dashicons dashicons-external"></span>
	</a>
</p>

<?php if (isset($options['seopress_advanced_appearance_adminbar'])) {
		esc_attr($options['seopress_advanced_appearance_adminbar']);
	}
}

function seopress_advanced_appearance_universal_metabox_callback() {
	$options = get_option('seopress_advanced_option_name');

	if(!$options){
		$check = "1";
	} else {
		$check = isset($options['seopress_advanced_appearance_universal_metabox']) && $options['seopress_advanced_appearance_universal_metabox'] === '1' ? true : false;
	}
?>

<label for="seopress_advanced_appearance_universal_metabox">
	<input id="seopress_advanced_appearance_universal_metabox"
		name="seopress_advanced_option_name[seopress_advanced_appearance_universal_metabox]"
		type="checkbox"
		<?php checked($check, "1"); ?>
		value="1"/>

	<?php _e('Enable the universal SEO metabox for the Block Editor (Gutenberg)', 'wp-seopress'); ?>

    <p class="description"><?php _e('Uncheck this option to keep the old SEO metaboxes located below the post content with the Block Editor.','wp-seopress'); ?></p>
    <p class="description"><?php _e('The previous option must be unchecked.','wp-seopress'); ?></p>
</label>

<?php if (isset($options['seopress_advanced_appearance_adminbar'])) {
		esc_attr($options['seopress_advanced_appearance_adminbar']);
	}
}

function seopress_advanced_appearance_adminbar_noindex_callback() {
	$options = get_option('seopress_advanced_option_name');

	$check = isset($options['seopress_advanced_appearance_adminbar_noindex']); ?>

<label for="seopress_advanced_appearance_adminbar_noindex">
	<input id="seopress_advanced_appearance_adminbar_noindex"
		name="seopress_advanced_option_name[seopress_advanced_appearance_adminbar_noindex]" type="checkbox" <?php if ('1' == $check) { ?>
	checked="yes"
	<?php } ?>
	value="1"/>

	<?php _e('Remove noindex item from Admin Bar in backend and frontend', 'wp-seopress'); ?>
</label>

<?php if (isset($options['seopress_advanced_appearance_adminbar_noindex'])) {
		esc_attr($options['seopress_advanced_appearance_adminbar_noindex']);
	}
}

function seopress_advanced_appearance_metaboxe_position_callback() {
	$options = get_option('seopress_advanced_option_name');

	$selected = isset($options['seopress_advanced_appearance_metaboxe_position']) ? $options['seopress_advanced_appearance_metaboxe_position'] : null; ?>

<select id="seopress_advanced_appearance_metaboxe_position"
	name="seopress_advanced_option_name[seopress_advanced_appearance_metaboxe_position]">
	<option <?php if ('high' == $selected) { ?>
		selected="selected"
		<?php } ?>
		value="high"><?php _e('High priority (top)', 'wp-seopress'); ?>
	</option>
	<option <?php if ('default' == $selected) { ?>
		selected="selected"
		<?php } ?>
		value="default"><?php _e('Normal priority (default)', 'wp-seopress'); ?>
	</option>
	<option <?php if ('low' == $selected) { ?>
		selected="selected"
		<?php } ?>
		value="low"><?php _e('Low priority', 'wp-seopress'); ?>
	</option>
</select>

<?php if (isset($options['seopress_advanced_appearance_metaboxe_position'])) {
		esc_attr($options['seopress_advanced_appearance_metaboxe_position']);
	}
}

function seopress_advanced_appearance_title_col_callback() {
	$options = get_option('seopress_advanced_option_name');

	$check = isset($options['seopress_advanced_appearance_title_col']); ?>

<label for="seopress_advanced_appearance_title_col">
	<input id="seopress_advanced_appearance_title_col"
		name="seopress_advanced_option_name[seopress_advanced_appearance_title_col]" type="checkbox" <?php if ('1' == $check) { ?>
	checked="yes"
	<?php } ?>
	value="1"/>

	<?php _e('Add title column', 'wp-seopress'); ?>
</label>

<?php if (isset($options['seopress_advanced_appearance_title_col'])) {
		esc_attr($options['seopress_advanced_appearance_title_col']);
	}
}

function seopress_advanced_appearance_meta_desc_col_callback() {
	$options = get_option('seopress_advanced_option_name');

	$check = isset($options['seopress_advanced_appearance_meta_desc_col']); ?>

<label for="seopress_advanced_appearance_meta_desc_col">
	<input id="seopress_advanced_appearance_meta_desc_col"
		name="seopress_advanced_option_name[seopress_advanced_appearance_meta_desc_col]" type="checkbox" <?php if ('1' == $check) { ?>
	checked="yes"
	<?php } ?>
	value="1"/>

	<?php _e('Add meta description column', 'wp-seopress'); ?>
</label>

<?php if (isset($options['seopress_advanced_appearance_meta_desc_col'])) {
		esc_attr($options['seopress_advanced_appearance_meta_desc_col']);
	}
}

function seopress_advanced_appearance_redirect_enable_col_callback() {
	$options = get_option('seopress_advanced_option_name');

	$check = isset($options['seopress_advanced_appearance_redirect_enable_col']); ?>

<label for="seopress_advanced_appearance_redirect_enable_col">
	<input id="seopress_advanced_appearance_redirect_enable_col"
		name="seopress_advanced_option_name[seopress_advanced_appearance_redirect_enable_col]" type="checkbox" <?php if ('1' == $check) { ?>
	checked="yes"
	<?php } ?>
	value="1"/>

	<?php _e('Add redirection enable column', 'wp-seopress'); ?>
</label>

<?php if (isset($options['seopress_advanced_appearance_redirect_enable_col'])) {
		esc_attr($options['seopress_advanced_appearance_redirect_enable_col']);
	}
}

function seopress_advanced_appearance_redirect_url_col_callback() {
	$options = get_option('seopress_advanced_option_name');

	$check = isset($options['seopress_advanced_appearance_redirect_url_col']); ?>

<label for="seopress_advanced_appearance_redirect_url_col">
	<input id="seopress_advanced_appearance_redirect_url_col"
		name="seopress_advanced_option_name[seopress_advanced_appearance_redirect_url_col]" type="checkbox" <?php if ('1' == $check) { ?>
	checked="yes"
	<?php } ?>
	value="1"/>

	<?php _e('Add redirection URL column', 'wp-seopress'); ?>
</label>

<?php if (isset($options['seopress_advanced_appearance_redirect_url_col'])) {
		esc_attr($options['seopress_advanced_appearance_redirect_url_col']);
	}
}

function seopress_advanced_appearance_canonical_callback() {
	$options = get_option('seopress_advanced_option_name');

	$check = isset($options['seopress_advanced_appearance_canonical']); ?>

<label for="seopress_advanced_appearance_canonical">
	<input id="seopress_advanced_appearance_canonical"
		name="seopress_advanced_option_name[seopress_advanced_appearance_canonical]" type="checkbox" <?php if ('1' == $check) { ?>
	checked="yes"
	<?php } ?>
	value="1"/>

	<?php _e('Add canonical URL column', 'wp-seopress'); ?>
</label>

<?php if (isset($options['seopress_advanced_appearance_canonical'])) {
		esc_attr($options['seopress_advanced_appearance_canonical']);
	}
}

function seopress_advanced_appearance_target_kw_col_callback() {
	$options = get_option('seopress_advanced_option_name');

	$check = isset($options['seopress_advanced_appearance_target_kw_col']); ?>

<label for="seopress_advanced_appearance_target_kw_col">
	<input id="seopress_advanced_appearance_target_kw_col"
		name="seopress_advanced_option_name[seopress_advanced_appearance_target_kw_col]" type="checkbox" <?php if ('1' == $check) { ?>
	checked="yes"
	<?php } ?>
	value="1"/>

	<?php _e('Add target keyword column', 'wp-seopress'); ?>
</label>

<?php if (isset($options['seopress_advanced_appearance_target_kw_col'])) {
		esc_attr($options['seopress_advanced_appearance_target_kw_col']);
	}
}

function seopress_advanced_appearance_noindex_col_callback() {
	$options = get_option('seopress_advanced_option_name');

	$check = isset($options['seopress_advanced_appearance_noindex_col']); ?>

<label for="seopress_advanced_appearance_noindex_col">
	<input id="seopress_advanced_appearance_noindex_col"
		name="seopress_advanced_option_name[seopress_advanced_appearance_noindex_col]" type="checkbox" <?php if ('1' == $check) { ?>
	checked="yes"
	<?php } ?>
	value="1"/>

	<?php _e('Display noindex status', 'wp-seopress'); ?>
</label>

<?php if (isset($options['seopress_advanced_appearance_noindex_col'])) {
		esc_attr($options['seopress_advanced_appearance_noindex_col']);
	}
}

function seopress_advanced_appearance_nofollow_col_callback() {
	$options = get_option('seopress_advanced_option_name');

	$check = isset($options['seopress_advanced_appearance_nofollow_col']); ?>

<label for="seopress_advanced_appearance_nofollow_col">
	<input id="seopress_advanced_appearance_nofollow_col"
		name="seopress_advanced_option_name[seopress_advanced_appearance_nofollow_col]" type="checkbox" <?php if ('1' == $check) { ?>
	checked="yes"
	<?php } ?>
	value="1"/>

	<?php _e('Display nofollow status', 'wp-seopress'); ?>
</label>

<?php if (isset($options['seopress_advanced_appearance_nofollow_col'])) {
		esc_attr($options['seopress_advanced_appearance_nofollow_col']);
	}
}

function seopress_advanced_appearance_inbound_col_callback() {
	$options = get_option('seopress_advanced_option_name');

	$check = isset($options['seopress_advanced_appearance_inbound_col']); ?>

<label for="seopress_advanced_appearance_inbound_col">
	<input id="seopress_advanced_appearance_inbound_col"
		name="seopress_advanced_option_name[seopress_advanced_appearance_inbound_col]" type="checkbox" <?php if ('1' == $check) { ?>
	checked="yes"
	<?php } ?>
	value="1"/>

	<?php _e('Display number of inbound links', 'wp-seopress'); ?>
</label>

<?php if (isset($options['seopress_advanced_appearance_inbound_col'])) {
		esc_attr($options['seopress_advanced_appearance_inbound_col']);
	}
}

function seopress_advanced_appearance_outbound_col_callback() {
	$options = get_option('seopress_advanced_option_name');

	$check = isset($options['seopress_advanced_appearance_outbound_col']); ?>

<label for="seopress_advanced_appearance_outbound_col">
	<input id="seopress_advanced_appearance_outbound_col"
		name="seopress_advanced_option_name[seopress_advanced_appearance_outbound_col]" type="checkbox" <?php if ('1' == $check) { ?>
	checked="yes"
	<?php } ?>
	value="1"/>

	<?php _e('Display number of outbound links', 'wp-seopress'); ?>
</label>

<?php if (isset($options['seopress_advanced_appearance_outbound_col'])) {
		esc_attr($options['seopress_advanced_appearance_outbound_col']);
	}
}

function seopress_advanced_appearance_score_col_callback() {
	$options = get_option('seopress_advanced_option_name');

	$check = isset($options['seopress_advanced_appearance_score_col']); ?>

<label for="seopress_advanced_appearance_score_col">
	<input id="seopress_advanced_appearance_score_col"
		name="seopress_advanced_option_name[seopress_advanced_appearance_score_col]" type="checkbox" <?php if ('1' == $check) { ?>
	checked="yes"
	<?php } ?>
	value="1"/>

	<?php _e('Display Content Analysis results column ("Good" or "Should be improved")', 'wp-seopress'); ?>
</label>

<?php if (isset($options['seopress_advanced_appearance_score_col'])) {
		esc_attr($options['seopress_advanced_appearance_score_col']);
	}
}

function seopress_advanced_appearance_ca_metaboxe_callback() {
	$options = get_option('seopress_advanced_option_name');

	$check = isset($options['seopress_advanced_appearance_ca_metaboxe']); ?>

<label for="seopress_advanced_appearance_ca_metaboxe">
	<input id="seopress_advanced_appearance_ca_metaboxe"
		name="seopress_advanced_option_name[seopress_advanced_appearance_ca_metaboxe]" type="checkbox" <?php if ('1' == $check) { ?>
	checked="yes"
	<?php } ?>
	value="1"/>

	<?php _e('Remove Content Analysis Metabox', 'wp-seopress'); ?>
</label>


<p class="description">
	<?php _e('By checking this option, we will no longer track the significant keywords.','wp-seopress'); ?>
</p>

<?php if (isset($options['seopress_advanced_appearance_ca_metaboxe'])) {
		esc_attr($options['seopress_advanced_appearance_ca_metaboxe']);
	}
}

function seopress_advanced_appearance_genesis_seo_metaboxe_callback() {
	$options = get_option('seopress_advanced_option_name');

	$check = isset($options['seopress_advanced_appearance_genesis_seo_metaboxe']); ?>

<label for="seopress_advanced_appearance_genesis_seo_metaboxe">
	<input id="seopress_advanced_appearance_genesis_seo_metaboxe"
		name="seopress_advanced_option_name[seopress_advanced_appearance_genesis_seo_metaboxe]" type="checkbox" <?php if ('1' == $check) { ?>
	checked="yes"
	<?php } ?>
	value="1"/>

	<?php _e('Remove Genesis SEO Metabox', 'wp-seopress'); ?>
</label>

<?php if (isset($options['seopress_advanced_appearance_genesis_seo_metaboxe'])) {
		esc_attr($options['seopress_advanced_appearance_genesis_seo_metaboxe']);
	}
}

function seopress_advanced_appearance_genesis_seo_menu_callback() {
	$options = get_option('seopress_advanced_option_name');

	$check = isset($options['seopress_advanced_appearance_genesis_seo_menu']); ?>

<label for="seopress_advanced_appearance_genesis_seo_menu">
	<input id="seopress_advanced_appearance_genesis_seo_menu"
		name="seopress_advanced_option_name[seopress_advanced_appearance_genesis_seo_menu]" type="checkbox" <?php if ('1' == $check) { ?>
	checked="yes"
	<?php } ?>
	value="1"/>

	<?php _e('Remove Genesis SEO link in WP Admin Menu', 'wp-seopress'); ?>
</label>

<?php if (isset($options['seopress_advanced_appearance_genesis_seo_menu'])) {
		esc_attr($options['seopress_advanced_appearance_genesis_seo_menu']);
	}
}

function seopress_advanced_security_metaboxe_role_callback() {
	$docs  = seopress_get_docs_links();

	$options = get_option('seopress_advanced_option_name');

	global $wp_roles;

	if ( ! isset($wp_roles)) {
		$wp_roles = new WP_Roles();
	} ?>


	<?php foreach ($wp_roles->get_names() as $key => $value) {
		$check = isset($options['seopress_advanced_security_metaboxe_role'][$key]); ?>

	<p>

		<label
			for="seopress_advanced_security_metaboxe_role_<?php echo $key; ?>">
			<input
			id="seopress_advanced_security_metaboxe_role_<?php echo $key; ?>"
			name="seopress_advanced_option_name[seopress_advanced_security_metaboxe_role][<?php echo $key; ?>]"
			type="checkbox" <?php if ('1' == $check) { ?>
				checked="yes"
				<?php } ?>
				value="1"/>
			<strong><?php echo $value; ?></strong> (<em><?php echo translate_user_role($value,  'default'); ?>)</em>
		</label>

	</p>

	<?php if (isset($options['seopress_advanced_security_metaboxe_role'][$key])) {
			esc_attr($options['seopress_advanced_security_metaboxe_role'][$key]);
		}
	} ?>

<?php echo seopress_tooltip_link($docs['security']['metaboxe_seo'], __('Hook to filter structured data types metabox call by post type - new window', 'wp-seopress')); ?>

<?php
}

function seopress_advanced_security_metaboxe_ca_role_callback() {
	$docs    = seopress_get_docs_links();
	$options = get_option('seopress_advanced_option_name');

	global $wp_roles;

	if ( ! isset($wp_roles)) {
		$wp_roles = new WP_Roles();
	} ?>

	<?php foreach ($wp_roles->get_names() as $key => $value) {
		$check = isset($options['seopress_advanced_security_metaboxe_ca_role'][$key]); ?>

	<p>
		<label
			for="seopress_advanced_security_metaboxe_ca_role_<?php echo $key; ?>">
			<input
				id="seopress_advanced_security_metaboxe_ca_role_<?php echo $key; ?>"
				name="seopress_advanced_option_name[seopress_advanced_security_metaboxe_ca_role][<?php echo $key; ?>]"
				type="checkbox" <?php if ('1' == $check) { ?>
			checked="yes"
			<?php } ?>
			value="1"/>

			<strong><?php echo $value; ?></strong> (<em><?php echo translate_user_role($value,  'default'); ?>)</em>
		</label>
	</p>

	<?php if (isset($options['seopress_advanced_security_metaboxe_ca_role'][$key])) {
			esc_attr($options['seopress_advanced_security_metaboxe_ca_role'][$key]);
		}
	} ?>

<?php echo seopress_tooltip_link($docs['security']['metaboxe_ca'], __('Hook to filter structured data types metabox call by post type - new window', 'wp-seopress')); ?>

<?php
}
