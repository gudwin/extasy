<?php
use \Faid\DB;
/**
 * В данном тесте проверяется работа с sitemap
 * @author Gisma
 *
 */
class customConfigSitemapTest extends baseSystemRegisterTest{
	/**
	 * Тест установки sitemap-индекса к конфигу  
	 */
	public function testSetupLink() {
		$schema = CConfig::getSchema('test');
		$schema->setupSitemapLink(2);
		$this->assertEquals(2,$schema->getSitemapLink());
		// Проверяет на уровне бд
		$sql = 'SELECT `sitemapId` FROM `custom_config_schema` WHERE `name`="%s" ';
		$sql = sprintf($sql,'test');
		$field = DB::getField($sql,'sitemapId');
		//
		$this->assertEquals('2',$field);
		
	}
	/**
	 * В данном тесте мы проверяем сохранность sitemap-ссылки после обновления инфы по схеме
	 */
	public function testSetupLinkAfterUpdate() {
		$sitemapId = 999;
		$schema = CConfig::getSchema('test');
		$schema->updateSchema('test1','Ссыки-ссыки');
		$schema->setupSitemapLink($sitemapId);
		//
		$this->assertEquals($sitemapId,$schema->getSitemapLink());
		//
		$schema = CConfig::getSchema('test1');
		$this->assertEquals($sitemapId,$schema->getSitemapLink());
		
		
	}
	public function testGetLink() {
		$schema = CConfig::getSchema('test');
		$this->assertEquals(0,$schema->getSitemapLink());
	}
	
}