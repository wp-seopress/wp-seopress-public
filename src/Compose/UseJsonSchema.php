<?php // phpcs:ignore

namespace SEOPress\Compose;

use SEOPress\Models\GetJsonFromFile;

/**
 * UseJsonSchema
 */
trait UseJsonSchema {
	/**
	 * The schemas available.
	 *
	 * @var array
	 */
	protected $schemas_available = null;

	/**
	 * The getSchemaClass function.
	 *
	 * @since 4.5.0
	 *
	 * @param string $key The key.
	 *
	 * @return GetJsonFromFile
	 */
	public function getSchemaClass( $key ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$schemas_available = $this->getSchemasAvailable();
		$element           = null;
		// Check key <=> schema.
		if ( array_key_exists( $key, $schemas_available ) ) {
			$element = $schemas_available[ $key ];
		}

		// Check alias <=> schema.
		if ( null === $element ) {
			foreach ( $schemas_available as $schema ) {
				if ( null !== $element ) {
					break;
				}

				if ( ! array_key_exists( 'alias', $schema ) || empty( $schema['alias'] ) ) {
					continue;
				}

				if ( in_array( $key, $schema['alias'], true ) ) {
					$element = $schema;
				}
			}
		}

		// Check custom element.
		if ( null === $element ) {
			foreach ( $schemas_available as $schema ) {
				if ( null !== $element ) {
					break;
				}

				if ( ! array_key_exists( 'custom', $schema ) || null === $schema['custom'] ) {
					continue;
				}

				if ( 0 === strpos( $key, $schema['custom'] ) ) {
					$element = $schema;
				}
			}
		}

		if ( ! $element ) {
			return null;
		}

		if ( is_string( $element['class'] ) ) {
			$element['class'] = new $element['class']();
		}

		if ( $element['class'] instanceof GetJsonFromFile ) {
			return $element['class'];
		}

		return null;
	}

	/**
	 * The buildSchemas function.
	 *
	 * @since 4.5.0
	 *
	 * @param string $directory The directory.
	 * @param array  $schemas The schemas.
	 * @param array  $namespaces_option The namespaces option.
	 *
	 * @return array
	 */
	public function buildSchemas( // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$directory,
		$schemas = array(),
		$namespaces_option = array(
			'root'         => '\\SEOPress\\JsonSchemas\\%s%s',
			'subNamespace' => '',
		)
	) {
		$files = array_diff( scandir( $directory ), array( '..', '.' ) );

		foreach ( $files as $filename ) {
			$class      = str_replace( '.php', '', $filename );
			$class_file = sprintf( $namespaces_option['root'], $namespaces_option['subNamespace'], $class );
			$full_path  = sprintf( '%s/%s', $directory, $filename );

			if ( is_dir( $full_path ) ) {
				$namespaces_option['subNamespace'] = $filename . '\\';
				$schemas                           = $this->buildSchemas( $full_path, $schemas, $namespaces_option );
			} else {
				if ( defined( $class_file . '::NAME' ) ) {
					$name = $class_file::NAME;
				} else {
					$name = strtolower( $class );
				}

				$schemas[ $name ] = array(
					'class'  => $class_file,
					'name'   => $name,
					'alias'  => defined( $class_file . '::ALIAS' ) ? $class_file::ALIAS : array(),
					'custom' => defined( $class_file . '::CUSTOM_FORMAT' ) ? $class_file::CUSTOM_FORMAT : null,
					'input'  => sprintf( '[[%s]]', $name ),
				);
			}
		}

		return $schemas;
	}

	/**
	 * The getSchemasAvailable function.
	 *
	 * @since  4.5.0
	 *
	 * @return array
	 */
	public function getSchemasAvailable() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		if ( null !== $this->schemas_available ) {
			return apply_filters( 'seopress_schemas_available', $this->schemas_available );
		}

		$schemas = $this->buildSchemas( SEOPRESS_PLUGIN_DIR_PATH . 'src/JsonSchemas' );

		if ( defined( 'SEOPRESS_PRO_PLUGIN_DIR_PATH' ) && file_exists( SEOPRESS_PRO_PLUGIN_DIR_PATH . 'src/JsonSchemas' ) && is_dir( SEOPRESS_PRO_PLUGIN_DIR_PATH . 'src/JsonSchemas' ) ) {
			$schemas = $this->buildSchemas(
				SEOPRESS_PRO_PLUGIN_DIR_PATH . 'src/JsonSchemas',
				$schemas,
				array(
					'root'         => '\\SEOPressPro\\JsonSchemas\\%s%s',
					'subNamespace' => '',
				)
			);
		}
		$this->schemas_available = $schemas;

		return apply_filters( 'seopress_schemas_available', $this->schemas_available );
	}

	/**
	 * The __call function.
	 *
	 * @since 4.5.0
	 *
	 * @param string $name The name.
	 * @param any    $params The params.
	 */
	public function __call( $name, $params ) { // phpcs:ignore -- TODO: check if we still need this method.
		$schema_class = $this->getSchemaClass( $name );

		if ( null === $schema_class ) {
			return '';
		}

		return $schema_class->getJsonWithName( $name );
	}
}
