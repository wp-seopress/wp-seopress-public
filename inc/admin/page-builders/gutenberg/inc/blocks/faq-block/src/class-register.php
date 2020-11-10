<?php
namespace WPSeoPressGutenbergAddon\FAQBlock;

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

class Register {
	use \WPSeoPressGutenbergAddon\Singleton;

	/**
	 * Initialize class
	 *
	 * @return  void  
	 */
	private function _initialize() {
		$seopress_get_toggle_rich_snippets_option = get_option('seopress_toggle');
		$this->structured_data_enabled = isset( $seopress_get_toggle_rich_snippets_option['toggle-rich-snippets'] ) ? $seopress_get_toggle_rich_snippets_option['toggle-rich-snippets'] : '0';
		
		add_action( 'init', [ $this, 'register_blocks' ] );
	}

	/**
	 * Register blocks
	 *
	 * @return  void  
	 */
	public function register_blocks() {
		$asset_file = include SEOPRESS_GUTENBERG_ADDON_DIR . 'inc/blocks/faq-block/build/index.asset.php';
		
		$this->_register_faq_block( $asset_file );
	}

	/**
	 * Register FAQ Block
	 *
	 * @param   array  $asset_file  
	 *
	 * @return  vodi               
	 */
	private function _register_faq_block( $asset_file ) {
		wp_register_script(
			'wp-seopress-gutenberg-faq-block',
			SEOPRESS_GUTENBERG_ADDON_URL . 'inc/blocks/faq-block/build/index.js',
			$asset_file['dependencies'],
			$asset_file['version']
		);

		wp_register_style(
			'wp-seopress-gutenberg-faq-block',
			SEOPRESS_GUTENBERG_ADDON_URL . 'inc/blocks/faq-block/build/index.css',
			array('wp-edit-blocks'),
			$asset_file['version']
		);

		register_block_type( 'wpseopress/faq-block', [
			'editor_script' => 'wp-seopress-gutenberg-faq-block',
			'editor_style' => 'wp-seopress-gutenberg-faq-block',
			'attributes' => array(
				'faqs' => array(
					'type'    => 'array',
					'default' => array( '' ),
					'items'   => array(
						'type' => 'object',
					),
				),
				'listStyle' => array(
					'type' => 'string',
					'default' => 'none'
				),
				'titleWrapper' => array(
					'type' => 'string',
					'default' => 'p'
				),
				'imageSize' => array(
					'type' => 'string',
					'default' => 'thumbnail'
				),
				'showFAQScheme' => array(
					'type' => 'boolean',
					'default' => false
				)
			),
			'render_callback' => array( $this, 'render_frontend' ),
		] );
	}

	/**
	 * Render the frontend for the block
	 *
	 * @param   array  $attributes  
	 *
	 * @return  string               
	 */
	public function render_frontend( $attributes ) {
		if ( is_admin() || defined( 'REST_REQUEST' ) ) {
			return;
		}

		switch ( $attributes['titleWrapper'] ) {
			case 'h2': $titleTag = '<h2 class="wpseopress-faq-question">'; $titleCloseTag = '</h2>'; break;
			case 'h3': $titleTag = '<h3 class="wpseopress-faq-question">'; $titleCloseTag = '</h3>'; break;
			case 'h4': $titleTag = '<h4 class="wpseopress-faq-question">'; $titleCloseTag = '</h4>'; break;
			case 'h5': $titleTag = '<h5 class="wpseopress-faq-question">'; $titleCloseTag = '</h5>'; break;
			case 'h6': $titleTag = '<h6 class="wpseopress-faq-question">'; $titleCloseTag = '</h6>'; break;
			case 'p': $titleTag = '<p class="wpseopress-faq-question">'; $titleCloseTag = '</p>'; break;
			case 'div': $titleTag = '<div class="wpseopress-faq-question">'; $titleCloseTag = '</div>'; break;
		}
		
		switch ( $attributes['listStyle'] ) {
			case 'ul': $listStyleTag = '<ul class="wpseopress-faqs">'; $listStyleCloseTag = '</ul>'; $listItemStyle = '<li class="wpseopress-faq">'; $listItemStyleClosingTag = '</li>'; break;
			case 'ol': $listStyleTag = '<ol class="wpseopress-faqs">'; $listStyleCloseTag = '</ol>'; $listItemStyle = '<li class="wpseopress-faq">'; $listItemStyleClosingTag = '</li>'; break;
			case 'div': $listStyleTag = '<div class="wpseopress-faqs">'; $listStyleCloseTag = '</div>'; $listItemStyle = '<div class="wpseopress-faq">'; $listItemStyleClosingTag = '</div>'; break;
		}

		$entities = [];
		
		ob_start(); ?>
		<?php echo $listStyleTag; ?>
			<?php 
				foreach ( $attributes['faqs'] as $faq ) : 
					$entity = [
						'@type' => 'Question',
						'name' => $faq['question'],
						'acceptedAnswer' => [
							'@type' => 'Answer',
							'text' => $faq['answer']
						]
					];
					$entities[] = $entity;

					$image = wp_get_attachment_image_src( $faq['image'], $attributes['imageSize'] );
					$image_url = '';
					if ( $image ) {
						$image_url = $image[0];
					}
					?>
					<?php echo $listItemStyle; ?>
						<?php echo $titleTag . $faq['question'] . $titleCloseTag; ?>
						<div class="wpseopress-faq-answer">
							<div class="wpseopress-faq-answer-image">
								<?php if ( ! empty( $image_url ) ): ?>
									<img src="<?php echo $image_url; ?>" alt="Image">
								<?php endif; ?>
							</div>
							<div class="wpseopress-faq-answer-desc"><?php echo $faq['answer']; ?></div>
						</div>
					<?php echo $listItemStyleClosingTag; ?>
				<?php endforeach;
			?>
		<?php echo $listStyleCloseTag; ?>
		<?php 
			if ( '0' != $this->structured_data_enabled && (int) $attributes['showFAQScheme'] ) {
				$schema = '<script type="application/ld+json">
					{
					"@context": "https://schema.org",
					"@type": "FAQPage",
					"mainEntity": '. json_encode( $entities ) . '
					}
				</script>';
				
				echo apply_filters( 'seopress_schemas_faq_html', $schema );
			}
		?>
		<?php return ob_get_clean();
	}
}
