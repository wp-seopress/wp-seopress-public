<?php

namespace SEOPress\Thirds\WPML;

defined('ABSPATH') or exit('Cheatin&#8217; uh?');

class AutomaticTranslations {
    public function __construct() {
        add_filter( 'wpml_tm_adjust_translation_fields', [$this, 'adjust_translation_fields'] );
    }

    public function adjust_translation_fields( array $fields ) {
        foreach ( $fields as &$field ) {
            $fieldKey = preg_replace( '/^(field-)(.*)(-0)$/', '$2', $field['field_type'] );
      
            if ( $fieldKey === '_seopress_titles_title' ) {
                $field['purpose'] = 'seo_title';
            } elseif ( $fieldKey === '_seopress_titles_desc' ) {
                $field['purpose'] = 'seo_meta_description';
            }
        }
      
        return $fields;
    }
}

new AutomaticTranslations();