<?php
namespace Extasy\tests\Users\Social\Api\Facebook {
	class TestFacebookApi {
		/**
		 * @var \Exception
		 */
		protected $exception = null;
		protected $result = [];
		public function getCurrentSession() {
			if ( !empty( $this->exception ) )  {
				throw $this->exception;
			};
			return $this->result;

		}
		public function setResult( $value ) {
			$this->result = $value;
		}
		public function setException( $e ) {
			$this->exception = $e;
		}
	}
}