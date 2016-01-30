<?php
namespace Extasy\tests\Users\Social\Api\Facebook {
	use \Extasy\Users\Social\FacebookApiFactory;
	use \Extasy\Users\Social\Api\Facebook\GetCurrentSession;
	class GetCurrentSessionTest extends BaseTest {


		public function testNotLogined() {
			$this->api->setException( new \ForbiddenException());
			try {
				$api = new GetCurrentSession();
				$api->Exec();

				$this->fail('Exception must be thrown');
			} catch (\Exception $e ) {
			}

		}
		public function testLogined() {
			$fixture = [
				'id' => 1,
				'name' => 'my name'
			];
			$this->api->setResult( $fixture );
			$api = new GetCurrentSession();
			$result = $api->Exec();
			$this->assertEquals( $result, $fixture );
		}


	}
}