<?php

namespace Extasy\Schedule\Api;


class BaseScheduleApi extends \Extasy\Users\Api\ApiOperation {
	protected $requiredACLRights = [\CMSAuth::SystemAdministratorRoleName ];

} 