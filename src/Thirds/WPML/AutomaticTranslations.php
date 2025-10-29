<?php // phpcs:ignore

namespace SEOPress\Thirds\WPML;

defined( 'ABSPATH' ) || exit( 'Cheatin&#8217; uh?' );

/**
 * Automatic Translations
 */
class AutomaticTranslations {
	/**
	 * Constructor
	 */
	public function __construct() {
		add_filter( 'wpml_tm_adjust_translation_fields', array( $this, 'adjust_translation_fields' ) );
	}

	/**
	 * Adjust translation fields
	 *
	 * @param array $fields The fields.
	 * @return array
	 */
	public function adjust_translation_fields( array $fields ) { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
		foreach ( $fields as &$field ) {
			$field_key = preg_replace( '/^(field-)(.*)(-0)$/', '$2', $field['field_type'] );

			if ( '_seopress_titles_title' === $field_key ) {
				$field['purpose'] = 'seo_title';
			} elseif ( '_seopress_titles_desc' === $field_key ) {
				$field['purpose'] = 'seo_meta_description';
			}
		}

		return $fields;
	}
}

new AutomaticTranslations();
