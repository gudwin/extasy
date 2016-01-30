<?
namespace Extasy\Columns {
	use \Faid\DBSimple, \Faid\DB;
	use \Exception;
	use \DAO;
	class Calculated extends BaseColumn {
		public function __construct( $szFieldName, $fieldInfo, $aValues ) {
			// Если обозначен путь, где находится функция - грузим его
			if ( !empty( $fieldInfo[ 'func_path' ] ) ) {
				require_once APPLICATION_PATH . $fieldInfo[ 'func_path' ];
			}
			// Заплата, на случай если callback подан в виде строки
			if ( is_string( $fieldInfo[ 'callback' ] ) ) {
				$fieldInfo[ 'callback' ] = explode( '::', $fieldInfo[ 'callback' ] );
			}
			// Проверяем существование функции
			if ( !is_callable( $fieldInfo[ 'callback' ] ) ) {
				throw new Exception ( 'Calculated: `callback` not defined' );
			}
			parent::__construct( $szFieldName, $fieldInfo, $aValues );


		}

		/**
		 *
		 * Возвращает значение
		 */
		public function calculateValue() {

			if ( is_array( $this->fieldInfo[ 'callback' ] ) ) {
				$result = call_user_func( array( $this->fieldInfo[ 'callback' ][ 0 ],
												 $this->fieldInfo[ 'callback' ][ 1 ] ),
										  $this->document->id->getValue(),
										  $this->document );
			} else {
				$result = call_user_func( $this->fieldInfo[ 'callback' ],
										  $this->document->id->getValue(),
										  $this->document );
			}

			return $result;
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

		public function onInsert(\Extasy\ORM\QueryBuilder $query ) {
		}

		function onAfterInsert() {
			$this->aValue = $this->calculateValue();

			DBSimple::update( $this->document->getTableName(),
							  array(
								  $this->szFieldName => $this->aValue,
							  ),
							  array(
								  $this->document->getIndex() => $this->document->getId()
							  ) );
		}

		function onUpdate(\Extasy\ORM\QueryBuilder $query ) {
			$this->aValue = $this->calculateValue();
			$query->setSet( $this->szFieldName, $this->aValue );
		}

		public function getViewValue() {
			return $this->aValue;
		}

		public function getFormValue() {
			return $this->aValue . '<input type="hidden" name="' . $this->szFieldName . '" value="' . $this->aValue . '" />';
		}

		public function onCreateTable( \Extasy\ORM\QueryBuilder $queryBuilder ) {
			throw new \ForbiddenException('Calculated::onCreateTable not allows to create model tables');
		}
	}
}
?>