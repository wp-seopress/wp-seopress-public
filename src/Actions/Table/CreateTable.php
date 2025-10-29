<?php // phpcs:ignore

namespace SEOPress\Actions\Table;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Core\Hooks\ExecuteHooks;
use SEOPress\Core\Hooks\ActivationHook;

/**
 * CreateTable
 */
class CreateTable implements ExecuteHooks, ActivationHook {
	/**
	 * The CreateTable hooks.
	 *
	 * @since 4.3.0
	 *
	 * @return void
	 */
	public function hooks() {
		add_action( 'admin_init', array( $this, 'init' ) );
	}

	/**
	 * The init function.
	 *
	 * @since 4.3.0
	 *
	 * @return void
	 */
	public function init() {
		if ( ! is_user_logged_in() || ! is_seopress_page() ) {
			return;
		}

		$this->createTables();
	}

	/**
	 * The activate function.
	 *
	 * @since 4.3.0
	 *
	 * @return void
	 */
	public function activate() {
		$this->createTables();
	}

	/**
	 * The createTables function.
	 *
	 * @since 4.3.0
	 *
	 * @return void
	 */
	private function createTables() {
		$tables = seopress_get_service( 'TableList' )->getTables();
		seopress_get_service( 'TableManager' )->createTablesIfNeeded( $tables );
	}
}
