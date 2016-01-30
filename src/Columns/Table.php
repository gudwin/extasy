<?
namespace Extasy\Columns {
	use \Faid\UParser, \Faid\DB;
	use \Exception;
	use \DAO;
	class Table extends BaseColumn {
		public function __construct( $szFieldName, $fieldInfo, $aValue ) {
			if ( empty( $fieldInfo[ 'columns' ] ) || !is_array( $fieldInfo[ 'columns' ] ) ) {
				if ( !empty( $fieldInfo[ 'columns' ] ) ) {
					$columns                 = explode( '&', $fieldInfo[ 'columns' ] );
					$fieldInfo[ 'columns' ] = array();
					foreach ( $columns as $row ) {
						$tmp = explode( '=', $row );
						$key = $tmp[ 0 ];
						if ( sizeof( $tmp ) < 2 ) {
							$value = '';
						} else {
							$value = $tmp[ 1 ];
						}
						$fieldInfo[ 'columns' ][ $key ] = $value;
					}

				} else {
					throw new Exception ( 'fieldInfo["columns"] not defined' );
				}
			}
			if ( empty( $aValue ) ) {
				$aValue = array();
			}
			if ( is_string( $aValue ) ) {
				$aValue = unserialize( $aValue );
			}
			parent::__construct( $szFieldName, $fieldInfo, $aValue );
		}

		/**
		 *
		 * @param unknown $dbData
		 */
		public function onAfterSelect( $dbData ) {
			if ( !empty( $dbData[ $this->szFieldName ] ) ) {
				if ( is_string( $dbData[ $this->szFieldName ] ) ) {
					$this->aValue = unserialize( $dbData[ $this->szFieldName ] );
				} else {
					$this->aValue = $dbData[ $this->szFieldName ];
				}
			}
		}

		public function onInsert(\Extasy\ORM\QueryBuilder $query ) {
			$szValue = serialize( $this->aValue );

			$query->setSet( $this->szFieldName,$this->aValue );

		}

		public function onUpdate(\Extasy\ORM\QueryBuilder $query ) {
			$szValue = serialize( $this->aValue );
			$query->setSet( $this->szFieldName, $szValue );

		}

		public function getAdminFormValue() {

			//
			//
			//
			$strings = \CMS_Strings::getInstance();
			$aParse  = array(
				'lang'         => $strings->language,
				'aTableHeader' => $this->fieldInfo[ 'columns' ],
				'aData'        => ( $this->aValue ),
				'szName'       => $this->szFieldName,
				'szComment'    => '',
			);

			return UParser::parsePHPFile( __DIR__ . DIRECTORY_SEPARATOR  . 'table/admin-form.tpl', $aParse );

		}

		public function getViewValue() {
			return $this->aValue;
		}

	}
}
?>