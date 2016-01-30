<?
namespace Extasy\Columns {
	use \Faid\UParser;

//************************************************************//
//                                                            //
//            Контрол выбора изображения                      //
//       Copyright (c) 2008 Ext-CMS (http://www.ext-cms.com/) //
//               отдел/сектор                                 //
//       Email:    info@gisma.ru (http://www.gisma.ru/)       //
//                                                            //
//  Разработчик: Gisma (10.10.2008)                           //
//  Модифицирован:  10.10.2008  by Gisma                      //
//                                                            //
//************************************************************//

	class Simple_Image extends Faile {
		public function onInsert() {
			$szPath = $this->getPath();
			if ( !empty( $this->aValue ) && file_exists( $szPath ) ) {
				$aInfo = @getimagesize( $szPath );
				if ( !empty( $aInfo ) ) {
					parent::onInsert();
				}
			}
		}

		public function onUpdate() {
			$szPath = $this->getPath();
			if ( !empty( $this->aValue ) && file_exists( $szPath ) ) {
				$aInfo = @getimagesize( $szPath );
				if ( !empty( $aInfo ) ) {
					parent::onUpdate();
				}
			}
		}

		/**
		 *   -------------------------------------------------------------------------------------------
		 *   Возвращает информацию по изображению
		 * @return
		 *   -------------------------------------------------------------------------------------------
		 */
		public function getViewValue() {
			$aResult = array();
			$szPath  = $this->getPath();
			if ( !empty( $this->aValue ) && file_exists( $szPath ) ) {
				//
				$aInfo = @getimagesize( $szPath );
				if ( !empty( $aInfo ) ) {
					$aResult = array(
						'src'    => $this->aValue,
						'width'  => $aInfo[ 0 ],
						'height' => $aInfo[ 1 ],
					);
					return $aResult;
				}
			}
			return null;
		}

		public function getAdminFormValue() {
			// Данные для парсинга
			$aParse           = array();
			$aParse[ 'name' ] = $this->szFieldName;
			$szPath           = $this->getPath();
			if ( !empty( $this->aValue ) && file_exists( $szPath ) ) {
				$aInfo = @getimagesize( $szPath );
				//
				$aParse[ 'value' ] = $this->aValue;
			} else {
				$aParse[ 'value' ] = '';
			}
			//
			$szResult = UParser::parsePHPFile( __DIR__ . DIRECTORY_SEPARATOR  . 'simple_image/form.tpl', $aParse );
			return $szResult;
		}
	}
}
?>