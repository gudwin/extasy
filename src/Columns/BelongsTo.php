<?php
namespace Extasy\Columns {
	use \Register;
	use \DAO_Exception;

	class BelongsTo extends Category2 {

		/**
		 * (non-PHPdoc)
		 * @see kernel.4.0/dao/_types/DAO_Tcategory2::getViewValue()
		 */
		public function getViewValue() {
			$documentId = $this->GetValue();
			$class      = $this->fieldInfo[ 'model' ] ;

			$model = new $class();
			$found = $model->get( $documentId );
			if ( $found ) {
				return $model->getPreviewParseData( true );
			} else {
				return null;
			}
		}
		public function getLinkedRow() {

			if ( !empty( $this->aValue )) {
				$model = $this->getLinkedModel();
				if ( !empty( $model )) {
					return $model->getParseData();
				}

			}
			return null;
		}
		public function getLinkedModel() {
			$model = new $this->fieldInfo['model']();
			$found = $model->get( $this->aValue );


			if ( !empty( $found )) {
				return $model;
			}
			return null;
		}
		public function onCreateTable( \Extasy\ORM\QueryBuilder $queryBuilder ) {
			$queryBuilder->addFields( sprintf( '`%s` int not null default 0', $this->szFieldName ) );
			if ( !empty( $this->fieldInfo['index'])) {
				$queryBuilder->addFields( sprintf( 'index `search_%s` (`%s`)', $this->szFieldName,$this->szFieldName ) );
			}

		}
	}
}