<?php
namespace Extasy\Columns {
	use \Faid\UParser, \Faid\DB;
	use DAO;
	class File extends BaseColumn {
		/**
		 *
		 * @param unknown $dbData
		 */
		public function onAfterSelect( $dbData ) {
			if ( isset( $dbData[ $this->szFieldName ] ) ) {
				$this->aValue = $dbData[ $this->szFieldName ];
			}
		}

		/**
		 * @desc
		 * @return
		 */
		public function getViewValue() {

			return $this->aValue;
		}

		/**
		 * @desc
		 * @return
		 */
		public function getAdminFormValue() {
			// �������� ������ ��������
			$aParse            = array();
			$aParse[ 'value' ] = $this->aValue;
			$aParse[ 'name' ]  = $this->szFieldName;
			$aParse[ 'id' ]    = $this->document->id->getValue();
			// ������
			$szResult = UParser::parsePHPFile( __DIR__ . DIRECTORY_SEPARATOR  . 'file/form.tpl', $aParse );
			// ���������� ���������
			return $szResult;
		}

		/**
		 * @return
		 */
		public function setValue( $value, $fullPath = false ) {
			if ( $fullPath ) {
				$fileValue    = substr( realpath( $value ), strlen( realpath( WEBROOT_PATH ) ) - 1 );
				$fileValue    = str_replace( '\\', '/', $fileValue );
				$this->aValue = $fileValue;
			} else {
				$this->aValue = $value;
			}
		}

		/**
		 * @desc
		 * @return
		 */
		public function getPath() {
			if ( !empty( $this->aValue ) ) {
				return WEBROOT_PATH . substr( $this->aValue, 1 );
			} else {
				return '';
			}

		}

		public function getFileName() {
			$szPath = $this->getPath();
			if ( file_exists( $szPath ) ) {
				return basename( $szPath );
			} else {
				return '';
			}
		}

		function getHTTPPath() {
			if ( !empty( $this->aValue ) ) {
				return $this->aValue;
			} else {
				return '';
			}
		}

	}
}
?>