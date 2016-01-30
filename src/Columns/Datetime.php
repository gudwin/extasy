<?
namespace Extasy\Columns {
use \Faid\UParser, \Faid\DB;
	use \Date_Helper;
	use \DAO;
//************************************************************//
//                                                            //
//                 Элемент Datetime                           //
//       Copyright (c) 2006  ООО SmartDesign                  //
//               отдел/сектор                                 //
//       Email: gis2002@inbox.ru                              //
//                                                            //
//  Разработчик: Gisma 2006.02.20                             //
//                                                            //
//************************************************************//
//  22.12.2008 Добавлена совместимость с MySQL типом date     //
//************************************************************//
	class Datetime extends BaseColumn {
		var $aMonth;

		function __construct( $szFieldName, $fieldInfo, $Value ) {
			parent::__construct( $szFieldName, $fieldInfo, $Value );

			if ( empty( $Value ) ) {
				$this->aValue = !empty( $this->fieldInfo['default'] ) ? $this->fieldInfo['default'] : date( 'Y-m-d H:i:s' );
			}
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

		function getValueArray() {
			$pattern = '#([0-9]+)\-([0-9]+)\-([0-9]+) ([0-9]+):([0-9]+):([0-9]+)#';
			if ( preg_match( $pattern, $this->aValue, $aMatch ) ) {
				return array(
					'year'   => $aMatch[ 1 ],
					'month'  => ( intval( $aMatch[ 2 ] ) < 10 ? '0' : '' ) . intval( $aMatch[ 2 ] ),
					'day'    => ( intval( $aMatch[ 3 ] ) < 10 ? '0' : '' ) . intval( $aMatch[ 3 ] ),
					'hour'   => ( intval( $aMatch[ 4 ] ) < 10 ? '0' : '' ) . intval( $aMatch[ 4 ] ),
					'minute' => ( intval( $aMatch[ 5 ] ) < 10 ? '0' : '' ) . intval( $aMatch[ 5 ] ),
					'second' => ( intval( $aMatch[ 6 ] ) < 10 ? '0' : '' ) . intval( $aMatch[ 6 ] ),
				);
			} else {
				return array(
					'year'   => 0,
					'month'  => 0,
					'day'    => 0,
					'hour'   => 0,
					'minute' => 0,
					'second' => 0
				);
			}

		}

		function getCyrilicViewValue( ) {

			return Date_Helper::getCyrilicViewValue( $this->aValue);
		}

		function getEnglishViewValue(  ) {

			return Date_Helper::getEnglishViewValue( $this->aValue);
		}

		/**
		 * @desc Выводит форму для редактирования даты
		 * @return
		 */
		function getFormValue() {
			// Выдираем текущий год, месяц и день

			$aDate = preg_split( '#[ \:\-]#', $this->aValue );
			// Подготавливаем массив для парсинга
			$aParse = array(
				'current_year'   => $aDate[ 0 ],
				'current_month'  => $aDate[ 1 ],
				'current_day'    => $aDate[ 2 ],
				'current_hour'   => $aDate[ 3 ],
				'current_minute' => $aDate[ 4 ],
				'fieldname'      => $this->szFieldName,
				'value'          => $aDate[ 0 ] . '-' . intval( $aDate[ 1 ] ) . '-' . $aDate[ 2 ] . ' ' . intval( $aDate[ 3 ] ) . ':' . intval( $aDate[ 4 ] )
			);

			// Парсим
			$szResult = UParser::parsePHPFile( __DIR__ . DIRECTORY_SEPARATOR  . 'datetime/form.tpl', $aParse );
			return $szResult;
		}

		function getViewValue( $bShowSecunds = false ) {
			if ( $bShowSecunds ) {
				return $this->aValue;
			} else {
				return substr( $this->aValue, 0, strlen( $this->aValue ) );
			}
		}

		function getViewDate() {

			return $this->getCyrilicViewValue( true );
		}

		function getViewTimeWithoutSeconds() {
			$aDate = preg_split( '#[ \:\-]#', $this->aValue );
			return $aDate[ 0 ] . '-' . $aDate[ 1 ] . '-' . $aDate[ 2 ] . ' ' . $aDate[ 3 ] . ':' . $aDate[ 4 ];

		}

		function check( $value, $fieldInfo = array() ) {
			// Проверяем на datetime
			if ( empty( $value ) ) {
				return true;
			}
			$bResult = preg_match( '/[0-9]{2,4}\-[0-9]{1,2}\-[0-9]{1,2} [0-9]{1,2}\:[0-9]{1,2}/', $value );
			if ( !$bResult ) {
				$bResult = preg_match( '/[0-9]{2,4}\-[0-9]{1,2}\-[0-9]{1,2}/', $value );
			}
			return $bResult;
		}

		/**
		 *   -------------------------------------------------------------------------------------------
		 *   Возвращает дату в формате rfc
		 * @return
		 *   -------------------------------------------------------------------------------------------
		 */
		public function getRFC822() {
			$aDate = preg_split( '#[ \:\-]#', $this->aValue );
			$nTime = mktime( $aDate[ 3 ], $aDate[ 4 ], 0, $aDate[ 1 ], $aDate[ 2 ], $aDate[ 0 ] );
			return date( 'r', $nTime );
		}
		public function onCreateTable( \Extasy\ORM\QueryBuilder $queryBuilder ) {
			$queryBuilder->addFields( sprintf( '`%s` datetime not null default "0000-00-00 00:00:00"', $this->szFieldName ) );
		}
	}
}
?>