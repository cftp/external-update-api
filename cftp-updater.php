<?php
/*
Plugin Name: CFTP Updater
Description: Get updates to CFTP code from GitHub
Version:     1.0
Author:      Code for the People
Author URI:  http://codeforthepeople.com/

Copyright © 2012 Code for the People Ltd

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

function cftp_update_handler( $handler, $item ) {

	$url = untrailingslashit( $item->url );

	if ( preg_match( '#^https://github\.com/(cftp|imsimond|johnbillion|simonwheatley)/#', $url ) ) {

		$handler = new EUAPI_Handler_Github( array(
			'type'       => $item->type,
			'file'       => $item->file,
			'github_url' => $url,
			'sslverify'  => false
		) );

	}

	return $handler;

}

add_filter( 'euapi_plugin_handler', 'cftp_update_handler', 9, 2 );
add_filter( 'euapi_theme_handler',  'cftp_update_handler', 9, 2 );

defined( 'ABSPATH' ) or die();
