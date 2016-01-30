<?
use \Faid\UParser;

class adminOrderPage extends extasyPage {
	protected $typeName = '';
	protected $fieldName = 'name';
	protected $fieldOrder = 'order';
	protected $jump_to = 'order.php';
	protected $back = 'index.php';
	protected $aBegin = array();
	protected $aHidden = array();
	protected $szTitle = '';
	protected $fieldCondition = '1';

	public function __construct( $aBegin, $szTitle ) {
		parent::__construct();
		$this->addPost( 'submit,order_value', 'order' );

		$this->aBegin  = $aBegin;
		$this->szTitle = $szTitle;

	}

	public function main() {

		$aType = call_user_func( [ $this->typeName, 'getFieldsInfo' ] );

		$szIndex = call_user_func( [ $this->typeName, 'getIndexKey' ] );
		$sql     = sprintf( 'select `%s`, `%s` as name, `%s` from `%s` where %s order by `%s` ',
							$szIndex,
							$this->fieldName,
							$this->fieldOrder,
							$aType[ 'table' ],
							$this->fieldCondition,
							$this->fieldOrder
		);
		$aData   = \Extasy\ORM\DB::query( $sql );
		foreach ( $aData as $key => $value ) {
			foreach ( $value as $key2 => $value2 ) {
				$aData[ $key ][ $key2 ] = str_replace( "\n", '', $value2 );
			}
		}
		print UParser::parsePHPFile( __DIR__ . DIRECTORY_SEPARATOR . 'order.tpl',
									 array(
										 'szTitle' => $this->szTitle,
										 'aBegin'  => $this->aBegin,
										 'type'    => $this->typeName,
										 'back'    => $this->back,
										 'aHidden' => $this->aHidden,
										 'aData'   => $aData,
									 ) );
		$this->output();
	}

	/**
	 *   -------------------------------------------------------------------------------------------
	 * @desc Сохраняет отсортированные данные
	 * @return
	 *   -------------------------------------------------------------------------------------------
	 */
	public function order() {
		$typeName  = $_POST[ 'type' ];
		$validator = new \Extasy\Validators\IsModelClassNameValidator( $typeName );
		if ( !$validator->isValid() ) {
			throw new InvalidArgumentException( 'Not a model - ' . $typeName );
		}

		$aType   = call_user_func( [ $typeName, 'getFieldsInfo' ] );
		$szIndex = call_user_func( [ $typeName, 'getIndexKey' ] );
		$i       = 1;
		$aRows   = explode( "\n", $_POST[ 'order_value' ] );
		unset( $aRows[ 0 ] );
		foreach ( $aRows as $value ) {
			\Extasy\ORM\DBSimple::update( $aType[ 'table' ],
										  [ 'order' => $i ],
										  [ $szIndex => $value ] );

			$i++;
		}
		$this->afterOrder();

		$this->jump( $this->jump_to );
	}

	/*
	* @desc Вызывается после пересортировка
	*/
	protected function afterOrder() {
		if ( !empty( $this->typeName ) ) {
			// вызов блоков подключенных
			$szPath = CFG_PATH . 'blocks/' . $this->typeName . '_after.php';
			if ( file_exists( $szPath ) ) {
				$nResult = include $szPath;
			}
		}
	}
}

?>