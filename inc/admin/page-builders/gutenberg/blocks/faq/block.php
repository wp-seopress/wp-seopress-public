<?php
/**
 * FAQ Block
 *
 * @package Gutenberg
 */

defined( 'ABSPATH' ) || exit( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

/**
 * FAQ Block Render Frontend
 *
 * @param array $attributes Attributes.
 *
 * @return string HTML.
 */
function seopress_block_faq_render_frontend( $attributes ) {
	if ( is_admin() || defined( 'REST_REQUEST' ) ) {
		return;
	}

	switch ( $attributes['titleWrapper'] ) {
		case 'h2':
			$title_tag       = '<h2 class="wpseopress-faq-question">';
			$title_close_tag = '</h2>';
			break;
		case 'h3':
			$title_tag       = '<h3 class="wpseopress-faq-question">';
			$title_close_tag = '</h3>';
			break;
		case 'h4':
			$title_tag       = '<h4 class="wpseopress-faq-question">';
			$title_close_tag = '</h4>';
			break;
		case 'h5':
			$title_tag       = '<h5 class="wpseopress-faq-question">';
			$title_close_tag = '</h5>';
			break;
		case 'h6':
			$title_tag       = '<h6 class="wpseopress-faq-question">';
			$title_close_tag = '</h6>';
			break;
		case 'p':
			$title_tag       = '<p class="wpseopress-faq-question">';
			$title_close_tag = '</p>';
			break;
		default:
			$title_tag       = '<div class="wpseopress-faq-question">';
			$title_close_tag = '</div>';
			break;
	}

	$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'wpseopress-faqs' ) );
	switch ( $attributes['listStyle'] ) {
		case 'ul':
			$list_style_tag              = sprintf( '<ul %s>', $wrapper_attributes );
			$list_style_close_tag        = '</ul>';
			$list_item_style             = '<li class="wpseopress-faq">';
			$list_item_style_closing_tag = '</li>';
			break;
		case 'ol':
			$list_style_tag              = sprintf( '<ol %s>', $wrapper_attributes );
			$list_style_close_tag        = '</ol>';
			$list_item_style             = '<li class="wpseopress-faq">';
			$list_item_style_closing_tag = '</li>';
			break;
		default:
			$list_style_tag              = sprintf( '<div %s>', $wrapper_attributes );
			$list_style_close_tag        = '</div>';
			$list_item_style             = '<div class="wpseopress-faq">';
			$list_item_style_closing_tag = '</div>';
			break;
	}

	$entities = array();

	ob_start(); ?>
	<?php echo $list_style_tag; ?>
		<?php
		foreach ( $attributes['faqs'] as $faq ) :
			$i = wp_rand();
			if ( empty( $faq['question'] ) ) {
				continue;
			}

			$entity     = array(
				'@type'          => 'Question',
				'name'           => $faq['question'],
				'acceptedAnswer' => array(
					'@type' => 'Answer',
					'text'  => ! empty( $faq['answer'] ) ? $faq['answer'] : '',
				),
			);
			$entities[] = $entity;

			$accordion = $attributes['showAccordion'];

			if ( $accordion ) {
				// Load our inline CSS only once.
				if ( ! isset( $css ) ) {
					$css = '<style>.wpseopress-hide {display: none;}.wpseopress-accordion-button{width:100%}</style>';
					$css = apply_filters( 'seopress_faq_block_inline_css', $css );
					echo $css;
				}
			}

			$image     = '';
			$image_alt = '';
			if ( isset( $faq['image'] ) && is_int( $faq['image'] ) ) {
				$image     = wp_get_attachment_image_src( $faq['image'], $attributes['imageSize'] );
				$image_alt = get_post_meta( $faq['image'], '_wp_attachment_image_alt', true );
			}

			$image_url = '';
			if ( isset( $image ) && ! empty( $image ) ) {
				$image_url = $image[0];
			}
			?>
				<?php echo $list_item_style; ?>
					<?php if ( $accordion ) { ?>
						<div id="wpseopress-faq-title-<?php echo $i; ?>" class="wpseopress-wrap-faq-question">
							<button class="wpseopress-accordion-button" type="button" aria-expanded="false" aria-controls="wpseopress-faq-answer-<?php echo $i; ?>">
					<?php } ?>
					<?php echo $title_tag . $faq['question'] . $title_close_tag; ?>
					<?php if ( $accordion ) { ?>
							</button>
						</div>
					<?php } ?>

					<?php if ( $accordion ) { ?>
						<div id="wpseopress-faq-answer-<?php echo $i; ?>" class="wpseopress-faq-answer wpseopress-hide" aria-labelledby="wpseopress-faq-title-<?php echo $i; ?>">
					<?php } else { ?>
						<div class="wpseopress-faq-answer">
					<?php } ?>
					<?php if ( ! empty( $image_url ) ) : ?>
							<div class="wpseopress-faq-answer-image">
								<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $image_alt ); ?>">
							</div>
						<?php endif; ?>
					<?php if ( ! empty( $faq['answer'] ) ) : ?>
							<p class="wpseopress-faq-answer-desc"><?php echo $faq['answer']; ?></p>
						<?php endif; ?>
					</div>
				<?php
				echo $list_item_style_closing_tag;
				?>
			<?php endforeach; ?>
	<?php
	echo $list_style_close_tag;

	// FAQ Schema.
	if ( (bool) $attributes['isProActive'] && (int) $attributes['showFAQScheme'] ) {
		$schema = '<script type="application/ld+json">
				{
				"@context": "https://schema.org",
				"@type": "FAQPage",
				"mainEntity": ' . wp_json_encode( $entities ) . '
				}
			</script>';

		echo apply_filters( 'seopress_schemas_faq_html', $schema );
	}
	$html = apply_filters( 'seopress_faq_block_html', ob_get_clean() );
	return $html;
}
