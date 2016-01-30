<?
namespace Extasy\Columns {

	class IP extends BaseColumn {
		public function __construct( $fieldName, $fieldInfo, $value ) {

			if ( empty( $value ) ) {
				if ( !empty( $_SERVER[ 'REMOTE_ADDR' ] ) ) {
					$value = $_SERVER[ 'REMOTE_ADDR' ];
				} else {
					$value = '127.0.0.1';
				}
			}
			if ( $value == '::1') {
				$value = '127.0.0.1';
			}
			parent::__construct( $fieldName, $fieldInfo, $value );
		}
		public function setValue( $newValue ) {
			if ( $newValue == '::1') {
				$newValue = '127.0.0.1';
			}
			parent::setValue( $newValue );
		}
        /**
         * Вызывается в процессе вставки
         */
        public function onInsert(\Extasy\ORM\QueryBuilder $query ) {
            $query->setSet( $this->szFieldName,ip2long($this->aValue ));
        }

        public function onUpdate( \Extasy\ORM\QueryBuilder $query ) {
            $query->setSet( $this->szFieldName, ip2long( $this->aValue ) );
        }
		/**
		 *
		 * @param unknown $dbData
		 */
		public function onAfterSelect( $dbData ) {
			if ( isset( $dbData[ $this->szFieldName ] ) ) {
				$this->aValue = long2ip( $dbData[ $this->szFieldName ] );
			}
		}
		public function onCreateTable( \Extasy\ORM\QueryBuilder $queryBuilder ) {
			$queryBuilder->addFields( sprintf( '`%s` int unsigned not null default 0', $this->szFieldName ) );
		}
	}
}
?>