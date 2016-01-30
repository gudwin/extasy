<?
//************************************************************//
//                                                            //
//            Класс редактирования документов                 //
//       Copyright (c) 2008 Ext-CMS (http://www.ext-cms.com/) //
//               отдел/сектор                                 //
//       Email:    info@gisma.ru (http://www.gisma.ru/)       //
//                                                            //
//  Разработчик: Gisma (19.10.2008)                           //
//  Модифицирован:  19.10.2008  by Gisma                      //
//                                                            //
//************************************************************//
use Extasy\acl\ModelHelper;

class SiteMap_Controller_Edit extends AdminPage {
	protected $szDocumentName = ''; // Имя документа
	protected $nId = 0;
	protected $aSitemap = null;

	public function __construct() {
		parent::__construct();
		$this->addGet( 'id', 'showEditForm' );
		$this->addPost( 'sitemapId,submit', 'post' );
		$this->addPost( 'sitemapId,fieldName,action', 'ajaxEditColumn' );
		$this->addPost( 'sitemapId,fieldnName,action,data', 'ajaxEditColumn' );

	}

	public function showEditForm( $id ) {

		$this->getModel( $_GET[ 'id' ] );
		$this->checkUserHasEnoughRights();
		$this->document->getAdminUpdateForm();

		$this->output();
	}

	/**
	 *
	 */
	protected function ajaxEditColumn( $sitemapId, $columnName, $action, $data = array() ) {
		$this->document = RegisteredDocument::autoLoad( $sitemapId );
		$this->checkUserHasEnoughRights();

		$column = $this->document->attr( $columnName );
		$result = $column->ajaxCall( $action, $data );
		print json_encode( $result );
		die();
	}

	protected function post( $sitemapId ) {

		$this->getModel( $sitemapId );

		$this->checkUserHasEnoughRights();


		try {
			$result = $this->document->updateFromPost( $_POST );
			if ( $result == true ) {
				// Если успешно, пишем всё ок
				$this->addAlert( _msg( 'Документ был успешно обновлен' ) );
			} else {
				// Если нет, пишем всё галимо
				$this->addAlert( _msg( 'Ошибки при обновлении документа, попробуйте позже' ) );
			}
			SitemapCMSForms::updateSitemapPageFromPost( $this->aSitemap );
		}
		catch ( SiteMapException $e ) {
			CMSLog::addMessage( 'sitemap', $e );
			$this->addError( 'При обновлении документа произошла ошибка сохранения в базе документов' );
		}

		// Показываем страницу
		$url = 'edit.php?id=' . $sitemapId;
		$this->jump( $url );

	}

	/**
	 *   Проверяет страницу, если она скрипт редиректит на страницу скрипта, иначе возвращает модель класса
	 * @return
	 */
	protected function getModel( $id ) {
		// Получаем документ
		try {
			$aRow           = SiteMap_Sample::get( $id );
			$this->aSitemap = $aRow;
		}
		catch ( SiteMapException $e ) {

			$this->AddError( _msg( 'Документ не найден' ) );
			$this->jump( './' );
		}
		if ( !empty( $aRow[ 'script' ] ) ) {
			if ( !empty( $aRow[ 'script_admin_url' ] ) ) {
				$this->jump( \Extasy\CMS::getDashboardWWWRoot() . $aRow[ 'script_admin_url' ] );
			} else {
				$this->addError( 'У данного скрипта нету панели редактирования' );
				$this->jump( './' );
			}
		} else {

			$this->szDocumentName = $aRow[ 'document_name' ];
			$this->nId            = $aRow[ 'document_id' ];


			$validator = new \Extasy\Validators\IsModelClassNameValidator( $aRow['document_name']);
			if ( !$validator->isValid() ) {
				throw new \ForbiddenException('Not a model class:'. $aRow['document_name']);
			}
			$szClassName = $aRow['document_name'];

		}
		$this->document = new $szClassName();
		$found          = $this->document->get( $this->nId );
		if ( empty( $found ) ) {
			throw new NotFoundException( 'Document with id=' . $this->nId . ' not found' );
		}
		return $szClassName;
	}

	protected function checkUserHasEnoughRights() {

		$isEditable = ModelHelper::isEditable( $this->document );
		if ( !$isEditable ) {
			$error = sprintf( 'Document `%s` not editable for current user', $this->document->getModelName() );
			throw new ForbiddenException( $error );
		}
	}
}
