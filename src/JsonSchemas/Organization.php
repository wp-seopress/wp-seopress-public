<?php

namespace SEOPress\JsonSchemas;

if ( ! defined('ABSPATH')) {
    exit;
}

use SEOPress\Models\GetJsonData;
use SEOPress\Models\JsonSchemaValue;

class Organization extends JsonSchemaValue implements GetJsonData {
    const NAME = 'organization';

    protected function getName() {
        return self::NAME;
    }

    /**
     * @since 4.5.0
     *
     * @param array $context
     *
     * @return array|string
     */
    public function getJsonData($context = null) {
        $data = $this->getArrayJson();

        $type = seopress_get_service('SocialOption')->getSocialKnowledgeType();
        if ('Organization' === $type) {
            // Use "contactPoint"
            $schema = seopress_get_service('JsonSchemaGenerator')->getJsonFromSchema(ContactPoint::NAME, [], ['remove_empty'=> true]);
            if (count($schema) > 1) {
                $data['contactPoint'][] = $schema;
            }
        }

        // Not Organization -> Like Is Person
        else {
            // Remove "logo"
            if (array_key_exists('logo', $data)) {
                unset($data['logo']);
            }
        }

        return apply_filters('seopress_get_json_data_organization', $data);
    }

    /**
     * @since 4.5.0
     *
     * @param  $data
     *
     * @return array
     */
    public function cleanValues($data) {
        if (isset($data['sameAs'])) {
            $data['sameAs'] = array_values($data['sameAs']);

            if (empty($data['sameAs'])) {
                unset($data['sameAs']);
            }
        }

        return parent::cleanValues($data);
    }
}
