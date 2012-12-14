<?php

// Prevent loading this file directly - Busted!
if ( !defined('ABSPATH') )
	die('-1');

if ( ! class_exists( 'EUAPI_Handler' ) ) :

class EUAPI_Handler {

	/**
	 * Temporary store the data fetched from remote repo, so it only gets loaded once per class instance
	 */
	public $data;

	/**
	 * Class Constructor
	 *
	 * @since 1.0
	 * @param array $config configuration
	 * @return void
	 */
	public function __construct() {

		$this->config = apply_filters( "euapi_{$this->config['type']}_handler_config", $this->config );

	}

	function get_plugin_url() {
		return false;
	}

	function get_package_url() {
		return false;
	}

	function get_file() {
		return $this->config['file'];
	}

	function get_current_version() {

		if ( isset( $this->item ) )
			return $this->item->get_version();
		else
			return false;

	}

	function get_new_version() {

		if ( isset( $this->new_version ) )
			return $this->new_version;
		else
			return $this->new_version = $this->fetch_new_version();

	}

	function fetch_new_version() {
		return false;
	}

	function fetch_info() {
		return false;
	}

	function get_update() {

		if ( isset( $this->update ) )
			return $this->update;

		return $this->update = new EUAPI_Update( array(
			'slug'        => $this->get_file(),
			'new_version' => $this->get_new_version(),
			'url'         => $this->get_plugin_url(),
			'package'     => $this->get_package_url(),
			'config'      => $this->get_config(),
		) );

	}

	function get_info() {

		if ( isset( $this->info ) )
			return $this->info;

		if ( !( $info = $this->fetch_info() ) )
			return $this->info = false;

		return $this->info = new EUAPI_Info( $info );

	}

	function get_config() {
		return $this->config;
	}

	function get_type() {
		return $this->config['type'];
	}

}

endif; // endif class exists
