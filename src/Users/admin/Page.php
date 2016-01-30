<?php
namespace Extasy\Users\admin;

use \UserAccount;

class Page extends \AdminPage {
	protected $aclActionList = array(
		UserAccount::PermissionName
	);
}