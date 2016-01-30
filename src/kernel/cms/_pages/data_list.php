<?
use \Faid\DBSimple;

/**
 * Class CMS_Page_DataList
 * @package extasycms.Dashboard
 */
class CMS_Page_DataList extends CMS_DataPage {
	public $viewBeginPath = array();

	protected $bBlockEdit;
	protected $bBlockAdd;
	protected $bBlockDelelete;
	protected $szEditScript = 'edit.php';
	protected $szScript = 'list.php';
	protected $szOrderScript = 'order.php'; // Скрипт сортировки
	public function __construct() {
		parent::__construct();
		$this->addPost( 'typeName,id,parent', 'delete' );
		$this->addPost( 'typeName,id', 'delete' );
		$this->addGet( 'type', 'show' );

	}

	public function main() {
		$this->jump( \Extasy\CMS::getDashboardWWWRoot() );
	}

	public function delete( $type, $id, $parent = 0 ) {
		$this->setupModelName( $type );
		foreach ( $id as $nId ) {
			$nId      = intval( $nId );
			$document = new $this->modelName();
			$found    = $document->get( $nId );
			if ( !empty( $found ) ) {
				$document->delete();
			}
		}
		$jumpTo = $this->szScript . '?type=' . $this->modelName;
		$this->jump( $jumpTo );
	}

	public function show( $type ) {
		$this->setupModelName( $type );
		// Получаем список полей для отображения и флаги, какие колонки отображать
		$aList              = !empty( $this->typeInfo[ 'list_fields' ] ) ? explode( ',',
																					$this->typeInfo[ 'list_fields' ] ) : array( 'id' );
		$this->bBlockAdd    = ( !empty( $this->typeInfo[ 'interface' ] ) ) && ( $this->typeInfo[ 'interface' ][ 'block_add' ] ) ? 1 : 0;
		$this->bBlockEdit   = ( !empty( $this->typeInfo[ 'interface' ] ) ) && ( $this->typeInfo[ 'interface' ][ 'block_edit' ] ) ? 1 : 0;
		$this->bBlockDelete = ( !empty( $this->typeInfo[ 'interface' ] ) ) && ( $this->typeInfo[ 'interface' ][ 'block_delete' ] ) ? 1 : 0;

		$docList = $this->selectItem();
		$docList = $this->selectObject( $docList );

		$title                         = call_user_func( array( $this->modelName, 'getLabel' ),
														 \Extasy\Model\Model::labelAllItems );
		$this->viewBeginPath[ $title ] = '#';
		$columns                       = $this->getColumn();
		$buttons                       = $this->getButton();
		$this->output( $docList, $buttons, $columns, $title );

	}

	/**
	 *   Возвращает все элементы указанного типа
	 * @return
	 */
	protected function selectItem() {
		$orderBy     = '';
		if ( !empty( $this->typeInfo[ 'order' ] ) ) {
			$orderBy = trim( $this->typeInfo[ 'order' ] );
		}
		return DBSimple::select( $this->typeInfo[ 'table' ], null, $orderBy );
	}

	/**
	 *   Собирает на основе переданных данных (имя типа, тип, ряды данных)
	 * @return
	 */
	public function selectObject( $aData ) {
		$modelClass = $this->modelName;
		$result     = array();
		foreach ( $aData as $key => $row ) {
			$result[ $key ] = new $modelClass( $row );
		}
		return $result;
	}

	protected function getColumn() {
		$aFields = !empty( $this->typeInfo[ 'list_fields' ] ) ? explode( ',',
																		 $this->typeInfo[ 'list_fields' ] ) : array();
		if ( !$this->bBlockDelete ) {
			$aResult[ ] = array( '&nbsp;', '5' );
		} else {
			$aResult = array();
		}
		if ( !empty( $aFields ) ) {
			$nWidth = intval( 90 / sizeof( $aFields ) );
		}
		foreach ( $aFields as $key ) {
			if ( !empty( $this->typeInfo[ 'fields' ][ $key ] ) ) {
				$aResult[ ] = array(
					!empty( $this->typeInfo[ 'fields' ][ $key ][ 'title' ] ) ? $this->typeInfo[ 'fields' ][ $key ][ 'title' ] : $key,
					$nWidth
				);
			}
		}
		if ( !$this->bBlockEdit ) {
			$aResult[ ] = array( _msg( 'CMS_EDIT' ), '5' );
		}
		return $aResult;
	}

	protected function getButton() {
		$result = $this->buttons;

		if ( !$this->bBlockAdd ) {
			$result[ _msg( 'Добавить' ) ] = $this->szEditScript . '?type=' . $this->modelName . '&add=1';
		}

		if ( isset( $this->typeInfo[ 'cms_buttons' ] ) ) {
			if ( is_array( $this->typeInfo[ 'cms_buttons' ] ) ) {
				$result = array_merge( $result, $this->typeInfo[ 'cms_buttons' ] );
			} else {
				throw new Exception( 'getButton directive cms_buttons muste an array' );
			}
		}

		return $result;
	}

	/**
	 *   Отображает форму
	 * @return
	 */
	public function output( $aData = null, $aButton = null, $aColumn = array(), $szTitle = '' ) {
		$listFields = explode( ',', $this->typeInfo[ 'list_fields' ] );
		$design     = CMSDesign::getInstance();
		$this->outputHeader( $this->viewBeginPath, $szTitle );
		if ( !empty( $aButton ) ) {
			$design->buttons( $aButton );
		}
        $this->outputBeforeList();
		$design->formBegin();
		$design->tableBegin();
		$design->tableHeader( $aColumn );
		foreach ( $aData as $row ) {
			$this->outputListRow( $row, $listFields );
		}
		$design->tableEnd();
		if ( !$this->bBlockDelete ) {
			$design->submit( 'delete', _msg( 'Удалить' ) );
		}
		if ( !empty( $this->typeInfo[ 'has_parent' ] ) && !empty( $_REQUEST[ 'parent' ] ) ) {
			$design->hidden( 'parent', htmlspecialchars( $_REQUEST[ 'parent' ] ) );
		}
		$design->hidden( 'typeName', $this->modelName );
		$design->formEnd();
		$this->outputFooter();
		parent::output();
	}

	protected function outputListRow( $row, $listFields ) {
		$design = CMSDesign::getInstance();

		$design->rowBegin();
		if ( !$this->bBlockDelete ) {
			$design->listCell( $row->obj_id->getCheckbox() );
		}
		foreach ( $listFields as $fieldName ) {
			if ( $fieldName == 'id' ) {
				continue;
			}
			$design->listCell( $row->attr( $fieldName )->getAdminViewValue() );
		}
		if ( !$this->bBlockEdit ) {
			$szEditLink = $this->szEditScript . '?type=' . $this->modelName . '&id=' . $row->id;
			if ( !empty( $this->typeInfo[ 'has_parent' ] ) && !empty( $_REQUEST[ 'parent' ] ) ) {
				$szEditLink .= '&parent=' . htmlspecialchars( $_REQUEST[ 'parent' ] );
			}
			$design->editCell( $szEditLink );
		}

		$design->rowEnd();
	}
    protected function outputBeforeList() {

    }
}

?>