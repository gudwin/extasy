<?
namespace Extasy\Columns {
	use \Faid\DB;
	use \DAO;

	class Input extends BaseColumn {

		public function onSelect(\Extasy\ORM\QueryBuilder $query ) {
			if ( !empty( $this->aValue ) ) {
				$query->setWhere( $this->szFieldName, $this->aValue );
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

		public function getAdminFormValue() {
			$view = new \Faid\View\View( __DIR__ . '/Views/inputAdminForm.tpl' );
			$view->set( 'name', $this->szFieldName );
			$view->set( 'value', $this->aValue );
			$view->set( 'title',
						!empty( $this->fieldInfo[ 'title' ] ) ? $this->fieldInfo[ 'title' ] : $this->szFieldName );
			$view->set( 'formEdit', !empty( $this->fieldInfo[ 'form_edit' ] ) ?$this->fieldInfo[ 'form_edit' ] : '' );
			if ( !empty( $this->fieldInfo[ 'required' ] ) ) {
				$view->set( 'requiredField', true );
			}
			return $view->render();
		}

		public function getValue() {
			return $this->aValue;
		}

		/**
		 * @desc
		 * @return
		 */
		public function getViewValue() {
			return htmlspecialchars( $this->aValue );
		}

		public function onCreateTable( \Extasy\ORM\QueryBuilder $queryBuilder ) {
			$queryBuilder->addFields( sprintf( '`%s` varchar(255) not null default ""', $this->szFieldName ) );
		}
	}
}
?>