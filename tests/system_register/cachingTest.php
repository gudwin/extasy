<?
require_once __DIR__ . DIRECTORY_SEPARATOR . 'base.php';

class TestSystemRegisterCaching extends SystemRegisterTestCase {
	/**
	 * Проверяем работу кеша
	 */
	public function testCache() {
		$register = new SystemRegister();
		$register->get('AppData/test1/value1');
		//
		$observer = $this->getMock('DB');
		$observer->expects($this->never())
			->method('get');
		//
		$szValue = $register->AppData->test1->value1;
	}

	/**
	 * Проверяем удаляется ли из кеша
	 * @expectedException SystemRegisterException
	 */
	public function testCacheOnDeletedItem() {
		$register = new SystemRegister();
		$register->insert('AppData/test1/new_value', 'New Value');
		//
		$szValue = $register->get('AppData/test1/new_value');
		//
		$test1 = $register->get('AppData/test1');
		$register->delete('AppData/test1/new_value');
		//
		$test1->get('new_value');
	}
}
