<?
use \Faid\DB;
use \Faid\DBSimple;
require_once __DIR__ . DIRECTORY_SEPARATOR . 'base.php';

class SystemRegisterImportTest extends SystemRegisterTestCase {
	protected function getRowsCount() {

		return DBSimple::getRowsCount( SYSTEMREGISTER_TABLE );
	}

	public function testImportEmptyArray() {
		$nCount = self::getRowsCount();
		//
		SystemRegisterHelper::import(new SystemRegister('/System/'), array());
		//
		$nCount2 = self::getRowsCount();
		//
		$this->assertEquals($nCount, $nCount2);
	}

	/**
	 * @expectedException SystemRegisterException
	 */
	public function testIncorrectPath() {
		SystemRegisterHelper::import(new SystemRegister('/System/XXX'), array());
	}

	/**
	 * Вставляем массив из значений
	 */
	public function testImport() {
		$nCount = self::getRowsCount();
		SystemRegisterHelper::import(
			new SystemRegister('/System/'),
			array('XX' => '1', 'yy' => '2', 'Gisma' => 'rulit!')
		);
		$nCount2 = self::getRowsCount();
		$this->assertEquals($nCount2 - 3, $nCount);
		//
		$system = new SystemRegister('/System');
		$this->assertEquals($system->Gisma->value, 'rulit!');
	}

	/**
	 * Импортируем массив-массивов
	 */
	public function testImportWithSubArrays() {
		$nCount = self::getRowsCount();
		//_debug( $nCount, DBSimple::select( SYSTEMREGISTER_TABLE ));
		SystemRegisterHelper::import(
			new SystemRegister('/System/'),
			array(
				'XX' => '1',
				'yy' => '2',
				'Gisma' => 'rulit!',
				'subdata' => array(
					'param1' => 1,
						'param2' => 2
				))
		);
		$nCount2 = self::getRowsCount();

		$this->assertEquals($nCount, $nCount2 - 6);


		//
		$system = new SystemRegister('/System');
		$this->assertEquals($system->subdata->param2->value, 2);
		$this->assertEquals($system->XX->value, '1');
	}

	/**
	 * Проверяем дублирование
	 */
	public function testImportDuplicated() {
		$nCount = self::getRowsCount();
		//

		SystemRegisterHelper::import(
			new SystemRegister('/System/'),
			array('XX' => '1', 'yy' => '2', 'Gisma' => 'rulit!')
		);

		SystemRegisterHelper::import(
			new SystemRegister('/System/'),
			array('XX' => '1', 'yy' => '2', 'Gisma' => 'rulit!')
		);

		SystemRegisterHelper::import(
			new SystemRegister('/System/'),
			array('XX' => '1', 'yy' => '2', 'Gisma' => 'rulit!')
		);
		//
		$nCount2 = self::getRowsCount();
		//
		$this->assertEquals($nCount2 - 3, $nCount);
	}

	/**
	 * В данном тесте мы проводим достаточно сложный импорт
	 */
	public function testComplicatedImport() {
		$system = new SystemRegister('/System/');
		SystemRegisterHelper::import(
			$system,
			array('XX' => '1', 'yy' => '2', 'Gisma' => 'rulit!', 'array' => array('0' => 'value1'))
		);
		SystemRegisterHelper::import(
			$system,
			array('zz' => '3', 'array' => array('1' => 'value2'))
		);
		$system = new SystemRegister('/System/');
		$this->assertEquals('1', $system->XX->value);
		$this->assertEquals('2', $system->yy->value);
		$this->assertEquals('3', $system->zz->value);
		$this->assertEquals('value1', $system->array->get('0')->value);
		$this->assertEquals('value2', $system->array->get('1')->value);
	}

	/**
	 * В данном тесте мы проверяем, что метод import корректно обновляет файловый кеш
	 */
	public function testImportUpdatingFileCache() {
		SystemRegisterSample::loadAll();
		$system = new SystemRegister('/System/');
		$this->assertEquals(false, isset($system->yy));
		SystemRegisterHelper::import(
			$system,
			array('XX' => '1', 'yy' => '2', 'Gisma' => 'rulit!', 'array' => array('0' => 'value1'))
		);
		SystemRegisterSample::loadAll();
		$base = new SystemRegister('System/');
		$this->assertEquals('rulit!', $base->Gisma->value);
		$this->assertEquals('1', $base->XX->value);
	}

	public function testImportUpdatingFileCache2() {
		$system = new SystemRegister('/System/');
		$this->assertEquals(false, isset($system->yy));
		SystemRegisterHelper::import(
			$system,
			array('XX' => '1', 'yy' => '2', 'Gisma' => 'rulit!', 'array' => array('0' => 'value1'))
		);
		$this->assertEquals(true, isset($system->yy));
		SystemRegisterSample::loadAll();
		$this->assertEquals(true, isset($system->yy));
		$base = new SystemRegister('System/');
		$this->assertEquals('rulit!', $base->Gisma->value);
	}

	public function testImportUpdatingFileCacheWithDeletion() {
		$system = new SystemRegister('/System/');
		$this->assertEquals(false, isset($system->array));
		SystemRegisterHelper::import(
			$system,
			array('XX'    => '1', 'yy' => '2', 'Gisma' => 'rulit!',
				  'array' => array('0' => 'value1', '1' => array('1' => '2', '3' => '4'))
			)
		);
		$this->assertEquals(true, isset($system->array));
		//
		SystemRegisterSample::loadAll();
		$base = new SystemRegister('System');
		$this->assertEquals(true, isset($system->array));
		$base->delete('array');
		$this->assertEquals(false, isset($base->array));
		//
		SystemRegisterSample::clearCache();
		SystemRegisterSample::loadAll();
		$system = new SystemRegister('/System/');
		$this->assertEquals(false, isset($base->array));
	}

}

?>