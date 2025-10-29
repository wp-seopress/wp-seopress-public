<?php
/**
 * Text Letter Counter Control
 *
 * @package Elementor
 */
namespace WPSeoPressElementorAddon\Controls;

defined( 'ABSPATH' ) || exit( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

/**
 * Text Letter Counter Control
 */
class Text_Letter_Counter_Control extends \Elementor\Base_Data_Control {
	/**
	 * Get type.
	 *
	 * @return string
	 */
	public function get_type() {
		return 'seopresstextlettercounter';
	}

	/**
	 * Enqueue.
	 *
	 * @return void
	 */
	public function enqueue() {
		wp_enqueue_style(
			'sp-el-text-letter-counter-style',
			SEOPRESS_ELEMENTOR_ADDON_URL . 'assets/css/text-letter-counter.css',
			array(),
			SEOPRESS_VERSION
		);

		wp_enqueue_script(
			'sp-el-text-letter-counter-script',
			SEOPRESS_ELEMENTOR_ADDON_URL . 'assets/js/text-letter-counter.js',
			array( 'jquery' ),
			11,
			true
		);
	}

	/**
	 * Get default settings.
	 *
	 * @return array
	 */
	protected function get_default_settings() {
		return array(
			'field_type'  => 'text',
			'description' => '',
			'rows'        => 7,
		);
	}

	/**
	 * Content template.
	 *
	 * @return void
	 */
	public function content_template() {
		$control_uid = $this->get_control_uid();
		?>
		<div class="elementor-control-field seopress-text-letter-counter">
			<?php do_action( 'seopress_elementor_seo_titles_before' ); ?>

			<label for="<?php echo esc_attr( $control_uid ); ?>" class="elementor-control-title">{{{ data.label }}}</label>
			<div class="elementor-control-input-wrapper">
				<# if ( data.field_type === 'text' ) { #>
					<input type="text" id="<?php echo esc_attr( $control_uid ); ?>" class="elementor-control-tag-area" data-setting="{{ data.name }}" placeholder="{{ data.placeholder }}" />
				<# } else { #>
					<textarea id="<?php echo esc_attr( $control_uid ); ?>" class="elementor-control-tag-area" rows="{{ data.rows }}" data-setting="{{ data.name }}" placeholder="{{ data.placeholder }}"></textarea>
				<# } #>
			<div>
			<div class="sp-progress">
				<div class="seopress_counters_progress sp-progress-bar" role="progressbar" style="width: 2%;" aria-valuenow="1" aria-valuemin="0" aria-valuemax="100">1%</div>
			</div>
			<div class="wrap-seopress-counters">
				<div class="seopress_pixel"></div>
				<strong>
					<# if ( data.field_type === 'text' ) { #>
						<?php esc_html_e( ' / 568 pixels - ', 'wp-seopress' ); ?>
					<# } else { #>
						<?php esc_html_e( ' / 940 pixels - ', 'wp-seopress' ); ?>
					<# } #>
				</strong>
				<div class="seopress_counters"></div>
				<?php esc_html_e( ' (maximum recommended limit)', 'wp-seopress' ); ?>
			</div>

			<div class="wrap-tags">
				<# if ( data.field_type === 'text' ) { #>
					<span class="seopress-tag-single-title tag-title" data-tag="%%post_title%%" ><span class="dashicons dashicons-plus-alt2"></span><?php esc_html_e( 'Post Title', 'wp-seopress' ); ?></span>
					<span class="seopress-tag-single-sep tag-title" data-tag="%%sep%%"><span class="dashicons dashicons-plus-alt2"></span><?php esc_html_e( 'Separator', 'wp-seopress' ); ?></span>
					<span class="seopress-tag-single-site-title tag-title" data-tag="%%sitetitle%%"><span class="dashicons dashicons-plus-alt2"></span><?php esc_html_e( 'Site Title', 'wp-seopress' ); ?></span>
				<# } else { #>
					<span class="seopress-tag-single-excerpt tag-title" data-tag="%%post_excerpt%%"><span class="dashicons dashicons-plus-alt2"></span><?php esc_html_e( 'Post Excerpt', 'wp-seopress' ); ?></span>
				<# } #>
			</div>
		</div>
		<# if ( data.description ) { #>
			<div class="elementor-control-field-description">{{{ data.description }}}</div>
		<# } #>
		<?php
	}
}
