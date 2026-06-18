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
			$data[ $key ] = wp_json_encode( $this->decodeHtmlEntities( $data[ $key ] ) );
		}

		return apply_filters( 'seopress_json_schema_generator_get_jsons_encoded', $data );
	}

	/**
	 * Recursively decode HTML entities in schema values before JSON-LD encoding.
	 *
	 * Variable-fed fields (e.g. %%sitetitle%% resolves via get_bloginfo('name'),
	 * which WordPress stores entity-encoded) can carry entities like `&amp;`.
	 * JSON-LD must not contain HTML entities: wp_json_encode() handles its own
	 * escaping, so a Site Title such as "360 Automotive & Repair" should keep a
	 * real `&` in the output instead of leaking as `&amp;`. Decoding here covers
	 * every schema type and field in one place.
	 *
	 * @since 10.0.0
	 *
	 * @param mixed $value The schema value (array or scalar).
	 * @return mixed
	 */
	private function decodeHtmlEntities( $value ) {
		if ( is_array( $value ) ) {
			return array_map( array( $this, 'decodeHtmlEntities' ), $value );
		}

		if ( is_string( $value ) ) {
			return html_entity_decode( $value, ENT_QUOTES, 'UTF-8' );
		}

		return $value;
	}
}
