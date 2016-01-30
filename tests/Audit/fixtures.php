<?php
namespace Extasy\tests\Audit;

use Faid\DBSimple;
use \Extasy\tests\Helper as TestsHelper;
use \UserAccount;
use \ACL;
use \Extasy\Audit\Api\ApiOperation;
use \Extasy\Audit\Record;
use \Extasy\Audit\Log;
use \Extasy\tests\system_register\Restorator;
use \Extasy\Columns\Password as passwordColumn;
$register = new \SystemRegister( '/System/' );
try {
	$register->delete( 'Audit' );
}
catch ( \Exception $e ) {
}

Restorator::restore();

\SystemRegisterSample::createCache();

TestsHelper::dbFixture(
		   ACL_TABLE,
		   array()
);
ACL::create( ApiOperation::RightName );

// user record
TestsHelper::dbFixture(
		   USERS_TABLE,
		   array(
			   array( 'login' => 'login', 'password' => passwordColumn::hash( 'testtest' ) ),
			   array( 'login' => 'guest', 'password' => passwordColumn::hash( 'testtest' ) )
		   )
);

// grant user permission
$user = UserAccount::getByLogin( 'login' );
ACL::grant( ApiOperation::RightName, $user->obj_rights->getEntity() );

\UsersLogin::login( 'login', 'testtest' );
// base logs
TestsHelper::dbFixture(
		   Log::getTableName(),
		   array(
			   array( 'name' => 'Log1', 'critical' => 0, 'enable_logging' => 1 ),
			   array( 'name' => 'Log2', 'critical' => 1, 'enable_logging' => 1 ),
		   )
);
// base records
// - [different by user_id]
// - [different by date]
// - [different by content]
TestsHelper::dbFixture(
		   Record::getTableName(),
		   array(
			   array( 'log_id'     => 1,
					  'date'       => '2001-01-01 00:00:00',
					  'short'      => 'short log',
					  'full'       => 'full_log',
					  'user_id'    => 1,
					  'user_login' => 'login'
			   ),
			   array( 'log_id'     => 2,
					  'date'       => '2001-01-02 00:00:00',
					  'short'      => 'short log',
					  'full'       => 'full_log',
					  'user_id'    => 1,
					  'user_login' => 'login'
			   ),
			   array( 'log_id' => 1, 'date' => '2001-01-03 00:00:00', 'short' => 'short log', 'full' => 'full_log' ),
		   )
);

// Create custom config if it exists
$schemaName = 'Audit.CriticalEventName';
try {
	$config = \CConfig::getSchema( $schemaName );
	$config->delete();
}
catch ( \Exception $e ) {
}
finally {
	$config = \CConfig::createSchema( $schemaName );
	$config->addControl( 'to', 'inputfield', 'Получатели письма', array(), 'dmitry@dd-team.org' );
	$config->addControl( 'subject', 'inputfield', 'Тема письма', array(), 'Email Subject' );
	$config->addControl( 'content', 'htmlfield', 'Шаблон письма', array(), 'Message body' );
	$config->updateSchema( $schemaName, 'Шаблон письма-оповещения о наступлении критического события аудита' );
}
