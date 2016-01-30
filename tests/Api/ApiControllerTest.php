<?php
namespace Extasy\tests\Api;

use Extasy\Api\ApiOperation;

class ApiControllerTest extends \PHPUnit_Framework_TestCase {

	/**
	 *
	 */
	const InternalErrorMethodName = 'Api.Internal';

	const MissingHelloParameterFixture = 'Missing parameter: msg';
	const MissingCallbackFixture = 'Missing parameter: callback';

	const HelloFixture = 'Hello world!';

	/**
	 *
	 */
	const HelloMethodname = 'Test.Hello';
	const HelloWithParamsMethodname = 'Test.HelloWithParams';

	public function testUnknownApi( ) {
		$helper = new ApiTestHelper( );
		$result = $helper->json(array(
			'method' => ApiOperationTest::WrongMethodName,
			'data' => array( )
		) );
        $this->assertArrayHasKey( 'error', $result );
		$this->assertEquals( 'Unknown method: ' . ApiOperationTest::WrongMethodName, $result['error']);

	}
	public function testApiWithInternal( ) {
		$helper = new ApiTestHelper();
		$result = $helper->json(array(
													   'method' => self::InternalErrorMethodName,
													   'data' => array( )
												  ) );
	}

	/**
	 * @group testMissedRequiredParams
	 */
	public function testMissedRequiredParams( ) {
		$helper = new ApiTestHelper();
		$result = $helper->json(array(
													   'method' => self::HelloWithParamsMethodname,
													   'data' => array( )
												  ) );
		$this->assertArrayHasKey( 'error', $result );
		$this->assertEquals( self::MissingHelloParameterFixture, $result['error'] );

	}
	public function testApi( ) {
		$helper = new ApiTestHelper( );
		$result = $helper->json(array(
													   'method' => self::HelloWithParamsMethodname,
													   'data' => array(
														   'msg' => self::HelloFixture
													    )
												  ) );
		$this->assertEquals( array('msg'=>self::HelloFixture), $result );
	}
	public function testJSONPWithoutCallbackParam( ) {
		$helper = new ApiTestHelper();
		$response  = $helper->makeRequest(array(
										  'method' => self::HelloMethodname,
										  'data' => array(),
										  'response_method' => ApiOperation::responseJSONP
									 ) );

		$result = json_decode( $response->response, true);
		$this->assertArrayHasKey( 'error', $result );
		$this->assertEquals( self::MissingCallbackFixture, $result['error'] );
	}
	public function testApiReturnsJSONP( ) {
		$fixture = 'test';
		$responseFixture = 'test('.json_encode( self::HelloFixture ).');';
		$helper = new ApiTestHelper();
		$response = $helper->makeRequest(array(
													   'method' => self::HelloMethodname,
													   'data' => array(),
													   'callback' => $fixture,
													   'response_method' => ApiOperation::responseJSONP
												  ) );
		$this->assertEquals( $responseFixture, $response->response );
	}


}