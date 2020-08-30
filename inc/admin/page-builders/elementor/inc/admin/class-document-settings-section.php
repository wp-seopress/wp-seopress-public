<?php
namespace WPSeoPressElementorAddon\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

class Document_Settings_Section {
	use \WPSeoPressElementorAddon\Singleton;

	/**
	 * Initialize class
	 *
	 * @return  void
	 */
	private function _initialize() {
		add_action( 'elementor/documents/register_controls', [ $this, 'add_wp_seopress_section_to_document_settings' ] );
		add_action( 'elementor/document/after_save', [ $this, 'on_save' ], 99, 2 );
		add_action( 'seopress/page-builders/elementor/save_meta', [ $this, 'on_seopress_meta_save' ], 99 );
    }

	/**
	 * Add WP SeoPress section under document settings
	 * 
	 * @param \Elementor\Core\Base\Document $document
	 * 
	 * @return void
	 */
    public function add_wp_seopress_section_to_document_settings( \Elementor\Core\Base\Document $document ) {
		$post_id = $document->get_main_id();

		$this->_add_title_section( $document, $post_id );
		$this->_add_advanced_section( $document, $post_id );
		$this->_add_social_section( $document, $post_id );
		$this->_add_redirection_section( $document, $post_id );
		$this->_add_content_analysis_section( $document, $post_id );
	}
	
	/**
	 * Add title section
	 *
	 * @param   \Elementor\Core\Base\Document $document
	 * @param   int $post_id
	 * 
	 * @return  void
	 */
	private function _add_title_section( $document, $post_id ) {
		$document->start_controls_section(
			'seopress_title_settings',
			[
				'label' => __( 'SEO Title / Description', 'wp-seopress' ),
				'tab' => \Elementor\Controls_Manager::TAB_SETTINGS
			]
		);

		$title = get_post_meta( $post_id, '_seopress_titles_title', true );
		$desc = get_post_meta( $post_id, '_seopress_titles_desc', true );

		$document->add_control(
			'_seopress_titles_title',
			[
				'label' => __( 'Title', 'wp-seopress' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
				'separator' => 'none', 
				'default' => $title ? $title : ''
			]
		);

		$document->add_control(
			'_seopress_titles_desc',
			[
				'label' => __( 'Meta Description', 'wp-seopress' ),
				'type' => \Elementor\Controls_Manager::TEXTAREA,
				'label_block' => true,
				'separator' => 'none',
				'default' => $desc ? $desc : ''
			]
		);

		$document->end_controls_section();
	}

	/**
	 * Add advanced section
	 *
	 * @param   \Elementor\Core\Base\Document $document
	 * @param   int $post_id
	 *
	 * @return  void
	 */
	private function _add_advanced_section( $document, $post_id ) {
		$document->start_controls_section(
			'_seopress_advanced_settings',
			[
				'label' => __( 'SEO Advanced', 'wp-seopress' ),
				'tab' => \Elementor\Controls_Manager::TAB_SETTINGS
			]
		);

		$robots_index = get_post_meta( $post_id, '_seopress_robots_index', true );
		$robots_follow = get_post_meta( $post_id, '_seopress_robots_follow', true );
		$robots_odp = get_post_meta( $post_id, '_seopress_robots_odp', true );
		$robots_imageindex = get_post_meta( $post_id, '_seopress_robots_imageindex', true );
		$robots_archive = get_post_meta( $post_id, '_seopress_robots_archive', true );
		$robots_snippet = get_post_meta( $post_id, '_seopress_robots_snippet', true );
		$robots_canonical = get_post_meta( $post_id, '_seopress_robots_canonical', true );

		$document->add_control(
			'_seopress_robots_index',
			[
				'label' => __( 'Do not display this page in search engine results / XML - HTML sitemaps (noindex)', 'wp-seopress' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_block' => true,
				'separator' => 'none',
				'default' => 'yes' === $robots_index ? 'yes' : ''
			]
		);

		$document->add_control(
			'_seopress_robots_follow',
			[
				'label' => __( 'Do not follow links for this page (nofollow)', 'wp-seopress' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_block' => true,
				'separator' => 'none',
				'default' => 'yes' === $robots_follow ? 'yes' : ''
			]
		);

		$document->add_control(
			'_seopress_robots_odp',
			[
				'label' => __( 'Do not use Open Directory project metadata for titles or excerpts for this page (noodp)', 'wp-seopress' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_block' => true,
				'separator' => 'none',
				'default' => 'yes' === $robots_odp ? 'yes' : ''
			]
		);
		
		$document->add_control(
			'_seopress_robots_imageindex',
			[
				'label' => __( 'Do not index images for this page (noimageindex)', 'wp-seopress' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_block' => true,
				'separator' => 'none',
				'default' => 'yes' === $robots_imageindex ? 'yes' : ''
			]
		);

		$document->add_control(
			'_seopress_robots_archive',
			[
				'label' => __( 'Do not display a "Cached" link in the Google search results (noarchive)', 'wp-seopress' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_block' => true,
				'separator' => 'none',
				'default' => 'yes' === $robots_archive ? 'yes' : ''
			]
		);

		$document->add_control(
			'_seopress_robots_snippet',
			[
				'label' => __( 'Do not display a description in search results for this page (nosnippet)', 'wp-seopress' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_block' => true,
				'separator' => 'none',
				'default' => 'yes' === $robots_snippet ? 'yes' : ''
			]
		);

		$document->add_control(
			'_seopress_robots_canonical',
			[
				'label' => __( 'Canonical URL', 'wp-seopress' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
				'separator' => 'none',
				'default' => $robots_canonical ? $robots_canonical : ''
			]
		);

		$document->end_controls_section();
	}

	/**
	 * Add social section
	 *
	 * @param   \Elementor\Core\Base\Document $document
	 * @param   int $post_id
	 *
	 * @return  void
	 */
	private function _add_social_section( $document, $post_id ) {
		$document->start_controls_section(
			'_seopress_social_settings',
			[
				'label' => __( 'SEO Social', 'wp-seopress' ),
				'tab' => \Elementor\Controls_Manager::TAB_SETTINGS
			]
		);

		$fb_title = get_post_meta( $post_id, '_seopress_social_fb_title', true );
		$fb_desc = get_post_meta( $post_id, '_seopress_social_fb_desc', true );
		$fb_image = get_post_meta( $post_id, '_seopress_social_fb_img', true );
		$twitter_title = get_post_meta( $post_id, '_seopress_social_twitter_title', true );
		$twitter_desc = get_post_meta( $post_id, '_seopress_social_twitter_desc', true );
		$twitter_image = get_post_meta( $post_id, '_seopress_social_twitter_img', true );

		$default_preview_title = get_the_title( $post_id );
		$default_preview_desc = substr( strip_tags( get_the_content( null, false, $post_id ) ), 0, 140 );

		$document->add_control(
			'_seopress_social_fb_title',
			[
				'label' => __( 'Facebook Title', 'wp-seopress' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
				'separator' => 'none',
				'default' => $fb_title ? $fb_title : ''
			]
		);

		$document->add_control(
			'_seopress_social_fb_desc',
			[
				'label' => __( 'Facebook description', 'wp-seopress' ),
				'type' => \Elementor\Controls_Manager::TEXTAREA,
				'label_block' => true,
				'separator' => 'none',
				'default' => $fb_desc ? $fb_desc : ''
			]
		);

		$document->add_control(
			'_seopress_social_fb_img',
			[
				'label' => __( 'Facebook Thumbnail', 'wp-seopress' ),
				'type' => \Elementor\Controls_Manager::MEDIA,
				'label_block' => true,
				'separator' => 'none',
				'default' => [
					'url' => $fb_image ? $fb_image : ''
				]
			]
		);

		$document->add_control(
			'social_preview_facebook',
			[
				'label' => __( 'Facebook Preview', 'wp-seopress' ),
				'type' => 'seopress-social-preview',
				'label_block' => true,
				'separator' => 'none',
				'network' => 'facebook',
				'image' => $fb_image ? $fb_image : '',
				'title' => $fb_title ? $fb_title : $default_preview_title,
				'description' => $fb_desc ? $fb_desc : $default_preview_desc
			]
		);

		$document->add_control(
			'_seopress_social_twitter_title',
			[
				'label' => __( 'Twitter Title', 'wp-seopress' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
				'separator' => 'none',
				'default' => $twitter_title ? $twitter_title : ''
			]
		);

		$document->add_control(
			'_seopress_social_twitter_desc',
			[
				'label' => __( 'Twitter description', 'wp-seopress' ),
				'type' => \Elementor\Controls_Manager::TEXTAREA,
				'label_block' => true,
				'separator' => 'none',
				'default' => $twitter_desc ? $twitter_desc : ''
			]
		);

		$document->add_control(
			'_seopress_social_twitter_img',
			[
				'label' => __( 'Twitter Thumbnail', 'wp-seopress' ),
				'type' => \Elementor\Controls_Manager::MEDIA,
				'label_block' => true,
				'separator' => 'none',
				'default' => [
					'url' => $twitter_image ? $twitter_image : ''
				]
			]
		);

		$document->add_control(
			'social_preview_twitter',
			[
				'label' => __( 'Twitter Preview', 'wp-seopress' ),
				'type' => 'seopress-social-preview',
				'label_block' => true,
				'separator' => 'none',
				'network' => 'twitter',
				'image' => $twitter_image ? $twitter_image : '',
				'title' => $twitter_title ? $twitter_title : $default_preview_title,
				'description' => $twitter_desc ? $twitter_desc : $default_preview_desc,
			]
		);

		$document->end_controls_section();
	}

	/**
	 * Add redirection section
	 *
	 * @param   \Elementor\Core\Base\Document $document
	 * @param   int $post_id
	 *
	 * @return  void
	 */
	private function _add_redirection_section( $document, $post_id ) {
		$document->start_controls_section(
			'seopress_redirection_settings',
			[
				'label' => __( 'SEO Redirection', 'wp-seopress' ),
				'tab' => \Elementor\Controls_Manager::TAB_SETTINGS
			]
		);

		$redirections_enabled = get_post_meta( $post_id, '_seopress_redirections_enabled', true );
		$redirections_type = get_post_meta( $post_id, '_seopress_redirections_type', true );
		$redirections_value = get_post_meta( $post_id, '_seopress_redirections_value', true );

		$document->add_control(
			'_seopress_redirections_enabled',
			[
				'label' => __( 'Enable redirection?', 'wp-seopress' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_block' => false,
				'separator' => 'none',
				'default' => 'yes' === $redirections_enabled ? 'yes' : ''
			]
		);

		$document->add_control(
			'_seopress_redirections_type',
			[
				'label' => __( 'URL redirection', 'wp-seopress' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'label_block' => true,
				'separator' => 'none',
				'options' => [
					301 => __( '301 Moved Permanently', 'wp-seopress' ),
					302 => __( '302 Found / Moved Temporarily', 'wp-seopress' ),
					307 => __( '307 Moved Temporarily', 'wp-seopress' ),
					410 => __( '410 Gone', 'wp-seopress' ),
					451 => __( '451 Unavailable For Legal Reasons', 'wp-seopress' )
				],
				'default' => $redirections_type ? (int) $redirections_type : 301
			]
		);

		$document->add_control(
			'_seopress_redirections_value',
			[
				'label' => __( 'Enter your new URL in absolute (eg: https://www.example.com/)', 'wp-seopress' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
				'separator' => 'none',
				'default' => $redirections_value ? $redirections_value : ''
			]
		);

		$document->end_controls_section();
	}

	/**
	 * Add Content analysis section
	 *
	 * @param   \Elementor\Core\Base\Document $document
	 * @param   int $post_id
	 *
	 * @return  void
	 */
	private function _add_content_analysis_section( $document, $post_id ) {
		$document->start_controls_section(
			'seopress_content_analysis_settings',
			[
				'label' => __( 'SEO Content Analysis', 'wp-seopress' ),
				'tab' => \Elementor\Controls_Manager::TAB_SETTINGS
			]
		);

		$document->add_control(
			'_seopress_analysis_target_kw',
			[
				'label' => __( 'Target keywords', 'wp-seopress' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'Separate target keywords with commas. Do not use spaces after the commas, unless you want to include them', 'wp-seopress' ),
				'label_block' => true,
				'separator' => 'none'
			]
		);

		$document->end_controls_section();
	}

	/**
	 * Before saving of the values in elementor
	 *
	 * @param \Elementor\Core\Base\Document $document
	 * @param array                         $data
	 *
	 * @return void
	 */
	public function on_save( \Elementor\Core\Base\Document $document, $data ) {
		$settings = ! empty( $data['settings'] ) ? $data['settings'] : array();

		if ( empty( $settings ) ) {
			return;
		}

		$post_id = $document->get_main_id();

		if ( ! $post_id ) {
			return;
		}

		$seopress_settings = array_filter(
			$settings,
			function( $key ) {
				return in_array( $key, $this->get_allowed_meta_keys(), true );
			},
			ARRAY_FILTER_USE_KEY
		);

		if ( empty( $seopress_settings ) ) {
			return;
		}

		if ( isset( $seopress_settings['_seopress_social_fb_img'] ) ) {
			$seopress_settings['_seopress_social_fb_img'] = $seopress_settings['_seopress_social_fb_img']['url'];
		}

		if ( isset( $seopress_settings['_seopress_social_twitter_img'] ) ) {
			$seopress_settings['_seopress_social_twitter_img'] = $seopress_settings['_seopress_social_twitter_img']['url'];
		}

		$seopress_settings = array_map( 'sanitize_text_field', $seopress_settings );

		$post_id = wp_update_post(
			array(
				'ID'         => $post_id,
				'meta_input' => $seopress_settings,
			)
		);

		if ( is_wp_error( $post_id ) ) {
			throw new \Exception( $post_id->get_error_message() );
		}
	}

	/**
	 * Save seopress meta to elementor
	 *
	 * @param   int $post_id
	 *
	 * @return  void
	 */
	public function on_seopress_meta_save( $post_id ) {
		$meta = get_post_meta( $post_id );

		$seopress_meta = array_filter(
			$meta,
			function( $key ) {
				return in_array( $key, $this->get_allowed_meta_keys(), true );
			},
			ARRAY_FILTER_USE_KEY
		);

		if ( empty( $seopress_meta ) ) {
			return;
		}

		$settings = array();

		foreach ( $seopress_meta as $key => $sm ) {
			$settings[ $key ] = maybe_unserialize( ! empty( $sm[0] ) ? $sm[0] : '' );
		}

		$seo_data['settings'] = $settings;

		if ( ! class_exists( '\Elementor\Core\Settings\Manager' ) ) {
			return;
		}

		$page_settings_manager = \Elementor\Core\Settings\Manager::get_settings_managers( 'page' );
		$page_settings_manager->save_settings( $settings, $post_id );
	}

	public function get_allowed_meta_keys() {
		return seopress_get_meta_helper()->get_meta_fields();
	}
}