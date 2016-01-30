<?
//************************************************************//
//                                                            //
//          Display form quick-search for user                //
//       Copyright (c) 2008 Ext-CMS (http://www.ext-cms.com/) //
//               отдел/сектор                                 //
//       Email:    info@gisma.ru (http://www.gisma.ru/)       //
//                                                            //
//  Разработчик: Gisma (23.01.2009)                           //
//  Модифицирован:  23.01.2009  by Gisma                      //
//                                                            //
//************************************************************//
use \Extasy\Users\admin\Page;

class Users_Admin_Search extends Page {
	public function __construct() {
		parent::__construct();
		$this->addGet( 'term', 'search' );
		$this->addGet( 'found', 'goManageLogin' );
	}

	/**
	 *   -------------------------------------------------------------------------------------------
	 *   Displaying main form
	 * @return
	 *   -------------------------------------------------------------------------------------------
	 */
	public function main() {
		$szTitle = 'Поиск по пользователям (по логину)';
		$aBegin  = array(
			'Пользователи' => 'index.php',
			$szTitle       => '#',
		);
		//
		$design = CMSDesign::getInstance();
		$design->begin( $aBegin, $szTitle );
		$design->documentBegin();
		$design->header( $szTitle . ' ( минимальное количество символов 2)' );
		$design->contentBegin();

		$userControl       = new CUserSelect();
		$userControl->name = 'search';
		print $userControl;

		?>

		<script type="text/javascript">
			jQuery(function () {
				search.config.onSelect = function (item) {
					window.location = './manage?id=' + item.id;
				}
			});
		</script>
		<?php
		$design->contentEnd();
		$design->documentEnd();
		$design->end();
		$this->output();
	}

	/**
	 *
	 * Отыскивает по ключевому запросу пользователя
	 *
	 * @param string $query
	 */
	public function search( $query ) {
		$results = array();
		try {
			$items = UsersDBManager::searchByLogin( $query );
			foreach ( $items as $row ) {
				$results[ ] = array(
					'id'    => $row[ 'id' ],
					'login' => $row[ 'login' ],
					'email' => $row[ 'email' ]
				);
			}
		}
		catch ( Exception $e ) {
			$results[ ] = array(
				'id'    => 0,
				'login' => 'nothing ',
				'email' => 'found'
			);
		}
		print json_encode( $results );
		die();
	}

}

?>