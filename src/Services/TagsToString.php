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
            $value = $this->getValueFromTag($tag, $context);

            $string = str_replace($tags[0][$key], $value, $string);
        }

        return $string;
    }
}
