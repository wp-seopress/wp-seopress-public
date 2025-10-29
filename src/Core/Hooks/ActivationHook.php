<?php // phpcs:ignore

namespace SEOPress\Core\Hooks;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ActivationHook
 */
interface ActivationHook {
	public function activate(); // phpcs:ignore -- TODO: rename method.
}
