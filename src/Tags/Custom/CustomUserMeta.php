<?php

namespace SEOPress\Tags\Custom;

if ( ! defined('ABSPATH')) {
    exit;
}

use SEOPress\Models\AbstractCustomTagValue;
use SEOPress\Models\GetTagValue;

class CustomUserMeta extends AbstractCustomTagValue implements GetTagValue {
    const CUSTOM_FORMAT = '_ucf_';
    const NAME          = '_ucf_your_user_meta';

    public static function getDescription() {
        return __('Custom User Meta', 'wp-seopress');
    }

    public function getValue($args = null) {

        $context = isset($args[0]) ? $args[0] : null;
        $tag     = isset($args[1]) ? $args[1] : null;
        $value   = '';
        if (null === $tag || ! $context) {
            return $value;
        }

        if ( ! $context['post']) {
            return $value;
        }
        $regex = $this->buildRegex(self::CUSTOM_FORMAT);

        preg_match($regex, $tag, $matches);

        if (empty($matches) || ! array_key_exists('field', $matches)) {
            return $value;
        }

        $field = $matches['field'];

        $userId = $context['user_id'] ?? get_current_user_id();

        if(!$userId || intval($userId) === 0){
            if(isset($context['is_author']) && isset($context['author']->ID)){
                $authorId = $context['author']->ID;
            }
            if($context['post'] && isset($context['post']->post_author)){
                $authorId = $context['post']->post_author;
            }
        }

        $value = esc_attr(get_user_meta($userId, $field, true));

        return apply_filters('seopress_get_tag_' . $tag . '_value', $value, $context);
    }
}
