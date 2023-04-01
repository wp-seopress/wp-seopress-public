<?php

namespace SEOPress\JsonSchemas;

if ( ! defined('ABSPATH')) {
    exit;
}

use SEOPress\Helpers\RichSnippetType;
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
     * @return array
     */
    public function getJsonData($context = null) {
        $data = $this->getArrayJson();

        $typeSchema = isset($context['type']) ? $context['type'] : RichSnippetType::DEFAULT_SNIPPET;

        switch ($typeSchema) {
            default:
                $variables = [
                    'type'                   => '%%knowledge_type%%',
                    'name'                   => '%%social_knowledge_name%%',
                    'url'                    => '%%siteurl%%',
                    'logo'                   => '%%social_knowledge_image%%',
                    'account_facebook'       => '%%social_account_facebook%%',
                    'account_twitter'        => '%%social_account_twitter%%',
                    'account_pinterest'      => '%%social_account_pinterest%%',
                    'account_instagram'      => '%%social_account_instagram%%',
                    'account_youtube'        => '%%social_account_youtube%%',
                    'account_linkedin'       => '%%social_account_linkedin%%',
                    'account_extra'          => '%%social_account_extra%%',
                ];
                break;

            case RichSnippetType::SUB_TYPE:
                $variables = isset($context['variables']) ? $context['variables'] : [];
                break;
        }

        $data = seopress_get_service('VariablesToString')->replaceDataToString($data, $variables);

        $type = seopress_get_service('SocialOption')->getSocialKnowledgeType();

        if ('Organization' === $type) {
            // Use "contactPoint"
            $schema = seopress_get_service('JsonSchemaGenerator')->getJsonFromSchema(ContactPoint::NAME, $context, ['remove_empty'=> true]);
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

            // Create a new empty array to store the updated values
            $newArray = [];

            // Loop through the original array
            foreach ($data['sameAs'] as $value) {
                // Check if the value contains a line break
                if (strpos($value, PHP_EOL) !== false) {
                    // If it does, split the value into an array based on the line breaks
                    $splitValues = explode(PHP_EOL, $value);
                    // Add each split value to the new array
                    foreach ($splitValues as $splitValue) {
                        $splitValue = str_replace(["\r", "\n"], '', $splitValue);
                        $newArray[] = $splitValue;
                    }
                } else {
                    // If it doesn't, simply add the original value to the new array
                    $newArray[] = $value;
                }
            }

            $data['sameAs'] = $newArray;

            if (empty($data['sameAs'])) {
                unset($data['sameAs']);
            }
        }

        return parent::cleanValues($data);
    }
}
