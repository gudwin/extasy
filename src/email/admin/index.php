<?
//************************************************************//
//                                                           //
//              Страница отправки сообщения                   //
//       Copyright (c) 2008 Ext-CMS (http://www.ext-cms.com/) //
//               отдел/сектор                                 //
//       Email:    info@gisma.ru (http://www.gisma.ru/)       //
//                                                            //
//  Разработчик: Gisma (06.10.2008)                           //
//  Модифицирован:  06.10.2008  by Gisma                      //
//                                                            //
//************************************************************//
use \Extasy\Audit\Record;

class Email_Admin_Index extends AdminPage {
	public function __construct() {
		parent::__construct();
		$this->addPost( 'subject,to,content', 'post' );
	}

	public function main() {
		require_once CONTROL_PATH . 'input.php';
		require_once CONTROL_PATH . 'htmlarea.php';

		//
		$oSubject         = new CInput();
		$oRecipient       = new CInput();
		$oContent         = new CHtmlarea();
		$oSubject->name   = 'subject';
		$oRecipient->name = 'to';
		$oContent->name   = 'content';
		// width:99%
		$oSubject->style   = 'width:300px;';
		$oRecipient->style = 'width:300px;';

		// Собираем данные для отображения
		$szTitle = 'Отправка письма';
		$aBegin  = array(
			'Почта'  => '#',
			$szTitle => '#',
		);
		$aButton = array(
			'Перейти к настройке smtp' => 'config.php',
			'История отсылки почты'    => 'logs.php',
		);

		// Отображаем
		$design = CMSDesign::getInstance();
		$design->begin( $aBegin, $szTitle );
		$design->documentBegin();
		$design->header( $szTitle );
		$design->buttons( $aButton );
		$design->br();
		$design->formBegin('index.php');
		$design->submit( 'submit', _msg( 'Послать' ) );
		$design->tableBegin();
		$design->row2cell( 'Получатель (адрес email)', $oRecipient->generate() );
		$design->row2cell( 'Тема письма', $oSubject->generate() );
		$design->row2cell( 'Сообщение', $oContent->generate() );
		$design->TableEnd();
		$design->submit( 'submit', _msg( 'Послать' ) );
		$design->formEnd();
		$design->end();
		$this->output();
	}

	/**
	 *   -------------------------------------------------------------------------------------------
	 *   Отправляет письмо
	 * @return
	 *   -------------------------------------------------------------------------------------------
	 */
	public function post( $subject, $to, $content ) {
		require_once LIB_PATH . 'email/controller/send.php';
		try {
			Email_Controller::send( $to, $subject, $content );
			$this->addAlert( 'Ваше письмо отправлено' );
			$this->jump( './index.php' );
		}
		catch ( Exception $e ) {
			$this->addError( 'Ошибка отправки письма' );
			$this->jump( './index.php' );
		}

	}
}

?>