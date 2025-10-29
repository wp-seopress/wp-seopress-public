<?php // phpcs:ignore

namespace SEOPress\Services\Metas\Title\Specifications;

use SEOPress\Constants\MetasDefaultValues;

/**
 * SingularSpecification
 */
class SingularSpecification {


	/**
	 * The checkBuddypressPostId function.
	 *
	 * @param int $id The id.
	 *
	 * @return int
	 */
	protected function checkBuddypressPostId( $id ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		// IS BUDDYPRESS ACTIVITY PAGE.
		if ( function_exists( 'bp_is_current_component' ) && bp_is_current_component( 'activity' ) ) {
			return buddypress()->pages->activity->id;
		}
		// IS BUDDYPRESS MEMBERS PAGE.
		if ( function_exists( 'bp_is_current_component' ) && bp_is_current_component( 'members' ) ) {
			return buddypress()->pages->members->id;
		}

		// IS BUDDYPRESS GROUPS PAGE.
		if ( function_exists( 'bp_is_current_component' ) && bp_is_current_component( 'groups' ) ) {
			return buddypress()->pages->groups->id;
		}

		return $id;
	}


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
	public function getValue( $params ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.

		$post     = $params['post'];
		$title    = $params['title'];
		$context  = $params['context'];
		$post->ID = $this->checkBuddypressPostId( $post->ID );
		// Re-request the title from the post meta for check buddypress.
		$value = get_post_meta( $post->ID, '_seopress_titles_title', true );

		if ( ! $value || empty( $value ) ) { // This is the Default Global.
			$post_type = isset( $context['post'] ) ? $context['post']->post_type : null;
			$value     = seopress_get_service( 'TitleOption' )->getTitleFromSingle( $post_type );
		}

		if ( empty( $value ) ) {
			$value = MetasDefaultValues::getPostTypeTitleValue();
		}

		$context['user_id'] = $post->post_author;

		return seopress_get_service( 'TagsToString' )->replace( $value, $context );
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
	public function isSatisfyBy( $params ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$context = $params['context'];

		if ( $context['is_singular'] ) {
			return true;
		}

		return false;
	}
}
