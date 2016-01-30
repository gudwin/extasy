<?php
use \Faid;
use \Extasy\Model\Model as extasyDocument;
/**
 *
 * User: Gisma
 * Date: 23.03.13
 * Time: 18:53
 * @package extasycms.testSuite
 */
class extasyTestQuickAddAdminPage  extends  AdminPage {
	public function __construct( ) {
		parent::__construct( );
		$this->addPost('level', 'getByLevel');
		$this->addPost('urls','add');
	}
	public function main() {
		$title = 'Быстрое добавление';
		$path = array(
			extasyTestModel::getLabel( extasyDocument::labelAllItems ) => './index.php',
			$title => '#'
		);
		$input = new CInput();
		$textarea = new CInput();
		$input->name = 'level';
		$textarea->name = 'urls';
		$textarea->rows = 16;
		$textarea->style = 'width:99%';
		// display design layout
		$design = CMSDesign::getInstance();
		$design->layout->Begin( $path );
		CMSDesign::insertScript(\Extasy\CMS::getResourcesUrl().'extasy/js/administrative/testSuite/quick_add.js');
		$design->layout->documentBegin();
		$design->text->header( "Введите url списком. Каждый адрес в отдельной строке" );

		$design->forms->begin();
		$design->table->begin();
		$design->table->fullRow( 'Список url-ов для сканирования:');
		$design->table->fullRow( $textarea );
		$design->table->end();
		$design->forms->submit('submit','Сохранить');
		$design->forms->end();
		$design->text->header( "Добавление URL-ов из карты сайта" );
		$design->forms->begin('./quick_add','post','quickAddForm');
		$design->table->begin();
		$design->table->row2cell('Добавить N-уровней sitemap-дерева', $input );
		$design->table->end();
		$design->forms->submit('getTree','Добавить к списку');
		$design->forms->end();
		$design->layout->documentEnd();

		$design->layout->end();
		$this->output( );
	}

	/**
	 *
	 */
	public function getByLevel( $level ) {
		$list = $this->returnByLevel( 0,$level );
		print json_encode( $list );
		die(0);
	}

	/**
	 * @param $urls
	 */
	public function add( $urls ) {
		$urls = explode("\n", $urls) ;
		foreach ( $urls as $url ) {
			$url = trim( $url );
			if ( empty( $url )) {
				continue;
			}
			if ( ! extasyTestModel::exists( $url ) ) {
				$model = new extasyTestModel();
				if ( preg_match('#^\/\/#', $url )) {
					$url = 'http:' . $url;
				}
				$model->url = $url;
				$model->method = 'GET';
				$model->insert( );
			}
		}
		$msg = sprintf( 'Added %d urls', sizeof( $urls ));
		$this->addAlert( $msg );
		$this->jumpBack( );
	}
	protected function returnByLevel( $parentId, $level ) {
		if ( $level <= 0) {
			return array();
		}
		$result = array();
		$childList = Sitemap_Sample::selectChild( $parentId );
		foreach ( $childList as $row ) {
			$result[] = $row['full_url'];
			if ( !empty( $row['count'] )) {
				$result = array_merge( $result, $this->returnByLevel( $row['id'] , $level - 1 ));
			}
		}
		return $result;
	}
}