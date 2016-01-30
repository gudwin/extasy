<?php

/**
 * В данном тесте проверяется работа с sitemap
 * @author Gisma
 *
 */
class manageControlSitemapTest extends baseSystemRegisterTest {

	public function tearDown() {
		$path = array(
			LIB_PATH.'custom_config/controls/failed_to_load.php',
			LIB_PATH.'custom_config/test.php',
			LIB_PATH.'custom_config/test2.php');
		foreach ($path as $row) {
			if (file_exists($row)) {
				unlink($row);
			}	
		}
		
		$register = new SystemRegister('Applications/cconfig/user_control_path');
		$child = SystemRegisterSample::selectChild($register->getId());
		foreach ($child as $row) {
			$register->delete($row['name']);
		}
	}
/**
	 * Попытка создать контрол неизвестного класса
	 * @expectedException CConfigException
	 */
	public function testCreateUnknownControl() {
		CConfigControlManager::createControl('1','test','unknown_field','XX',array(),'0',null);
	}
	/**
	 * Создаем контрол
	 */
	public function testCreateControl() {
		$control = CConfigControlManager::createControl('2','test','inputField','XX',array(),'1',null);
		$this->assertEquals(true,$control instanceof CConfigBaseControl);
		
	}
	/**
	 * Попытка загрузить контрол, файл для которого есть, но нету исключения
	 * @expectedException CConfigException
	 */
	public function testLoadControlWhenClassNotExists() {
		CConfigControlManager::loadControl('failed_to_load');
	}
	/**
	 * Тест установки sitemap-индекса к конфигу  
	 */
	public function testSelectControlNames() {
		SystemRegisterSample::clearCache();
		$fileList = \DAO_FileSystem::getInstance()->getFileList(LIB_PATH.'custom_config/controls/');
		$list = CConfigControlManager::selectAll();
		$this->assertEquals(sizeof($fileList),sizeof($list));
		// Добавляем в реестр файлег, но не пишем класс
		$path = LIB_PATH . 'custom_config/test.php';
		$register = new SystemRegister('Applications/cconfig/user_control_path');
		$register->insert('test',$path,'');
				
		file_put_contents($path,'');
		$list = CConfigControlManager::selectAll();
		$this->assertEquals(sizeof($fileList),sizeof($list));
		
		// Заполняем инфо о классе, таким образом класс становится доступным
		$path = 'custom_config/test2.php';
		$contents = <<<EOD
<?
class CConfigControl_Test2 extends CConfigBaseControl {
	public function getXType() {
		return "test2";
	}
}
?>
EOD;
		file_put_contents(LIB_PATH.$path,$contents);
		$register->insert('test2',$path,'');
		$list = CConfigControlManager::selectAll();
		$this->assertEquals(sizeof($fileList) + 1,sizeof($list));
		
	}
		

}