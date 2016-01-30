<?
namespace Extasy\Columns {
	class Integer extends Input {
		public function __construct( $fieldName, $fieldInfo, $value ) {
			if ( empty( $value )) {
				$value = 0;
			}
			parent::__construct( $fieldName, $fieldInfo, $value );
		}
		/**
		 *
		 * @param array $dbData
		 */
		public function onAfterSelect( $dbData ) {
			if ( isset( $dbData[ $this->szFieldName ] ) ) {
				$this->aValue = intval( $dbData[ $this->szFieldName ] );
			}
		}
		public function onCreateTable( \Extasy\ORM\QueryBuilder $queryBuilder ) {
			$queryBuilder->addFields( sprintf( '`%s` int not null default 0', $this->szFieldName ) );
			if ( !empty( $this->fieldInfo['index'])) {
				$queryBuilder->addFields( sprintf( 'index `search_%s` (`%s`)', $this->szFieldName,$this->szFieldName ) );
			}
		}
	}
}