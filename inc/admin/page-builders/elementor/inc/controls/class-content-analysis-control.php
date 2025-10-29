<?php
/**
 * Content Analysis Control
 *
 * @package Elementor
 */

namespace WPSeoPressElementorAddon\Controls;

defined( 'ABSPATH' ) || exit( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

/**
 * Content Analysis Control
 */
class Content_Analysis_Control extends \Elementor\Base_Control {
	/**
	 * Get type.
	 *
	 * @return string
	 */
	public function get_type() {
		return 'seopress-content-analysis';
	}

	/**
	 * Enqueue.
	 *
	 * @return void
	 */
	public function enqueue() {
		wp_enqueue_style(
			'sp-el-content-analysis-style',
			SEOPRESS_ELEMENTOR_ADDON_URL . 'assets/css/content-analysis.css',
			array(),
			SEOPRESS_VERSION
		);

		wp_enqueue_script(
			'sp-el-content-analysis-script',
			SEOPRESS_ELEMENTOR_ADDON_URL . 'assets/js/content-analysis.js',
			array( 'seopress-elementor-base-script', 'jquery-ui-tabs', 'jquery-ui-accordion' ),
			SEOPRESS_VERSION,
			true
		);
	}

	/**
	 * Get default settings.
	 *
	 * @return array
	 */
	protected function get_default_settings() {
		global $post;

		return array(
			'post_id'     => isset( $post ) ? $post->ID : '',
			'post_type'   => isset( $post ) ? $post->post_type : '',
			'loading'     => __( 'Analysis in progress...', 'wp-seopress' ),
			'description' => '',
		);
	}

	/**
	 * Content template.
	 *
	 * @return void
	 */
	public function content_template() {
		?>
<div class="elementor-control-field seopress-content-analyses">
	<button id="seopress_launch_analysis" type="button"
		class="btn btnSecondary elementor-button elementor-button-default" data_id="{{ data.post_id }}"
		data_post_type="{{ data.post_type }}">
		<?php esc_html_e( 'Refresh analysis', 'wp-seopress' ); ?>
	</button>

	<# if ( data.description ) { #>
		<div class="elementor-control-field-description">{{{ data.description }}}</div>
		<# } #>
			<div id="seopress-analysis-tabs">
				<div class="analysis-score">
					<p class="notgood loading">
						<svg role="img" aria-hidden="true" focusable="false" width="100%" height="100%"
							viewBox="0 0 200 200" version="1.1" xmlns="http://www.w3.org/2000/svg">
							<circle r="90" cx="100" cy="100" fill="transparent" stroke-dasharray="565.48"
								stroke-dashoffset="0"></circle>
							<circle id="bar" r="90" cx="100" cy="100" fill="transparent" stroke-dasharray="565.48"
								stroke-dashoffset="0" style="stroke-dashoffset: 101.788px;"></circle>
						</svg>
						<span>{{{ data.loading }}}</span>
					</p>
				</div>
			</div>
</div>
		<?php
	}
}
