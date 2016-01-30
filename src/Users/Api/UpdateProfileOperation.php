<?php
namespace Extasy\Users\Api {
use \UserAccount;
use \Extasy\Validators\ModelConfigValidator;
	class UpdateProfileOperation extends ApiOperation  {
		const MethodName = 'user.profile.update';

		protected $requiredParams = array('userInfo');
		protected $optionalParams = array('commitAvatar');
		protected $fields = array( 'name','surname','fathers_name','city','profession');
		protected function autoloadFields() {
			$validator = new ModelConfigValidator( '\UserAccount', ['api','profileUpdateFields']);
			if ( $validator->isValid() ) {
				$this->fields = array_merge( $this->fields, explode(',', $validator->getData()));
			}
			return $this->fields;
		}
		/**
		 *
		 */
		protected  function action( ) {
			$userInfo = $this->getParam('userInfo');
			$this->autoloadFields();
			//
			if ( !empty( $userInfo) && is_array( $userInfo )) {

				foreach ( $userInfo as $key=>$row ) {
					if ( in_array( $key, $this->fields )) {
						$this->userSession->$key->setValue( $row ) ;
					}

				}
				$this->userSession->update();
			}
			//
			$commitAvatar = $this->GetParam('commitAvatar');
			if ( !empty( $commitAvatar )) {
				$value = $this->userSession->obj_avatar_preview->getValue();
				if ( !empty( $value )) {
					$this->userSession->obj_avatar->copyFrom( FILE_PATH . $value );
					$this->userSession->update();
				}

			}
			$api = new GetUserInfoOperation();
			return $api->exec();
		}

	}
}