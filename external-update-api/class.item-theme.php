<?php

class EUAPI_Item_Theme {

	var $type = 'theme';

	function __construct( $theme, $data ) {

		$this->file    = $theme;
		$this->url     = $data['ThemeURI'];
		$this->version = $data['Version'];
		$this->data    = $data;

	}

	function get_version() {
		return $this->version;
	}

}
