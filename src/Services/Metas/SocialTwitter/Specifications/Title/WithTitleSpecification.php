<?php // phpcs:ignore

namespace SEOPress\Services\Metas\SocialTwitter\Specifications\Title;

use SEOPress\Helpers\Metas\SocialSettings;
use SEOPress\Services\Metas\Title\TitleMeta;
use SEOPress\Services\Metas\SocialTwitter\Specifications\Title\AbstractTitleSpecification;

/**
 * WithTitleSpecification
 */
class WithTitleSpecification extends AbstractTitleSpecification {

	const NAME_SERVICE = 'WithTitleSocialTwitterSpecification';

	/**
	 * The getValue function.
	 *
	 * @param array $params The params.
	 *
	 * @example [
	 *     'context' => array
	 *
	 * ]
	 * @return string
	 */
	public function getValue( $params ) {

		$context = $params['context'];

		$post = $params['post'];

		$title_meta = new TitleMeta();
		return $this->applyFilter( $title_meta->getValue( $params['context'] ) );
	}



	/**
	 * The isSatisfyBy function.
	 *
	 * @param array $params The params.
	 *
	 * @example [
	 *     'post' => \WP_Post
	 *     'title' => string
	 *     'context' => array
	 *
	 * ]
	 * @return boolean
	 */
	public function isSatisfyBy( $params ) {
		$title_meta = new TitleMeta();
		$value      = $title_meta->getValue( $params['context'] );
		return ! empty( $value );
	}
}
