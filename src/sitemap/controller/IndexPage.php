<?
namespace Extasy\sitemap\controller {
	use Extasy\ORM\DBSimple;
	use \IndexPage as IndexPageModel;

	class IndexPage extends \Sitemap_Controller_Data_List {
		const schemaName = 'index';
		const pagingSize = 50;
		const title = 'Просмотр списка подразделов';
		protected $sitemapInfo = array();
		public function __construct() {
			$doc = DBSimple::get( IndexPageModel::TableName,'' );
			$doc = new IndexPageModel($doc );
			$this->sitemapInfo = $doc->getSiteMapData();
			$this->title = self::title;
			$this->begin = array(
				self::title => '#'
			);

			$this->parent = intval($this->sitemapInfo['id']);
			$this->paging_size = self::pagingSize;
			parent::__construct();
		}
	}
}
?>