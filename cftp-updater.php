<?php
/*
Plugin Name: CFTP Updater
Description: Get updates to CFTP code from GitHub using the External Update API
Version:     1.0.2
Author:      Code for the People
Author URI:  http://codeforthepeople.com/

Copyright © 2013 Code for the People Ltd

            _____________
           /      ____   \
     _____/       \   \   \
    /\    \        \___\   \
   /  \    \                \
  /   /    /          _______\
 /   /    /          \       /
/   /    /            \     /
\   \    \ _____    ___\   /
 \   \    /\    \  /       \
  \   \  /  \____\/    _____\
   \   \/        /    /    / \
    \           /____/    /___\
     \                        /
      \______________________/

*/

defined( 'ABSPATH' ) or die();

/**
 * [cftp_update_handler description]
 *
 * @author John Blackbourn
 * @param  EUAPI_Handler|null $handler [description]
 * @param  EUAPI_Item         $item    [description]
 * @return EUAPI_Handler|null          [description]
 */
function cftp_update_handler( EUAPI_Handler $handler = null, EUAPI_Item $item ) {

	$url = untrailingslashit( $item->url );

	if ( preg_match( '#^https://github\.com/(cftp|imsimond|johnbillion|simonwheatley|scottsweb)/#', $url ) ) {

		$handler = new EUAPI_Handler_GitHub( array(
			'type'         => $item->type,
			'file'         => $item->file,
			'github_url'   => $url,
			'access_token' => get_option( 'euapi_github_access_token', null ),
			'sslverify'    => false
		) );

	}

	return $handler;

}

add_filter( 'euapi_plugin_handler', 'cftp_update_handler', 9, 2 );
add_filter( 'euapi_theme_handler',  'cftp_update_handler', 9, 2 );

add_action( 'plugins_loaded', function() {
	if ( function_exists( 'euapi_flush_transients' ) ) {
		register_activation_hook( __FILE__,   'euapi_flush_transients' );
		register_deactivation_hook( __FILE__, 'euapi_flush_transients' );
	}
} );
