<?php

class EUAPI_Item_Theme extends EUAPI_Item {

	var $type = 'theme';

	function __construct( $theme, array $data ) {

		$this->file    = $theme;
		$this->url     = $data['ThemeURI'];
		$this->version = $data['Version'];
		$this->data    = $data;

	}

}
