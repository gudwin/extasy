<?php
namespace Extasy\Users\admin;

use UserAccount;
class ConfigPage extends \CConfigAdminEditPage {
	protected $aclActionList = array(
		UserAccount::PermissionName
	);
	protected $schemaName = '';
	public function __construct() {
		$_REQUEST[ 'schema' ] = $_POST[ 'schema' ] = $_GET[ 'schema' ] = $this->schemaName;
		return parent::__construct();
	}
}