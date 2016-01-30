<?php
namespace Extasy\Service {
	class Fixture extends FilesService {
		protected $extensions = [ ];
		protected $excludePaths = [ ];
		protected $excludeFolderNames = [ ];

		public function __construct() {
			$this->extensions         = [ 'php',
										  'tpl',
										  '.htaccess',
										  'gif',
										  'png',
										  'jpg',
										  'css',
										  'scss',
										  'js',
										  'sh',
										  'p12', // google sertificate
										  'rb',
										  'conf',
										  'html',
										  'eot',
										  'svg',
										  'ttf',
										  'woff'
			];
			$this->excludePaths       = array( realpath( FILE_PATH ),
											   realpath( APPLICATION_PATH . 'cache/' ),
											   realpath( SYS_ROOT . 'logs/' ),
											   realpath( SYS_ROOT . 'migration/' ),
											   realpath( APPLICATION_PATH . 'config/' ),
											   realpath( VIEW_PATH . 'blocks/parsed/' )
			);
			$this->excludeFolderNames = [ '.sass-cache',
										  '.svn',
										  'node_modules',
										  'bower_components',
										  '.idea',
										  '.mailrc',
										  '.DS_Store',
										  'app-tests',
										  'cache',
										  'logs'
			];
		}

		public function setUp() {
			$this->loadTable();
			file_put_contents( $this->getMapPath(), json_encode( $this->filesMap ) );
			print '[SUCCESS]' . "\r\n";
		}

		protected function loadTable() {
			$filesMap = $this->scanFiles( '', SYS_ROOT );
			foreach ( $filesMap as $path ) {
				$this->filesMap[ ] = array(
					$path,
					$this->getCheckSum( $path )
				);
			}
		}

		protected function scanFiles( $folderPrefix, $baseFolder ) {
			if ( $this->hasToSkipFolder( $baseFolder ) ) {
				return [ ];
			}
			$result = [ ];
			$dir    = dir( $baseFolder );
			while ( false !== ( $fileName = $dir->read() ) ) {
				if ( ( $fileName == '.' ) || ( $fileName == '..' ) ) {
					continue;
				}
				if ( $this->hasToSkipFileName( $fileName ) ) {
					continue;
				}
				$returnPath = $folderPrefix . '/' . $fileName;
				$path       = $baseFolder . '/' . $fileName;
				if ( is_dir( $path ) ) {
					$add    = $this->scanFiles( $returnPath, $path );
					$result = array_merge( $result, $add );
				} elseif ( is_file( $path ) ) {
					$result[ ] = $returnPath;
				}
			}
			$dir->close();
			return $result;
		}

		protected function hasToSkipFolder( $path ) {
			$path = realpath( $path );
			foreach ( $this->excludePaths as $row ) {
				if ( $path == $row ) {
					return true;
				}
			}
			return false;
		}

		protected function hasToSkipFileName( $fileName ) {
			foreach ( $this->excludeFolderNames as $row ) {
				if ( $fileName == $row ) {
					return true;
				}
			}
			return false;
		}
	}
}