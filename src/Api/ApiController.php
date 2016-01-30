<?php
namespace Extasy\Api {
	use \Extasy\tests\Api\TestApiOperation;
	use \Extasy\tests\Api\TestApiOperationWithParams;
	use \Extasy\tests\Api\TestApiWithACLOperation;
	use \SiteMapController;

	class ApiController extends SiteMapController  {

		protected static $instance = NULL;

		protected $operationList = array();

		protected $responseType = NULL;

		protected $data = array();

		protected $jsonpCallback = '';

		protected $methodName = '';

		public function __construct( ) {
			parent::__construct( );
			self::$instance = $this;
			$this->addGet('testJS', 'testJSSide');
		}

		public function main() {
			$result = NULL;
			try {
				// fetch information about request
				$this->fetchInformation();
				// call
				$this->loadApiOperations();

				//
				$result = $this->findAndCallActiveOperation();
			} catch (\Exception $e) {
				$result = array(
					'error' => $e->getMessage()
				);
			}

			print $this->outputResponse($result);

		}

		public function testJSSIde() {
			$this->output('api/testjs');
		}

		public static function getInstance() {
			return static::$instance;
		}

		public function add($operation) {
			$this->operationList[ ] = $operation;
		}

		protected function loadApiOperations() {

			\EventController::callEvent(ApiOperation::EventName);

			$this->add( new TestApiOperation());
			$this->add( new TestApiOperationWithParams());
			$this->add( new TestApiWithACLOperation());


		}

		protected function findAndCallActiveOperation() {
			//
			$found = false;
			foreach ($this->operationList as $operation) {
				if ( $operation->match($this->methodName) ) {
					$found = true;
					$operation->setParamsData($this->data);
					$result = $operation->exec();
					break;
				}
			}

			if ( empty($found) ) {
				$error = sprintf('Unknown method: %s', $this->methodName);
				throw new Exception($error);
			}

			return $result;
		}

		/**
		 *
		 */
		protected function fetchInformation() {
			if ( !empty($_REQUEST[ 'data' ]) ) {
				$this->data = $_REQUEST[ 'data' ];
			}
			if ( !empty($_REQUEST[ 'method' ]) ) {
				$this->methodName = $_REQUEST[ 'method' ];
			}
			if ( !empty($_REQUEST[ 'response_method' ]) ) {
				$this->responseType = $_REQUEST[ 'response_method' ];
				if ( ApiOperation::responseJSONP == $this->responseType ) {
					if ( !empty($_REQUEST[ 'callback' ]) ) {
						$this->jsonpCallback = $_REQUEST[ 'callback' ];
					} else {
						$this->responseType = 'json';
						throw new Exception('Missing parameter: callback');
					}
				}
			}

		}

		protected function outputResponse($data) {

			switch ($this->responseType) {
				case ApiOperation::responseJSON:
					$result = json_encode($data);
					break;
				case ApiOperation::responseJSONP:
					$json   = json_encode($data);
					$result = sprintf('%s(%s);', $this->jsonpCallback, $json);
					break;
				default:
					$result = json_encode($data);
					break;
			}

			return $result;
		}
	}
}