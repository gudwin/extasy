<?php

namespace Extasy;
use \Trace;

class SimpleCache extends \Faid\SimpleCache {
	const TraceCategory = 'SimpleCache';
	public static function get($key) {
		Trace::addMessage(self::TraceCategory,'Start loading cache: '. $key );
		$result = parent::get( $key );
		Trace::addMessage(self::TraceCategory,'Cache loaded: '. $key );
		return $result;
	}
	public static function set($key, $value, $timeActual = 0 ) {
		Trace::addMessage(self::TraceCategory,'Start setting cache: '. $key );
		$result = parent::set( $key, $value, $timeActual );
		Trace::addMessage(self::TraceCategory,'Cache set: '. $key );
		return $result;
	}
	public static function clear($key) {
		Trace::addMessage(self::TraceCategory,'Start cleaning cache: '. $key );
		parent::clear( $key );
		Trace::addMessage(self::TraceCategory,'Cache deleted: '. $key );
	}

}
