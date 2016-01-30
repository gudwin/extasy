<?php
namespace Extasy\Columns {

	use \Faid\DB;
	use \CHTMLArea;

	/**
	 *
	 * @author Gisma
	 *
	 */
	class Html extends BaseColumn {
		/**
		 *
		 * @param unknown $dbData
		 */
		public function onAfterSelect( $dbData ) {
			if ( isset( $dbData[ $this->szFieldName ] ) ) {
				$this->aValue = $dbData[ $this->szFieldName ];
			}
		}

		function getAdminFormValue() {
			$area           = new CHTMLarea();
			$area->name     = $this->szFieldName;
			$area->content  = $this->aValue;
			$area->title    = !empty( $this->fieldInfo[ 'title' ] ) ? $this->fieldInfo[ 'title' ] : $this->szFieldName;
			$area->required = !empty( $this->fieldInfo[ 'required' ] );
			return $area->generate();

		}

		/**
		 * (non-PHPdoc)
		 * @see \Extasy\Columns\BaseColumn::getViewValue()
		 */
		function getViewValue() {
			if ( !empty( $this->fieldInfo[ 'nop' ] ) ) {

				return ( preg_replace( '/<p>(.*?)<\/p>/ism', '$1<br>' . "\r\n", $this->aValue ) );
			} else {
				return $this->aValue;
			}
		}

		public function onCreateTable( \Extasy\ORM\QueryBuilder $queryBuilder ) {
			$queryBuilder->addFields( sprintf( '`%s` text null', $this->szFieldName ) );
		}
	}
}