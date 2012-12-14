<?php

class EUAPI_Info {

	function __construct( $args ) {

		foreach ( $args as $k => $v )
			$this->$k = $v;

	}

}
