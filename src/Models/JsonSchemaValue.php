<?php // phpcs:ignore

namespace SEOPress\Models;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * JsonSchemaValue
 *
 * @abstract
 */
abstract class JsonSchemaValue implements GetJsonFromFile {
	/**
	 * The getName function.
	 *
	 * @return string
	 */
	abstract protected function getName(); // phpcs:ignore -- TODO: check if method is outside this class before renaming.

	/**
	 * The getJson function.
	 *
	 * @since 4.5.0
	 *
	 * @return string
	 */
	public function getJson() {
		$file = apply_filters( 'seopress_get_json_from_file', sprintf( '%s/%s.json', SEOPRESS_TEMPLATE_JSON_SCHEMAS, $this->getName(), '.json' ) );

		if ( ! file_exists( $file ) ) {
			return '';
		}

		$json = file_get_contents( $file );

		return $json;
	}

	/**
	 * The getArrayJson function.
	 *
	 * @since 4.5.0
	 *
	 * @return array
	 */
	public function getArrayJson() {
		$json = $this->getJson();
		try {
			$data = json_decode( $json, true );

			return apply_filters( 'seopress_schema_get_array_json', $data, $this->getName() );
		} catch ( \Exception $th ) {
			return array();
		}
	}

	/**
	 * The renderJson function.
	 *
	 * @since 4.5.0
	 *
	 * @param array $data The data.
	 *
	 * @return array|string
	 */
	public function renderJson( $data ) {
		return wp_json_encode( $data );
	}

	/**
	 * The cleanValues function.
	 *
	 * @since 4.5.0
	 *
	 * @param array $data The data.
	 *
	 * @return array
	 */
	public function cleanValues( $data ) {
		return apply_filters( 'seopress_schema_clean_values', $data, $this->getName() );
	}
}
