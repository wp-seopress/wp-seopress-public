<?php
namespace WPSeoPressElementorAddon\Controls;

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

class Social_Preview_Control extends \Elementor\Base_Control {
	public function get_type() {
		return 'seopress-social-preview';
	}

	public function enqueue() {
		wp_enqueue_style( 
			'sp-el-social-preview-style',
			SEOPRESS_ELEMENTOR_ADDON_URL . 'assets/css/social-preview.css'
		);

		wp_enqueue_script(
			'sp-el-social-preview-script',
			SEOPRESS_ELEMENTOR_ADDON_URL . 'assets/js/social-preview.js',
			array('jquery'),
			SEOPRESS_VERSION,
			true
		);
	}

	protected function get_default_settings() {
		return [
			'image' => null,
			'title' => '',
			'description' => ''
		];
	}

	public function content_template() {
		$site_url = explode( '//', get_bloginfo('url') )[1];
		?>
		<# if ( data.network === 'facebook' ) { #>
			<label class="elementor-control-title"><?php _e( 'Facebook Preview', 'wp-seopress' ); ?></label>
			<div class="elementor-control-field facebook-snippet-box">
				<div class="snippet-fb-img">
					<img src="{{{data.image}}}">
				</div>
				<div class="facebook-snippet-text">
					<div class="snippet-meta">
						<div class="snippet-fb-url"><?php echo $site_url; ?></div>
						<div class="fb-sep">|</div>
						<div class="fb-by"><?php _e( 'By', 'wp-seopress' ); ?>&nbsp;</div>
						<div class="snippet-fb-site-name"><?php echo get_bloginfo('name'); ?></div>
					</div>
					<div class="title-desc">
						<div class="snippet-fb-title">{{{data.title}}}</div>
						<div class="snippet-fb-description-custom">{{{data.description}}}</div>
					</div>
				</div>
			</div>
		<# } else if( data.network === 'twitter' ) { #>
			<label class="elementor-control-title"><?php _e( 'Twitter Preview', 'wp-seopress' ); ?></label>
			<div class="elementor-control-field twitter-snippet-box">
				<div class="snippet-twitter-img-default">
					<img src="{{{data.image}}}">
				</div>
				<div class="twitter-snippet-text">
					<div class="title-desc">
						<div class="snippet-twitter-title">{{{data.title}}}</div>
						<div class="snippet-twitter-description">{{{data.description}}}</div>
						<div class="snippet-meta">
							<div class="snippet-twitter-url"><?php echo $site_url; ?></div>
						</div>
					</div>
				</div>
			</div>
		<# } #>
		<?php
	}
}