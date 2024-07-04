<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

function seopress_block_faq_render_frontend( $attributes ){
    if (is_admin() || defined('REST_REQUEST')) {
        return;
    }

    switch ($attributes['titleWrapper']) {
        case 'h2':
            $titleTag = '<h2 class="wpseopress-faq-question">';
            $titleCloseTag = '</h2>';
            break;
        case 'h3':
            $titleTag = '<h3 class="wpseopress-faq-question">';
            $titleCloseTag = '</h3>';
            break;
        case 'h4':
            $titleTag = '<h4 class="wpseopress-faq-question">';
            $titleCloseTag = '</h4>';
            break;
        case 'h5':
            $titleTag = '<h5 class="wpseopress-faq-question">';
            $titleCloseTag = '</h5>';
            break;
        case 'h6':
            $titleTag = '<h6 class="wpseopress-faq-question">';
            $titleCloseTag = '</h6>';
            break;
        case 'p':
            $titleTag = '<p class="wpseopress-faq-question">';
            $titleCloseTag = '</p>';
            break;
        default:
            $titleTag = '<div class="wpseopress-faq-question">';
            $titleCloseTag = '</div>';
            break;
    }

    $wrapper_attributes = get_block_wrapper_attributes([ 'class'=> 'wpseopress-faqs' ]);
    switch ($attributes['listStyle']) {
        case 'ul':
            $listStyleTag = sprintf( '<ul %s>', $wrapper_attributes);
            $listStyleCloseTag = '</ul>';
            $listItemStyle = '<li class="wpseopress-faq">';
            $listItemStyleClosingTag = '</li>';
            break;
        case 'ol':
            $listStyleTag = sprintf( '<ol %s>', $wrapper_attributes);
            $listStyleCloseTag = '</ol>';
            $listItemStyle = '<li class="wpseopress-faq">';
            $listItemStyleClosingTag = '</li>';
            break;
        default:
            $listStyleTag = sprintf( '<div %s>', $wrapper_attributes);
            $listStyleCloseTag = '</div>';
            $listItemStyle = '<div class="wpseopress-faq">';
            $listItemStyleClosingTag = '</div>';
            break;
    }

    $entities = [];

    ob_start(); ?>
	<?php echo $listStyleTag; ?>
		<?php
            foreach ($attributes['faqs'] as $faq) :
                $i = wp_rand();
                if (empty($faq['question'])) {
                    continue;
                }

                $entity = [
                    '@type' => 'Question',
                    'name' => $faq['question'],
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => ! empty($faq['answer']) ? $faq['answer'] : ''
                    ]
                ];
                $entities[] = $entity;

                $accordion = $attributes['showAccordion'];

                if ($accordion) {
                    // Load our inline CSS only once
                    if (!isset($css)) {
                        $css = '<style>.wpseopress-hide {display: none;}.wpseopress-accordion-button{width:100%}</style>';
                        $css = apply_filters( 'seopress_faq_block_inline_css', $css );
                        echo $css;
                    }
                }

                $image = '';
                $image_alt = '';
                if ( isset( $faq['image'] ) && is_int( $faq['image'] ) ) {
                    $image = wp_get_attachment_image_src( $faq['image'], $attributes['imageSize'] );
                    $image_alt = get_post_meta($faq['image'], '_wp_attachment_image_alt', true);
                }

                $image_url = '';
                if ( isset( $image ) && ! empty( $image ) ) {
                    $image_url = $image[0];
                } ?>
                <?php echo $listItemStyle; ?>
                    <?php if ($accordion) { ?>
                        <div id="wpseopress-faq-title-<?php echo $i; ?>" class="wpseopress-wrap-faq-question">
                            <button class="wpseopress-accordion-button" type="button" aria-expanded="false" aria-controls="wpseopress-faq-answer-<?php echo $i; ?>">
                    <?php } ?>
                    <?php echo $titleTag . $faq['question'] . $titleCloseTag; ?>
                    <?php if ($accordion) { ?>
                            </button>
                        </div>
                    <?php } ?>

                    <?php if ($accordion) { ?>
                        <div id="wpseopress-faq-answer-<?php echo $i; ?>" class="wpseopress-faq-answer wpseopress-hide" aria-labelledby="wpseopress-faq-title-<?php echo $i; ?>">
                    <?php } else { ?>
                        <div class="wpseopress-faq-answer">
                    <?php } ?>
                    <?php if (! empty($image_url)): ?>
                            <div class="wpseopress-faq-answer-image">
                                <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($image_alt); ?>">
                            </div>
                        <?php endif; ?>
                        <?php if (! empty($faq['answer'])): ?>
                            <p class="wpseopress-faq-answer-desc"><?php echo $faq['answer']; ?></p>
                        <?php endif; ?>
                    </div>
                <?php echo $listItemStyleClosingTag;
                ?>
			<?php endforeach; ?>
	<?php echo $listStyleCloseTag;

    // FAQ Schema
    if ( (bool) $attributes['isProActive'] && (int) $attributes['showFAQScheme'] ) {
        $schema = '<script type="application/ld+json">
				{
				"@context": "https://schema.org",
				"@type": "FAQPage",
				"mainEntity": '. wp_json_encode($entities) . '
				}
			</script>';

        echo apply_filters('seopress_schemas_faq_html', $schema);
    }
    $html = apply_filters('seopress_faq_block_html', ob_get_clean());
    return $html;
}
