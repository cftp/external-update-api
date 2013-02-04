<?php

defined( 'ABSPATH' ) or die();

if ( ! class_exists( 'EUAPI_Handler_Github' ) ) :

class EUAPI_Handler_Github extends EUAPI_Handler {

	/**
	 * Class Constructor
	 *
	 * @since 1.0
	 * @param array $config configuration
	 * @return void
	 */
	public function __construct( array $config = array() ) {

		if ( !isset( $config['github_url'] ) or !isset( $config['file'] ) )
			return;

		$defaults = array(
			'type'         => 'plugin',
			'access_token' => $this->find_access_token( $config['github_url'] ),
			'folder_name'  => dirname( $config['file'] ),
			'file_name'    => basename( $config['file'] ),
			'sslverify'    => true,
		);

		$path = trim( parse_url( $config['github_url'], PHP_URL_PATH ), '/' );
		list( $username, $repo ) = explode( '/', $path, 2 );

		$defaults['base_url'] = sprintf( 'https://raw.github.com/%1$s/%2$s/master',
			$username,
			$repo
		);
		$defaults['zip_url'] = sprintf( 'https://api.github.com/repos/%1$s/%2$s/zipball',
			$username,
			$repo
		);

		$config = wp_parse_args( $config, $defaults );

		parent::__construct( $config );

	}

	function find_access_token( $key ) {

		$op = get_option( 'euapi_github_access_token' );

		if ( !empty( $op ) )
			return $op;

		return null;

	}

	/**
	 * Get New Version from github
	 *
	 * @since 1.0
	 * @return false|string New version number
	 */
	public function fetch_new_version() {

		$response = EUAPI::fetch( $this->get_file_url(), array(
			'sslverify' => $this->config['sslverify']
		) );

		if ( is_wp_error( $response ) )
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

		$url = trailingslashit( $this->config['base_url'] ) . $file;

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

	/**
	 * @return WP_Error|EUAPI_info
	 */

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

		$response = EUAPI::fetch( $file, array(
			'sslverify' => $this->config['sslverify']
		) );

		if ( is_wp_error( $response ) )
			return $response;

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

		return new EUAPI_Info( $info );

	}

}

endif; // endif class exists
