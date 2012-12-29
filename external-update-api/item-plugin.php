<?php

class EUAPI_Item_Plugin extends EUAPI_Item {

	var $type = 'plugin';

	function __construct( $plugin, array $data ) {

		$this->file    = $plugin;
		$this->url     = $data['PluginURI'];
		$this->version = $data['Version'];
		$this->data    = $data;

	}

}
