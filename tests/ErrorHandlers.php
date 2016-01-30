<?php

namespace Extasy\tests;
use \Faid\Configure\Configure;

class  ErrorHandlers {
	const ClassName = '\\Extasy\\tests\\ErrorHandlers';

	public static function onError( $errno, $errstr, $errfile = '', $errline = '' ) {
		var_dump( $errno );
		var_dump( $errstr );
		var_dump( $errfile );
		var_dump( $errline );
		printf( '%s[ %d ] %d: %s', $errfile, $errline, $errno, $errstr );
		die();
	}

	public static function onException( \Exception $e ) {
		var_dump( $e );
		die();
	}

	public static function onFatalError( $message, $file, $line ) {
		$error = sprintf( 'Fatal error "%s"  at : "%s:%d"', $message, $file, $line );
        \Faid\debug\defaultDebugBackTrace();
        die( $error );
	}

	public static function setUp() {
		Configure::write( 'Error.Handler', array( self::ClassName, 'onError' ) );
		Configure::write( 'Exception.Handler', array( self::ClassName, 'onException' ) );
		Configure::write( 'FatalError.Handler', array( self::ClassName, 'onFatalError' ) );
	}
}
