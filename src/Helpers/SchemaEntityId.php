<?php
/**
 * Shared identifiers for site-level schema entities.
 *
 * @package SEOPress
 */
namespace SEOPress\Helpers;

defined( 'ABSPATH' ) || exit;

class SchemaEntityId {
	/**
	 * Resolve an entity at the current language's home URL.
	 *
	 * @param string $entity Fragment identifying the entity.
	 * @return string
	 */
	public static function for_home( $entity ) {
		$id = home_url( '/#' . $entity );
		if ( function_exists( 'pll_current_language' ) && function_exists( 'pll_home_url' ) ) {
			$language = pll_current_language();
			$localized = $language ? pll_home_url( $language ) : '';
			if ( is_string( $localized ) && '' !== $localized ) {
				$id = trailingslashit( $localized ) . '#' . $entity;
			}
		}

		/**
		 * Customize a site entity identifier shared by its schema references.
		 *
		 * @param string $id     Absolute entity identifier.
		 * @param string $entity Entity name, such as organization, person or website.
		 */
		return (string) apply_filters( 'seopress_schema_entity_id', $id, $entity );
	}
}
