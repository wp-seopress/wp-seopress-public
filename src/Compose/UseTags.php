<?php

namespace SEOPress\Compose;

use SEOPress\Helpers\TagCompose;
use SEOPress\Models\GetTagValue;

trait UseTags {
    protected $tagsAvailable = null;

    /**
     * @since 4.4.0
     *
     * @param string $key
     *
     * @return GetTagValue
     */
    public function getTagClass($key) {
        $tagsAvailable = $this->getTagsAvailable();

        $element = null;
        // Check key <=> tag
        if (array_key_exists($key, $tagsAvailable)) {
            $element = $tagsAvailable[$key];
        }

        // Check alias <=> tag
        if (null === $element) {
            foreach ($tagsAvailable as $tag) {
                if (null !== $element) {
                    break;
                }

                if ( ! array_key_exists('alias', $tag) || empty($tag['alias'])) {
                    continue;
                }

                if (in_array($key, $tag['alias'], true)) {
                    $element = $tag;
                }
            }
        }

        // Check custom element
        if (null === $element) {
            foreach ($tagsAvailable as $tag) {
                if (null !== $element) {
                    break;
                }

                if ( ! array_key_exists('custom', $tag) || null === $tag['custom']) {
                    continue;
                }

                if (0 === strpos($key, $tag['custom'])) {
                    $element = $tag;
                }
            }
        }

        if ( ! $element) {
            return null;
        }

        if (is_string($element['class'])) {
            $element['class'] = new $element['class']();
        }

        if ($element['class'] instanceof GetTagValue) {
            return $element['class'];
        }

        return null;
    }

    /**
     * @since 4.4.0
     *
     * @param string $directory
     * @param array  $tags
     * @param array  $namespacesOption
     *
     * @return array
     */
    public function buildTags($directory, $namespacesOption, $tags = []) {
        $files  = array_diff(scandir($directory), ['..', '.']);

        foreach ($files as $filename) {
            $class     = str_replace('.php', '', $filename);
            $classFile = sprintf($namespacesOption['root'], $namespacesOption['subNamespace'], $class);

            $fullPath  = sprintf('%s/%s', $directory, $filename);

            if (is_dir($fullPath)) {
                $tags  = $this->buildTags($fullPath, [
                    'root'         => $namespacesOption['root'],
                    'subNamespace' => $namespacesOption['subNamespace'] . $filename . '\\',
                ], $tags);
            } else {
                if (defined($classFile . '::NAME')) {
                    $name = $classFile::NAME;
                } else {
                    $name = strtolower($class);
                }

                $tags[$name] = [
                    'class'  => $classFile,
                    'name'   => $name,
                    'schema' => 0 === strpos($classFile, "\SEOPress\Tags\Schema\\") ? true : false,
                    'alias'  => defined($classFile . '::ALIAS') ? $classFile::ALIAS : [],
                    'custom' => defined($classFile . '::CUSTOM_FORMAT') ? $classFile::CUSTOM_FORMAT : null,
                    'input'  => TagCompose::getValueWithTag($name),
                ];
            }
        }

        return $tags;
    }

    /**
     * @since  4.4.0
     *
     * @return array
     */
    public function getTagsAvailable() {
        if (null !== $this->tagsAvailable) {
            return apply_filters('seopress_tags_available', $this->tagsAvailable);
        }

        $tags = $this->buildTags(SEOPRESS_PLUGIN_DIR_PATH . 'src/Tags', ['root' => '\\SEOPress\\Tags\\%s%s', 'subNamespace' => '']);

        if (defined('SEOPRESS_PRO_PLUGIN_DIR_PATH') && file_exists(SEOPRESS_PRO_PLUGIN_DIR_PATH . 'src/Tags') && is_dir(SEOPRESS_PRO_PLUGIN_DIR_PATH . 'src/Tags')) {
            $tags = $this->buildTags(SEOPRESS_PRO_PLUGIN_DIR_PATH . 'src/Tags', ['root' => '\\SEOPressPro\\Tags\\%s%s', 'subNamespace' => ''], $tags);
        }

        $this->tagsAvailable = $tags;

        return apply_filters('seopress_tags_available', $this->tagsAvailable);
    }

    /**
     * @since 4.4.0
     *
     * @param string $name
     * @param any    $params
     */
    public function __call($name, $params) {
        $tagClass = $this->getTagClass($name);

        if (null === $tagClass) {
            return '';
        }

        return $tagClass->getValue($params);
    }
}
