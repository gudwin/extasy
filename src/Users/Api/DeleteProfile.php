<?php
namespace Extasy\Users\Api {
	use \Email_Controller;
	use \CConfig;

	class DeleteProfile extends \Extasy\Users\Api\ApiOperation {
		const MethodName = 'user.deleteProfile';

		const SchemaName = 'user.deleteProfileEmail';

		protected $requiredParams = array();

		/**
		 *
		 */
		protected function action() {
			// $this->userSession->confirmation_code = md5( time() - rand( 0, 1000 ) );
			$this->sendEmail();
			return array(
				'status' => true
			);
		}

		protected function sendEmail() {
			$schema = CConfig::getSchema( self::SchemaName );
			$values = $schema->getValues();
			try {
				$parseData = $this->userSession->getParseData();
				$parseData['password'] = $this->userSession->password->getValue();
				Email_Controller::parseAndSend(
								$this->userSession->email->GetValue(),
								$values[ 'subject' ],
								$values[ 'content' ],
								$parseData
				);
			}
			catch ( \MailException $e ) {

			}

		}
	}
}