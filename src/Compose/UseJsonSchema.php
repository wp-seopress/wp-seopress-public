<?php

namespace SEOPress\Compose;

use SEOPress\Models\GetJsonFromFile;

trait UseJsonSchema {
    protected $schemasAvailable = null;

    /**
     * @since 4.5.0
     *
     * @param string $key
     *
     * @return GetJsonFromFile
     */
    public function getSchemaClass($key) {
        $schemasAvailable = $this->getSchemasAvailable();
        $element          = null;
        // Check key <=> schema
        if (array_key_exists($key, $schemasAvailable)) {
            $element = $schemasAvailable[$key];
        }

        // Check alias <=> schema
        if (null === $element) {
            foreach ($schemasAvailable as $schema) {
                if (null !== $element) {
                    break;
                }

                if ( ! array_key_exists('alias', $schema) || empty($schema['alias'])) {
                    continue;
                }

                if (in_array($key, $schema['alias'], true)) {
                    $element = $schema;
                }
            }
        }

        // Check custom element
        if (null === $element) {
            foreach ($schemasAvailable as $schema) {
                if (null !== $element) {
                    break;
                }

                if ( ! array_key_exists('custom', $schema) || null === $schema['custom']) {
                    continue;
                }

                if (0 === strpos($key, $schema['custom'])) {
                    $element = $schema;
                }
            }
        }

        if ( ! $element) {
            return null;
        }

        if (is_string($element['class'])) {
            $element['class'] = new $element['class']();
        }

        if ($element['class'] instanceof GetJsonFromFile) {
            return $element['class'];
        }

        return null;
    }

    /**
     * @since 4.5.0
     *
     * @param string $directory
     * @param array  $schemas
     * @param string $subNamespace
     * @param mixed  $namespacesOption
     *
     * @return array
     */
    public function buildSchemas($directory, $schemas = [], $namespacesOption = ['root' => '\\SEOPress\\JsonSchemas\\%s%s', 'subNamespace' => '']) {
        $files  = array_diff(scandir($directory), ['..', '.']);

        foreach ($files as $filename) {
            $class     = str_replace('.php', '', $filename);
            $classFile = sprintf($namespacesOption['root'], $namespacesOption['subNamespace'], $class);
            $fullPath  = sprintf('%s/%s', $directory, $filename);

            if (is_dir($fullPath)) {
                $namespacesOption['subNamespace'] =  $filename . '\\';
                $schemas                          = $this->buildSchemas($fullPath, $schemas, $namespacesOption);
            } else {
                if (defined($classFile . '::NAME')) {
                    $name = $classFile::NAME;
                } else {
                    $name = strtolower($class);
                }

                $schemas[$name] = [
                    'class'     => $classFile,
                    'name'      => $name,
                    'alias'     => defined($classFile . '::ALIAS') ? $classFile::ALIAS : [],
                    'custom'    => defined($classFile . '::CUSTOM_FORMAT') ? $classFile::CUSTOM_FORMAT : null,
                    'input'     => sprintf('[[%s]]', $name),
                ];
            }
        }

        return $schemas;
    }

    /**
     * @since  4.5.0
     *
     * @return array
     */
    public function getSchemasAvailable() {
        if (null !== $this->schemasAvailable) {
            return apply_filters('seopress_schemas_available', $this->schemasAvailable);
        }

        $schemas = $this->buildSchemas(SEOPRESS_PLUGIN_DIR_PATH . 'src/JsonSchemas');

        if (defined('SEOPRESS_PRO_PLUGIN_DIR_PATH') && file_exists(SEOPRESS_PRO_PLUGIN_DIR_PATH . 'src/JsonSchemas') && is_dir(SEOPRESS_PRO_PLUGIN_DIR_PATH . 'src/JsonSchemas')) {
            $schemas = $this->buildSchemas(SEOPRESS_PRO_PLUGIN_DIR_PATH . 'src/JsonSchemas', $schemas, ['root' => '\\SEOPressPro\\JsonSchemas\\%s%s', 'subNamespace' => '']);
        }
        $this->schemasAvailable = $schemas;

        return apply_filters('seopress_schemas_available', $this->schemasAvailable);
    }

    /**
     * @since 4.5.0
     *
     * @param string $name
     * @param any    $params
     */
    public function __call($name, $params) {
        $schemaClass = $this->getSchemaClass($name);

        if (null === $schemaClass) {
            return '';
        }

        return $schemaClass->getJsonWithName($name);
    }
}
