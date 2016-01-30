<?
require_once __DIR__ . DIRECTORY_SEPARATOR . 'base.php';

class TestSystemRegisterIsset extends SystemRegisterTestCase {
	public function testIsset() {
		$register = new SystemRegister('AppData');
		$this->assertEquals(true, isset($register->test1));
		$this->assertEquals(true, isset($register->test1->value1));
		$this->assertEquals(false, isset($register->test1->value2));
		$this->assertEquals(false, isset($register->test2));
	}

	/**
	 * Проверка удаления элементов
	 */
	public function testUnset() {
		$register = new SystemRegister('AppData');
		unset($register->test1->value1);
		//
		$this->assertEquals(false, isset($register->test1->value1));
	}

	/**
	 * Проверка удаления несуществующего элемента
	 * @expectedException SystemRegisterException
	 */
	public function testUnsetUknownElement() {
		$register = new SystemRegister('AppData');

		unset($register->test1->value2);
		//
	}
}

?>