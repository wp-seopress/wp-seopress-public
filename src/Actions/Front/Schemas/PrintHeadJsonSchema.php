<?php

namespace SEOPress\Actions\Front\Schemas;

if ( ! defined('ABSPATH')) {
    exit;
}

use SEOPress\Core\Hooks\ExecuteHooksFrontend;

class PrintHeadJsonSchema implements ExecuteHooksFrontend {
    public function hooks() {
        if (apply_filters('seopress_old_social_accounts_jsonld_hook', false)) {
            return;
        }

        add_action('wp_head', [$this, 'render'], 2);
    }

    public function render() {
        if ('none' === seopress_get_service('SocialOption')->getSocialKnowledgeType()) {
            return;
        }

        $jsons = seopress_get_service('JsonSchemaGenerator')->getJsonsEncoded([
            'organization',
        ]); ?>
        <script type="application/ld+json">
            <?php echo apply_filters('seopress_schemas_organization_html', $jsons[0]); ?>
        </script>
        <?php
    }
}
