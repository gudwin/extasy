<?
namespace Extasy\Columns {
	use \Faid\DB;
	use \Date_Helper;
	use \CDate;
	use \DAO;
//************************************************************//
//                                                            //
//                 Элемент Date                               //
//       Copyright (c) 2006  ООО SmartDesign                  //
//               отдел/сектор                                 //
//       Email: gis2002@inbox.ru                              //
//                                                            //
//  Разработчик: Gisma (19.07.2006)                           //
//  Модифицирован:    19.07.2006 by Gisma                     //
//                                                            //
//************************************************************//
//  Модифицирован:    19.09.2008 by Gisma                     //
//  Удалена поддержка DAO::Parser и view_template             //
//************************************************************//

	class Date extends BaseColumn {
		var $aMonth;

		function __construct( $szFieldName, $fieldInfo, $Value ) {
			parent::__construct( $szFieldName, $fieldInfo, $Value );
			if ( empty( $Value ) ) {
				$this->aValue = date( 'Y-m-d' );
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
		public function setValue($newValue ) {
			if ( $this->check( $newValue ) ) {
				parent::setValue( $newValue );
			} else {
				parent::setValue( date('Y-m-d'));
			}
		}
		function getFormValue() {
			$date        = new CDate();
			$date->name  = $this->szFieldName;
			$date->value = $this->aValue;
			return $date->generate();
		}

		function getValueArray() {
			$aDate = explode( '-', $this->aValue );
			return array(
				'year'  => $aDate[ 0 ],
				'month' => $aDate[ 1 ],
				'day'   => intval( $aDate[ 2 ] ),
			);
		}

		function getViewValue() {

			return $this->aValue;
		}

		function getCyrilicViewValue() {
			require_once LIB_PATH . 'kernel/functions/date.func.php';
			return Date_Helper::getCyrilicViewValue( $this->aValue );
		}

		function getEnglishViewValue() {
			require_once LIB_PATH . 'kernel/functions/date.func.php';
			return Date_Helper::getEnglishViewValue( $this->aValue );
		}

		function MakeViewElement() {
			return array(
				'value' => $this->aValue,
				'empty' => '0000-00-00' == $this->aValue
			);
		}

		public function check( $value, $fieldInfo = array() ) {
			return preg_match( '/[0-9]{2,4}\-[0-9]{1,2}\-[0-9]{1,2}/', $value );
		}
		public function onCreateTable( \Extasy\ORM\QueryBuilder $queryBuilder ) {
			$queryBuilder->addFields( sprintf( '`%s` date not null default "0000-00-00"', $this->szFieldName ) );
			if ( !empty( $this->fieldInfo['index'])) {
				$queryBuilder->addFields( sprintf( 'index `search_%s` ( `%s`)', $this->szFieldName, $this->szFieldName ));
			}
		}
	}
}
?>