<?
//************************************************************//
//                                                            //
//              CMS-скрипт управления пользователем           //
//       Copyright (c) 2006  ООО Extasy-CMS                   //
//               отдел/сектор                                 //
//       Email:   gisma@ext-cms.com                           //
//                                                            //
//  Разработчик: Gisma (29.08.2008)                           //
//  Модифицирован:  29.08.2008  by Gisma                      //
//                                                            //
//************************************************************//
//  27.10.2009 - Обновление скрипта, нормализуем работу с 
//  правами пользователя

require_once CONTROL_PATH . 'checkbox.php';
require_once CONTROL_PATH . 'input.php';

class AdminCMSUsers extends AdminPage {

	public function __construct() {
		//
		$auth = CMSAuth::getInstance();
		if ( !$auth->isSuperAdmin( UsersLogin::getCurrentSession() ) ) {
			$this->addError( 'Access denied' );
			$this->jump( \Extasy\CMS::getDashboardWWWRoot() );
		}
		parent::__construct();
		// Вызов формы редактирования
		$this->addGet( 'id', 'showEdit' );

		// Вызов формы добавления
		$this->addGet( 'add', 'showAdd' );
		// Вызов функции редактирования
		$this->addPost( 'id,login,password,rights', 'postEdit' );
		$this->addPost( 'id,login,password', 'postEdit' );
		// Вызов функции добавления
		$this->addPost( 'login,password,rights', 'postAdd' );
		$this->addPost( 'login,password', 'postAdd' );
		// Удаление
		$this->addPost( 'id', 'delete' );
	}

	/**
	 *   Возвращает список пользователей
	 * @return
	 */
	public function main() {
		$userList = CMSUsers::selectAllUsers();
		//
		$title       = 'Пользователи';
		$begin       = array(
			$title => '#'
		);
		$tableHeader = array(
			array( '&nbsp', '5' ),
			array( _msg( 'Логин' ), 90 ),
			array( _msg( 'Редактировать' ), 5 ),
		);
		$button      = array(
			'Добавить пользователя' => 'users.php?add=1'
		);
		//
		$this->outputHeader( $begin, $title );
		$design = CMSDesign::getInstance();
		$design->buttons( $button );
		$design->formBegin();
		$design->tableBegin();
		$design->tableHeader( $tableHeader );
		foreach ( $userList as $row ) {
			$checkbox        = new CCheckbox();
			$checkbox->name  = 'id[]';
			$checkbox->value = $row[ 'id' ];

			$design->rowBegin();
			$design->listCell( $checkbox );
			$design->listCell( $row[ 'login' ] );
			$design->editCell( 'users.php?edit=1&id=' . $row[ 'id' ] );
			$design->rowEnd();
		}
		$design->tableEnd();
		$design->submit( 'submit', 'Удалить' );
		$this->outputFooter();
		$this->output();
	}

	public function showAdd() {
		$szTitle = 'Создание нового аккаунта';
		$aBegin  = array(
			$szTitle => '#',
		);
		//
		$this->defaultOutput( $aBegin, $szTitle, array() );
	}

	public function showEdit( $id ) {
		$szTitle = 'Редактирование пользователя';
		$aBegin  = array(
			$szTitle => '#',
		);
		$aData   = CMSUsers::getUser( $id );
		if ( empty( $aData ) ) {
			$this->jump( 'users.php' );
		}
		//
		$this->defaultOutput( $aBegin, $szTitle, $aData );
	}

	public function postAdd( $login, $password, $rights = array() ) {

		//
		CMSUsers::createUser( $login, $password, $rights );
		$this->addAlert( 'Страница была добавлена' );
		$this->jump( 'users.php' );
	}

	public function postEdit( $id, $login, $password, $rights = array() ) {
		//
		CMSUsers::updateUser( $id, $login, $password, $rights );
		$this->addAlert( 'Запись была отредактирована' );
		$this->jump( 'users.php?id=' . intval( $id ) );

	}

	public function delete( $aId ) {
		foreach ( $aId as $nId ) {
			CMSUsers::deleteUser( $nId );
		}
		$this->jump( 'users.php' );
	}

	protected function displayRights( $rightsString ) {
		// Получаем все права существующие в системе
		$register       = new SystemRegister( 'System/CMS/userRights' );
		$fullRightsList = SystemRegisterHelper::exportData( $register->getId() );

		$usersRights = explode( "\r\n", $rightsString );
		// Перебор прав
		$result = array();
		foreach ( $fullRightsList as $key => $row ) {
			$checkbox        = new CCheckbox();
			$checkbox->name  = 'rights[]';
			$checkbox->value = $key;
			$checkbox->title = $row[ 'comment' ];
			if ( in_array( $key, $usersRights ) ) {
				$checkbox->checked = true;
			}
			$result[ ] = $checkbox;
		}
		return implode( '<br/>', $result );
	}

	public function defaultOutput( $begin, $title, $data ) {

		$input  = new CInput();
		$design = CMSDesign::getInstance();
		$this->outputHeader( $begin, $title );

		$design->formBegin();
		$design->submit( 'submit', _msg( 'Сохранить' ) );
		$design->tableBegin();
		//
		$input->name  = 'login';
		$input->value = isset( $data[ 'login' ] ) ? $data[ 'login' ] : '';
		$design->row2cell( 'Логин', $input );
		$input->name  = 'password';
		$input->value = isset( $data[ 'password' ] ) ? $data[ 'password' ] : '';
		$design->row2cell( 'Пароль', $input );
		$rights = isset( $data[ 'rights' ] ) ? $data[ 'rights' ] : '';
		$design->row2cell( 'Права', $this->displayRights( $rights ) );
		$design->tableEnd();
		if ( isset( $data[ 'id' ] ) ) {
			$design->hidden( 'id', $data[ 'id' ] );
		}
		$design->submit( 'submit', _msg( 'Сохранить' ) );

		$design->formEnd();
		$this->outputFooter();
		$this->output();
	}
}

?>