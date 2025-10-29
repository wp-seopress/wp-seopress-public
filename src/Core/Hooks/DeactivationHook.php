<?php // phpcs:ignore

namespace SEOPress\Core\Hooks;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * DeactivationHook
 */
interface DeactivationHook {
	public function deactivate(); // phpcs:ignore -- TODO: rename method.
}
