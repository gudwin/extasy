<?php 
/**
 * 
 * Basic front-end exception
 * @author Gisma
 *
 */
class SiteMapException extends Exception
{
	public function __construct($message)
	{
		parent::__construct($message);
		if (!preg_match('#\/favicon\.ico\/#',$message)) {
			/*
				 * @todo Сделать настраиваемым отсылку в логи данной информации
			 */
			//$log = CMS::getInstance('log');
			//$log->addMessage('sitemap',$message);
		}
		
	}

}
?>