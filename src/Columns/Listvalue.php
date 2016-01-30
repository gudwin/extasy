<?
namespace Extasy\Columns {
	use \Faid\DB;
	use \DAO_Exception;
	use \DAO;
	use \CSelect;
//************************************************************//
//                                                            //
//                 Элемент listvalue                          //
//       Copyright (c) 2006  ООО SmartDesign                  //
//               отдел/сектор                                 //
//       Email: support@smartdesign.by                        //
//                                                            //
//  Разработчик: Gisma                                        //
//  Модифицирован:   2006.02.20 by Gisma                      //
//                                                            //
//************************************************************//
	/**
	 * @name List Defined Value Type
	 * @desc   Класс вывода списка обозначенных (т.е. статических, заданных заранее) значение.
	 * @author Gisma
	 */
	class listvalue extends BaseColumn {
		function __construct( $szFieldName, $fieldInfo, $Value ) {
			if ( !empty( $fieldInfo[ 'values' ] ) && is_string( $fieldInfo[ 'values' ] ) ) {
				$fieldInfo[ 'values' ] = explode( ';', $fieldInfo[ 'values' ] );
			}
			if ( ( empty( $fieldInfo[ 'values' ] ) ) || ( !is_array( $fieldInfo[ 'values' ] ) ) ) {
				throw new DAO_Exception( 'входящий параметр $fieldInfo["values"] не массив. Имя поля:', $szFieldName );
			}

			if ( empty( $Value ) ) {
				$Value = 0;
			}

			parent::__construct( $szFieldName, $fieldInfo, intval( $Value ) );
		}

		/**
		 *
		 * @param unknown $dbData
		 */
		public function onAfterSelect( $dbData ) {
			if ( isset( $dbData[ $this->szFieldName ] ) ) {
				$this->aValue = intval( $dbData[ $this->szFieldName ] );
			}
		}

		function __getValue() {
			if ( array_key_exists( $this->aValue, $this->fieldInfo[ 'values' ] ) ) {
				if ( is_array( $this->fieldInfo[ 'values' ][ $this->aValue ] ) ) {
					if ( !isset( $this->fieldInfo[ 'values' ][ $this->aValue ][ 'value' ] ) ) {
						throw new DAO_Exception( '__getValue ошибка не найдено значение элемента', $this->szFieldName );
					} else {
						return $this->fieldInfo[ 'values' ][ $this->aValue ][ 'value' ];
					}
				} else {
					return $this->fieldInfo[ 'values' ][ $this->aValue ];
				}
			} else {

				return '&nbsp;';
			}
		}

		function onInsert(\Extasy\ORM\QueryBuilder $query ) {
			$query->setSet( $this->szFieldName, !empty( $this->aValue ) ? $this->aValue : 0 );
		}

		function onUpdate(\Extasy\ORM\QueryBuilder $query ) {
			$query->setSet( $this->szFieldName, $this->aValue );
		}

		function getFormValue() {
			$aValues = array();
			foreach ( $this->fieldInfo[ 'values' ] as $key => $value ) {
				$aValues[ ] = array(
					'name' => $value,
					'id'   => $key,
				);
			}
			require_once CONTROL_PATH . 'select.php';
			$oSelect          = new CSelect();
			$oSelect->name    = $this->szFieldName;
			$oSelect->current = $this->aValue;
			$oSelect->values  = $aValues;

			return $oSelect->generate();
		}

		/**
		 * (non-PHPdoc)
		 * @see \Extasy\Columns\BaseColumn::getAdminFormValue()
		 */
		function getAdminFormValue() {
			if ( !empty( $this->fieldInfo[ 'size' ] ) ) {
				$szSize = $this->fieldInfo[ 'size' ];
			} else {
				$szSize = 1;
			}
			if ( !empty( $this->fieldInfo[ 'class' ] ) ) {
				$szClass = $this->fieldInfo[ 'class' ];
			} else {
				$szClass = '';
			}
			$szId = 'listvalue_' . $this->szFieldName;

			$szResult = '<select size="' . $szSize . '" id="' . $szId . '" name="' . $this->szFieldName . '" class="' . $szClass . '">';
			$szName   = '';

			foreach ( $this->fieldInfo[ 'values' ] as $key => $value ) {
				$szSelected = '';
				if ( $key == $this->aValue ) {
					$szSelected = 'selected';
					// Нашли текущее значение
					$szName = $value;
				}
				if ( is_array( $value ) ) {
					if ( !empty( $value[ 'class' ] ) ) {
						$szClass = $value[ 'class' ];
					} else {
						$szClass = '';
					}
					$szResult .= '<option value="' . $key . '" class="' . $szClass . '" ' . $szSelected . '>' . $value[ 'value' ] . '</option>' . "\n";
				} else {
					$szResult .= '<option value="' . $key . '" ' . $szSelected . '>' . $value . '</option>' . "\n";
				}
			}
			$szResult .= '</select>';
			return $szResult;
		}

		/**
		 *   -------------------------------------------------------------------------------------------
		 * @desc
		 * @return
		 *   -------------------------------------------------------------------------------------------
		 */
		function getValue() {
			return $this->aValue;
		}

		/**
		 *
		 * @return multitype:unknown
		 */
		public function getTitles() {
			$titles = array();
			foreach ( $this->fieldInfo[ 'values' ] as $key => $value ) {
				$titles[ ] = is_array( $value ) ? $value[ 'value' ] : $value;
			}
			return $titles;
		}


		function getViewValue() {
			return $this->__getValue();
		}

		public static function setDefaultValue( $type, $fieldname, $value = 0 ) {
			setcookie( $type . '[' . $fieldname . '][default_value]', $value, time() + 86400 * 60, '/' );
		}

		public static function getDefaultValue( $type, $fieldname ) {
			if ( isset( $_COOKIE[ $type ][ $fieldname ][ 'default_value' ] ) ) {
				return $_COOKIE[ $type ][ $fieldname ][ 'default_value' ];
			} else {
				return 0;
			}
		}

		public static function onSetting( $type, $fieldname, $key, $value ) {
			switch ( $key ) {
				case 'setDefault':
					self::setDefaultValue( $type, $fieldname, $value );
					$szMessage = _msg( 'Сохранено' );
					break;
				default:
					$szMessage = _msg( 'Неизвестная команда' );
					break;
			}
			return sprintf( 'alert(%s)', to_ajax_string( $szMessage ) );
		}
	}

}
?>