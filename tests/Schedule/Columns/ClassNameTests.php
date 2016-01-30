<?php

namespace Extasy\tests\Schedule\Columns;

use \Extasy\Schedule\Columns\ClassName;
use \Extasy\tests\Schedule\TestAction;
class ClassNameTests extends \Extasy\tests\Schedule\BaseTest {
	/**
	 * @expectedException \NotFoundException
	 */
	public function testClassNotFound() {
		ClassName::validateClassName('unknown_class_Name');
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testClassNotAnJobAncestor() {
		ClassName::validateClassName( \Extasy\Model\Model::ModelName );
	}
	public function testWithCorrectClass() {
		$this->assertTrue( ClassName::validateClassName( TestAction::ModelName ) ) ;
	}
} 