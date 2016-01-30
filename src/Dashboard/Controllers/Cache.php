<?php
namespace Extasy\Dashboard\Controllers {
	use \Faid\SimpleCache;

	class Cache extends \AdminPage{
		/**
		 * Deletes all caches and displays success message
		 */
		public function main( ) {
			$fs = \DAO_FileSystem::getInstance();
//			$baseDir = SimpleCache::getBaseDir();
//			$fileList = $fs->getFileList( $baseDir );
//			foreach ( $fileList as $key=> $row ) {
//				$fs->delete( $baseDir . $row );
//			}
			$title = 'Очистка кешей';
			$begin = array(
				$title => '#'
			);

			$this->outputHeader( $begin, $title );
			$design = \CMSDesign::getInstance();
//			$design->text->header('Очистка кешей завершена');
			$design->text->header('В разработке');
			$this->outputFooter( );
			$this->output();
		}
		public static function startup( ) {
			$instance = new Cache();
			$instance->process();
		}
	}
}