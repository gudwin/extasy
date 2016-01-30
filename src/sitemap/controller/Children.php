<?php
namespace Extasy\sitemap\controller {
	use \Sitemap_Sample;
	class Children extends \Sitemap_Controller_Data_List {
		const PagingSize = 50;
		protected $sitemapInfo = array();
		public function __construct()  {
			parent::__construct();
			$this->autoConfigure();
		}
		protected function autoConfigure() {
			$this->autoConfigureFromHttpRequest();
			//
			$this->setupUI();

			$this->parent = intval( $this->sitemapInfo['id']);
			$this->paging_size = self::PagingSize;
		}
		protected function autoConfigureFromHttpRequest( ) {
			if ( isset( $_GET['pageurl'])) {
				$url = $_GET['pageurl'];
				$this->sitemapInfo = Sitemap_Sample::getByUrl( $url );
			} elseif ( !empty( $_REQUEST['parent'] ) ) {
				$id = intval( $_REQUEST['parent'] );
				$this->sitemapInfo = Sitemap_Sample::get( $id );
			}
			if ( empty( $this->sitemapInfo )) {
				throw new \NotFoundException( sprintf( 'Parent page not found '));
			}
		}
		protected function setupUI( ) {
			$this->standalone = false;
			$this->title = $this->sitemapInfo['name'];
			$this->begin = array(
				$this->sitemapInfo['name'] => '#'
			);
		}
	}

}