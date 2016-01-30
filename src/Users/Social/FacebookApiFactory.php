<?php

namespace Extasy\Users\Social;


class FacebookApiFactory {
	protected static $instance = null;
	public static function setInstance( $value ) {
		self::$instance = $value;
	}
	public static function getInstance() {
		if ( !empty( self::$instance )) {
			return self::$instance;
		} else {
			return new FacebookApi();
		}
	}
} 