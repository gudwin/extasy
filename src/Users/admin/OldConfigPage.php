<?php
namespace Extasy\Users\admin;

class OldConfigPage extends \AdminConfig {
	protected $aclActionList = array(
		\UserAccount::PermissionName
	);
}