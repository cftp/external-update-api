# External Update API #

**Contributors:** codeforthepeople, johnbillion  
**Tags:** updates, github  
**Requires at least:** 3.4  
**Tested up to:** 3.6  
**Stable tag:** trunk  
**License:** GPL v2 or later  

Add support for updating themes and plugins via external sources. Includes an update handler for plugins and themes hosted on GitHub.

## Description ##

Add support for updating themes and plugins via external sources instead of the WordPress.org repos. Includes an update handler for plugins and themes hosted on GitHub.

## Installation ##

1. Download the plugin ZIP file and extract it into your plugins directory, or clone the repo into your plugins directory with `git clone git@github.com:cftp/external-update-api`.
2. Activate the plugin.
3. See the Usage section below.

### Usage ###

The plugin comes bundled with an update handler for GitHub. To add a handler for a different external source, see the 'Writing a new Handler' section below.

You can tell the update API to use a public or private GitHub repo to update a plugin or theme on your site. To do this, hook into the `euapi_plugin_handler` or 'euapi_theme_handler' hook, respectively, and return a handler for your plugin or theme.

Example:

`function my_update_handler( EUAPI_Handler $handler = null, EUAPI_Item $item ) {

	if ( 'my-plugin/my-plugin.php' == $item->file ) {

		$handler = new EUAPI_Handler_GitHub( array(
			'type'       => $item->type,
			'file'       => $item->file,
			'github_url' => 'https://github.com/my-username/my-plugin',
			'sslverify'  => false
		) );

	}

	return $handler;

}
add_filter( 'euapi_plugin_handler', 'my_update_handler', 10, 2 );
`

### Writing a new Handler ###

To write a new handler, your best bet is to copy the `EUAPI_Handler_GitHub` class included in the plugin and go from there. See the `EUAPI_Handler` class for the abstract methods which must be defined in your class.

## Frequently Asked Questions ##

None yet.

## Upgrade Notice ##

### 0.3.1 ###

* Allow a handler to return boolean false to prevent update checks being performed altogether.
* Prevent false positives when reporting available updates.
* Prevent multiple simultaneous updates breaking due to a variable name clash.

## Changelog ##

### 0.3.1 ###

* Prevent false positives when reporting available updates.
* Prevent multiple simultaneous updates breaking due to a variable name clash.

### 0.3 ###

* Allow a handler to return boolean false to prevent update checks being performed altogether.

### 0.2.4 ###
* First public release.

## Screenshots ##

None yet.
