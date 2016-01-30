<?php
namespace Extasy {
	class ClassLocator {
        /**
         * Autoload function that uses standart PSR-0 for class load
         *
         * @param $className
         */
        public static function psr4autoload( $className ) {
            $fileName = str_replace('\\','/', $className );

            if ( $lastNsPos = strrpos( $className, '\\' ) ) {
                $namespace = substr( $className, 0, $lastNsPos );
                $isExtasyNameSpace = substr( $namespace, 0, 6 ) == 'Extasy';
                if ( $isExtasyNameSpace ) {
                    $fileName =  substr( $className, 6 );
                    $fileName = str_replace( '\\','/', $fileName );
					$isTestsNameSpace = substr( $namespace, 0, 12 ) == 'Extasy\\tests';
					if ( $isTestsNameSpace ) {
						$fileName =  substr( $className, 13 );
						$fileName = str_replace( '\\','/', $fileName );
						$fileName = '../tests/' . $fileName;
					}
                }
            }
			$result = self::findClassNameIfNotExists(
                          $fileName,
                          $className,
                          array( APPLICATION_PATH, SYS_ROOT, LIB_PATH )
            );

            return $result;
        }

		public static function findClassNameIfNotExists( $path, $className, $baseDirs = array() ) {
			if ( !class_exists( $className ) ) {
				return self::findClassName( $path, $baseDirs );
			} else {
				return true;
			}
		}

		public static function findClassName( $path, $baseDirs = array() ) {
			if ( empty( $baseDirs ) ) {
				$baseDirs = array( APPLICATION_PATH, LIB_PATH );
			}
			foreach ( $baseDirs as $dirName ) {
				$fullPath = $dirName . $path;
				$result   = self::loadFile( $fullPath );
				if ( $result ) {
					return true;
				}
			}

			return false;
		}

		public static function loadFile( $path ) {
			$path .= '.php';
            if ( file_exists( $path ) ) {
				require_once $path;
				return true;
			}

			return false;
		}
	}
}