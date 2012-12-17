<?php
/*
Plugin Name:  External Update API
Description:  Add support for updating themes and plugins via external sources instead of wordpress.org
Version:      0.2
Author:       Code for the People
Author URI:   http://codeforthepeople.com/
Text Domain:  euapi
Domain Path:  /languages/
License:      GPL v2 or later

Copyright © 2012 Code for the People Ltd

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

*/

function euapi_autoloader( $class ) {

	if ( 0 !== strpos( $class, 'EUAPI' ) )
		return;

	$name = str_replace( 'EUAPI_', '', $class );
	$name = str_replace( '_', '-', $name );
	$name = strtolower( $name );

	$file = sprintf( '%1$s/external-update-api/class.%2$s.php',
		dirname( __FILE__ ),
		$name
	);

	if ( file_exists( $file ) )
		include $file;

}

spl_autoload_register( 'euapi_autoloader' );

global $euapi;

$euapi = new EUAPI;

defined( 'ABSPATH' ) or die();
