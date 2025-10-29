<?php
/**
 * Analytics callback.
 *
 * @package SEOPress
 * @subpackage Callbacks
 */

defined( 'ABSPATH' ) || exit( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

/**
 * Print the Google Analytics enable callback.
 *
 * @return void
 */
function seopress_google_analytics_enable_callback() {
	$options = get_option( 'seopress_google_analytics_option_name' );

	$check = isset( $options['seopress_google_analytics_enable'] ) ? $options['seopress_google_analytics_enable'] : null; ?>

<label for="seopress_google_analytics_enable">
	<input id="seopress_google_analytics_enable"
		name="seopress_google_analytics_option_name[seopress_google_analytics_enable]" type="checkbox" <?php if ( '1' === $check ) { ?>
	checked="yes"
	<?php } ?>
	value="1"/>

	<?php esc_attr_e( 'Enable Google Analytics tracking (Global Site Tag: gtag.js)', 'wp-seopress' ); ?>
</label>
	<?php
	if ( isset( $options['seopress_google_analytics_enable'] ) ) {
		esc_attr( $options['seopress_google_analytics_enable'] );
	}
}

/**
 * Print the Google Analytics GA4 callback.
 *
 * @return void
 */
function seopress_google_analytics_ga4_callback() {
	$options = get_option( 'seopress_google_analytics_option_name' );
	$check   = isset( $options['seopress_google_analytics_ga4'] ) ? $options['seopress_google_analytics_ga4'] : null;

	printf(
		'<input type="text" name="seopress_google_analytics_option_name[seopress_google_analytics_ga4]" placeholder="' . esc_html__( 'Enter your measurement ID (G-XXXXXXXXXX)', 'wp-seopress' ) . '" aria-label="' . esc_attr__( 'Enter your measurement ID', 'wp-seopress' ) . '" value="%s"/>',
		esc_html( $check )
	);
	?>

<p class="seopress-help description">
	<a href="https://support.google.com/analytics/answer/9539598?hl=en&ref_topic=9303319" target="_blank">
		<?php esc_attr_e( 'Find your measurement ID', 'wp-seopress' ); ?>
	</a>
	<span class="dashicons dashicons-external"></span>
</p>
	<?php
}

/**
 * Print the Google Analytics hook callback.
 *
 * @return void
 */
function seopress_google_analytics_hook_callback() {
	$options = get_option( 'seopress_google_analytics_option_name' );

	$selected = isset( $options['seopress_google_analytics_hook'] ) ? $options['seopress_google_analytics_hook'] : null;
	?>

<select id="seopress_google_analytics_hook"
	name="seopress_google_analytics_option_name[seopress_google_analytics_hook]">
	<option <?php if ( 'wp_body_open' === $selected ) { ?>
		selected="selected"
		<?php } ?>
		value="wp_body_open"><?php esc_attr_e( 'After the opening body tag (recommended)', 'wp-seopress' ); ?>
	</option>
	<option <?php if ( 'wp_footer' === $selected ) { ?>
		selected="selected"
		<?php } ?>
		value="wp_footer"><?php esc_attr_e( 'Footer', 'wp-seopress' ); ?>
	</option>
	<option <?php if ( 'wp_head' === $selected ) { ?>
		selected="selected"
		<?php } ?>
		value="wp_head"><?php esc_attr_e( 'Head (not recommended)', 'wp-seopress' ); ?>
	</option>
</select>

<p class="description">
	<?php echo wp_kses_post( __( 'Your theme must be compatible with <code>wp_body_open</code> hook introduced in WordPress 5.2 if "opening body tag" option selected.', 'wp-seopress' ) ); ?>
</p>

	<?php
	if ( isset( $options['seopress_google_analytics_hook'] ) ) {
		esc_attr( $options['seopress_google_analytics_hook'] );
	}
}

/**
 * Print the Google Analytics disable callback.
 *
 * @return void
 */
function seopress_google_analytics_disable_callback() {
	$docs = seopress_get_docs_links();

	$options = get_option( 'seopress_google_analytics_option_name' );

	$check = isset( $options['seopress_google_analytics_disable'] ) ? $options['seopress_google_analytics_disable'] : null;
	?>

<label for="seopress_google_analytics_disable">
	<input id="seopress_google_analytics_disable"
		name="seopress_google_analytics_option_name[seopress_google_analytics_disable]" type="checkbox" <?php if ( '1' === $check ) { ?>
	checked="yes"
	<?php } ?>
	value="1"/>
	<?php esc_attr_e( 'Request user consent for analytics tracking (required by GDPR)', 'wp-seopress' ); ?>
</label>

<div class="seopress-notice">
	<p><?php echo wp_kses_post( __( 'Users must click the <strong>Accept</strong> button to allow tracking.', 'wp-seopress' ) ); ?>
	</p>
</div>

<p class="description">
	<?php esc_attr_e( 'User roles excluded from tracking will not see the consent banner.', 'wp-seopress' ); ?>
</p>
<p class="description">
	<?php esc_attr_e( 'If you use a caching plugin, you have to exclude this JS file in your settings:', 'wp-seopress' ); ?>
</p>
<p class="description">
	<?php echo wp_kses_post( __( '<code>/wp-content/plugins/wp-seopress/assets/js/seopress-cookies-ajax.js</code> and this cookie <code>seopress-user-consent-accept</code>', 'wp-seopress' ) ); ?>
	<?php echo seopress_tooltip_link( esc_url( $docs['analytics']['custom_tracking'] ), esc_attr__( 'Hook to add custom tracking code with user consent - new window', 'wp-seopress' ) ); ?>
</p>

	<?php
	if ( isset( $options['seopress_google_analytics_disable'] ) ) {
		esc_attr( $options['seopress_google_analytics_disable'] );
	}
}

/**
 * Print the Google Analytics half disable callback.
 *
 * @return void
 */
function seopress_google_analytics_half_disable_callback() {
	$options = get_option( 'seopress_google_analytics_option_name' );

	$check = isset( $options['seopress_google_analytics_half_disable'] ) ? $options['seopress_google_analytics_half_disable'] : null;
	?>

<label for="seopress_google_analytics_half_disable">
	<input id="seopress_google_analytics_half_disable"
		name="seopress_google_analytics_option_name[seopress_google_analytics_half_disable]" type="checkbox" <?php if ( '1' === $check ) { ?>
	checked="yes"
	<?php } ?>
	value="1"/>
	<?php esc_attr_e( 'Display and automatically accept user consent on page load (not fully GDPR compliant)', 'wp-seopress' ); ?>
</label>

<p class="description">
	<?php esc_attr_e( 'The previous option must be enabled to use this.', 'wp-seopress' ); ?>
</p>

	<?php
	if ( isset( $options['seopress_google_analytics_half_disable'] ) ) {
		esc_attr( $options['seopress_google_analytics_half_disable'] );
	}
}

/**
 * Print the Google Analytics opt out edit choice callback.
 *
 * @return void
 */
function seopress_google_analytics_opt_out_edit_choice_callback() {
	$options = get_option( 'seopress_google_analytics_option_name' );

	$check = isset( $options['seopress_google_analytics_opt_out_edit_choice'] ) ? $options['seopress_google_analytics_opt_out_edit_choice'] : null;
	?>

<label for="seopress_google_analytics_opt_out_edit_choice">
	<input id="seopress_google_analytics_opt_out_edit_choice"
		name="seopress_google_analytics_option_name[seopress_google_analytics_opt_out_edit_choice]" type="checkbox"
		<?php if ( '1' === $check ) { ?>
	checked="yes"
	<?php } ?>
	value="1"/>
	<?php esc_attr_e( 'Allow users to change their cookie preferences', 'wp-seopress' ); ?>
</label>

<p class="description">
	<?php esc_attr_e( 'Display a button that allows users to reopen the cookie consent banner and change their choice after accepting or declining.', 'wp-seopress' ); ?>
</p>

	<?php
	if ( isset( $options['seopress_google_analytics_opt_out_edit_choice'] ) ) {
		esc_attr( $options['seopress_google_analytics_opt_out_edit_choice'] );
	}
}

/**
 * Print the Google Analytics opt out msg callback.
 *
 * @return void
 */
function seopress_google_analytics_opt_out_msg_callback() {
	$docs    = seopress_get_docs_links();
	$options = get_option( 'seopress_google_analytics_option_name' );
	$check   = isset( $options['seopress_google_analytics_opt_out_msg'] ) ? $options['seopress_google_analytics_opt_out_msg'] : null;

	printf(
		'<textarea id="seopress_google_analytics_opt_out_msg" name="seopress_google_analytics_option_name[seopress_google_analytics_opt_out_msg]" rows="4" placeholder="' . esc_html__( 'Enter your message (HTML allowed)', 'wp-seopress' ) . '" aria-label="' . esc_attr__( 'Cookie consent banner message', 'wp-seopress' ) . '">%s</textarea>',
		esc_html( $check )
	);
	?>

	<?php echo seopress_tooltip_link( $docs['analytics']['consent_msg'], esc_attr__( 'Hook to filter user consent message - new window', 'wp-seopress' ) ); ?>

<p class="description">
	<?php esc_attr_e( 'The message displayed in the cookie consent banner.', 'wp-seopress' ); ?>
</p>
<p class="description">
	<?php esc_attr_e( 'HTML tags allowed: strong, em, br, a href / target', 'wp-seopress' ); ?>
</p>
<p class="description">
	<?php esc_attr_e( 'Shortcode allowed to get the privacy page set in WordPress settings: [seopress_privacy_page]', 'wp-seopress' ); ?>
</p>
	<?php
}

/**
 * Print the Google Analytics opt out msg ok callback.
 *
 * @return void
 */
function seopress_google_analytics_opt_out_msg_ok_callback() {
	$options = get_option( 'seopress_google_analytics_option_name' );
	$check   = isset( $options['seopress_google_analytics_opt_out_msg_ok'] ) ? $options['seopress_google_analytics_opt_out_msg_ok'] : null;

	printf(
		'<input type="text" name="seopress_google_analytics_option_name[seopress_google_analytics_opt_out_msg_ok]" placeholder="' . esc_html__( 'Accept', 'wp-seopress' ) . '" aria-label="' . esc_attr__( 'Accept button text', 'wp-seopress' ) . '" value="%s"/>',
		esc_html( $check )
	);
	?>

<p class="description">
	<?php esc_attr_e( 'Text displayed on the button that accepts cookie tracking.', 'wp-seopress' ); ?>
</p>

	<?php
}

/**
 * Print the Google Analytics opt out msg close callback.
 *
 * @return void
 */
function seopress_google_analytics_opt_out_msg_close_callback() {
	$options = get_option( 'seopress_google_analytics_option_name' );
	$check   = isset( $options['seopress_google_analytics_opt_out_msg_close'] ) ? $options['seopress_google_analytics_opt_out_msg_close'] : null;

	printf(
		'<input type="text" name="seopress_google_analytics_option_name[seopress_google_analytics_opt_out_msg_close]" placeholder="' . esc_html__( 'Decline', 'wp-seopress' ) . '" aria-label="' . esc_attr__( 'Decline button text', 'wp-seopress' ) . '" value="%s"/>',
		esc_html( $check )
	);
	?>

<p class="description">
	<?php esc_attr_e( 'Text displayed on the button that declines cookie tracking.', 'wp-seopress' ); ?>
</p>

	<?php
}

/**
 * Print the Google Analytics opt out msg edit callback.
 *
 * @return void
 */
function seopress_google_analytics_opt_out_msg_edit_callback() {
	$options = get_option( 'seopress_google_analytics_option_name' );
	$check   = isset( $options['seopress_google_analytics_opt_out_msg_edit'] ) ? $options['seopress_google_analytics_opt_out_msg_edit'] : null;

	printf(
		'<input type="text" name="seopress_google_analytics_option_name[seopress_google_analytics_opt_out_msg_edit]" placeholder="' . esc_html__( 'Cookie preferences', 'wp-seopress' ) . '" aria-label="' . esc_attr__( 'Cookie preferences button text', 'wp-seopress' ) . '" value="%s"/>',
		esc_html( $check )
	);
	?>

<p class="description">
	<?php esc_attr_e( 'Text displayed on the button that allows users to reopen the cookie consent banner.', 'wp-seopress' ); ?>
</p>

	<?php
}

/**
 * Print the Google Analytics cb exp date callback.
 *
 * @return void
 */
function seopress_google_analytics_cb_exp_date_callback() {
	$options = get_option( 'seopress_google_analytics_option_name' );

	$check = isset( $options['seopress_google_analytics_cb_exp_date'] ) ? $options['seopress_google_analytics_cb_exp_date'] : null;
	?>

<input type="number" min="1" name="seopress_google_analytics_option_name[seopress_google_analytics_cb_exp_date]" <?php if ( '1' === $check ) { ?>
value="<?php echo esc_attr( $options['seopress_google_analytics_cb_exp_date'] ); ?>"
<?php } ?>
value="30"/>

	<?php
	if ( isset( $options['seopress_google_analytics_cb_exp_date'] ) ) {
		esc_html( $options['seopress_google_analytics_cb_exp_date'] );
	}
	?>

<p class="description">
	<?php esc_attr_e( 'Number of days before the user consent cookie expires. Default: 30 days.', 'wp-seopress' ); ?>
</p>

	<?php
}

/**
 * Print the Google Analytics cb pos callback.
 *
 * @return void
 */
function seopress_google_analytics_cb_pos_callback() {
	$options = get_option( 'seopress_google_analytics_option_name' );

	$selected = isset( $options['seopress_google_analytics_cb_pos'] ) ? $options['seopress_google_analytics_cb_pos'] : null;
	?>

<select id="seopress_google_analytics_cb_pos"
	name="seopress_google_analytics_option_name[seopress_google_analytics_cb_pos]">
	<option <?php if ( 'bottom' === $selected ) { ?>
		selected="selected"
		<?php } ?>
		value="bottom"><?php esc_attr_e( 'Bottom (default)', 'wp-seopress' ); ?>
	</option>
	<option <?php if ( 'center' === $selected ) { ?>
		selected="selected"
		<?php } ?>
		value="center"><?php esc_attr_e( 'Middle', 'wp-seopress' ); ?>
	</option>
	<option <?php if ( 'top' === $selected ) { ?>
		selected="selected"
		<?php } ?>
		value="top"><?php esc_attr_e( 'Top', 'wp-seopress' ); ?>
	</option>
</select>

<p class="description">
	<?php esc_attr_e( 'Cookie bar vertical alignment.', 'wp-seopress' ); ?>
</p>

	<?php
	if ( isset( $options['seopress_google_analytics_cb_pos'] ) ) {
		esc_attr( $options['seopress_google_analytics_cb_pos'] );
	}
}

/**
 * Print the Google Analytics cb txt align callback.
 *
 * @return void
 */
function seopress_google_analytics_cb_txt_align_callback() {
	$options = get_option( 'seopress_google_analytics_option_name' );

	$selected = isset( $options['seopress_google_analytics_cb_txt_align'] ) ? $options['seopress_google_analytics_cb_txt_align'] : 'center';
	?>

<select id="seopress_google_analytics_cb_txt_align"
	name="seopress_google_analytics_option_name[seopress_google_analytics_cb_txt_align]">
	<option <?php if ( 'left' === $selected ) { ?>
		selected="selected"
		<?php } ?>
		value="left"><?php esc_attr_e( 'Left', 'wp-seopress' ); ?>
	</option>
	<option <?php if ( 'center' === $selected ) { ?>
		selected="selected"
		<?php } ?>
		value="center"><?php esc_attr_e( 'Center (default)', 'wp-seopress' ); ?>
	</option>
	<option <?php if ( 'right' === $selected ) { ?>
		selected="selected"
		<?php } ?>
		value="right"><?php esc_attr_e( 'Right', 'wp-seopress' ); ?>
	</option>
</select>

	<?php
	if ( isset( $options['seopress_google_analytics_cb_txt_align'] ) ) {
		esc_attr( $options['seopress_google_analytics_cb_txt_align'] );
	}
}

/**
 * Print the Google Analytics cb width callback.
 *
 * @return void
 */
function seopress_google_analytics_cb_width_callback() {
	$options = get_option( 'seopress_google_analytics_option_name' );
	$check   = isset( $options['seopress_google_analytics_cb_width'] ) ? $options['seopress_google_analytics_cb_width'] : null;

	printf(
		'<input type="text" name="seopress_google_analytics_option_name[seopress_google_analytics_cb_width]" aria-label="' . esc_attr__( 'Change the cookie bar width', 'wp-seopress' ) . '" value="%s"/>',
		esc_html( $check )
	);
	?>

<p class="description">
	<?php esc_attr_e( 'Default unit is Pixels. Add % just after your custom value to use percentages (e.g. 80%). Default width is 100%.', 'wp-seopress' ); ?>
</p>
	<?php
}

/**
 * Print the Google Analytics cb align callback.
 *
 * @return void
 */
function seopress_google_analytics_cb_align_callback() {
	$options = get_option( 'seopress_google_analytics_option_name' );

	$selected = isset( $options['seopress_google_analytics_cb_align'] ) ? $options['seopress_google_analytics_cb_align'] : 'center';
	?>

<select id="seopress_google_analytics_cb_align"
	name="seopress_google_analytics_option_name[seopress_google_analytics_cb_align]">
	<option <?php if ( 'left' === $selected ) { ?>
		selected="selected"
		<?php } ?>
		value="left"><?php esc_attr_e( 'Left', 'wp-seopress' ); ?>
	</option>
	<option <?php if ( 'center' === $selected ) { ?>
		selected="selected"
		<?php } ?>
		value="center"><?php esc_attr_e( 'Center (default)', 'wp-seopress' ); ?>
	</option>
	<option <?php if ( 'right' === $selected ) { ?>
		selected="selected"
		<?php } ?>
		value="right"><?php esc_attr_e( 'Right', 'wp-seopress' ); ?>
	</option>
</select>

<p class="description">
	<?php esc_attr_e( 'Cookie bar horizontal alignment (only applies when width is not 100%):', 'wp-seopress' ); ?>
</p>

	<?php
	if ( isset( $options['seopress_google_analytics_cb_align'] ) ) {
		esc_attr( $options['seopress_google_analytics_cb_align'] );
	}
}

/**
 * Print the Google Analytics cb backdrop callback.
 *
 * @return void
 */
function seopress_google_analytics_cb_backdrop_callback() {
	$options = get_option( 'seopress_google_analytics_option_name' );

	$check = isset( $options['seopress_google_analytics_cb_backdrop'] ) ? $options['seopress_google_analytics_cb_backdrop'] : null;
	?>

<hr>

<h2>
	<?php esc_attr_e( 'Backdrop', 'wp-seopress' ); ?>
</h2>

<p>
	<?php echo wp_kses_post( __( 'Customize the cookie bar <strong>backdrop</strong>.', 'wp-seopress' ) ); ?>
</p>

<label for="seopress_google_analytics_cb_backdrop">
	<input id="seopress_google_analytics_cb_backdrop"
		name="seopress_google_analytics_option_name[seopress_google_analytics_cb_backdrop]" type="checkbox" <?php if ( '1' === $check ) { ?>
	checked="yes"
	<?php } ?>
	value="1"/>
	<?php esc_attr_e( 'Display a backdrop with the cookie bar', 'wp-seopress' ); ?>
</label>

	<?php
	if ( isset( $options['seopress_google_analytics_cb_backdrop'] ) ) {
		esc_attr( $options['seopress_google_analytics_cb_backdrop'] );
	}
}

/**
 * Print the Google Analytics cb backdrop bg callback.
 *
 * @return void
 */
function seopress_google_analytics_cb_backdrop_bg_callback() {
	$options = get_option( 'seopress_google_analytics_option_name' );
	$check   = isset( $options['seopress_google_analytics_cb_backdrop_bg'] ) ? $options['seopress_google_analytics_cb_backdrop_bg'] : null;
	?>

<p class="description">
	<?php esc_attr_e( 'Background color: ', 'wp-seopress' ); ?>
</p>

	<?php
	printf(
		'<input type="text" data-default-color="rgba(0,0,0,0.5)" data-alpha="true" name="seopress_google_analytics_option_name[seopress_google_analytics_cb_backdrop_bg]" placeholder="rgba(0,0,0,0.5)" aria-label="' . esc_attr__( 'Change the background color of the backdrop', 'wp-seopress' ) . '" value="%s" class="color-picker"/>',
		esc_html( $check )
	);
}

/**
 * Print the Google Analytics cb bg callback.
 *
 * @return void
 */
function seopress_google_analytics_cb_bg_callback() {
	$options = get_option( 'seopress_google_analytics_option_name' );
	$check   = isset( $options['seopress_google_analytics_cb_bg'] ) ? $options['seopress_google_analytics_cb_bg'] : null;
	?>
<hr>

<h2><?php esc_attr_e( 'Main settings', 'wp-seopress' ); ?>
</h2>

<p>
	<?php echo wp_kses_post( __( 'Customize the general settings of the <strong>cookie bar</strong>.', 'wp-seopress' ) ); ?>
</p>

<p class="description">
	<?php esc_attr_e( 'Background color: ', 'wp-seopress' ); ?>
</p>

	<?php
	printf(
		'<input type="text" data-alpha="true" data-default-color="#FFFFFF" name="seopress_google_analytics_option_name[seopress_google_analytics_cb_bg]" placeholder="#FFFFFF" aria-label="' . esc_attr__( 'Change the color of the cookie bar background', 'wp-seopress' ) . '" value="%s" class="color-picker"/>',
		esc_html( $check )
	);
}

/**
 * Print the Google Analytics cb txt col callback.
 *
 * @return void
 */
function seopress_google_analytics_cb_txt_col_callback() {
	$options = get_option( 'seopress_google_analytics_option_name' );
	$check   = isset( $options['seopress_google_analytics_cb_txt_col'] ) ? $options['seopress_google_analytics_cb_txt_col'] : null;
	?>

<p class="description">
	<?php esc_attr_e( 'Text color: ', 'wp-seopress' ); ?>
</p>

	<?php
	printf(
		'<input type="text" data-default-color="#2c3e50" name="seopress_google_analytics_option_name[seopress_google_analytics_cb_txt_col]" placeholder="#2c3e50" aria-label="' . esc_attr__( 'Change the color of the cookie bar text', 'wp-seopress' ) . '" value="%s" class="color-picker"/>',
		esc_html( $check )
	);
}

/**
 * Print the Google Analytics cb lk col callback.
 *
 * @return void
 */
function seopress_google_analytics_cb_lk_col_callback() {
	$options = get_option( 'seopress_google_analytics_option_name' );
	$check   = isset( $options['seopress_google_analytics_cb_lk_col'] ) ? $options['seopress_google_analytics_cb_lk_col'] : null;
	?>

<p class="description">
	<?php esc_attr_e( 'Link color: ', 'wp-seopress' ); ?>
</p>

	<?php
	printf(
		'<input type="text" data-default-color="#1a1a1a" name="seopress_google_analytics_option_name[seopress_google_analytics_cb_lk_col]" placeholder="#1a1a1a" aria-label="' . esc_attr__( 'Change the color of the cookie bar link', 'wp-seopress' ) . '" value="%s" class="color-picker"/>',
		esc_html( $check )
	);
}

/**
 * Print the Google Analytics cb btn bg callback.
 *
 * @return void
 */
function seopress_google_analytics_cb_btn_bg_callback() {
	$options = get_option( 'seopress_google_analytics_option_name' );
	$check   = isset( $options['seopress_google_analytics_cb_btn_bg'] ) ? $options['seopress_google_analytics_cb_btn_bg'] : null;
	?>

<hr>

<h2>
	<?php esc_attr_e( 'Primary button', 'wp-seopress' ); ?>
</h2>

<p>
	<?php echo wp_kses_post( __( 'Customize the <strong>Accept button</strong>.', 'wp-seopress' ) ); ?>
</p>

<p class="description">
	<?php esc_attr_e( 'Background color: ', 'wp-seopress' ); ?>
</p>

	<?php
	printf(
		'<input type="text" data-alpha="true" data-default-color="#1a1a1a" name="seopress_google_analytics_option_name[seopress_google_analytics_cb_btn_bg]" placeholder="#1a1a1a" aria-label="' . esc_attr__( 'Change the color of the cookie bar button background', 'wp-seopress' ) . '" value="%s" class="color-picker"/>',
		esc_html( $check )
	);
}

/**
 * Print the Google Analytics cb btn bg hov callback.
 *
 * @return void
 */
function seopress_google_analytics_cb_btn_bg_hov_callback() {
	$options = get_option( 'seopress_google_analytics_option_name' );
	$check   = isset( $options['seopress_google_analytics_cb_btn_bg_hov'] ) ? $options['seopress_google_analytics_cb_btn_bg_hov'] : null;
	?>

<p class="description">
	<?php esc_attr_e( 'Background color on hover: ', 'wp-seopress' ); ?>
</p>

	<?php
	printf(
		'<input type="text" data-alpha="true" data-default-color="#000000" name="seopress_google_analytics_option_name[seopress_google_analytics_cb_btn_bg_hov]" placeholder="#000000" aria-label="' . esc_attr__( 'Change the color of the cookie bar button hover background', 'wp-seopress' ) . '" value="%s" class="color-picker"/>',
		esc_html( $check )
	);
}

/**
 * Print the Google Analytics cb btn col callback.
 *
 * @return void
 */
function seopress_google_analytics_cb_btn_col_callback() {
	$options = get_option( 'seopress_google_analytics_option_name' );
	$check   = isset( $options['seopress_google_analytics_cb_btn_col'] ) ? $options['seopress_google_analytics_cb_btn_col'] : null;
	?>

<p class="description">
	<?php esc_attr_e( 'Text color: ', 'wp-seopress' ); ?>
</p>

	<?php
	printf(
		'<input type="text" data-default-color="#ffffff" name="seopress_google_analytics_option_name[seopress_google_analytics_cb_btn_col]" placeholder="#ffffff" aria-label="' . esc_attr__( 'Change the color of the cookie bar button', 'wp-seopress' ) . '" value="%s" class="color-picker"/>',
		esc_html( $check )
	);
}

/**
 * Print the Google Analytics cb btn col hov callback.
 *
 * @return void
 */
function seopress_google_analytics_cb_btn_col_hov_callback() {
	$options = get_option( 'seopress_google_analytics_option_name' );
	$check   = isset( $options['seopress_google_analytics_cb_btn_col_hov'] ) ? $options['seopress_google_analytics_cb_btn_col_hov'] : null;
	?>

<p class="description">
	<?php esc_attr_e( 'Text color on hover: ', 'wp-seopress' ); ?>
</p>

	<?php
	printf(
		'<input type="text" data-default-color="#ffffff" name="seopress_google_analytics_option_name[seopress_google_analytics_cb_btn_col_hov]" placeholder="#ffffff" aria-label="' . esc_attr__( 'Change the color of the cookie bar button hover', 'wp-seopress' ) . '" value="%s" class="color-picker"/>',
		esc_html( $check )
	);
}

/**
 * Print the Google Analytics cb btn sec bg callback.
 *
 * @return void
 */
function seopress_google_analytics_cb_btn_sec_bg_callback() {
	$options = get_option( 'seopress_google_analytics_option_name' );
	$check   = isset( $options['seopress_google_analytics_cb_btn_sec_bg'] ) ? $options['seopress_google_analytics_cb_btn_sec_bg'] : null;
	?>

<hr>

<h2>
	<?php esc_attr_e( 'Secondary button', 'wp-seopress' ); ?>
</h2>

<p>
	<?php echo wp_kses_post( __( 'Customize the <strong>Close button</strong>.', 'wp-seopress' ) ); ?>
</p>

<p class="description">
	<?php esc_attr_e( 'Background color: ', 'wp-seopress' ); ?>
</p>

	<?php
	printf(
		'<input type="text" data-alpha="true" data-default-color="#ffffff" name="seopress_google_analytics_option_name[seopress_google_analytics_cb_btn_sec_bg]" placeholder="#ffffff" aria-label="' . esc_attr__( 'Change the color of the cookie bar secondary button background', 'wp-seopress' ) . '" value="%s" class="color-picker"/>',
		esc_html( $check )
	);
}

/**
 * Print the Google Analytics cb btn sec bg hov callback.
 *
 * @return void
 */
function seopress_google_analytics_cb_btn_sec_bg_hov_callback() {
	$options = get_option( 'seopress_google_analytics_option_name' );
	$check   = isset( $options['seopress_google_analytics_cb_btn_sec_bg_hov'] ) ? $options['seopress_google_analytics_cb_btn_sec_bg_hov'] : null;
	?>

<p class="description">
	<?php esc_attr_e( 'Background color on hover: ', 'wp-seopress' ); ?>
</p>

	<?php
	printf(
		'<input type="text" data-alpha="true" data-default-color="#f9fafb" name="seopress_google_analytics_option_name[seopress_google_analytics_cb_btn_sec_bg_hov]" placeholder="#f9fafb" aria-label="' . esc_attr__( 'Change the color of the cookie bar secondary button', 'wp-seopress' ) . '" value="%s" class="color-picker"/>',
		esc_html( $check )
	);
}

/**
 * Print the Google Analytics cb btn sec col callback.
 *
 * @return void
 */
function seopress_google_analytics_cb_btn_sec_col_callback() {
	$options = get_option( 'seopress_google_analytics_option_name' );
	$check   = isset( $options['seopress_google_analytics_cb_btn_sec_col'] ) ? $options['seopress_google_analytics_cb_btn_sec_col'] : null;
	?>

<p class="description">
	<?php esc_attr_e( 'Text color: ', 'wp-seopress' ); ?>
</p>

	<?php
	printf(
		'<input type="text" data-default-color="#374151" name="seopress_google_analytics_option_name[seopress_google_analytics_cb_btn_sec_col]" placeholder="#374151" aria-label="' . esc_attr__( 'Change the color of the cookie bar secondary button hover background', 'wp-seopress' ) . '" value="%s" class="color-picker"/>',
		esc_html( $check )
	);
}

/**
 * Print the Google Analytics cb btn sec col hov callback.
 *
 * @return void
 */
function seopress_google_analytics_cb_btn_sec_col_hov_callback() {
	$options = get_option( 'seopress_google_analytics_option_name' );
	$check   = isset( $options['seopress_google_analytics_cb_btn_sec_col_hov'] ) ? $options['seopress_google_analytics_cb_btn_sec_col_hov'] : null;
	?>

<p class="description">
	<?php esc_attr_e( 'Text color on hover: ', 'wp-seopress' ); ?>
</p>

	<?php
	printf(
		'<input type="text" data-default-color="#1f2937" name="seopress_google_analytics_option_name[seopress_google_analytics_cb_btn_sec_col_hov]" placeholder="#1f2937" aria-label="' . esc_attr__( 'Change the color of the cookie bar secondary button hover', 'wp-seopress' ) . '" value="%s" class="color-picker"/>',
		esc_html( $check )
	);
}

/**
 * Print the Google Analytics roles callback.
 *
 * @return void
 */
function seopress_google_analytics_roles_callback() {
	$options = get_option( 'seopress_google_analytics_option_name' );

	global $wp_roles;

	if ( ! isset( $wp_roles ) ) {
		$wp_roles = new WP_Roles(); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
	}

	foreach ( $wp_roles->get_names() as $key => $value ) {
		$check = isset( $options['seopress_google_analytics_roles'][ $key ] ) ? $options['seopress_google_analytics_roles'][ $key ] : null;
		?>

<p>
	<label for="seopress_google_analytics_roles_<?php echo esc_attr( $key ); ?>">
		<input
			id="seopress_google_analytics_roles_<?php echo esc_attr( $key ); ?>"
			name="seopress_google_analytics_option_name[seopress_google_analytics_roles][<?php echo esc_attr( $key ); ?>]"
			type="checkbox" <?php if ( '1' === $check ) { ?>
		checked="yes"
		<?php } ?>
		value="1"/>
		<strong><?php echo esc_html( $value ); ?></strong>(<em><?php echo esc_html( translate_user_role( $value, 'default' ) ); ?></em>)
	</label>
</p>

		<?php
		if ( isset( $options['seopress_google_analytics_roles'][ $key ] ) ) {
			esc_attr( $options['seopress_google_analytics_roles'][ $key ] );
		}
	}
}

/**
 * Print the Google Analytics ads callback.
 */
function seopress_google_analytics_ads_callback() {
	$docs    = seopress_get_docs_links();
	$options = get_option( 'seopress_google_analytics_option_name' );
	$check   = isset( $options['seopress_google_analytics_ads'] ) ? $options['seopress_google_analytics_ads'] : null;
	?>

	<?php
	printf(
		'<input type="text" name="seopress_google_analytics_option_name[seopress_google_analytics_ads]" placeholder="' . esc_html__( 'Enter your Google Ads conversion ID (e.g. AW-123456789)', 'wp-seopress' ) . '" value="%s" aria-label="' . esc_attr__( 'AW-XXXXXXXXX', 'wp-seopress' ) . '"/>',
		esc_html( $check )
	);
	?>
<p class="description">
	<a class="seopress-help" href="<?php echo esc_url( $docs['analytics']['gads'] ); ?>" target="_blank">
		<?php esc_attr_e( 'Learn how to find your Google Ads Conversion ID', 'wp-seopress' ); ?>
	</a>
	<span class="seopress-help dashicons dashicons-external"></span>
</p>

	<?php
}

/**
 * Print the Google Analytics other tracking callback.
 */
function seopress_google_analytics_other_tracking_callback() {
	if ( current_user_can( 'unfiltered_html' ) ) {
		$options = get_option( 'seopress_google_analytics_option_name' );
		$check   = isset( $options['seopress_google_analytics_other_tracking'] ) ? $options['seopress_google_analytics_other_tracking'] : '';

		printf( '<textarea id="seopress_google_analytics_other_tracking" name="seopress_google_analytics_option_name[seopress_google_analytics_other_tracking]" rows="16" placeholder="' . esc_html__( 'Paste your tracking code here like Google Tag Manager (head). Do NOT paste GA4 or Matomo codes here. They are automatically added to your source code.', 'wp-seopress' ) . '" aria-label="' . esc_attr__( 'Additional tracking code field', 'wp-seopress' ) . '">%s</textarea>', esc_textarea( $check ) );
		?>

		<p class="description">
			<?php esc_attr_e( 'This code will be added in the head section of your page.', 'wp-seopress' ); ?>
		</p>
	<?php } else { ?>
		<input type="hidden" name="seopress_google_analytics_option_name[seopress_google_analytics_other_tracking]" value="none" />

		<div class="seopress-notice">
			<p>
				<?php /* translators: %$ name of the user capability "unfiltered_html" */ echo wp_kses_post( sprintf( __( 'Only users with %s capability can edit this field.', 'wp-seopress' ), '<code>unfiltered_html</code>' ) ); ?>
			</p>
		</div>
		<?php
	}
}

/**
 * Print the Google Analytics other tracking body callback.
 */
function seopress_google_analytics_other_tracking_body_callback() {
	$docs = seopress_get_docs_links();
	if ( current_user_can( 'unfiltered_html' ) ) {
		$options = get_option( 'seopress_google_analytics_option_name' );
		$check   = isset( $options['seopress_google_analytics_other_tracking_body'] ) ? $options['seopress_google_analytics_other_tracking_body'] : '';

		printf(
			'<textarea id="seopress_google_analytics_other_tracking_body" name="seopress_google_analytics_option_name[seopress_google_analytics_other_tracking_body]" rows="16" placeholder="' . esc_html__( 'Paste your tracking code here like Google Tag Manager (body)', 'wp-seopress' ) . '" aria-label="' . esc_attr__( 'Additional tracking code field added to body', 'wp-seopress' ) . '">%s</textarea>',
			esc_textarea( $check )
		);
		?>
		<p class="description"><?php esc_attr_e( 'This code will be added just after the opening body tag of your page.', 'wp-seopress' ); ?></p>

		<p class="description"><?php echo wp_kses_post( __( 'You don‘t see your code? Make sure to call <code>wp_body_open();</code> just after the opening body tag in your theme.', 'wp-seopress' ) ); ?></p>
	<?php } else { ?>
		<input type="hidden" name="seopress_google_analytics_option_name[seopress_google_analytics_other_tracking_body]" value="none" />

		<div class="seopress-notice">
			<p>
				<?php /* translators: %$ name of the user capability "unfiltered_html" */ echo wp_kses_post( sprintf( __( 'Only users with %s capability can edit this field.', 'wp-seopress' ), '<code>unfiltered_html</code>' ) ); ?>
			</p>
		</div>
	<?php } ?>

<p class="description">
	<a class="seopress-help"
		href="<?php echo esc_url( $docs['analytics']['gtm'] ); ?>"
		target="_blank">
		<?php esc_attr_e( 'Learn how to integrate Google Tag Manager', 'wp-seopress' ); ?>
	</a>
	<span class="seopress-help dashicons dashicons-external"></span>
</p>

	<?php
}

/**
 * Print the Google Analytics other tracking footer callback.
 */
function seopress_google_analytics_other_tracking_footer_callback() {
	if ( current_user_can( 'unfiltered_html' ) ) {
		$options = get_option( 'seopress_google_analytics_option_name' );
		$check   = isset( $options['seopress_google_analytics_other_tracking_footer'] ) ? $options['seopress_google_analytics_other_tracking_footer'] : '';

		printf(
			'<textarea id="seopress_google_analytics_other_tracking_footer" name="seopress_google_analytics_option_name[seopress_google_analytics_other_tracking_footer]" rows="16" placeholder="' . esc_html__( 'Paste your tracking code here (footer)', 'wp-seopress' ) . '" aria-label="' . esc_attr__( 'Additional tracking code field added to footer', 'wp-seopress' ) . '">%s</textarea>',
			esc_textarea( $check )
		);
		?>

		<p class="description">
			<?php esc_attr_e( 'This code will be added just before the closing body tag of your page.', 'wp-seopress' ); ?>
		</p>
	<?php } else { ?>
		<input type="hidden" name="seopress_google_analytics_option_name[seopress_google_analytics_other_tracking_footer]" value="none" />

		<div class="seopress-notice">
			<p>
				<?php /* translators: %$ name of the user capability "unfiltered_html" */ echo wp_kses_post( sprintf( __( 'Only users with %s capability can edit this field.', 'wp-seopress' ), '<code>unfiltered_html</code>' ) ); ?>
			</p>
		</div>
	<?php } ?>
	<?php
}

/**
 * Print the Google Analytics link tracking enable callback.
 */
function seopress_google_analytics_link_tracking_enable_callback() {
	$options = get_option( 'seopress_google_analytics_option_name' );

	$check = isset( $options['seopress_google_analytics_link_tracking_enable'] ) ? $options['seopress_google_analytics_link_tracking_enable'] : null;
	?>

<label for="seopress_google_analytics_link_tracking_enable">
	<input id="seopress_google_analytics_link_tracking_enable"
		name="seopress_google_analytics_option_name[seopress_google_analytics_link_tracking_enable]" type="checkbox"
		<?php if ( '1' === $check ) { ?>
	checked="yes"
	<?php } ?>
	value="1"/>
	<?php esc_attr_e( 'Enable external links tracking', 'wp-seopress' ); ?>
</label>

	<?php
	if ( isset( $options['seopress_google_analytics_link_tracking_enable'] ) ) {
		esc_attr( $options['seopress_google_analytics_link_tracking_enable'] );
	}
}

/**
 * Print the Google Analytics download tracking enable callback.
 */
function seopress_google_analytics_download_tracking_enable_callback() {
	$options = get_option( 'seopress_google_analytics_option_name' );

	$check = isset( $options['seopress_google_analytics_download_tracking_enable'] ) ? $options['seopress_google_analytics_download_tracking_enable'] : null;
	?>

<label for="seopress_google_analytics_download_tracking_enable">
	<input id="seopress_google_analytics_download_tracking_enable"
		name="seopress_google_analytics_option_name[seopress_google_analytics_download_tracking_enable]" type="checkbox"
		<?php if ( '1' === $check ) { ?>
	checked="yes"
	<?php } ?>
	value="1"/>
	<?php esc_attr_e( 'Enable download tracking', 'wp-seopress' ); ?>
</label>

	<?php
	if ( isset( $options['seopress_google_analytics_download_tracking_enable'] ) ) {
		esc_attr( $options['seopress_google_analytics_download_tracking_enable'] );
	}
}

/**
 * Print the Google Analytics download tracking callback.
 */
function seopress_google_analytics_download_tracking_callback() {
	$options = get_option( 'seopress_google_analytics_option_name' );
	$check   = isset( $options['seopress_google_analytics_download_tracking'] ) ? $options['seopress_google_analytics_download_tracking'] : null;

	printf(
		'<input type="text" name="seopress_google_analytics_option_name[seopress_google_analytics_download_tracking]" placeholder="' . esc_html__( 'pdf|docx|pptx|zip', 'wp-seopress' ) . '" aria-label="' . esc_attr__( 'Track downloads\' clicks', 'wp-seopress' ) . '" value="%s"/>',
		esc_html( $check )
	);
	?>
<p class="description">
	<?php esc_attr_e( 'Separate each file type extensions with a pipe "|"', 'wp-seopress' ); ?>
</p>

	<?php
}

/**
 * Print the Google Analytics affiliate tracking enable callback.
 */
function seopress_google_analytics_affiliate_tracking_enable_callback() {
	$options = get_option( 'seopress_google_analytics_option_name' );

	$check = isset( $options['seopress_google_analytics_affiliate_tracking_enable'] ) ? $options['seopress_google_analytics_affiliate_tracking_enable'] : null;
	?>

<label for="seopress_google_analytics_affiliate_tracking_enable">
	<input id="seopress_google_analytics_affiliate_tracking_enable"
		name="seopress_google_analytics_option_name[seopress_google_analytics_affiliate_tracking_enable]"
		type="checkbox" <?php if ( '1' === $check ) { ?>
	checked="yes"
	<?php } ?>
	value="1"/>
	<?php esc_attr_e( 'Enable affiliate/outbound tracking', 'wp-seopress' ); ?>
</label>

	<?php
	if ( isset( $options['seopress_google_analytics_affiliate_tracking_enable'] ) ) {
		esc_attr( $options['seopress_google_analytics_affiliate_tracking_enable'] );
	}
}

/**
 * Print the Google Analytics affiliate tracking callback.
 */
function seopress_google_analytics_affiliate_tracking_callback() {
	$options = get_option( 'seopress_google_analytics_option_name' );
	$check   = isset( $options['seopress_google_analytics_affiliate_tracking'] ) ? $options['seopress_google_analytics_affiliate_tracking'] : null;

	printf(
		'<input type="text" name="seopress_google_analytics_option_name[seopress_google_analytics_affiliate_tracking]" placeholder="' . esc_html__( 'aff|go|out', 'wp-seopress' ) . '" aria-label="' . esc_attr__( 'Track affiliate/outbound links', 'wp-seopress' ) . '" value="%s"/>',
		esc_html( $check )
	);
	?>
<p class="description">
	<?php esc_attr_e( 'Separate each keyword with a pipe "|"', 'wp-seopress' ); ?>
</p>
	<?php
}

/**
 * Print the Google Analytics phone tracking callback.
 */
function seopress_google_analytics_phone_tracking_callback() {
	$options = get_option( 'seopress_google_analytics_option_name' );

	$check = isset( $options['seopress_google_analytics_phone_tracking'] ) ? $options['seopress_google_analytics_phone_tracking'] : null;
	?>

<label for="seopress_google_analytics_phone_tracking">
	<input id="seopress_google_analytics_phone_tracking"
		name="seopress_google_analytics_option_name[seopress_google_analytics_phone_tracking]"
		type="checkbox" <?php if ( '1' === $check ) { ?>
	checked="yes"
	<?php } ?>
	value="1"/>
	<?php esc_attr_e( 'Enable tracking of "tel:" links', 'wp-seopress' ); ?>
</label>

<p class="description">
	<pre>&lt;a href="tel:+33123456789"&gt;</pre>
</p>

	<?php
	if ( isset( $options['seopress_google_analytics_phone_tracking'] ) ) {
		esc_attr( $options['seopress_google_analytics_phone_tracking'] );
	}
}

/**
 * Print the Google Analytics cd author callback.
 */
function seopress_google_analytics_cd_author_callback() {
	$options = get_option( 'seopress_google_analytics_option_name' );

	$selected = isset( $options['seopress_google_analytics_cd_author'] ) ? $options['seopress_google_analytics_cd_author'] : null;
	?>
<select id="seopress_google_analytics_cd_author"
	name="seopress_google_analytics_option_name[seopress_google_analytics_cd_author]">
	<option <?php if ( 'none' === esc_attr( $selected ) ) { ?>
		selected="selected"
		<?php } ?>
		value="none"><?php esc_attr_e( 'None', 'wp-seopress' ); ?>
	</option>

	<?php for ( $i = 1; $i <= 20; ++$i ) { ?>
	<option <?php if ( 'dimension' . intval( $i ) . '' === esc_attr( $selected ) ) { ?>
		selected="selected"
		<?php } ?>
		value="dimension<?php echo intval( $i ); ?>"><?php /* translators: %d dimension number number, eg: #1 */ printf( esc_html__( 'Custom Dimension #%d', 'wp-seopress' ), intval( $i ) ); ?>
	</option>
	<?php } ?>
</select>

	<?php
	if ( isset( $options['seopress_google_analytics_cd_author'] ) ) {
		esc_attr( $options['seopress_google_analytics_cd_author'] );
	}
}

/**
 * Print the Google Analytics cd category callback.
 */
function seopress_google_analytics_cd_category_callback() {
	$options = get_option( 'seopress_google_analytics_option_name' );

	$selected = isset( $options['seopress_google_analytics_cd_category'] ) ? $options['seopress_google_analytics_cd_category'] : null;
	?>
<select id="seopress_google_analytics_cd_category"
	name="seopress_google_analytics_option_name[seopress_google_analytics_cd_category]">
	<option <?php if ( 'none' === esc_attr( $selected ) ) { ?>
		selected="selected"
		<?php } ?>
		value="none"><?php esc_attr_e( 'None', 'wp-seopress' ); ?>
	</option>

	<?php for ( $i = 1; $i <= 20; ++$i ) { ?>
		<option <?php if ( 'dimension' . intval( $i ) . '' === esc_attr( $selected ) ) { ?>
		selected="selected"
		<?php } ?>
		value="dimension<?php echo intval( $i ); ?>"><?php /* translators: %d dimension number number, eg: #1 */ printf( esc_html__( 'Custom Dimension #%d', 'wp-seopress' ), intval( $i ) ); ?>
	</option>
	<?php } ?>
</select>

	<?php
	if ( isset( $options['seopress_google_analytics_cd_category'] ) ) {
		esc_attr( $options['seopress_google_analytics_cd_category'] );
	}
}

/**
 * Print the Google Analytics cd tag callback.
 */
function seopress_google_analytics_cd_tag_callback() {
	$options = get_option( 'seopress_google_analytics_option_name' );

	$selected = isset( $options['seopress_google_analytics_cd_tag'] ) ? $options['seopress_google_analytics_cd_tag'] : null;
	?>

<select id="seopress_google_analytics_cd_tag"
	name="seopress_google_analytics_option_name[seopress_google_analytics_cd_tag]">
	<option <?php if ( 'none' === esc_attr( $selected ) ) { ?>
		selected="selected"
		<?php } ?>
		value="none"><?php esc_attr_e( 'None', 'wp-seopress' ); ?>
	</option>

	<?php for ( $i = 1; $i <= 20; ++$i ) { ?>
	<option <?php if ( 'dimension' . intval( $i ) . '' === esc_attr( $selected ) ) { ?>
		selected="selected"
		<?php } ?>
		value="dimension<?php echo intval( $i ); ?>"><?php /* translators: %d dimension number number, eg: #1 */ printf( esc_html__( 'Custom Dimension #%d', 'wp-seopress' ), intval( $i ) ); ?>
	</option>
	<?php } ?>
</select>

	<?php
	if ( isset( $options['seopress_google_analytics_cd_tag'] ) ) {
		esc_attr( $options['seopress_google_analytics_cd_tag'] );
	}
}

/**
 * Print the Google Analytics cd post type callback.
 */
function seopress_google_analytics_cd_post_type_callback() {
	$options = get_option( 'seopress_google_analytics_option_name' );

	$selected = isset( $options['seopress_google_analytics_cd_post_type'] ) ? $options['seopress_google_analytics_cd_post_type'] : null;
	?>

<select id="seopress_google_analytics_cd_post_type"
	name="seopress_google_analytics_option_name[seopress_google_analytics_cd_post_type]">
	<option <?php if ( 'none' === esc_attr( $selected ) ) { ?>
		selected="selected"
		<?php } ?>
		value="none"><?php esc_attr_e( 'None', 'wp-seopress' ); ?>
	</option>

	<?php for ( $i = 1; $i <= 20; ++$i ) { ?>
	<option <?php if ( 'dimension' . intval( $i ) . '' === esc_attr( $selected ) ) { ?>
		selected="selected"
		<?php } ?>
		value="dimension<?php echo intval( $i ); ?>"><?php /* translators: %d dimension number number, eg: #1 */ printf( esc_html__( 'Custom Dimension #%d', 'wp-seopress' ), intval( $i ) ); ?>
	</option>
	<?php } ?>
</select>
	<?php
	if ( isset( $options['seopress_google_analytics_cd_post_type'] ) ) {
		esc_attr( $options['seopress_google_analytics_cd_post_type'] );
	}
}

/**
 * Print the Google Analytics cd logged in user callback.
 */
function seopress_google_analytics_cd_logged_in_user_callback() {
	$options = get_option( 'seopress_google_analytics_option_name' );

	$selected = isset( $options['seopress_google_analytics_cd_logged_in_user'] ) ? $options['seopress_google_analytics_cd_logged_in_user'] : null;
	?>

<select id="seopress_google_analytics_cd_logged_in_user"
	name="seopress_google_analytics_option_name[seopress_google_analytics_cd_logged_in_user]">
	<option <?php if ( 'none' === esc_attr( $selected ) ) { ?>
		selected="selected"
		<?php } ?>
		value="none"><?php esc_attr_e( 'None', 'wp-seopress' ); ?>
	</option>
	<?php for ( $i = 1; $i <= 20; ++$i ) { ?>
	<option <?php if ( 'dimension' . intval( $i ) . '' === esc_attr( $selected ) ) { ?>
		selected="selected"
		<?php } ?>
		value="dimension<?php echo intval( $i ); ?>"><?php /* translators: %d dimension number number, eg: #1 */ printf( esc_html__( 'Custom Dimension #%d', 'wp-seopress' ), intval( $i ) ); ?>
	</option>
	<?php } ?>
</select>
	<?php
	if ( isset( $options['seopress_google_analytics_cd_logged_in_user'] ) ) {
		esc_attr( $options['seopress_google_analytics_cd_logged_in_user'] );
	}
}

/**
 * Print the Google Analytics matomo enable callback.
 */
function seopress_google_analytics_matomo_enable_callback() {
	$options = get_option( 'seopress_google_analytics_option_name' );
	$check   = isset( $options['seopress_google_analytics_matomo_enable'] ) ? $options['seopress_google_analytics_matomo_enable'] : null;
	?>


<label for="seopress_google_analytics_matomo_enable">
	<input id="seopress_google_analytics_matomo_enable"
		name="seopress_google_analytics_option_name[seopress_google_analytics_matomo_enable]" type="checkbox" <?php if ( '1' === $check ) { ?>
	checked="yes"
	<?php } ?>
	value="1"/>

	<?php esc_attr_e( 'Enable Matomo tracking', 'wp-seopress' ); ?>
	<p class="description">
		<?php esc_attr_e( 'A Matomo Cloud account or a self hosted Matomo installation is required.', 'wp-seopress' ); ?>
	</p>
</label>

	<?php
	if ( isset( $options['seopress_google_analytics_matomo_enable'] ) ) {
		esc_attr( $options['seopress_google_analytics_matomo_enable'] );
	}
}

/**
 * Print the Google Analytics matomo self hosted callback.
 */
function seopress_google_analytics_matomo_self_hosted_callback() {
	$docs    = seopress_get_docs_links();
	$options = get_option( 'seopress_google_analytics_option_name' );
	$check   = isset( $options['seopress_google_analytics_matomo_self_hosted'] ) ? $options['seopress_google_analytics_matomo_self_hosted'] : null;
	?>


<label for="seopress_google_analytics_matomo_self_hosted">
	<input id="seopress_google_analytics_matomo_self_hosted"
		name="seopress_google_analytics_option_name[seopress_google_analytics_matomo_self_hosted]" type="checkbox" <?php if ( '1' === $check ) { ?>
	checked="yes"
	<?php } ?>
	value="1"/>

	<?php esc_attr_e( 'Yes, self-hosted installation', 'wp-seopress' ); ?>
	<p class="description">
		<?php esc_attr_e( 'If you use Matomo Cloud, uncheck this option.', 'wp-seopress' ); ?>
	</p>
	<p class="description">
		<?php printf( '<a href="%s" target="_blank" class="seopress-help">' . esc_attr__( 'Learn how to install Matomo On-Premise on your server', 'wp-seopress' ) . '</a>', esc_url( $docs['analytics']['matomo']['on_premise'] ) ); ?>
		<span class="dashicons dashicons-external seopress-help"></span>
	</p>
</label>

	<?php
	if ( isset( $options['seopress_google_analytics_matomo_self_hosted'] ) ) {
		esc_attr( $options['seopress_google_analytics_matomo_self_hosted'] );
	}
}

/**
 * Print the Google Analytics matomo id callback.
 */
function seopress_google_analytics_matomo_id_callback() {
	$options = get_option( 'seopress_google_analytics_option_name' );

	$check = isset( $options['seopress_google_analytics_matomo_id'] ) ? $options['seopress_google_analytics_matomo_id'] : null;

	printf(
		'<input type="text" name="seopress_google_analytics_option_name[seopress_google_analytics_matomo_id]" placeholder="'
		. esc_html__( 'Enter "example" if you Matomo account URL is "example.matomo.cloud"', 'wp-seopress' )
		. '" value="%s" aria-label="' . esc_attr__( 'Matomo URL (Cloud or Self-hosted)', 'wp-seopress' ) . '"/>',
		esc_html( $check )
	);
	?>

<p class="description">
	<?php echo wp_kses_post( __( 'Enter only the <strong>host without the quotes</strong> like this <code>"example.matomo.cloud"</code> (Cloud) or <code>"matomo.example.com"</code> (self-hosted).', 'wp-seopress' ) ); ?>
</p>

	<?php
}

/**
 * Print the Google Analytics matomo site id callback.
 */
function seopress_google_analytics_matomo_site_id_callback() {
	$options = get_option( 'seopress_google_analytics_option_name' );
	$check   = isset( $options['seopress_google_analytics_matomo_site_id'] ) ? $options['seopress_google_analytics_matomo_site_id'] : null;

	printf(
		'<input type="text" name="seopress_google_analytics_option_name[seopress_google_analytics_matomo_site_id]"
			placeholder="' . esc_html__( 'Enter your site ID here', 'wp-seopress' ) . '" value="%s"
			aria-label="' . esc_attr__( 'Matomo Site ID', 'wp-seopress' ) . '" />',
		esc_html( $check )
	);
	?>

<p class="description">
	<?php echo wp_kses_post( __( 'To find your site ID, go to your <strong>Matomo Cloud account, Websites, Manage page</strong>. Look at "Site ID" on the right part.', 'wp-seopress' ) ); ?><br>
	<?php esc_attr_e( 'For self-hosted installations, go to your Matomo administration, Settings, Websites, Manage. From the list of your websites, find the ID line.', 'wp-seopress' ); ?>
</p>
	<?php
}

/**
 * Print the Google Analytics matomo subdomains callback.
 */
function seopress_google_analytics_matomo_subdomains_callback() {
	$options = get_option( 'seopress_google_analytics_option_name' );

	$check = isset( $options['seopress_google_analytics_matomo_subdomains'] ) ? $options['seopress_google_analytics_matomo_subdomains'] : null;
	?>

<label for="seopress_google_analytics_matomo_subdomains">
	<input id="seopress_google_analytics_matomo_subdomains"
		name="seopress_google_analytics_option_name[seopress_google_analytics_matomo_subdomains]" type="checkbox"
		<?php if ( '1' === $check ) { ?>
	checked="yes"
	<?php } ?>
	value="1"/>
	<?php esc_attr_e( 'Tracking one domain and its subdomains in the same website', 'wp-seopress' ); ?>
</label>

<p class="description">
	<?php esc_attr_e( 'If one visitor visits x.example.com and y.example.com, they will be counted as a unique visitor.', 'wp-seopress' ); ?>
</p>

	<?php
	if ( isset( $options['seopress_google_analytics_matomo_subdomains'] ) ) {
		esc_attr( $options['seopress_google_analytics_matomo_subdomains'] );
	}
}

/**
 * Print the Google Analytics matomo site domain callback.
 */
function seopress_google_analytics_matomo_site_domain_callback() {
	$options = get_option( 'seopress_google_analytics_option_name' );
	$check   = isset( $options['seopress_google_analytics_matomo_site_domain'] ) ? $options['seopress_google_analytics_matomo_site_domain'] : null;
	?>

<label for="seopress_google_analytics_matomo_site_domain">
	<input id="seopress_google_analytics_matomo_site_domain"
		name="seopress_google_analytics_option_name[seopress_google_analytics_matomo_site_domain]" type="checkbox"
		<?php if ( '1' === $check ) { ?>
	checked="yes"
	<?php } ?>
	value="1"/>
	<?php esc_attr_e( 'Prepend the site domain to the page title when tracking', 'wp-seopress' ); ?>
</label>
<p class="description">
	<?php esc_attr_e( 'If someone visits the \'About\' page on blog.example.com it will be recorded as \'blog / About\'. This is the easiest way to get an overview of your traffic by sub-domain.', 'wp-seopress' ); ?>
</p>

	<?php
	if ( isset( $options['seopress_google_analytics_matomo_site_domain'] ) ) {
		esc_attr( $options['seopress_google_analytics_matomo_site_domain'] );
	}
}

/**
 * Print the Google Analytics matomo no js callback.
 */
function seopress_google_analytics_matomo_no_js_callback() {
	$options = get_option( 'seopress_google_analytics_option_name' );
	$check   = isset( $options['seopress_google_analytics_matomo_no_js'] ) ? $options['seopress_google_analytics_matomo_no_js'] : null;
	?>

<label for="seopress_google_analytics_matomo_no_js">
	<input id="seopress_google_analytics_matomo_no_js"
		name="seopress_google_analytics_option_name[seopress_google_analytics_matomo_no_js]" type="checkbox" <?php if ( '1' === $check ) { ?>
	checked="yes"
	<?php } ?>
	value="1"/>
	<?php esc_attr_e( 'Track users with JavaScript disabled', 'wp-seopress' ); ?>
</label>

	<?php
	if ( isset( $options['seopress_google_analytics_matomo_no_js'] ) ) {
		esc_attr( $options['seopress_google_analytics_matomo_no_js'] );
	}
}

/**
 * Print the Google Analytics matomo cross domain callback.
 */
function seopress_google_analytics_matomo_cross_domain_callback() {
	$options = get_option( 'seopress_google_analytics_option_name' );
	$check   = isset( $options['seopress_google_analytics_matomo_cross_domain'] ) ? $options['seopress_google_analytics_matomo_cross_domain'] : null;
	?>

<label for="seopress_google_analytics_matomo_cross_domain">
	<input id="seopress_google_analytics_matomo_cross_domain"
		name="seopress_google_analytics_option_name[seopress_google_analytics_matomo_cross_domain]" type="checkbox"
		<?php if ( '1' === $check ) { ?>
	checked="yes"
	<?php } ?>
	value="1"/>
	<?php esc_attr_e( 'Enables cross domain linking', 'wp-seopress' ); ?>
</label>

<p class="description">
	<?php esc_attr_e( 'By default, the visitor ID that identifies a unique visitor is stored in the browser\'s first party cookies which can only be accessed by pages on the same domain.', 'wp-seopress' ); ?>
</p>
<p class="description">
	<?php esc_attr_e( 'Enabling cross domain linking lets you track all the actions and pageviews of a specific visitor into the same visit even when they view pages on several domains.', 'wp-seopress' ); ?>
</p>
<p class="description">
	<?php esc_attr_e( 'Whenever a user clicks on a link to one of your website\'s alias URLs, it will append a URL parameter pk_vid forwarding the Visitor ID.', 'wp-seopress' ); ?>
</p>

	<?php
	if ( isset( $options['seopress_google_analytics_matomo_cross_domain'] ) ) {
		esc_attr( $options['seopress_google_analytics_matomo_cross_domain'] );
	}
}

/**
 * Print the Google Analytics matomo cross domain sites callback.
 */
function seopress_google_analytics_matomo_cross_domain_sites_callback() {
	$options = get_option( 'seopress_google_analytics_option_name' );
	$check   = isset( $options['seopress_google_analytics_matomo_cross_domain_sites'] ) ? $options['seopress_google_analytics_matomo_cross_domain_sites'] : null;

	printf(
		'<input type="text" name="seopress_google_analytics_option_name[seopress_google_analytics_matomo_cross_domain_sites]" placeholder="'
			. esc_html__( 'Enter your domains: seopress.org,sub.seopress.org,sub2.seopress.org', 'wp-seopress' )
		. '" value="%s" aria-label="' . esc_attr__( 'Cross domains', 'wp-seopress' ) . '"/>',
		esc_html( $check )
	);
}

/**
 * Print the Google Analytics matomo dnt callback.
 */
function seopress_google_analytics_matomo_dnt_callback() {
	$options = get_option( 'seopress_google_analytics_option_name' );
	$check   = isset( $options['seopress_google_analytics_matomo_dnt'] ) ? $options['seopress_google_analytics_matomo_dnt'] : null;
	?>

<label for="seopress_google_analytics_matomo_dnt">
	<input id="seopress_google_analytics_matomo_dnt"
		name="seopress_google_analytics_option_name[seopress_google_analytics_matomo_dnt]" type="checkbox" <?php if ( '1' === $check ) { ?>
	checked="yes"
	<?php } ?>
	value="1"/>
	<?php esc_attr_e( 'Enable client side DoNotTrack detection', 'wp-seopress' ); ?>
</label>

<p class="description">
	<?php esc_attr_e( 'Tracking requests will not be sent if visitors do not wish to be tracked.', 'wp-seopress' ); ?>
</p>

	<?php
	if ( isset( $options['seopress_google_analytics_matomo_dnt'] ) ) {
		esc_attr( $options['seopress_google_analytics_matomo_dnt'] );
	}
}

/**
 * Print the Google Analytics matomo no cookies callback.
 */
function seopress_google_analytics_matomo_no_cookies_callback() {
	$options = get_option( 'seopress_google_analytics_option_name' );
	$check   = isset( $options['seopress_google_analytics_matomo_no_cookies'] ) ? $options['seopress_google_analytics_matomo_no_cookies'] : null;
	?>

<label for="seopress_google_analytics_matomo_no_cookies">
	<input id="seopress_google_analytics_matomo_no_cookies"
		name="seopress_google_analytics_option_name[seopress_google_analytics_matomo_no_cookies]" type="checkbox"
		<?php if ( '1' === $check ) { ?>
	checked="yes"
	<?php } ?>
	value="1"/>
	<?php esc_attr_e( 'Disables all first party cookies. Existing Matomo cookies for this website will be deleted on the next page view.', 'wp-seopress' ); ?>
</label>

	<?php
	if ( isset( $options['seopress_google_analytics_matomo_no_cookies'] ) ) {
		esc_attr( $options['seopress_google_analytics_matomo_no_cookies'] );
	}
}

/**
 * Print the Google Analytics matomo link tracking callback.
 */
function seopress_google_analytics_matomo_link_tracking_callback() {
	$options = get_option( 'seopress_google_analytics_option_name' );
	$check   = isset( $options['seopress_google_analytics_matomo_link_tracking'] ) ? $options['seopress_google_analytics_matomo_link_tracking'] : null;
	?>

<label for="seopress_google_analytics_matomo_link_tracking">
	<input id="seopress_google_analytics_matomo_link_tracking"
		name="seopress_google_analytics_option_name[seopress_google_analytics_matomo_link_tracking]" type="checkbox"
		<?php if ( '1' === $check ) { ?>
	checked="yes"
	<?php } ?>
	value="1"/>
	<?php esc_attr_e( 'Enabling Download & Outlink tracking', 'wp-seopress' ); ?>
</label>

<p class="description">
	<?php esc_attr_e( 'By default, any file ending with one of these extensions will be considered a "download" in the Matomo interface:', 'wp-seopress' ); ?><br>
</p>

<pre>7z|aac|arc|arj|apk|asf|asx|avi|bin|bz|bz2|csv|deb|dmg|doc|exe|flv|gif|gz|gzip|hqx|jar|jpg|jpeg|js|mp2|mp3|mp4|mpg|mpeg|mov|movie|msi|msp|odb|odf|odg|odp|ods|odt|ogg|ogv| pdf|phps|png|ppt|qt|qtm|ra|ram|rar|rpm|sea|sit|tar|tbz|tbz2|tgz|torrent|txt|wav|wma|wmv|wpd|xls|xml|z|zip</pre>

	<?php
	if ( isset( $options['seopress_google_analytics_matomo_link_tracking'] ) ) {
		esc_attr( $options['seopress_google_analytics_matomo_link_tracking'] );
	}
}

/**
 * Print the Google Analytics matomo no heatmaps callback.
 */
function seopress_google_analytics_matomo_no_heatmaps_callback() {
	$options = get_option( 'seopress_google_analytics_option_name' );
	$check   = isset( $options['seopress_google_analytics_matomo_no_heatmaps'] ) ? $options['seopress_google_analytics_matomo_no_heatmaps'] : null;
	?>

<label for="seopress_google_analytics_matomo_no_heatmaps">
	<input id="seopress_google_analytics_matomo_no_heatmaps"
		name="seopress_google_analytics_option_name[seopress_google_analytics_matomo_no_heatmaps]" type="checkbox"
		<?php if ( '1' === $check ) { ?>
	checked="yes"
	<?php } ?>
	value="1"/>
	<?php esc_attr_e( 'Disabling all heatmaps and session recordings', 'wp-seopress' ); ?>
</label>

	<?php
	if ( isset( $options['seopress_google_analytics_matomo_no_heatmaps'] ) ) {
		esc_attr( $options['seopress_google_analytics_matomo_no_heatmaps'] );
	}
}

/**
 * Print the Google Analytics clarity enable callback.
 */
function seopress_google_analytics_clarity_enable_callback() {
	$options = get_option( 'seopress_google_analytics_option_name' );
	$check   = isset( $options['seopress_google_analytics_clarity_enable'] ) ? $options['seopress_google_analytics_clarity_enable'] : null;
	?>


<label for="seopress_google_analytics_clarity_enable">
	<input id="seopress_google_analytics_clarity_enable"
		name="seopress_google_analytics_option_name[seopress_google_analytics_clarity_enable]" type="checkbox" <?php if ( '1' === $check ) { ?>
	checked="yes"
	<?php } ?>
	value="1"/>
	<?php esc_attr_e( 'Add Microsoft Clarity code to your site', 'wp-seopress' ); ?>
</label>

	<?php
	if ( isset( $options['seopress_google_analytics_clarity_enable'] ) ) {
		esc_attr( $options['seopress_google_analytics_clarity_enable'] );
	}
}

/**
 * Print the Google Analytics clarity project id callback.
 */
function seopress_google_analytics_clarity_project_id_callback() {
	$docs    = seopress_get_docs_links();
	$options = get_option( 'seopress_google_analytics_option_name' );
	$check   = isset( $options['seopress_google_analytics_clarity_project_id'] ) ? $options['seopress_google_analytics_clarity_project_id'] : null;

	printf(
		'<input type="text" name="seopress_google_analytics_option_name[seopress_google_analytics_clarity_project_id]" placeholder="' . esc_html__( 'Enter your Project ID', 'wp-seopress' ) . '" aria-label="' . esc_attr__( 'Enter your Project ID', 'wp-seopress' ) . '" value="%s"/>',
		esc_html( $check )
	);
	?>

<p class="seopress-help description">
	<a href="<?php echo esc_url( $docs['analytics']['clarity']['project'] ); ?>" target="_blank">
		<?php esc_attr_e( 'Find your project ID', 'wp-seopress' ); ?>
	</a>
	<span class="dashicons dashicons-external"></span>
</p>
	<?php
}
