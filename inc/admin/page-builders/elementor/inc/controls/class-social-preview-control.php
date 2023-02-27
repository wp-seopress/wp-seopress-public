<?php

namespace WPSeoPressElementorAddon\Controls;

if ( ! defined('ABSPATH')) {
    exit();
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
            ['seopress-elementor-base-script'],
            11,
            true
        );

        wp_localize_script('sp-el-social-preview-script', 'seopressFiltersElementor', [
            'resize_panel' => apply_filters('seopress_resize_panel_elementor', true),
        ]);
    }

    protected function get_default_settings() {
        return [
            'image'       => null,
            'title'       => '',
            'description' => '',
        ];
    }

    public function content_template() {
        $site_url = explode('//', get_bloginfo('url'))[1]; ?>
		<# if ( data.network === 'facebook' ) { #>
			<label class="elementor-control-title">{{{ data.label }}}</label>
			<div class="elementor-control-field facebook-snippet-box">
				<div class="snippet-fb-img">
					<img src="{{{data.image}}}">
				</div>
				<div class="facebook-snippet-text">
					<div class="snippet-meta">
						<div class="snippet-fb-url"><?php echo $site_url; ?></div>
						<div class="fb-sep">|</div>
						<div class="fb-by"><?php _e('By', 'wp-seopress'); ?>&nbsp;</div>
						<div class="snippet-fb-site-name"><?php echo get_bloginfo('name'); ?></div>
					</div>
					<div class="title-desc">
						<div class="snippet-fb-title">{{{data.title}}}</div>
						<div class="snippet-fb-description-custom">{{{data.description}}}</div>
					</div>
				</div>
			</div>
		<# } else if( data.network === 'twitter' ) { #>
			<label class="elementor-control-title">{{{ data.label }}}</label>
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
		<# } else if( data.network === 'google' ) { #>
			<label class="elementor-control-title">{{{ data.label }}}</label>
			<div class="elementor-control-field google-snippet-box" data_id="{{ data.post_id }}" data_origin="{{ data.origin }}" data_post_type="{{ data.post_type }}">
				<div class="google-snippet-preview mobile-preview">
					<div class="wrap-toggle-preview">
						<p>
							<span class="dashicons dashicons-smartphone"></span>
							<strong><?php _e('Mobile Preview', 'wp-seopress'); ?></strong>
							<input type="checkbox" name="toggle-preview" id="toggle-preview" class="toggle" data-toggle="1">
							<label for="toggle-preview"></label>
						</p>
					</div>

                    <?php
                        $gp_title       = '';
                        $gp_permalink   = '';
                        $alt_site_title = !empty(seopress_get_service('TitleOption')->getHomeSiteTitleAlt()) ? seopress_get_service('TitleOption')->getHomeSiteTitleAlt() : get_bloginfo('name');

                        $gp_title       = '<div class="snippet-title-default" style="display:none">' . get_the_title() . ' - ' . get_bloginfo('name') . '</div>';
                        $gp_permalink   = '<div class="snippet-permalink"><span class="snippet-sitename">' . $alt_site_title . '</span>' . htmlspecialchars(urldecode(get_permalink())) . '</div>';

                        $siteicon = '<div class="snippet-favicon"><img aria-hidden="true" height="18" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAABs0lEQVR4AWL4//8/RRjO8Iucx+noO0MWUDo16FYABMGP6ZfUcRnWtm27jVPbtm3bttuH2t3eFPcY9pLz7NxiLjCyVd87pKnHyqXyxtCs8APd0rnyxiu4qSeA3QEDrAwBDrT1s1Rc/OrjLZwqVmOSu6+Lamcpp2KKMA9PH1BYXMe1mUP5qotvXTywsOEEYHXxrY+3cqk6TMkYpNr2FeoY3KIr0RPtn9wQ2unlA+GMkRw6+9TFw4YTwDUzx/JVvARj9KaedXRO8P5B1Du2S32smzqUrcKGEyA+uAgQjKX7zf0boWHGfn71jIKj2689gxp7OAGShNcBUmLMPVjZuiKcA2vuWHHDCQxMCz629kXAIU4ApY15QwggAFbfOP9DhgBJ+nWVJ1AZAfICAj1pAlY6hCADZnveQf7bQIwzVONGJonhLIlS9gr5mFg44Xd+4S3XHoGNPdJl1INIwKyEgHckEhgTe1bGiFY9GSFBYUwLh1IkiJUbY407E7syBSFxKTszEoiE/YdrgCEayDmtaJwCI9uu8TKMuZSVfSa4BpGgzvomBR/INhLGzrqDotp01ZR8pn/1L0JN9d9XNyx0AAAAAElFTkSuQmCC" width="18" alt="favicon"></div>';
                        if (get_site_icon_url(32)) {
                            $siteicon = '<div class="snippet-favicon"><img aria-hidden="true" height="18" src="' . get_site_icon_url(32) . '" width="18" alt="favicon"/></div>';
                        }
                    ?>
					<div class="wrap-snippet">
                        <div class="wrap-m-icon-permalink"><?php echo $siteicon . $gp_permalink; ?></div>
						<div class="snippet-title">{{{ data.title }}}</div>
                        <div class="wrap-snippet-mobile">
                            <div class="wrap-meta-desc">
                                <?php echo seopress_display_date_snippet(); ?>
						        <div class="snippet-description-default">{{{ data.description }}}</div>
                            </div>
                            <div class="wrap-post-thumb">
                                <?php the_post_thumbnail('full', ['class' => 'snippet-post-thumb']); ?>
                            </div>
                        </div>
					</div>
				</div>
			</div>
		<# } #>
		<?php
    }
}
