<?
namespace Extasy\Columns {
//************************************************************//
//                                                            //
//                 Контрол Index                              //
//       Copyright (c) 2006      SmartDesign                  //
//               Отдел Extasy                                 //
//       Email: support@smartdesign.by                        //
//                                                            //
//                                                            //
//    Создатель:   12.07.2006 by Gisma                        //
//                                                            //
//************************************************************//
	class Index extends BaseColumn {
		/**
		 * Если этот флаг в true, то контрол не учавствует в поиске
		 * @var string
		 */
		protected $ignore = false;

		/**
		 * (non-PHPdoc)
		 * @see \Extasy\Columns\BaseColumn::onInsert()
		 */
		public function onInsert( \Extasy\ORM\QueryBuilder $query ) {
		}

		/**
		 * (non-PHPdoc)
		 * @see \Extasy\Columns\BaseColumn::onAfterInsert()
		 */
		public function onAfterSelect( $dbRow ) {
			if ( isset( $dbRow[ $this->szFieldName ] ) ) {
				$this->aValue = $dbRow[ $this->szFieldName ];
			}
		}

		function onUpdate( \Extasy\ORM\QueryBuilder $query ) {
			$query->setWhere( $this->szFieldName, $this->aValue );
		}

		function onDelete( \Extasy\ORM\QueryBuilder $query ) {
			$query->setWhere( $this->szFieldName, $this->aValue );
		}

		public function onSelect( \Extasy\ORM\QueryBuilder $query) {
			if ( !$this->ignore ) {
				$query->setWhere( $this->szFieldName, $this->aValue );
			}
		}

		public function ignoreOnSelect() {
			$this->ignore = true;
		}

		public function getAdminFormValue() {
			return $this->aValue;
		}

		function getValue() {
			return $this->aValue;
		}


		function getViewValue() {
			return $this->aValue;
		}

		/*
		* @desc Возвращает input type=checkbox, со значением $this->aValue и именем $this->szFieldName[]. Применяется в админке
		*/
		public function getCheckBox() {
			return '<input type="checkbox" name="' . $this->szFieldName . '[]" value="' . $this->aValue . '"/>';
		}

		function getHidden() {
			return '<input name="' . $this->szFieldName . '" type="hidden" value="' . $this->aValue . '">';
		}

		function check( $value, $fieldInfo = array() ) {
			return !( ( $value < 0 ) || ( ( $value != '' ) && ( intval( $value ) != $value ) ) );
		}
		public function onCreateTable( \Extasy\ORM\QueryBuilder $queryBuilder ) {
			$queryBuilder->addFields( sprintf( '`%s` int not null auto_increment', $this->szFieldName ) );
			$queryBuilder->addFields( sprintf( 'primary key (`%s`) ', $this->szFieldName ) );
			$queryBuilder->setTableOptions( 'AUTO_INCREMENT=1' );
		}
	}
}
?>