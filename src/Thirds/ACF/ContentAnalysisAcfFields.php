<?php

namespace SEOPress\Thirds\ACF;

if ( ! defined('ABSPATH')) {
    exit;
}
class ContentAnalysisAcfFields
{
    protected $singleFieldTypes  = ['text', 'textarea', 'wysiwyg'];

    protected $complexFieldTypes = ['repeater', 'flexible_content', 'group'];

    /**
     * @since 4.6.0
     *
     * @return array
     */
    public function getSingleFieldTypes() {
        return apply_filters('seopress_single_field_types_acf_analysis', $this->singleFieldTypes);
    }

    /**
     * @since 4.6.0
     *
     * @return array
     */
    public function getComplexeFieldTypes() {
        return apply_filters('seopress_complex_field_types_acf_analysis', $this->complexFieldTypes);
    }

    /**
     * @since 4.6.0
     *
     * @return string
     *
     * @param int $id
     */
    public function addAcfContent($id) {
        if ( ! function_exists('get_field_objects')) {
            return '';
        }

        $fields = get_field_objects($id);

        $content = '';
        if ($fields) {
            foreach ($fields as $field) {
                $content .= $this->getFieldContent($field, $id);
            }
        }

        return $content;
    }

    /**
     * @since 4.6.0
     *
     * @param int   $id
     * @param mixed $field
     *
     * @return string
     */
    public function getFieldContent($field, $id) {
        if (in_array($field['type'], $this->getSingleFieldTypes())) {
            return $field['value'] . ' ';
        } else {
            if ( ! in_array($field['type'], $this->getComplexeFieldTypes())) {
                return '';
            }
            if ( ! have_rows($field['name'], $id)) {
                return '';
            }

            $content = '';
            while (have_rows($field['name'], $id)) {
                $row = the_row();
                foreach ($row as $rowFieldKey => $rowField) {
                    $subFieldArray = get_sub_field_object($rowFieldKey);
                    if ($subFieldArray) {
                        $content .= $this->getFieldContent($subFieldArray, $id);
                    }
                }
            }

            return $content;
        }

        return '';
    }
}
