<?php

namespace SEOPress\Services;

if ( ! defined('ABSPATH')) {
    exit;
}

use SEOPress\Compose\UseTags;

class TagsToString {
    use UseTags;

    const REGEX = "#\%\%(.*?)\%\%#";

    /**
     * @since 4.4.0
     *
     * @return int
     */
    public function getExcerptLengthForTags() {
        return apply_filters('seopress_excerpt_length', 50);
    }

    /**
     * @since 4.4.0
     *
     * @param string $string
     *
     * @return array
     */
    public function getTags($string) {
        if ( ! is_string($string)) {
            return [];
        }

        preg_match_all(self::REGEX, $string, $matches);

        return $matches;
    }

    /**
     * @since 4.4.0
     *
     * @param function $tag
     * @param array    $context
     *
     * @return void
     */
    public function getValueFromTag($tag, $context= []) {
        // 0 === 'context'
        // 1 === 'tag'
        return call_user_func_array([$this, $tag], [0 => $context, 1 => $tag]);
    }

    /**
     * @since 4.4.0
     *
     * @param string $string
     * @param mixed  $context
     *
     * @return string
     */
    public function replace($string, $context = []) {
        $tags = $this->getTags($string);

        if ( ! array_key_exists(1, $tags)) {
            return $string;
        }

        $tagsAvailable = $this->getTagsAvailable();

        foreach ($tags[1] as $key => $tag) {
            $value  = $this->getValueFromTag($tag, $context);
            $string = str_replace($tags[0][$key], $value, $string);
        }

        return $string;
    }

    /**
     * @since 4.5.0
     *
     * @param array $data
     *
     * @return array
     */
    protected function removeDataEmpty($data) {
        return array_filter($data);
    }

    /**
     * @since 4.5.0
     *
     * @param array $data
     * @param array $context
     * @param mixed $options
     *
     * @return array
     */
    public function replaceDataToString($data, $context = [], $options = []) {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->replaceDataToString($value, $context, $options);
            } else {
                $data[$key] = $this->replace($value, $context);
            }
        }

        if (isset($options['remove_empty']) && $options['remove_empty']) {
            $data = $this->removeDataEmpty($data);
        }

        return $data;
    }
}
