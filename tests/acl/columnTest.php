<?php
namespace Extasy\tests\acl {
	use \CDumper;
	use \ACL;


	class columnTest extends  \Extasy\tests\BaseTest {
		const TextFixture = 'Hello world!';
		const RightNameFixture = 'TestRight';
		const SecondRightNameFixture = 'TestRight/RightName';
		public function setUp( ) {
			parent::setUp();
			include __DIR__ . DIRECTORY_SEPARATOR . 'import.php';

			ACL::create( self::RightNameFixture, 'Test right name' );
			ACL::create( self::SecondRightNameFixture, 'Second right name');
		}
		public function tearDown( ) {
			$tableName = TestModel::TableName;
			$sql = <<<SQL
	DROP TABLE IF EXISTS {$tableName};

	CREATE TABLE {$tableName} (
		id int not null auto_increment,
		primary key (`id`)
	)
SQL;
			$dumper = new CDumper();
			$dumper->import( $sql );
		}

		public function testMyCodeAddedOnAdminFormValue( ) {
			$fixture = md5( time( ));
			$doc = new TestModel(array());
			$doc->grants->onAdminFormValue( self::RightNameFixture, $fixture );
			$formValue = $doc->grants->getAdminFormValue( );
			$match = strpos( $formValue, $fixture ) !== FALSE;
			$this->assertTrue( $match );
		}
		public function testHierarchyRightNamesSupported( ) {
			$fixture = md5( time( ));
			$doc = new TestModel(array());
			$doc->grants->onAdminFormValue( self::SecondRightNameFixture, $fixture );
			$formValue = $doc->grants->getAdminFormValue( );
			$match = strpos( $formValue, $fixture ) !== FALSE;
			$this->assertTrue( $match );
		}
	}
}