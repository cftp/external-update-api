<?php

class EUAPI_Item_Plugin {

	var $type = 'plugin';

	function __construct( $plugin, $data ) {

		$this->file    = $plugin;
		$this->url     = $data['PluginURI'];
		$this->version = $data['Version'];
		$this->data    = $data;

	}

	function get_version() {
		return $this->version;
	}

}
