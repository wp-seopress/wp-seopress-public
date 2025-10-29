<?php // phpcs:ignore

namespace SEOPress\Services\Metas;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Helpers\Metas\RedirectionSettings;

/**
 * RedirectionMeta
 */
class RedirectionMeta {

	/**
	 * The getKeyValue function.
	 *
	 * @param string $meta The meta.
	 *
	 * @return string
	 */
	protected function getKeyValue( $meta ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		switch ( $meta ) {
			case '_seopress_redirections_enabled':
				return 'enabled';
			case '_seopress_redirections_logged_status':
				return 'status';
			case '_seopress_redirections_type':
				return 'type';
			case '_seopress_redirections_value':
				return 'value';
		}

		return null;
	}

	/**
	 * The getMetaValue function.
	 *
	 * @param string $callback The callback.
	 * @param int    $id The id.
	 * @param string $meta The meta.
	 *
	 * @return string
	 */
	protected function getMetaValue( $callback, $id, $meta ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		switch ( $callback ) {
			case 'get_post_meta':
				return get_post_meta( $id, $meta, true );
			case 'get_term_meta':
				return get_term_meta( $id, $meta, true );
		}
	}

	/**
	 * The getPostMetaType function.
	 *
	 * @param int $post_id The post id.
	 *
	 * @return string
	 */
	public function getPostMetaType( $post_id ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->getMetaValue( 'get_post_meta', $post_id, '_seopress_redirections_type' );
	}

	/**
	 * The getPostMetaStatus function.
	 *
	 * @param int $post_id The post id.
	 *
	 * @return string
	 */
	public function getPostMetaStatus( $post_id ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->getMetaValue( 'get_post_meta', $post_id, '_seopress_redirections_logged_status' );
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

		$metas = RedirectionSettings::getMetaKeys( $id );

		$data = array();
		foreach ( $metas as $key => $value ) {
			$name = $this->getKeyValue( $value['key'] );
			if ( null === $name ) {
				continue;
			}

			if ( $value['use_default'] && $use_default ) {
				$data[ $name ] = $value['default'];
			} else {
				$result        = $callback( $id, $value['key'], true );
				$data[ $name ] = 'checkbox' === $value['type'] ? ( true === $result || 'yes' === $result ? true : false ) : $result; //phpcs:ignore
			}
		}

		return $data;
	}
}
