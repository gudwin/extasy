<?
namespace Extasy\Columns {
	class Boolean extends BaseColumn {
		public function __construct( $fieldName, $fieldInfo, $value ) {
			parent::__construct( $fieldName, $fieldInfo, boolval($value ) );
			if ( empty( $this->aValue ) ) {
				$this->aValue = false;
			}
		}

		public function onInsert(\Extasy\ORM\QueryBuilder $query ) {
			$query->setSet( $this->szFieldName,$this->aValue );
		}

		public function onUpdate(\Extasy\ORM\QueryBuilder $query ) {
			$query->setSet( sprintf( '`%s`= %s', $this->szFieldName, boolval( $this->aValue ) ? 'true' : 'false' ),
						 null,
						 true );
		}

		public function setValue( $newValue ) {
			parent::setValue( boolval( $newValue ) );
		}
		public function getViewValue() {
			return $this->aValue ;
		}
		public function getAdminFormValue() {
			$checkbox = new \CCheckbox();
			$checkbox->name = $this->szFieldName;
			$checkbox->checked = $this->aValue;
			$checkbox->value = 1;
			$checkbox->title = 'Да/Нет';
			return $checkbox->generate();

		}
		/**
		 *
		 * @param unknown $dbData
		 */
		public function onAfterSelect( $dbData ) {
			if ( isset( $dbData[ $this->szFieldName ] ) ) {
				$this->aValue = boolval( $dbData[ $this->szFieldName ] );
			}
		}
		public function onCreateTable( \Extasy\ORM\QueryBuilder $queryBuilder ) {
			$queryBuilder->addFields( sprintf( '`%s` bool not null default 0', $this->szFieldName ) );
			$queryBuilder->addFields( sprintf( 'index `search_%s` (`%s`)', $this->szFieldName,$this->szFieldName ) );
		}
	}
}