<?php

namespace Extasy\tests\Schedule;

use \Extasy\Schedule\Job;
use \Extasy\Schedule\Runner;
use \Extasy\tests\Helper;
use Extasy\tests\system_register\Restorator;
use \Faid\Configure\Configure;

abstract class BaseTest extends \Extasy\tests\BaseTest
{
    const Login = 'root';
    const Password = 'a1234567!';

    public function setUp()
    {
        parent::setUp();
        Helper::dbFixture(Job::TableName, array());
        $this->setRunnerTimeout(0);
        Restorator::restore();
        Helper::setupUsers([
            ['login' => self::Login, 'password' => self::Password],
            ['login' => 'guest', 'password' => self::Password]
        ]);


        $user = \UserAccount::getByLogin(self::Login);
        \ACL::create(\CMSAuth::SystemAdministratorRoleName);
        \ACL::grant(\CMSAuth::SystemAdministratorRoleName, $user->rights->getEntity());

        \UsersLogin::forceLogin($user);
        TestAction::setUp();
    }

    protected function setRunnerTimeout($value)
    {
        Configure::write(Runner::TimeoutConfigureKey, $value);
    }
} 