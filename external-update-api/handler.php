<?php

defined( 'ABSPATH' ) or die();

if ( ! class_exists( 'EUAPI_Handler' ) ) :

abstract class EUAPI_Handler {

	/**
	 * Class Constructor
	 *
	 * @since 1.0
	 * @param array $config configuration
	 * @return void
	 */
	public function __construct( array $config = array() ) {

		$this->config = apply_filters( "euapi_{$config['type']}_handler_config", $config );

	}

	abstract public function get_plugin_url();

	abstract public function get_package_url();

	abstract public function fetch_new_version();

	abstract public function fetch_info();

	final public function get_file() {
		return $this->config['file'];
	}

	final public function get_current_version() {

		if ( isset( $this->item ) )
			return $this->item->get_version();
		else
			return false;

	}

	final public function get_new_version() {

		if ( isset( $this->new_version ) )
			return $this->new_version;
		else
			return $this->new_version = $this->fetch_new_version();

	}

	final public function get_update() {

		if ( isset( $this->update ) )
			return $this->update;

		$package = add_query_arg( array(
			'_euapi_type' => $this->get_type(),
			'_euapi_file' => $this->get_file()
		), $this->get_package_url() );

		return $this->update = new EUAPI_Update( array(
			'slug'        => $this->get_file(),
			'new_version' => $this->get_new_version(),
			'url'         => $this->get_plugin_url(),
			'package'     => $package,
			'config'      => $this->get_config(),
		) );

	}

	final public function get_info() {

		if ( isset( $this->info ) )
			return $this->info;

		if ( !( $info = $this->fetch_info() ) )
			return $this->info = false;

		return $this->info = new EUAPI_Info( $info );

	}

	final public function get_config() {
		return $this->config;
	}

	final public function get_type() {
		return $this->config['type'];
	}

}

endif; // endif class exists
