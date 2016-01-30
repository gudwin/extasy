<?php
namespace Extasy\Validators {

	class ModelConfigValidator extends BaseValidator {
		protected $modelName = null;
		protected $subkey = null;
		protected $documentInfo = array();
		protected $data = null;

		public function __construct( $modelName, $subkey ) {
			$this->modelName = $modelName;
			$this->subkey    = $subkey;
		}

		protected function test() {

			if ( !class_exists( $this->modelName ) ) {
				return false;
			}
			$this->documentInfo = call_user_func( array( $this->modelName, 'getFieldsInfo' ) );

			return $this->testRecursively( $this->documentInfo, $this->subkey );
		}
		protected function testRecursively( $config,$path ) {
			if ( is_array( $path )) {
				$subkey = array_shift( $path );
			} else {
				$subkey = $path;
				$path = null;
			}

			$result = isset( $config[ $subkey ] );
			if ( $result ) {
				$this->data = $config[ $subkey ];
			}
			if ( !empty( $path )) {
				$result = $result && $this->testRecursively( $config[ $subkey ], $path );
			}


			return $result;
		}
		public function getData() {
			if ( $this->isValid() ) {
				return $this->data;
			} else {
				return false;
			}
		}
	}
}
