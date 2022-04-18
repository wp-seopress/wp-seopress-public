<?php

namespace SEOPress\JsonSchemas;

if ( ! defined('ABSPATH')) {
    exit;
}

use SEOPress\Models\GetJsonData;
use SEOPress\Models\JsonSchemaValue;

class ContactPoint extends JsonSchemaValue implements GetJsonData {
    const NAME = 'contact-point';

    protected function getName() {
        return self::NAME;
    }

    /**
     * @since 4.5.0
     *
     * @param array $context
     *
     * @return string|array
     */
    public function getJsonData($context = null) {
        $data = $this->getArrayJson();

        return apply_filters('seopress_get_json_data_contact_point', $data);
    }
}
