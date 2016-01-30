<?php
/**
 * В данном тесте мы тестируем получение всех конфигов 
 * @author Gisma
 *
 */
class customConfigSelectingTest extends baseSystemRegisterTest{
	/**
	 * Проверяем работу метода CConfig::selectAll, он должен вернуть объекты CConfigSchema 
	 */
	public function testSelect() {
		$schema = CConfig::createSchema('test2');
		$schema->updateSchema('test2','Title');
		// Проверяем кол-во схем 
		$list = CConfig::selectAll();
		$this->assertEquals(2,sizeof($list));
		$this->assertEquals(true,$list[0] instanceof CConfigSchema);
	}
}