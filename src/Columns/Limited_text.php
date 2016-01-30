<?
namespace Extasy\Columns {
	use \Faid\DB;
	use \DAO_Exception;
	use \DAO;

	class Limited_text extends BaseColumn {
		function __construct( $szFieldName, $fieldInfo, $Value ) {
			parent::__construct( $szFieldName, $fieldInfo, $Value );
			if ( empty( $fieldInfo[ 'max_size' ] ) ) {
				throw new DAO_Exception( 'Parameter `max_size` not found', $this->szFieldName );
			}

			// �������� �� HTML-��������
			$this->aValue = ( $this->aValue );
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
			$this->formatValue();
			//
			$query->setSet( $this->szFieldName,$this->aValue );
		}

		function onUpdate(\Extasy\ORM\QueryBuilder $query ) {
			$this->formatValue();
			//
			$query->setSet( $this->szFieldName, ( $this->aValue ) );
		}

		protected function formatValue() {
			$this->aValue = strip_tags( $this->aValue );
			if ( !empty( $this->fieldInfo[ 'rn' ] ) ) {
				$this->aValue = nl2br( $this->aValue );
			}
		}

		public function MakeFormElement() {
			$szResult = $this->getFormValue();
			$aResult  = array(
				'value' => $szResult,
			);
			return $aResult;
		}

		function MakeAdminFormElement() {
			$szResult = $this->getAdminFormValue();
			$aResult  = array(
				'value' => $szResult,
			);
			return $aResult;
		}

		function getFormValue() {
			$szResult = include __DIR__ . DIRECTORY_SEPARATOR . 'limited_text/form.tpl';
			return $szResult;
		}

		function getAdminFormValue() {
			$szResult = include __DIR__ . DIRECTORY_SEPARATOR . 'limited_text/admin.tpl';
			return $szResult;
		}

		function getValue() {
			return $this->aValue;
		}

		/**
		 * @desc
		 * @return
		 */
		function getViewValue() {
			return $this->aValue;
		}

	}
}
?>