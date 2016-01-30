<?php

namespace Extasy\Users\Social;


class VkontakteApiFactory {
	protected static $instance = null;
	public static function setInstance( $value ) {
		self::$instance = $value;
	}
	public static function getInstance() {
		if ( !empty( self::$instance )) {
			return self::$instance;
		} else {
			return new VkontakteApi();
		}
	}
} 