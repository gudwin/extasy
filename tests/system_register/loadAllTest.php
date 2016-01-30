<?
require_once __DIR__ . DIRECTORY_SEPARATOR . 'base.php';

class LoadAllTest extends SystemRegisterTestCase {

	public function testLoadAllAndNothing() {
		SystemRegisterSample::loadAll();
	}

	public function testCheckGetCache() {
		SystemRegisterSample::loadAll();
		// Проверяем что кеш создался
		$this->assertEquals(sizeof(SystemRegisterSample::$aGetCache), 6);
		// Получаем значение
		$data = new SystemRegister('AppData/test1');
		$this->assertEquals($data->value1->value, 'This is a simple value');
	}

	public function testCheckChildCache() {
		SystemRegisterSample::loadAll();
		// Проверяем что кеш создался
		$this->assertEquals(sizeof(SystemRegisterSample::$aChildCache), 3);
		// Получаем значение
		$data = new SystemRegister('AppData/test1');
		$this->assertEquals($data->value1->value, 'This is a simple value');
	}

}

?>