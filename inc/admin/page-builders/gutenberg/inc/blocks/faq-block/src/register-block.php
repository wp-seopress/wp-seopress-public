<?php

if (! defined('ABSPATH')) {
    die();
}

function seopress_register_block_faq($asset_file)
{
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

    register_block_type('wpseopress/faq-block', [
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
            ),
            'showAccordion' => array(
                'type' => 'boolean',
                'default' => false
            )
        ),
        'render_callback' => 'seopress_block_faq_render_frontend',
    ]);
}

function seopress_block_faq_render_frontend($attributes)
{
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

    switch ($attributes['listStyle']) {
        case 'ul':
            $listStyleTag = '<ul class="wpseopress-faqs">';
            $listStyleCloseTag = '</ul>';
            $listItemStyle = '<li class="wpseopress-faq">';
            $listItemStyleClosingTag = '</li>';
            break;
        case 'ol':
            $listStyleTag = '<ol class="wpseopress-faqs">';
            $listStyleCloseTag = '</ol>';
            $listItemStyle = '<li class="wpseopress-faq">';
            $listItemStyleClosingTag = '</li>';
            break;
        default:
            $listStyleTag = '<div class="wpseopress-faqs">';
            $listStyleCloseTag = '</div>';
            $listItemStyle = '<div class="wpseopress-faq">';
            $listItemStyleClosingTag = '</div>';
            break;
    }

    $entities = [];

    ob_start(); ?>
	<?php echo $listStyleTag; ?>
		<?php
            $i = 1;
            foreach ($attributes['faqs'] as $faq) :

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
                    //Load our inline CSS only once
                    if (!isset($css)) {
                        $css = '<style>.wpseopress-hide {display: none;}.wpseopress-accordion-button{width:100%}</style>';
                        $css = apply_filters( 'seopress_faq_block_inline_css', $css );
                        echo $css;
                    }
                    //Our simple accordion JS
                    wp_enqueue_script('seopress-accordion', plugins_url('accordion.js', __FILE__), '', SEOPRESS_VERSION, true);
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
                                <img src="<?php echo $image_url; ?>" alt="<?php echo $image_alt; ?>">
                            </div>
                        <?php endif; ?>
                        <?php if (! empty($faq['answer'])): ?>
                            <p class="wpseopress-faq-answer-desc"><?php echo $faq['answer']; ?></p>
                        <?php endif; ?>
                    </div>
                <?php echo $listItemStyleClosingTag;
                $i = $i++;
                ?>
			<?php endforeach; ?>
	<?php echo $listStyleCloseTag;

    //FAQ Schema
    $seopress_get_toggle_rich_snippets_option = get_option('seopress_toggle');
    $seopress_get_toggle_rich_snippets_option = isset($seopress_get_toggle_rich_snippets_option['toggle-rich-snippets']) ? $seopress_get_toggle_rich_snippets_option['toggle-rich-snippets'] : '0';
    if ('0' != $seopress_get_toggle_rich_snippets_option && (int) $attributes['showFAQScheme']) {
        $schema = '<script type="application/ld+json">
				{
				"@context": "https://schema.org",
				"@type": "FAQPage",
				"mainEntity": '. json_encode($entities) . '
				}
			</script>';

        echo apply_filters('seopress_schemas_faq_html', $schema);
    }
    $html = apply_filters('seopress_faq_block_html', ob_get_clean());
    return $html;
}
