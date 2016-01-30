<?
use \Extasy\Model\Model as extasyDocument;

class CMS_DataManage extends CMS_DataPage {
	protected $szScript = 'list.php';
	protected $szEditScript = 'edit.php';
	protected $aBackUrlParams = array(); // Список параметров, которые дописываются в урл возвращения к списку
	protected $nId = 0;
	public $viewBeginPath = array();

	public function __construct() {
		// Трансформируем массив возвратных урлов
		// 
		$aData = array();
		foreach ( $this->aBackUrlParams as $row ) {
			if ( !isset( $_REQUEST[ $row ] ) ) {
				throw new Exception( 'В параметрах страницы не передан "' . $row . '"' );
			}
			$aData[ $row ] = $_REQUEST[ $row ];
		}
		$this->aBackUrlParams = $aData;
		parent::__construct();
		$this->addGet( 'type,id', 'showEdit' );
		$this->addGet( 'type,add', 'add' );

		$this->addPost( 'typeName,id,submit', 'postEdit' );
		$this->addPost( 'typeName,id,fieldName,action', 'ajaxEditColumn' );
		$this->addPost( 'typeName,id,fieldName,action,data', 'ajaxEditColumn' );
	}

	public function showEdit( $type, $nId ) {

		$nId = intval( $nId );
		$this->setupModelName( $type );

		$document = new $this->modelName;
		$found    = $document->get( $nId );

		if ( empty( $found ) ) {
			$this->addError( 'Item with id="' . $nId . '" not found' );
			$this->jump( $this->generateBackUrl() );
		}
		if ( empty( $this->typeInfo[ 'edit_fields' ] ) ) {
			$document->getAdminUpdateForm();
			parent::output();
		} else {
			// Вывод информации
			$szTitle                                                                     = 'Редактирование документа';
			$this->viewBeginPath[ $document->getLabel( extasyDocument::labelAllItems ) ] = $this->generateBackUrl();
			$this->viewBeginPath[ $szTitle ]                                             = '#';
			//
			$this->output( $document, $szTitle, true );
		}


	}

	public function add( $type ) {
		$this->setupModelName( $type );

		$document = new $this->modelName ();
		$document->insert();
        $this->nId = $document->id->getValue();
            $this->addAlert( _msg( 'Запись добавлена' ) );
        $jumpTo    = $this->generateEditUrl();
        $this->jump( $jumpTo );
	}

	/**
	 *
	 * @param unknown $type
	 * @param unknown $id
	 * @param unknown $columnName
	 */
	public function ajaxEditColumn( $type, $id, $columnName, $action, $data = array() ) {
		$id = intval( $id );
		$this->setupModelName( $type );
		$oDocument = new $this->modelName();
		$found     = $oDocument->get( $id );
		if ( !$found ) {
			$error = sprintf( ' Document <%s,%s> not found', $type, $id );
			throw new Exception( $error );
		}
		//
		$column   = $oDocument->attr( $columnName, true );
		$response = $column->ajaxCall( $action, $data );
		print json_encode( $response );
		die();
	}

	public function postEdit( $type, $id ) {

		$this->setupModelName( $type );
		$oDocument = new $this->modelName();
		$oDocument->get( $id );
		$oDocument->updateFromPost( $_POST );

		$this->addAlert( _msg( 'Документ был успешно обновлен' ) );
		$this->nId = $id;
		$jumpTo    = $this->generateEditUrl();
		$this->jump( $jumpTo );
	}



	/**
	 * (non-PHPdoc)
	 * @see adminPage::output()
	 */
	public function output( extasyDocument $doc = null, $szTitle = '', $bEdit = true ) {
		if ( !empty( $this->typeInfo[ 'edit_fields' ] ) ) {
			$design = CMSDesign::getInstance();
			$design->begin( $this->viewBeginPath, $szTitle );
			$design->documentBegin();
			$design->header( $szTitle );
			$design->formBegin();
			$design->hidden( 'typeName', $this->modelName );
			if ( $bEdit ) {
				$design->hidden( 'id', $doc->id );
				$design->submit( 'submit', _msg( 'Сохранить' ) );
			} else {
				$design->submit( 'submit', _msg( 'Добавить' ) );
			}

			$design->tableBegin();

			$fieldList = explode( ',', $this->typeInfo[ 'edit_fields' ] );
			foreach ( $fieldList as $fieldName ) {
				$title = !empty( $this->typeInfo[ 'fields' ][ $fieldName ][ 'title' ] ) ? $this->typeInfo[ 'fields' ][ $fieldName ][ 'title' ] : $fieldName;
				$help  = !empty( $this->typeInfo[ 'fields' ][ $fieldName ][ 'cms_help' ] ) ? $this->typeInfo[ 'fields' ][ $fieldName ][ 'cms_help' ] : '';
				$design->row2cell(
					   $title,
					   $doc->attr( $fieldName, true )->getAdminFormValue(),
					   $help
				);
			}
			$design->TableEnd();

			foreach ( $this->aBackUrlParams as $key => $row ) {
				$design->hidden( $key, htmlspecialchars( $row ) );
			}
			if ( $bEdit ) {
				$design->submit( 'submit', _msg( 'Сохранить' ) );
			} else {
				$design->submit( 'submit', _msg( 'Добавить' ) );
			}
			$design->formEnd();
			$design->documentEnd();
			$design->end();
		} else {
			if ( $bEdit ) {
				$doc->getAdminUpdateForm();
			} else {
				$doc->getAdminInsertForm();
			}
		}
		parent::output();
	}

	protected function generateBackUrl() {
		$szResult = $this->szScript;
		$szResult .= '?type=' . $this->modelName;

		$szResult .= '&' . http_build_query( $this->aBackUrlParams );
		return $szResult;
	}

	protected function generateEditUrl() {

		$szResult = $this->szEditScript;
		$szResult .= '?type=' . $this->modelName . '&id=' . $this->nId;

		$szResult .= '&' . http_build_query( $this->aBackUrlParams );

		return $szResult;
	}

}

?>