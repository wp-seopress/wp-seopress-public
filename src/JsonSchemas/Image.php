<?php

namespace SEOPress\JsonSchemas;

if ( ! defined('ABSPATH')) {
    exit;
}

use SEOPress\Models\GetJsonData;
use SEOPress\Models\JsonSchemaValue;

class Image extends JsonSchemaValue implements GetJsonData {
    const NAME = 'image';

    protected function getName() {
        return self::NAME;
    }

    /**
     * @since 4.6.0
     *
     * @param array $context
     *
     * @return string|array
     */
    public function getJsonData($context = null) {
        $data = $this->getArrayJson();

        return apply_filters('seopress_get_json_data_image', $data);
    }
}
