<?php
namespace Extasy\acl {
    use \Extasy\acl\ACLUser;
	use \Exception;
	use \NotFoundException;

	class ModelHelper {
		/**
		 * @param $document
		 */
		public static function isEditable( $modelName ) {
			$permissionName = self::getPermissionName( $modelName );

			try {
				if ( !empty( $permissionName )) {
					ACLUser::checkCurrentUserGrants( array( $permissionName ));
				}
				return true;
			} catch (Exception $e ) {
				return false;
			}
		}
		protected static function getPermissionName( $modelName ) {
			if ( !is_object( $modelName )) {
				if ( !class_exists( $modelName )) {
					throw new NotFoundException("Model \"$modelName\" not found");
				}
			}
			$className = is_object( $modelName ) ? get_class( $modelName ) : $modelName;

			$callback = array( $className, 'getPermissionName');
			$result = call_user_func( $callback );
			return $result;
		}
	}
}