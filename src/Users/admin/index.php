<?
//************************************************************//
//                                                            //
//         Выводит полный список пользователей с пейджингом   //
//       Copyright (c) 2008 Ext-CMS (http://www.ext-cms.com/) //
//               отдел/сектор                                 //
//       Email:    info@gisma.ru (http://www.gisma.ru/)       //
//                                                            //
//  Разработчик: Gisma (23.01.2009)                           //
//  Модифицирован:  23.01.2009  by Gisma                      //
//                                                            //
//************************************************************//
define( 'PAGE_SIZE', 50 );
use \Extasy\Users\admin\Page;

class Users_Admin_Index extends Page {
	public function __construct() {
		parent::__construct();
		$this->addGet( 'page', 'main' );
		$this->addPost( 'id,delete', 'delete' );
	}

	public function main( $nPage = 0 ) {
		// Получаем элементы
		try {
			$aItem      = UsersDBManager::selectPaged( $nPage, PAGE_SIZE );
			$nItemCount = UsersDBManager::$nItemCount;
		}
		catch ( Exception $e ) {
			$this->jump( './' );
		}
		//
		$szTitle = 'Список пользователей.';
		if ( !empty( $nPage ) ) {
			$szTitle .= ' Страница #' . intval( $nPage );
		}
		$aBegin       = array(
			'Пользователи' => '#',
			$szTitle       => '#',
		);
		$aButton      = array(
			'Добавить пользователя' => './manage?insert=1'
		);
		$aTableHeader = array(
			array( '&nbsp;', '5' ),
			array( 'Логин', '50' ),
			array( 'Дата последней активности', '20' ),
			array( 'Редактировать', 10 ),
		);
		//
		$design = CMSDesign::getInstance();
		$design->begin( $aBegin, $szTitle );
		$design->documentBegin();
		$design->header( $szTitle );
		$design->buttons( $aButton );
		$design->formBegin();
		$design->paging( $nPage, ceil( $nItemCount / PAGE_SIZE ) );
		$design->tableBegin();
		$design->tableHeader( $aTableHeader );
		foreach ( $aItem as $row ) {
			$design->rowBegin();
			$design->listCell( sprintf( '<input type="checkbox" name="id[]" value="%d"/>', $row[ 'id' ] ) );
			$design->listCell( sprintf( '<a href="manage?id=%d">%s</a>', $row['id'], $row[ 'login' ] ));
			$design->listCell( $row[ 'last_activity_date' ] );
			$design->editCell( 'manage?id=' . $row[ 'id' ] );
			$design->rowEnd();
		}

		$design->tableEnd();
		$design->paging( $nPage, ceil( $nItemCount / PAGE_SIZE ) );
		$design->submit( 'delete', 'Удалить', 'Вы уверены, что хотите удалить этих пользователей?' );
		$design->formEnd();
		$design->documentEnd();
		$design->end();
		$this->output();
	}

	public function delete( $aId ) {

		foreach ( $aId as $row ) {
			try {
				$oAccount = new UserAccount( $row );
				$oAccount->delete();
			}
			catch ( Exception $e ) {
				$this->addError( $e->getMessage() );
			}
		}
		$this->jumpBack();
	}

}

?>