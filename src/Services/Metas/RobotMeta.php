<?php // phpcs:ignore

namespace SEOPress\Services\Metas;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Helpers\Metas\RobotSettings;

/**
 * RobotMeta
 */
class RobotMeta {

	/**
	 * The getKeyValue function.
	 *
	 * @param string $meta The meta.
	 *
	 * @return string
	 */
	protected function getKeyValue( $meta ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		switch ( $meta ) {
			case '_seopress_robots_index':
				return 'noindex';
			case '_seopress_robots_follow':
				return 'nofollow';
			case '_seopress_robots_snippet':
				return 'nosnippet';
			case '_seopress_robots_imageindex':
				return 'noimageindex';
			case '_seopress_robots_canonical':
				return 'canonical';
			case '_seopress_robots_primary_cat':
				return 'primarycat';
			case '_seopress_robots_breadcrumbs':
				return 'breadcrumbs';
		}

		return null;
	}

	/**
	 * The getValue function.
	 *
	 * @param array $context The context.
	 * @param bool  $use_default Use default value only if you get the value from the database after this function.
	 *
	 * @return string|null
	 */
	public function getValue( $context, $use_default = true ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$data = array();

		$id = null;

		$callback = 'get_post_meta';
		if ( isset( $context['post'] ) ) {
			$id = $context['post']->ID;
		} elseif ( isset( $context['term_id'] ) ) {
			$id       = $context['term_id'];
			$callback = 'get_term_meta';
		}

		if ( ! $id ) {
			return $data;
		}

		$metas = RobotSettings::getMetaKeys( $id );

		$data = array();
		foreach ( $metas as $key => $value ) {
			$name = $this->getKeyValue( $value['key'] );
			if ( null === $name ) {
				continue;
			}

			if ( $value['use_default'] && $use_default ) {
				$data[ $name ] = true === $value['default'] || 'yes' === $value['default'] ? true : $value['default']; //phpcs:ignore
			} else {
				$result = $callback( $id, $value['key'], true );

				$data[ $name ] = 'checkbox' === $value['type'] ? ( true === $result || 'yes' === $result ? true : false ) : $result; //phpcs:ignore
			}
		}

		return $data;
	}
}
