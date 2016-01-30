<?
namespace Extasy\Columns {
	class Number extends Input {
		/**
		 *
		 * @param unknown $dbData
		 */
		public function onAfterSelect( $dbData ) {
			if ( isset( $dbData[ $this->szFieldName ] ) ) {
				$this->aValue = floatval( $dbData[ $this->szFieldName ] );
			}
		}
	}
}