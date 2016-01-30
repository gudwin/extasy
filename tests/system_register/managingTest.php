<?
require_once __DIR__ . DIRECTORY_SEPARATOR . 'base.php';

class TestSystemRegisterManaging extends SystemRegisterTestCase {

	public function setUp() {
		parent::setUp();
		$this->sharedFixture = array(
			'simple_value'  => 'This is a simple value',
			'simple_value2' => 'This is as second value'
		);
	}

	public function testInsert() {
		$szMatch  = 'New value';
		$register = new SystemRegister('');
		$register->insert('AppData/test2');
		$register->insert('AppData/test3', $szMatch);
		//
		$value = $register->AppData->test3;
		$this->assertEquals($szMatch, (string) $value);

		// Добавляем в папку значени
	}

	/**
	 * @expectedException SystemRegisterException
	 */
	public function testInsertToMain() {
		$register = new SystemRegister('');
		$register->insert('failure_test');
	}

	/**
	 * Проверяем повторное добавление
	 * @expectedException SystemRegisterException
	 */
	public function testInsertDuplicate() {
		$register = new SystemRegister();
		$register->insert('AppData/test1/value1', 'New value');
	}

	/**
	 * Попытка создания ппапки
	 */
	public function testInsertToFolder() {
		$register = new SystemRegister('AppData');
		$register->insert('folder', NULL, '', SYSTEMREGISTER_BRANCH_TYPE);
		$register->insert('folder/new_value', 10);

		$AppData = new SystemRegister('AppData');
		$this->assertEquals(10, (string) $AppData->folder->new_value);
	}

	/**
	 * Попытка вставки в запись не являющейся веткой
	 * @expectedException SystemRegisterException
	 */
	public function testInsertToNotFolder() {
		$register = new SystemRegister('AddData/test1/');
		$register->insert('value1/unknown', 'new value');
	}

	public function testUpdate() {
		$register = new SystemRegister('/AppData');
		$register->update('test1/value1', $this->sharedFixture[ 'simple_value2' ]);
		//
		$this->assertEquals($this->sharedFixture[ 'simple_value2' ], (string) $register->test1->value1);
		// Повторяем :) 
		$register->update('test1/value1', $this->sharedFixture[ 'simple_value' ]);
		$this->assertEquals($this->sharedFixture[ 'simple_value' ], (string) $register->test1->value1);
	}

	/**
	 * Проверка обновлений с помощью свойств
	 */
	public function testUpdateUsingProperty() {
		$register = new SystemRegister('/AppData');
		//
		$register->test1->value1->value = $this->sharedFixture[ 'simple_value2' ];

		$this->assertEquals($this->sharedFixture[ 'simple_value2' ], (string) $register->test1->value1);
	}

	/**
	 * Тестирование удаления элементов
	 */
	public function testDelete() {
		$register = new SystemRegister('/AppData');
		//
		$register->delete('test1/value1');
		//
		try {
			$szValue = $register->test1->value1->value;
			$this->Fail('Property must be deleted');
		} catch (SystemRegisterException $e) {
		}

	}
}

?>