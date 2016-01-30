<?php
namespace Extasy\Users\Api {
use \UserAccount;
	class GetUserInfoOperation extends ApiOperation  {
		const MethodName = 'user.profile.get';
		/**
		 *
		 */
		protected  function action( ) {
			$data = $this->userSession->getParseData();
			$skipFields = UserAccount::getPrivateFields();
			$result = array();
			$allowedFields = array('login','email');
			foreach ( $data as $key=>$row ) {
				if ( !in_array( $key, $skipFields) || (in_array( $key, $allowedFields ))) {
					$result [ $key ] = $row;
				}
			}
			//
			$result['last_activity_date'] = $this->userSession->last_activity_date->getCyrilicViewValue();
			$loginInfo = \Extasy\Users\login\LoginInfo::getFromSession();
			$result[ 'loginAttempts'] = $loginInfo->getViewData();
			return $result;
		}
	}
}