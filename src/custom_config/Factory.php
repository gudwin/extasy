<?php
namespace Extasy\custom_config {
	use \CConfig;

	class Factory {
		public static function createEmail( $configName, $description = '' ) {
			//
			try {
				$config = CConfig::createSchema( $configName );
			} catch (\CConfigException $e ) {
				return ;
			}

			$config->updateSchema( $configName, $description );
			$config->addControl( 'subject', 'inputfield', 'Тема письма' );
			$config->addControl( 'content', 'inputfield', 'Шаблон текста письма', array( 'rows' => 10 ) );
			$config->setTabsheets(
				   array(
					   'Основные данные' => array( 'subject', 'content' )
				   )
			);
		}
	}

}
