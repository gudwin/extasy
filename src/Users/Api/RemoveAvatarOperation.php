<?php
namespace Extasy\Users\Api {

	class RemoveAvatarOperation extends ApiOperation  {
		const MethodName = 'user.avatar.remove';
		/**
		 *
		 */
		protected  function action( ) {
			//
			$this->userSession->avatar->setValue(\UserAccount::DefaultAvatarPath);
			$this->userSession->update();
			//
			return $this->userSession->avatar->getViewValue();
		}
	}
}