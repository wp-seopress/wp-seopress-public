<?php // phpcs:ignore

namespace SEOPress\Services;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Compose\UseJsonSchema;

/**
 * JsonSchemaGenerator
 */
class JsonSchemaGenerator {
	use UseJsonSchema;

	/**
	 * The tagsToString property.
	 *
	 * @var TagsToString
	 */
	protected $tags_to_string;

	/**
	 * The variablesToString property.
	 *
	 * @var VariablesToString
	 */
	protected $variables_to_string;

	/**
	 * The __construct function.
	 *
	 * @return void
	 */
	public function __construct() {
		$this->tags_to_string      = seopress_get_service( 'TagsToString' );
		$this->variables_to_string = seopress_get_service( 'VariablesToString' );
	}

	/**
	 * The getJsonFromSchema function.
	 *
	 * @since 4.5.0
	 *
	 * @param string $schema The schema.
	 * @param array  $context The context.
	 * @param array  $options The options.
	 *
	 * @return array
	 */
	public function getJsonFromSchema( $schema, $context = array(), $options = array() ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$class_json_schema = $this->getSchemaClass( $schema );
		if ( null === $class_json_schema ) {
			return null;
		}

		$json_data = $class_json_schema->getJsonData( $context );

		if ( isset( $context['variables'] ) ) {
			$json_data = $this->variables_to_string->replaceDataToString( $json_data, $context['variables'], $options );
		}

		$json_data = $this->tags_to_string->replaceDataToString( $json_data, $context, $options );
		if ( ! empty( $json_data ) ) {
			$json_data = $class_json_schema->cleanValues( $json_data );
		}

		return $json_data;
	}

	/**
	 * The getJsons function.
	 *
	 * @since 4.5.0
	 *
	 * @param array $data The data.
	 * @param array $context The context.
	 */
	public function getJsons( $data, $context = array() ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$jsons_available = $this->getSchemasAvailable();

		if ( ! is_array( $data ) ) {
			return array();
		}

		foreach ( $data as $key => $schema ) {
			$context['key_get_json_schema'] = $key;
			$data[ $key ]                   = $this->getJsonFromSchema( $schema, $context, array( 'remove_empty' => true ) );
		}

		return apply_filters( 'seopress_json_schema_generator_get_jsons', $data );
	}

	/**
	 * The getJsonsEncoded function.
	 *
	 * @since 4.5.0
	 *
	 * @param array $data The data.
	 * @param array $context The context.
	 */
	public function getJsonsEncoded( $data, $context = array() ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		if ( ! is_array( $data ) ) {
			return array();
		}

		$data = $this->getJsons( $data, $context );

		foreach ( $data as $key => $value ) {
			if ( null === $value ) {
				unset( $data[ $key ] );
				continue;
			}
			$data[ $key ] = wp_json_encode( $data[ $key ] );
		}

		return apply_filters( 'seopress_json_schema_generator_get_jsons_encoded', $data );
	}
}
