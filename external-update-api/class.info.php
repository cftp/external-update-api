<?php

class EUAPI_Info {

	function __construct( array $args ) {

		foreach ( $args as $k => $v )
			$this->$k = $v;

	}

}
