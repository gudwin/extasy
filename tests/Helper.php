<?php
namespace Extasy\tests;

use \ForbiddenException;
use \Extasy\Schedule\Job;
use \Extasy\Columns\Password;

class Helper
{
    const DefaultPassword = 'a123456!';

    public static function dbFixture($tableName, $data)
    {
        $sql = sprintf(' TRUNCATE `%s` ', $tableName);
        \Faid\DB::post($sql);
        //
        foreach ($data as $row) {
            \Faid\DBSimple::insert($tableName, $row);
        }
    }

    public static function dbFixtures($map)
    {
        foreach ($map as $tableName => $data) {
            self::dbFixture($tableName, $data);
        }
    }

    public static function cleanAudit()
    {
        self::dbFixture(\Extasy\Audit\Log::tableName, array());
        self::dbFixture(\Extasy\Audit\Record::tableName, array());
    }

    public static function cleanACL()
    {
        self::dbFixture(ACL_GRANT_TABLE, array());
        self::dbFixture(ACL_TABLE, array());
        \ACL::create(\CMSAuth::AdministratorRoleName);
    }

    public static function cleanSchedule()
    {
        self::dbFixture(Job::getTableName(), array());
    }

    public static function setupUsers($data)
    {
        self::dbFixture(\UserAccount::getTableName(), array());
        foreach ($data as $key => $userRow) {
            $userRights = null;
            if ( !isset( $userRow['password'])) {
                $userRow['password'] = self::DefaultPassword;
            }
            self::checkUserRow($userRow);
            if (isset($userRow['rights'])) {
                $userRights = $userRow['rights'];
            }

            $userRow['password'] = Password::hash($userRow ['password']);
            $user = new \UserAccount($userRow);

            $user->insert();
            if (!empty($userRights)) {
                $user->rights = $userRights;

                $user->update();
            }
        }

    }

    protected static function checkUserRow($userRow)
    {
        if (empty($userRow['login'])) {
            throw new ForbiddenException('Login not defined');
        }
    }
}