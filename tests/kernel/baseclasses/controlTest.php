<?php
//************************************************************//
//                                                            //
//    В данном тесте я протестирую функции контрола           //
//       Copyright (c) 2006-2011  Extasy Team                 //
//       Разработчик: Gisma (14.03.2011)                      //
//       Email:   dmitrey.schevchenko@gmail.com               //
//                                                            //
//************************************************************//
require_once dirname( __FILE__ ) . '/test_control.php';
class BasicControlTest extends PHPUnit_Framework_TestCase {
	public function testSetGetName() {
		$control = new CTestControl();
		$control->setName('test_control');
		$this->assertEquals('test_control',$control->getName());
	}
	public function testGenerate() {
		$control = new CTestControl();
		$this->assertEquals('succeded',$control->generate());
	}
	public function testToString() {
		$control = new CTestControl();
		$this->assertEquals('succeded',(string)$control);
	}
}
?>