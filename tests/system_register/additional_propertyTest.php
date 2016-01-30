<?
require_once __DIR__ . DIRECTORY_SEPARATOR . 'base.php';

class TestSystemRegisterAdditionalProperty extends SystemRegisterTestCase {

	public function testGetComment() {
		$register = new SystemRegister('AppData');
		$this->assertEquals('Test comment', $register->test1->value1->comment);

	}

	public function testGetType() {
		$register = new SystemRegister('AppData');
		$this->assertEquals('string', $register->test1->value1->type);
	}

	public function testSetType() {
		$register                      = new SystemRegister('AppData');
		$register->test1->value1->type = 'number';
		//
		$this->assertEquals('number', $register->get('test1/value1')->type);
	}
}

?>