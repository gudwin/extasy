<?php
use \Faid\DB;

class customConfigMainTest extends baseSystemRegisterTest {
	/**
	 * Очищаем системный реестр
	 */
	protected function clearSystemRegister() {
		SystemRegisterSample::clearCache();
		$register = new SystemRegister('Applications/cconfig');
		$register->delete('user_control_path');
		SystemRegisterSample::clearCache();
		$register->insert('user_control_path',null,'',SYSTEMREGISTER_BRANCH_TYPE);
	}
	/**
	 * @expectedException CConfigException
	 */
	public function testUknownSchema() {
		$schema = CConfig::getSchema('testxx');
	}
	/**
	 * @expectedException CConfigException
	 */
	public function testCreateDuplicateSchema() {
		$schema = CConfig::createSchema('test');
	}
	/**
	 * Попытка удаления неизвестного контрола
	 * @expectedException CConfigException
	 */
	public function testRemoveUnknownControl() {
		$schema = CConfig::getSchema('test');
		$schema->removeControl('xxx');
	}
	/**
	 * @expectedException CConfigException
	 */
	public function testAddUnknownTypeControl() {
		$schema = CConfig::getSchema('test');
		$schema->addControl('testControl','xxx','Ппц');
	}
	/**
	 * @expectedException CConfigException
	 */
	public function testLoadUsingUnknownId() {
		$schema = CConfig::getSchemaById(-1);
	}
	public function testLoadById() {
		$schema = CConfig::getSchemaById(1);
		$this->assertEquals(1,$schema->getId());
		$this->assertEquals('test',$schema->getName());
	}
	/**
	 * @expectedException CConfigException
	 */
	public function testAddDuplicateControlByName() {
		$schema = CConfig::getSchema('test');
		$schema->addControl('name','inputField','Ппц');
	}
	/**
	 * @expectedException CConfigException
	 */
	public function testSetupEmptySchemaName() {
		$schema = CConfig::createSchema('');
	}
	/**
	 * Проверяем, что в случае если на вход метода будет подано некорректное имя объект об этом сообщит
	 * @expectedException CConfigException
	 */
	public function testGetUnknownControl() {
		$schema = CConfig::getSchema('test');
		$control = $schema->getControlByName('unknown');
	}
	/**
	 * Проверяет, корректно ли работает ли поиск контрола по его имени
	 */
	public function testGetControl() {
		$schema = CConfig::getSchema('test');
		$control = $schema->getControlByName('subname');
		$this->assertEquals(2,$control->getId());
	}
	/**
	 * посылаем некорректные данные на установку вкладок
	 * @expectedException CConfigException
	 */
	public function testIncorrectTabData() {
		$schema = CConfig::getSchema('test');
		$schema->setTabSheets(1);
	}
	/**
	 * @expectedException CConfigException
	 */
	public function testCreateEmpty() {
		$schema = CConfig::createSchema('text');
		$schema->updateSchema('','Title');
	}
	
	public function testGetValues() {
		$schema = CConfig::getSchema('test');
		$data = $schema->getValues();
		$this->assertEquals($data,array(
			'name' => '1',
			'subname' => '2',
			'seo_title' => '3',
		));
	}
	public function testSetupValues() {
		$values = array(
			'name' => '111',
			'subname' => '222',
			'seo_title' => '333',
		);
		$schema = CConfig::getSchema('test');
		$schema->setValues($values);
		//
		$schema2 = CConfig::getSchema('test');
		$result = $schema2->getValues();
		$this->assertEquals($values,$result);
	}
	/**
	 * Проверяем игнорирование неизвестных полей, поступающих на конфиг
	 */
	public function testSetupUknownValues() {
		$values = array(
			'name' => '111',
			'uknown_field' => '222',
			'uknown_field222' => '333',
		);
		$schema = CConfig::getSchema('test');
		$schema->setValues($values);
		$result = $schema->getValues();
		$this->assertEquals('111',$result['name']);
	}
	public function testRemoveControl() {
		$old = $this->getCounts();
		$schema = CConfig::getSchema('test');
		$schema->removeControl('subname');
		$new = $this->getCounts();
		$this->assertEquals($old['controlcount'] - 1,$new['controlcount']);
		
	}
	/**
	 * Попытка обновления и занятие уже занятой схемы
	 * @expectedException CConfigException
	 */
	public function testUpdateOnExistsSchema() {
		$schema = CConfig::createSchema('test2');
		$schema->updateSchema('test','xx');
	}
	public function testGetTabsheets() {
		$valid = array(
			array('id' => 1, 'title' => 'Основные данные'),
			array('id' => 2, 'title' => 'SEO'),
		);
		$schema = CConfig::getSchema('test');
		$tabs = $schema->getTabSheets();
		$this->assertEquals($tabs,$valid);
	}
	public function testSetupTabsheets() {
		$schema = CConfig::getSchema('test');
		$schema->setTabsheets(array(
			'Основные данные' => 'name',
			'Подзаголовок' => array('subname','seo_title')
		));
		$schema = CConfig::getSchema('test');
		$result = $schema->getTabSheets();
		$this->assertEquals(sizeof($result),2);
		// Проверяем порядок вкладок
		$this->assertEquals($result[0]['title'],'Основные данные');
		$this->assertEquals($result[1]['title'],'Подзаголовок');
	}
	public function testCreate() {
		$old = $this->getCounts();
		//
		
		$schema = CConfig::createSchema('test2');
		// Проверяем, что схема была создана и обозначена в таблице
		$schemaCount = DB::getField('select count(*) as `count` from '.CCONFIG_SCHEMA_TABLE,'count');
		$this->assertEquals(2,$schemaCount);
		$schema->addControl('name','inputField','Имя');
		$schema->addControl('name2','inputField','Имя2');
		
		$schema->setTabSheets(array(
			'Данные' => 'name',
		));
		
		// Проверяем кол-во записей в обоих таблицах
		$result = $this->getCounts();
		$this->assertEquals($result,array(
			'tabcount' => $old['tabcount'] + 1,
			'controlcount' => $old['controlcount'] + 2
		));
		
	}
	public function testUpdate() {
		$schema = CConfig::getSchema('test');
		$schema->updateSchema('test2','xx');
		$schema = CConfig::getSchema('test2');
	}
	public function testDelete() {
		//
		$schema = CConfig::getSchema('test');
		$schema->delete();
		$result = $this->getCounts();
		$this->assertEquals($result['tabcount'],0);
		$this->assertEquals($result['controlcount'],0);
	}
	public function testSelectTabbed() {
		$schema = CConfig::getSchema('test');
		$controls = $schema->selectTabbedControls();
		$this->assertEquals(sizeof($controls),2);
		$this->assertEquals(isset($controls['SEO']),true);
		$this->assertEquals(sizeof($controls['SEO']),1);
	}
	/**
	 * Проверяем, как правильно работает возвращение полного списка контролов в схеме 
	 */
	public function testGetControlList() {
		$schema = CConfig::getSchema('test');
		$list = $schema->returnControlList();
		$this->assertEquals(3,sizeof($list));
		// Проверяем, что контролов 3 и что все они объекты
		$controlNames = array('name','subname','seo_title');	
		foreach ($list as $row) {
			$this->assertEquals(true,$row instanceof CConfigBaseControl);
			$pos = array_search($row->getName(),$controlNames);
			if ($pos === false) {
				$this->fail();
			} 
			unset($controlNames[$pos]);
		}
		if (!empty($controlNames)){
			$this->fail();
		}
		// Теперь добавляем контрол
		$schema->addControl('name2','inputField','Имя2');
		// Снова получаем контролы, проверяем их количтество
		$list = $schema->returnControlList();
		$this->assertEquals(4,sizeof($list));
		foreach ($list as $row) {
			if (!$row instanceof CConfigBaseControl) {
				$this->fail();
			}
		}
	}
	public function testSetupTabsheets2() {
		$schema = CConfig::getSchema('test');
		$schema->setTabsheets(array(
			'Основные данные' => 'name',
			'Подзаголовок' => 'subname',
			'SEO' => array('seo_title')
		));
		$schema = CConfig::getSchema('test');
		$result = $schema->selectTabbedControls();
		$this->assertEquals(sizeof($result),3);
		// Проверяем порядок вкладок
		$this->assertEquals(array_keys($result),array('Основные данные','Подзаголовок','SEO'));
		// Проверяем, что элемент есть 
		$this->assertEquals(isset($result['Подзаголовок']['subname']),true);
		// Проверяем его значение
		$this->assertEquals($result['Подзаголовок']['subname']->getValue(),2);

		
	}
	public function testGetNameAndTitleAndId() {
		$schema = CConfig::getSchema('test');
		$this->assertEquals('test',$schema->getName());
		$this->assertEquals('Тестовый конфиг',$schema->getTitle());
		$this->assertEquals('1',$schema->getId());
	}

	
	protected function getCounts() {
		$sql = <<<SQL
		select 
			(select count(*) from `custom_config_groups`) as `tabcount`,
			(select count(*) from `custom_config_items`) as `controlcount`
SQL;
		$result = DB::get($sql);
		return $result;
	}
}