<?
namespace Extasy\Columns {
	use \Faid\DB;
	use \DAO;
	use \Exception;

//************************************************************//
//                                                            //
//         Контрол статический рубрикатор                     //
//       Copyright (c) 2006  ООО SmartDesign                  //
//               отдел/сектор                                 //
//       Email: gisma@smartdesign.by                          //
//                                                            //
//  Разработчик: Gisma (04.06.2007)                           //
//  Модифицирован:  04.06.2007  by Gisma                      //
//                                                            //
//************************************************************//
// Обновлен 22.11.2012                                        //
//************************************************************//
	class Static_Rubricator extends BaseColumn {
		/**
		 *
		 * @param unknown $szFieldName
		 * @param unknown $fieldInfo
		 * @param unknown $Value
		 *
		 * @throws Exception
		 */
		function __construct( $szFieldName, $fieldInfo, $Value ) {
			parent::__construct( $szFieldName, $fieldInfo, $Value );
			if ( empty( $fieldInfo[ 'values' ] ) ) {
				throw new Exception( 'TStatic_Rubricator::TStatic_Rubricator не определено поле values' );
			}
			if ( empty( $this->aValue ) ) {
				$this->aValue = array();
			} elseif ( is_string( $this->aValue ) ) {
				$this->aValue = unserialize( $this->aValue );
			}
		}

		/**
		 *
		 * @param unknown $dbData
		 */
		public function onAfterSelect( $dbData ) {
			if ( !empty( $dbData[ $this->szFieldName ] ) ) {
				$this->aValue = unserialize( $dbData[ $this->szFieldName ] );
			}
		}

		/**
		 * (non-PHPdoc)
		 * @see \Extasy\Columns\BaseColumn::onInsert()
		 */
		function onInsert(\Extasy\ORM\QueryBuilder $query ) {
			$values = serialize( $this->aValue );

			$query->setSet( $this->szFieldName,$this->aValue );
		}


		/**
		 *
		 * @param unknown $nKey
		 *
		 * @return boolean
		 */
		function hasValue( $nKey ) {
			return ( array_search( $nKey, $this->aValue ) !== false );
		}

		function getViewValue() {
			$result = array();
			foreach ( $this->aValue as $key => $row ) {
				if ( !empty( $this->fieldInfo[ 'values' ][ $row ] ) ) {
					$result[ $row ] = $this->fieldInfo[ 'values' ][ $row ];
				}
			}
			return $result;
		}

		function getFormValue() {
			// инициализация начальных переменных
			$szResult = '<table width=100% cellpadding="0" cellspacing="0">';
			$template = '<tr><td><input type="checkbox" NAME="%s[]" %s value="%s" id="%s_%s"/>
			<label for="%s_%s"> %s</label></td></tr>';
			// перебор все элементов values
			foreach ( $this->fieldInfo[ 'values' ] as $key => $row ) {
				// если элемент существует в значениях рубрикатора, то устанавливаем CHECKED=true
				$bSeek = in_array( $key, $this->aValue );
				if ( $bSeek !== false ) {
					$szChecked = 'CHECKED="TRUE"';
				} else {
					// иначе, не устанавливаем
					$szChecked = '';
				}
				// добавляем строку в результат
				$szResult .= sprintf( $template,
									  $this->szFieldName,
									  $szChecked,
									  $key,
									  $this->szFieldName,
									  $key,
									  $this->szFieldName,
									  $key,
									  $row
				);
			}
			$szResult .= '</table>';
			return $szResult;
		}
	}
}
?>