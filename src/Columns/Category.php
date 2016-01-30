<?
namespace Extasy\Columns {
	use \Extasy\ORM\DB;
	use \DAO_Exception;
	use \DAO;
	use \CSelect;
//************************************************************//
//                                                            //
//                 Элемент Category                           //
//       Copyright (c) 2006  ООО SmartDesign                  //
//               отдел/сектор                                 //
//       Email: support@smartdesign.by                        //
//                                                            //
//  Разработчик: Gisma                                        //
//  Модифицирован:   2006.02.20 by Gisma                      //
//                                                            //
//************************************************************//
//  16.11.2008 Добавлена поддержка имени бд в конфиге ключ    //
//  `database`                                                //
//************************************************************//
	/**
	 * @name TCategory
	 * @author Gisma
	 * @desc <p>Тип категории применяется в деревьяв для индексирования по элементам дерева.
	 *        Этот тип хранится в базе данных как тип INT, т.к. фактически указывает на индекс элемента
	 *        к которому он являетя дочерним.</p>
	 *        <p><strong style="color:red">Важное ! это поле не может быть индексом.</strong></p>
	 *        <h3>Параметры:</h3>
	 *        <ul>
	 *        <li>        <b>cross_table</b> - таблица в которой будет организована выборка;
	 *        <li>        <b>name_field</b>        - поле по-которому будет организована выборрка
	 *        <li>        <b>index</b>                - поле индекс элементов в таблице
	 *        </ul>
	 */
	class CategoryColumn extends BaseColumn {
		use \Extasy\ORM\Columns\IntegerColumn;
		protected static $aCache = array();

		function __construct( $szFieldName, $fieldInfo, $Value ) {
			parent::__construct( $szFieldName, $fieldInfo, ( $Value ) );
			if ( !is_array( $this->fieldInfo ) ) {
				throw new DAO_Exception( 'CCategory::Ccategory данные об элементе должны быть массивом', $this->szFieldName );
			}
			if ( empty( $this->fieldInfo[ 'cross_name' ] ) )
				$this->fieldInfo[ 'cross_name' ] = 'name';
			if ( empty( $this->fieldInfo[ 'cross_table' ] ) )
				throw new DAO_Exception( 'Ccategory::Ccategory не опеределен поле cross_table', $this->szFieldName );
			if ( empty( $this->fieldInfo[ 'cross_index' ] ) )
				$this->fieldInfo[ 'cross_index' ] = 'id';
			if ( empty( $Value ) )
				if ( empty( $_REQUEST[ $szFieldName ] ) )
					$this->aValue = 0;
		}

		/**
		 *
		 * @param unknown $dbData
		 */
		public function onAfterSelect( $dbData ) {
			if ( isset( $dbData[ $this->szFieldName ] ) ) {
				$this->aValue = $dbData[ $this->szFieldName ];
			}
		}

		function onInsert(\Extasy\ORM\QueryBuilder $query ) {
			$query->setSet( $this->szFieldName,$this->aValue );
		}

		function onUpdate(\Extasy\ORM\QueryBuilder $query ) {
			$query->setSet( $this->szFieldName, $this->aValue );
		}
		protected function loadAllRows( $cond = ' 1 ' ) {
			$szNameField = $this->fieldInfo[ 'cross_name' ];
			$szIndex     = $this->fieldInfo[ 'cross_index' ];
			$szTable     = $this->fieldInfo[ 'cross_table' ];
			$szDataBase  = !empty( $this->fieldInfo[ 'database' ] ) ? $this->fieldInfo[ 'database' ] : '';
			// Если задано имя базы данных приаттачиваем
			if ( !empty( $szDataBase ) ) {
				$szTable = $szDataBase . '.' . $szTable;
			} else {
				$szTable = '`' . $szTable . '`';
			}
			if ( !empty( $this->fieldInfo[ 'cross_cond' ] ) )
				$szCond = ' WHERE ' . $this->fieldInfo[ 'cross_cond' ];

			elseif ( !empty( $this->fieldInfo[ 'cross_group' ] ) )
				$szCond = ' WHERE `' . $this->fieldInfo[ 'cross_parent' ] . '`="' . $this->document->id->getValue() . '"';
			else $szCond = '';
			if ( !empty( $cond )) {
				$szCond .= ' and ' . $cond;
			}
			if ( !empty( $this->fieldInfo[ 'ordered' ] ) ) {
				$szCond .= ' ORDER BY `' . $szIndex . '`';
			}


			$sql = sprintf( 'SELECT `%s`,`%s` FROM %s %s ', \Extasy\ORM\DB::escape( $szNameField ),
							\Extasy\ORM\DB::escape( $szIndex  ),
							\Extasy\ORM\DB::escape( $szTable ),
							$szCond );

			$aData = \Extasy\ORM\DB::query( $sql );
			return $aData;
		}
		function getFormValue() {
			require_once CONTROL_PATH . 'select.php';
			$aData = $this->loadAllRows();
			$szNameField = $this->fieldInfo[ 'cross_name' ];
			$szIndex     = $this->fieldInfo[ 'cross_index' ];
			$aList = array();
			foreach ( $aData as $key => $value ) {
				$aList[ $key ] = array(
					'id'   => $value[ $szIndex ],
					'name' => $value[ $szNameField ]
				);
			}
			array_unshift( $aList, array( 'id' => 0, 'name' => _msg( 'Пусто' ) ) );
			$oSelect          = new CSelect();
			$oSelect->name    = $this->szFieldName;
			$oSelect->values  = $aList;
			$oSelect->current = $this->aValue;

			return $oSelect->generate();
		}

		function getValue() {
			return $this->aValue;
		}

		function getViewValue() {
			foreach ( self::$aCache as $key => $row ) {
				if ( ( $row[ 0 ] == $this->fieldInfo[ 'cross_table' ] )
					&& ( $row[ 1 ] == $this->fieldInfo[ 'cross_index' ] )
					&& ( $row[ 2 ] == $this->aValue )
				) {
					return $row[ 3 ];
				}
			}
			$szNameField = $this->fieldInfo[ 'cross_name' ];
			$szIndex     = $this->fieldInfo[ 'cross_index' ];

			$aResult = $this->loadAllRows( ' `%s`= "%s"',DB::escape($szNameField),  DB::escape( $this->Value)  );

			$szResult        = ( !empty( $aResult[ 0 ][ $szNameField ] ) ? $aResult[ 0 ][ $szNameField ] : '' );
			self::$aCache[ ] = array(
				$this->fieldInfo[ 'cross_table' ],
				$this->fieldInfo[ 'cross_index' ],
				$this->aValue,
				$szResult
			);
			return $szResult;
		}

	}
}
?>