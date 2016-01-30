<?
require_once __DIR__ . DIRECTORY_SEPARATOR . 'base.php';

class TestSystemRegister_Selecting extends SystemRegisterTestCase
{
	public function setUp()
	{
		parent::setUp();
		$this->sharedFixture = array(
			'simple_value' => 'This is a simple value',
			'simple_value2' => 'This is as second value'
			);

	}
	/**
	 * Тестируем метод получения значения
	 */
	public function testSelectOneValueByDifferentWays()
	{
		$aData = array(
			'' => 'AppData/test1/value1',
			'/AppData/test1/' => 'value1', // 
			'AppData/test1/' => 'value1',  // Это две идентичные конструкции! 
			'AppData/' => 'test1/value1',
			'AppData/test1' => 'value1',
		);

		//
		foreach ($aData as $dir=>$key)
		{
			$register = new SystemRegister($dir);
			$value = $register->get($key);
			//
			$this->assertEquals($this->sharedFixture['simple_value'],(string)$value);
		}
	}
	/**
	 * Попытка вызова конструктора с неизвестной ветвью
	 * @expectedException SystemRegisterException
	 */
	public function testSelectUknownKeyInConstructor()
	{
			$register = new SystemRegister('AppData/uknown_branch');
	}
	/**
	 * Попытка вызова конструктора с не-ветвью в конструкторе
	 * @expectedException SystemRegisterException
	 */
	public function testSelectNotBranchkKeyInConstructor()
	{
		$register = new SystemRegister('AppData/test1/value1');
	}
	public function testGettingInstanceOfFolder()
	{
		$register = new SystemRegister('/AppData');
		$new_register = $register->get('test1');
		
		$this->assertEquals(true,$new_register instanceof SystemRegister);
		// 
		$value = $new_register->get('value1');
		$this->assertEquals($this->sharedFixture['simple_value'],(string)$value);
	}
	public function testProperty()
	{
		$register = new SystemRegister('/AppData');
		$this->assertEquals($this->sharedFixture['simple_value'],(string)$register->test1->value1);
		//
		$register->test1->value1->value = $this->sharedFixture['simple_value2'];

		$this->assertEquals($this->sharedFixture['simple_value2'],(string)$register->test1->value1);
	}
	public function testChild()
	{
		$register = new SystemRegister();
		$test1 = $register->get('AppData/test1');
		$this->assertEquals($this->sharedFixture['simple_value'],(string)$test1->value1);
	}
	/**
	 * Попытка получения доступа к неизвестной ветви через свойства
	 * @expectedException SystemRegisterException
	 */
	public function testGettingUnknownBranch()
	{
		$register = new SystemRegister();
		$register->get('AppData')->get('testUnknown');
	}
	/**
	 * Попытка получения доступа к неизвестной ветви через свойства
	 * @expectedException SystemRegisterException
	 */
	public function testGettingUnknownBranchUsingProperty()
	{
		$register = new SystemRegister();
		$register->AppData->testUnknown;
	}
	/**
	 * @expectedException SystemRegisterException
	 */
	public function testfChild()
	{
		$register = new SystemRegister('/AppData');
		// Некорректный вызов
		$register->get('test1/value2');
	}
}
?>