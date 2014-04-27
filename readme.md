# External Update API #

**Contributors:** codeforthepeople, johnbillion  
**Tags:** updates, github  
**Requires at least:** 3.7  
**Tested up to:** 3.9  
**Stable tag:** 0.5  
**License:** GPL v2 or later  

Add support for updating themes and plugins via external sources. Includes an update handler for plugins and themes hosted in public or private repos on GitHub.

## Description ##

Add support for updating themes and plugins via external sources instead of the WordPress.org repos. Includes an update handler for plugins and themes hosted on GitHub.

## Installation ##

1. Download the plugin ZIP file and extract it into your plugins directory, or clone the repo into your plugins directory with `git clone git@github.com:cftp/external-update-api`.
2. Activate the plugin.
3. See the Usage section below.

### Usage ###

The plugin comes bundled with an update handler for GitHub. To add a handler for a different external source, see the 'Writing a new Handler' section below.

You can tell the update API to use a public or private GitHub repo to update a plugin or theme on your site. To do this, hook into the `euapi_plugin_handler` or `euapi_theme_handler` hook, respectively, and return a handler for your plugin or theme.

Plugin Example:

```
function my_plugin_update_handler( EUAPI_Handler $handler = null, EUAPI_Item_Plugin $item ) {

	if ( 'my-plugin/my-plugin.php' == $item->file ) {

		$handler = new EUAPI_Handler_GitHub( array(
			'type'       => $item->type,
			'file'       => $item->file,
			'github_url' => 'https://github.com/my-username/my-plugin',
			'http'       => array(
				'sslverify' => false,
			),
		) );

	}

	return $handler;

}
add_filter( 'euapi_plugin_handler', 'my_plugin_update_handler', 10, 2 );
```

Theme Example:

```
function my_theme_update_handler( EUAPI_Handler $handler = null, EUAPI_Item_Theme $item ) {

	if ( 'my-theme/style.css' == $item->file ) {

		$handler = new EUAPI_Handler_GitHub( array(
			'type'       => $item->type,
			'file'       => $item->file,
			'github_url' => 'https://github.com/my-username/my-theme',
			'http'       => array(
				'sslverify' => false,
			),
		) );

	}

	return $handler;

}
add_filter( 'euapi_theme_handler', 'my_theme_update_handler', 10, 2 );
```

If your repo is private then you'll need to pass in an additional `access_token` parameter that contains your OAuth access token.

You can see some more example handlers in our [CFTP Updater repo](https://github.com/cftp/cftp-updater).

### Writing a new Handler ###

To write a new handler, your best bet is to copy the `EUAPI_Handler_GitHub` class included in the plugin and go from there. See the `EUAPI_Handler` class (and, optionally, the `EUAPI_Handler_Files` class) for the abstract methods which must be defined in your class.

## Frequently Asked Questions ##

None yet.

## Upgrade Notice ##

### 0.5 ###

* Fix integration with theme updates.
* EUAPI is now a network-only plugin when used on Multisite.
* Eat our own dog food. EUAPI now handles its own updates through GitHub.
* At long-last fix the wonky compatibility with WordPress 3.7+.
* Increase the minimum required WordPress version to 3.7.

## Changelog ##

### 0.5 ###

* Fix integration with theme updates.
* EUAPI is now a network-only plugin when used on Multisite.
* Eat our own dog food. EUAPI now handles its own updates through GitHub.
* Add support for an upgrade notice (not used by default).
* Introduce an abstract `EUAPI_Handler_Files` class to simplify extension by other handlers.
* Inline docs improvements.

### 0.4 ###

* At long-last fix the wonky compatibility with WordPress 3.7+.
* Increase the minimum required WordPress version to 3.7.

### 0.3.5 ###

* Support JSON-encoded API requests in addition to serialisation. This is pre-emptive support for WordPress 3.7.

### 0.3.4 ###

* Support the upcoming SSL communication with `api.wordpress.org`

### 0.3.3 ###

* Correct a method name in the `EUAPI_Handler` class.

### 0.3.2 ###

* Change a method name and inline docs to clarify that both plugins and themes are supported.

### 0.3.1 ###

* Prevent false positives when reporting available updates.
* Prevent multiple simultaneous updates breaking due to a variable name clash.

### 0.3 ###

* Allow a handler to return boolean false to prevent update checks being performed altogether.

### 0.2.4 ###

* First public release.

## Screenshots ##

None yet.
