<?php
namespace Extasy\acl;

use \Faid\Cache\Exception as SimpleCacheException;
use \Faid\SimpleCache;
use ACLException;
use ForbiddenException;
use UsersLogin;
use UserAccount;
use \Exception;
use \CMSLog;

class ACLUser
{
    const GuestUser = 'guest';
    const CacheKey = 'guest_grants';
    const CacheLifeTime = 86400;
    protected static $rightFailed = null;

    public static function hasUserRights($aclActionList)
    {
        if (empty($aclActionList)) {
            return;
        }
        if (!is_array($aclActionList)) {
            throw new \InvalidArgumentException('Property `aclActionList` should be array type ');
        }

        $grantList = self::getCurrentUserGrants();

        return self::testGrants($aclActionList, $grantList);

    }

    protected static function testGrants($aclActionList, $grantList)
    {
        self::$rightFailed = null;
        foreach ($aclActionList as $row) {
            if (!in_array($row, $grantList)) {
                self::$rightFailed = $row;
                if (in_array(\CMSAuth::SystemAdministratorRoleName, $grantList)) {
                    return true;
                }

                return false;
            }
        }

        return true;
    }

    public static function checkCurrentUserGrants($aclActionList)
    {
        if (empty($aclActionList)) {
            return;
        }
        if (!is_array($aclActionList)) {
            throw new ACLException('Property `aclActionList` should be array type ');
        }

        $grantList = self::getCurrentUserGrants();
        $result = self::testGrants($aclActionList, $grantList);
        if (!$result) {
            $msg = "User not have enough rights to access requested action - " . self::$rightFailed;
            $fullMsg = sprintf("%s\r\nRequested grants:%s\r\nCurrent user:\r\n%s\r\n%s", $msg,
                print_r($aclActionList, true), print_r(UsersLogin::getCurrentSession(), true),
                \Faid\Debug\defaultDebugBackTrace(false));
            \Extasy\Audit\Record::add(__CLASS__, $msg, $fullMsg);
            throw new ForbiddenException($msg);
        }
    }

    public static function getCurrentUserGrants()
    {
        $result = self::loadGuestUserRights();
        //
        if (UsersLogin::isLogined()) {
            $result = array_merge($result, self::extractUserGrants(UsersLogin::getCurrentSession()));
        }

        return $result;
    }

    public static function extractUserGrants($user)
    {
        $map = $user->rights->getValue();
        $result = [];
        foreach ($map as $key => $row) {
            if (!empty($row)) {
                $result[] = $key;
            }
        }

        return $result;
    }

    public static function regenerateGuestCache($user)
    {
        $isGuest = $user->login->getValue() == self::GuestUser;

        if ($isGuest) {
            $data = self::extractUserGrants($user);
            SimpleCache::set(self::CacheKey, $data, self::CacheLifeTime);
            \CMSLog::addMessage(__CLASS__, 'Guest user permissions regenerated');
        }
    }

    protected static function loadGuestUserRights()
    {
        try {
            $actual = SimpleCache::isActual(self::CacheKey);
        } catch (SimpleCacheException $e) {
            $actual = false;
        }

        if (!$actual) {
            try {
                $user = UserAccount::getByLogin('guest');
                $result = self::extractUserGrants($user);
            } catch (Exception $e) {
                CMSLog::addMessage('acl', $e);
                $result = array();
            }
            SimpleCache::set(self::CacheKey, $result, self::CacheLifeTime);
        } else {
            $result = SimpleCache::get(self::CacheKey);
        }

        return $result;
    }
}