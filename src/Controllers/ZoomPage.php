<?
namespace Extasy\Controllers {
	use \CMSDesign;

	class ZoomPage extends \adminPage {
		public function __construct() {
			parent::__construct();
			$this->addGet( 'url', 'showByURL' );
		}

		public function main() {
			$this->jump( \Extasy\CMS::getWWWRoot() );
		}

		public function showByUrl( $url ) {

			$design = CMSDesign::getInstance();
			$design->popupBegin();
			$design->popupHeader( 'Просмотр изображения' );
			$design->formBegin();
			$design->br();
			if ( !empty( $url ) ) {
				print '<img src="' . strip_tags( $url ) . '" width="100%" height="100%">';
			}

			$design->popupEnd();
			$this->output();
		}

		/**
		 * @param $path
		 * @param $type
		 * @param $szField
		 */
		public function show( $path, $type, $szField ) {
			$design = CMSDesign::getInstance();
			//
			$path    = stripslashes( $path );
			$type    = stripslashes( $type );
			$szField = stripslashes( $szField );

			$modelName = \Extasy\Model\Model::isModel( $type );
			$fields    = call_user_func( $modelName, 'getFieldsInfo' );


			$aInfo = @getimagesize( \Extasy\CMS::getFilesPath() . $$fields[ $szField ][ 'base_dir' ] . $path );

			$szFilename = \Extasy\CMS::getFilesHttpRoot() . $fields[ $szField ][ 'base_dir' ] . $path;
			$x          = '?' . rand( 0, 1000000 );

			if ( isset( $aInfo[ 2 ] ) ) {

				$aParse[ 'szFilename' ] = $szFilename;
			}
			$design->popupBegin();
			$design->popupHeader( 'Просмотр изображения' );
			$design->formBegin();
			$design->br();
			if ( !empty( $szFilename ) ) {
				print '<img src="' . $szFilename . $x . '">';
			}

			$design->popupEnd();
			$this->output();
		}
	}
}
?>