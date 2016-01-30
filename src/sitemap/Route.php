<?php
namespace Extasy\sitemap {
	use Faid\Dispatcher\HttpRoute;
	use \Faid\DBSimple;
	use \Sitemap_History;
	use \Register;
	use \ForbiddenException;
	use \Extasy\Validators\IsModelClassNameValidator;


	class Route extends HttpRoute {
		protected $sitemapInfo = array();
		protected static $currentUrlInfo = array();
		public static function setCurrentUrlInfo( $info ) {
			self::$currentUrlInfo = $info;
		}
		public static function getCurrentUrlInfo() {
			return self::$currentUrlInfo;
		}

		public function __construct( $config = array() ) {
			if ( empty( $config[ 'url' ] ) ) {
				$config[ 'url' ] = '/';
			}
			parent::__construct( $config );
		}

		public function test( $request ) {
			$this->request = $request;
			$url           = $this->getCurrentUrl();
			try {
				$this->sitemapInfo = \Sitemap_Sample::getByUrl( $url );
			}
			catch ( \SiteMapException $e ) {
				$this->sitemapInfo = $this->searchInHistory( $url );
			}
			if ( !empty( $this->sitemapInfo ) ) {
				self::$currentUrlInfo = $this->sitemapInfo;
			}
			return !empty( $this->sitemapInfo );
		}

		public function dispatch() {
			if ( !empty( $this->sitemapInfo ) ) {
				if ( !empty( $this->sitemapInfo[ 'script' ] ) ) {

					$this->loadAsScript();
				} else {
					$this->loadAsDocument();
				}

			} else {
				throw new ForbiddenException( 'Method requires sitemap information. Run method `test` first' );
			}
		}

		protected function loadAsScript() {
			$dirs = array( APPLICATION_PATH, LIB_PATH, EXTASY_PATH );
			foreach ( $dirs as $baseDir ) {
				$path = $baseDir . $this->sitemapInfo[ 'script' ];
				if ( file_exists( $path ) && is_readable( $path ) ) {
					$result = require_once $path;
					if ( is_object( $result ) ) {
						$this->controller = $result;
					}
				}
			}
			if ( !empty( $this->controller ) ) {
				$this->controller->process();
			} else {
			}

		}

		protected function loadAsDocument() {
			$validator = new IsModelClassNameValidator( $this->sitemapInfo[ 'document_name' ] );
			if ( !$validator->isValid() ) {
				throw new \ForbiddenException( "Not a model: {$this->sitemapInfo['document_name']}" );
			}
            $fieldsInfo = call_user_func( [$this->sitemapInfo['document_name'],'getFieldsInfo']);
            if ( !empty( $fieldsInfo['sitemap']) && !empty( $fieldsInfo['sitemap']['controller'])) {
                $className = $fieldsInfo['sitemap']['controller'];
            } else {
                $className = sprintf('%s_controller', $this->sitemapInfo [ 'document_name' ]);
            }
			//
			$_REQUEST[ 'id' ] = $_GET[ 'id' ] = $_POST[ 'id' ] = $this->sitemapInfo[ 'document_id' ];
			//
			$this->request->set( 'id', $this->sitemapInfo[ 'document_id' ] );
			//
			$this->controller = new $className( $this->sitemapInfo );

			$this->controller->process();

		}

		protected function getCurrentUrl() {
			$result = sprintf( '//%s%s', $this->request->domain(), $this->request->uri() );
			return $result;
		}

		protected function searchInHistory( $url ) {
			$found = DBSimple::get( SITEMAP_HISTORY_TABLE, array( 'url' => $url ) );
			if ( !empty( $found ) ) {
				return \Sitemap_Sample::get( $found[ 'page_id' ] );
			}
			return null;
		}
	}
}