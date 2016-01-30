<?php

namespace Extasy\Columns {
	use \Faid\UParser;
	use \Faid\Configure\Configure;
	use \Extasy\CMS;
	use \InvalidArgumentException;

	class Password extends Input {
		const MIN_LENGTH = 8;

		public function setValue( $newValue ) {
			if ( empty( $newValue ) ) {
				return;
			}
			$this->aValue = self::hash( $newValue );
		}

		public function getAdminFormValue() {
			$tpl       = __DIR__ . DIRECTORY_SEPARATOR . 'password/admin-form.tpl';
			$parseData = array(
				'name'       => $this->szFieldName,
				'forceInput' => empty( $this->aValue )
			);
			return UParser::parsePHPFile( $tpl, $parseData );
		}

		public static function hash( $str ) {


			$salt   = Configure::read( CMS::SaltConfigureKey );
			$salt   = md5( $salt );
			$result = crypt( $str, $salt );
			return $result;
		}

		public static function validatePassword( $value ) {
			if ( mb_strlen( $value ) < self::MIN_LENGTH ) {
				throw new InvalidArgumentException( sprintf( 'Password too short. Must be minimum %d symbols length',
															 self::MIN_LENGTH ) );
			}
			//
			$patterns = [
				'/[a-zA-Z]/',
				'/[0-9]/',
				'/[\/\,\.\-\_\!\~\#\$\%\^\&\*\(\)\-\+\=\>\<\?\[\]\{\}\@]/',
			];
			foreach ( $patterns as $pattern ) {
				if ( !preg_match( $pattern, $value ) ) {
					throw new InvalidArgumentException( 'Password must contains three alphabets of symbols: numbers, special symbols and latin alphabet' );
				}
			}
			//
			self::scanForDuplicates( $value );

		}
		public static function generatePassword() {
			$alphabet = 'abcdefghijklmnopqrstuvwxyz';
			$alphabetUpper = strtoupper( $alphabet);
			$numbers = '0123456789';
			$specialSymbols = ['/','\\',',','.','-','_','!','~','#','$','%','^','&','*','(',')','-','+','=','>','<','?','[',']','{','}','@'];
			$chars = $alphabet . $alphabetUpper . $numbers . implode('', $specialSymbols );
			$count = mb_strlen($chars);

			$valid = false;

			while( !$valid ) {
				$value = '';
				for ( $i = 0; $i <self::MIN_LENGTH; $i++ ) {
					$index = rand(0, $count - 1);
					$value .= mb_substr($chars, $index, 1);
				}
				try {
					self::validatePassword( $value );
					$valid = true;
				}
				catch (\InvalidArgumentException $e ) {

				}
			}
			return $value;

		}

		protected static function scanForDuplicates( $value ) {
			for ( $i = 0; $i < mb_strlen( $value ); $i++ ) {
				$char  = mb_substr( $value, $i, 1 );
				$pos   = mb_strpos( $value, $char,  $i + 1 );
				$count = 1;
				while ( $pos !== false ) {
					$count++;
					$pos = mb_strpos( $value, $char, $pos + 1 );
				}
				if ( $count > 2 ) {
					throw new InvalidArgumentException( 'Any symbol in password can`t repeat more than 2 times ' );
				}
			}
		}
	}
}