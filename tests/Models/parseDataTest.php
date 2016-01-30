<?php
namespace Extasy\tests\Models {
	use \Extasy\tests\BaseTest;
	class parseDataTest extends BaseTest {
		public function testParseData() {
			$fixtureData         = array(
				'name'    => 'name',
				'content' => 'my content',
			);
			$initialData         = $fixtureData;
			$initialData[ 'id' ] = 1;
			$model      = new testParseDataModel( $initialData );
			$resultData = $model->getParseData();
			$this->assertEquals( $fixtureData, $resultData );
		}


		public function testPreviewData() {
			$initalData = array(
				'id'      => 1,
				'name'    => '<h1>Test</h1>',
				'content' => 'content',
			);
			$fixture    = array(
				'name' => array(
					'value' => '<h1>Test</h1>',
					'view'  => '&lt;h1&gt;Test&lt;/h1&gt;'
				)
			);
			$model      = new testParseDataModel( $initalData );
			$resultData = $model->getPreviewParseData();
			$this->AssertEquals( $fixture, $resultData );
		}

		/**
		 * @expectedException \InvalidArgumentException
		 */
		public function testExceptionRaisedOnUnknownMethod() {
			$model      = new testParseDataModel( array() );
			$resultData = $model->getViewValueByKey(testParseDataModel::InvalidMethodName );
		}
		public function testStringMethodName() {
			$nameFixture = '<h1>Test</h1>';
			$initalData = array(
				'id'      => 1,
				'name'    => $nameFixture,
				'content' => 'content',
			);
			$fixture = array(
				'name'    => $nameFixture, // if test fails then column value will be return without html entities (htmlspecialchars)
				'content' => 'content',
			);
			$model      = new testParseDataModel( $initalData );
			$resultData = $model->getViewValueByKey(testParseDataModel::StringMethodName );
			$this->AssertEquals( $fixture , $resultData );
		}
		public function testFunctionMethodName() {

			$initalData = array(
				'content' => 'content',
			);
			$fixture = array(
				'content' => true
			);
			$model      = new testParseDataModel( $initalData );
			$resultData = $model->getViewValueByKey(testParseDataModel::FunctionMethodName );
			$this->AssertEquals( $fixture , $resultData );
			//

			$initalData = array(
				'content' => '',
			);
			$fixture = array(
				'content' => false
			);
			$model      = new testParseDataModel( $initalData );
			$resultData = $model->getViewValueByKey(testParseDataModel::FunctionMethodName );
			$this->AssertEquals( $fixture , $resultData );
		}
	}
}