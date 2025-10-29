<?php // phpcs:ignore

namespace SEOPress\Core\Container;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ContainerSeopress
 */
class ContainerSeopress implements ManageContainer {
	/**
	 * List actions WordPress.
	 *
	 * @var array
	 */
	protected $actions = array();

	/**
	 * List class services.
	 *
	 * @var array
	 */
	protected $services = array();

	/**
	 * Set actions.
	 *
	 * @param array $actions The actions.
	 */
	public function setActions( $actions ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$this->actions = $actions;

		return $this;
	}

	/**
	 * Set action.
	 *
	 * @param string $action The action.
	 */
	public function setAction( $action ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$this->actions[ $action ] = $action;

		return $this;
	}

	/**
	 * Get services.
	 *
	 * @return array
	 */
	public function getActions() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->actions;
	}

	/**
	 * Get action.
	 *
	 * @param string $name The name.
	 *
	 * @return object
	 */
	public function getAction( $name ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		try {
			if ( ! array_key_exists( $name, $this->actions ) ) {
				return null;
				// @TODO : Throw exception
			}

			if ( is_string( $this->actions[ $name ] ) ) {
				$this->actions[ $name ] = new $this->actions[ $name ]();
			}

			return $this->actions[ $name ];
		} catch ( \Exception $th ) {
			return null;
		}
	}

	/**
	 * Set services.
	 *
	 * @param array $services The services.
	 */
	public function setServices( $services ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		foreach ( $services as $service ) {
			$this->setService( $service );
		}

		return $this;
	}

	/**
	 * Set a service.
	 *
	 * @param string $service The service.
	 */
	public function setService( $service ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$name = explode( '\\', $service );
		end( $name );
		$key = key( $name );

		if ( defined( $service . '::NAME_SERVICE' ) ) {
			$name = $service::NAME_SERVICE;
		} else {
			$name = $name[ $key ];
		}

		$this->services[ $name ] = $service;

		return $this;
	}

	/**
	 * Get services.
	 *
	 * @return array
	 */
	public function getServices() {
		return $this->services;
	}

	/**
	 * Get service by name.
	 *
	 * @param string $name The name.
	 *
	 * @return object
	 */
	public function getServiceByName( $name ) {
		if ( ! isset( $this->services[ $name ] ) ) {
			return null;
		}

		try {
			if ( is_string( $this->services[ $name ] ) && class_exists( $this->services[ $name ] ) ) {
				$this->services[ $name ] = new $this->services[ $name ]();
			}
		} catch ( \Exception $e ) {
			$this->services[ $name ] = null;
		}

		return $this->services[ $name ];
	}
}
