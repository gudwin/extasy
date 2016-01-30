<?php
namespace Extasy\Users\Api {

	class UpdateAvatarOperation extends ApiOperation  {
		const MethodName = 'user.avatar.update';

		protected $requiredParams = array();
		protected $requiredFiles = array('avatar');
		/**
		 *
		 */
		protected  function action( ) {
			$avatar = $this->acceptFile( 'avatar' );
			$this->userSession->obj_avatar_preview->copyFrom( $avatar);
			$this->userSession->update();
			if ( file_exists(  $avatar )) {
				unlink( $avatar );
			}
			return $this->userSession->obj_avatar_preview->getViewValue();
		}
	}
}