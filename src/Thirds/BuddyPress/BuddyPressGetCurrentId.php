<?php // phpcs:ignore

namespace SEOPress\Thirds\BuddyPress;

defined( 'ABSPATH' ) || exit( 'Cheatin&#8217; uh?' );


/**
 * BuddyPress Get Current Id
 */
class BuddyPressGetCurrentId {

	/**
	 * Get Current Id
	 *
	 * @return int|null
	 */
	public function getCurrentId() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
		$id = null;

		if ( function_exists( 'bp_is_current_component' ) && true === bp_is_current_component( 'activity' ) ) {
			$id = buddypress()->pages->activity->id;
		}
		// IS BUDDYPRESS MEMBERS PAGE.
		if ( function_exists( 'bp_is_current_component' ) && true === bp_is_current_component( 'members' ) ) {
			$id = buddypress()->pages->members->id;
		}

		// IS BUDDYPRESS GROUPS PAGE.
		if ( function_exists( 'bp_is_current_component' ) && true === bp_is_current_component( 'groups' ) ) {
			$id = buddypress()->pages->groups->id;
		}

		return $id;
	}
}
