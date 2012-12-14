<?php

// Prevent loading this file directly - Busted!
if ( !defined('ABSPATH') )
	die('-1');

if ( ! class_exists( 'EUAPI_Handler_Github' ) ) :

class EUAPI_Handler_Github extends EUAPI_Handler {

	/**
	 * Class Constructor
	 *
	 * @since 1.0
	 * @param array $config configuration
	 * @return void
	 */
	public function __construct( $config = array() ) {

		if ( !isset( $config['github_url'] ) or !isset( $config['file'] ) )
			return;

		$defaults = array(
			'type'         => 'plugin',
			'access_token' => null,
			'folder_name'  => dirname( $config['file'] ),
			'file_name'    => basename( $config['file'] ),
			'sslverify'    => true,
		);

		$path = trim( parse_url( $config['github_url'], PHP_URL_PATH ), '/' );
		list( $username, $repo ) = explode( '/', $path, 2 );

		$defaults['api_url'] = sprintf( 'https://api.github.com/repos/%1$s/%2$s',
			$username,
			$repo
		);
		$defaults['raw_url'] = sprintf( 'https://raw.github.com/%1$s/%2$s/master',
			$username,
			$repo
		);
		$defaults['zip_url'] = sprintf( 'https://api.github.com/repos/%1$s/%2$s/zipball',
			$username,
			$repo
		);

		$this->config = wp_parse_args( $config, $defaults );

		parent::__construct();

	}

	/**
	 * Get New Version from github
	 *
	 * @since 1.0
	 * @return int $version the version number
	 */
	public function fetch_new_version() {

		$response = EUAPI::fetch( $this->get_file_url() );

		if ( empty( $response ) )
			return false;

		$data = EUAPI::get_content_data( $response, array(
			'version' => 'Version'
		) );

		if ( empty( $data['version'] ) )
			return false;

		return $data['version'];

	}

	function get_plugin_url() {

		return $this->config['github_url'];

	}

	function get_file_url( $file = null ) {

		if ( empty( $file ) )
			$file = $this->config['file_name'];

		$url = trailingslashit( $this->config['raw_url'] ) . $file;

		if ( !empty( $this->config['access_token'] ) ) {
			$url = add_query_arg( array(
				'access_token' => $this->config['access_token']
			), $url );
		}

		return $url;
	}

	function get_package_url() {

		$url = $this->config['zip_url'];

		if ( !empty( $this->config['access_token'] ) ) {
			$url = add_query_arg( array(
				'access_token' => $this->config['access_token']
			), $url );
		}

		return $url;

	}

	function fetch_info() {

		$fields = array(
			'author'      => 'Author',
			'description' => 'Description'
		);

		switch ( $this->get_type() ) {

			case 'plugin':
				$file = $this->get_file_url();
				$fields['plugin_name'] = 'Plugin Name';
				break;

			case 'theme':
				$file = $this->get_file_url( 'style.css' );
				$fields['theme_name'] = 'Theme Name';
				break;

		}

		$response = EUAPI::fetch( $file );

		if ( empty( $response ) )
			return false;

		$data = EUAPI::get_content_data( $response, $fields );

		$info = array_merge( $data, array(

			'slug'          => $this->get_file(),
			'version'       => $this->get_new_version(),
			'homepage'      => $this->get_plugin_url(),
			'download_link' => $this->get_package_url(),
	#		'requires'      => '',
	#		'tested'        => '',
	#		'last_updated'  => '',
			'downloaded'    => 0,
			'sections'      => array(
				'description' => $data['description'],
			),

		) );

		return $info;

	}

}

endif; // endif class exists
