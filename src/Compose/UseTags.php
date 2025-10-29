<?php // phpcs:ignore

namespace SEOPress\Compose;

use SEOPress\Helpers\TagCompose;
use SEOPress\Models\GetTagValue;

/**
 * UseTags
 */
trait UseTags {
	/**
	 * The tags available.
	 *
	 * @var array
	 */
	protected $tags_available = array();

	/**
	 * The getTagClass function.
	 *
	 * @since 4.4.0
	 *
	 * @param string $key The key.
	 *
	 * @return GetTagValue
	 */
	public function getTagClass( $key ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$tags_available = $this->getTagsAvailable();

		$element = null;
		// Check key <=> tag.
		if ( array_key_exists( $key, $tags_available ) ) {
			$element = $tags_available[ $key ];
		}

		// Check alias <=> tag.
		if ( null === $element ) {
			foreach ( $tags_available as $tag ) {
				if ( null !== $element ) {
					break;
				}

				if ( ! array_key_exists( 'alias', $tag ) || empty( $tag['alias'] ) ) {
					continue;
				}

				if ( in_array( $key, $tag['alias'], true ) ) {
					$element = $tag;
				}
			}
		}

		// Check custom element.
		if ( null === $element ) {
			foreach ( $tags_available as $tag ) {
				if ( null !== $element ) {
					break;
				}

				if ( ! array_key_exists( 'custom', $tag ) || null === $tag['custom'] ) {
					continue;
				}

				if ( 0 === strpos( $key, $tag['custom'] ) ) {
					$element = $tag;
				}
			}
		}

		if ( ! $element ) {
			return null;
		}

		if ( is_string( $element['class'] ) ) {
			$element['class'] = new $element['class']();
		}

		if ( $element['class'] instanceof GetTagValue ) {
			return $element['class'];
		}

		return null;
	}

	/**
	 * The buildTags function.
	 *
	 * @since 4.4.0
	 *
	 * @param string $directory The directory.
	 * @param array  $namespaces_option The namespaces option.
	 * @param array  $tags The tags.
	 *
	 * @return array
	 */
	public function buildTags( $directory, $namespaces_option, $tags = array() ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$files = array_diff( scandir( $directory ), array( '..', '.' ) );

		foreach ( $files as $filename ) {
			if ( strtolower( $filename ) === '.ds_store' ) {
				continue;
			}

			$class      = str_replace( '.php', '', $filename );
			$class_file = sprintf( $namespaces_option['root'], $namespaces_option['subNamespace'], $class );

			$full_path = sprintf( '%s/%s', $directory, $filename );

			if ( is_dir( $full_path ) ) {
				$tags = $this->buildTags(
					$full_path,
					array(
						'root'         => $namespaces_option['root'],
						'subNamespace' => $namespaces_option['subNamespace'] . $filename . '\\',
					),
					$tags
				);
			} else {
				if ( defined( $class_file . '::NAME' ) ) {
					$name = $class_file::NAME;
				} else {
					$name = strtolower( $class );
				}

				$description = '';
				if ( method_exists( $class_file, 'getDescription' ) ) {
					$description = $class_file::getDescription();
				}

				$tags[ $name ] = array(
					'class'       => $class_file,
					'name'        => $name,
					'schema'      => 0 === strpos( $class_file, '\SEOPress\Tags\Schema\\' ) ? true : false,
					'alias'       => defined( $class_file . '::ALIAS' ) ? $class_file::ALIAS : array(),
					'custom'      => defined( $class_file . '::CUSTOM_FORMAT' ) ? $class_file::CUSTOM_FORMAT : null,
					'input'       => TagCompose::getValueWithTag( $name ),
					'description' => $description,
				);
			}
		}

		return $tags;
	}

	/**
	 * The getTagsAvailable function.
	 *
	 * @param array $options The options.
	 *
	 * @since  4.4.0
	 *
	 * @return array
	 */
	public function getTagsAvailable( $options = array() ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.

		$hash = md5( serialize( $options ) ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.serialize_serialize -- Used only for cache key generation, never unserialized.
		if ( isset( $this->tags_available[ $hash ] ) ) {
			return $this->tags_available[ $hash ];
		}

		$tags = $this->buildTags(
			SEOPRESS_PLUGIN_DIR_PATH . 'src/Tags',
			array(
				'root'         => '\\SEOPress\\Tags\\%s%s',
				'subNamespace' => '',
			)
		);

		if ( defined( 'SEOPRESS_PRO_PLUGIN_DIR_PATH' ) && file_exists( SEOPRESS_PRO_PLUGIN_DIR_PATH . 'src/Tags' ) && is_dir( SEOPRESS_PRO_PLUGIN_DIR_PATH . 'src/Tags' ) ) {
			$tags = $this->buildTags(
				SEOPRESS_PRO_PLUGIN_DIR_PATH . 'src/Tags',
				array(
					'root'         => '\\SEOPressPro\\Tags\\%s%s',
					'subNamespace' => '',
				),
				$tags
			);
		}

		if ( isset( $options['without_classes'] ) ) {
			$without_classes     = isset( $options['without_classes'] );
			$without_classes_pos = isset( $options['without_classes_pos'] );
			foreach ( $tags as $key => $tag ) {
				if ( $without_classes && \in_array( $tag['class'], $options['without_classes'], true ) ) {
					unset( $tags[ $key ] );
				}

				if ( $without_classes_pos ) {
					foreach ( $options['without_classes_pos'] as $classes_pos ) {
						if ( strpos( $tag['class'], $classes_pos ) !== false ) {
							unset( $tags[ $key ] );
						}
					}
				}
			}
		}

		$this->tags_available[ $hash ] = apply_filters( 'seopress_tags_available', $tags );

		return $this->tags_available[ $hash ];
	}

	/**
	 * The __call function.
	 *
	 * @since 4.4.0
	 *
	 * @param string $name The name.
	 * @param any    $params The params.
	 */
	public function __call( $name, $params ) {
		$tag_class = $this->getTagClass( $name );

		if ( null === $tag_class ) {
			return '';
		}

		return $tag_class->getValue( $params );
	}
}
