<?php
namespace Extasy\tests\Validators {
	use \Extasy\Validators\Email;
	class EmailTest extends \Extasy\tests\BaseTest {
		public function testInvalid( ) {
			$invalidFixtures = array(
				'',
				'no',
				'no@no',
			);
			foreach ( $invalidFixtures as $fixture ) {
				$validator = new Email( $fixture );
				$this->assertFalse( $validator->isValid() );
			}

		}
		public function testValid( ) {
			$validator = new Email( 'some@email.com' );
			$this->assertTrue( $validator->isValid() );
		}

	}
}