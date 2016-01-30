<?php
namespace Extasy\tests\Models {
use \Faid\DB;
use \Faid\DBSimple;

use \Extasy\tests\BaseTest;
use \Extasy\Columns\InputColumn;

class basicModelsTest extends BaseTest {
	const fixtureName = 'test name';
	public static function setUpBeforeClass() {
		include __DIR__ . DIRECTORY_SEPARATOR . 'setup.php';
	}

	public static function tearDownAfterClass() {
		DB::post('DROP TABLE IF EXISTS `test_model`  ');
	}

	public function setUp() {
		DB::post('truncate table test_model');
		DBSimple::insert( 'test_model', array('id' => 1, 'name' => self::fixtureName));
	}

	public function tearDown() {

	}


	public function testCreateModel() {
		$model = new testModel();
	}

	public function testGetModelWithUnknownId() {
		$model = new testModel();
		$found = $model->get( 3 );
		//
		$this->assertEquals( $found, false );
	}

	public function testGetModel() {
		$model = new testModel();
		$found = $model->get( 1 );
		//
		$this->assertEquals( !empty( $found ), true );
	}

	/**
	 * - check that id created
	 */
	public function testInsertModel() {
		$model = new testModel();
		//
		$model->insert( );
		//
		$this->assertEquals( $model->id->getValue(), 2 );
	}

	public function testUpdateModel() {
		$fixture = 'new name';
		//
		$model = new testModel( );
		//
		$model->get( 1 );
		//
		$model->name = $fixture;
		//
		$model->update( );
		//
		$newModel = new testModel();
		$newModel->get( 1 );
		$this->assertEquals( $newModel->name->getValue(), $fixture );
	}

	public function testDeleteModel() {
		$model = new testModel( );
		$model->get( 1 );
		$model->delete( );
		//
		$model = new testModel( );
		$result = $model->get( 1 );
		//
		$this->assertEquals( $result, false );
		//
		$result = DBSimple::get( testModel::TableName, array('id' => 1));
		//
		$this->assertEquals( $result, null );
	}

	/**
	 *
	 */
	public function testGetUnknownColumn() {
		$model = new testModel();
		try {
			$model->uknownColumn;
			$this->fail('exception must raise');
		} catch (\Exception $e ) {

		}
	}

	public function testGetColumn() {
		$model = new testModel();
		$model->get( 1 );
		//
		$this->assertEquals( $model->name, self::fixtureName );
	}


	public function testSetColumn() {
		$fixtureName = 'test name2';
		//
		$model = new testModel();
		$model->get(1);
		$model->name = $fixtureName;
		//
		$this->assertEquals( $model->name, $fixtureName );
		//
		$model->update( );
		//
		$newModel = new testModel( );
		$newModel->get( 1 );
		//
		$this->assertEquals( $newModel->name, $fixtureName );
	}

	public function testGetData() {
		$model = new testModel();
		$model->get(1);
		$data = $model->getData();
		$this->assertEquals(
			$data, array(
						'id'   => 1,
						'name' => self::fixtureName
				   )
		);
	}

	public function testSetData() {
		$model = new testModel();
		$model->setData(array('id' => 2));
		$this->assertEquals($model->id->getValue(), 2);
	}

	public function testSetDataInConstructor() {
		$model = new testModel(array('id' => 2, 'name' => 'cool'));
		$this->assertEquals($model->id->getValue(), 2);
		$this->assertEquals($model->name->getValue(), 'cool');
	}
	public function testReload() {
		$fixture = 'reloaded value';
		$model = new testModel();
		$model->get( 1 );
		//
		DBSimple::update( testModel::TableName, array('name'=> $fixture ),array('id' => 1));
		//
		$model->reload();
		$this->AssertEquals( $fixture, $model->name->getValue());
	}
}
}