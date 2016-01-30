<?php
namespace Extasy\tests\system_register;

use Faid\Configure\Configure;

class Restorator {
	public static function restore( ) {
		$data = array(
			'Audit' => array(
				'notification_emails' => '',
				'maximumLogLength' => 65536
			),
			'Security' => array(
				'salt' => '123',
				'LoginAttempts' => array(
					'PerSession' => 5,
					'PerHost' => 10
				),
                'DDosDetector' => [
                    'MaxConnections' => 1000,
                    'Message' => '',
                    'Period' => '1 minute'
                ]
			),
			'Schedule' => array(
				'runningFlag' => '0',
			),
			'Sitemap' => array(
				'visible' => 'false'
			),
			'email' => array(
				'use_standart_mail_function' => 0,
				'enable_ssl' => 0,
				'smtp_server' => 0,
				'smtp_port' => '',
				'smtp_user' => '',
				'smtp_password' => '',
				'from_email' => '',
				'from_name' => '',
			),
			'Front-end' => array(
				'enable_debug' => true,
				'pack' => 0,
                'technical_message' => '',
                'need_cms_auth' => false
			)
		);
		self::restorePath( '/System/', $data );
		$applicationImport = array(
			'users' => array(
				'captcha_provider' => 'none',
				'registration_need_email' => 1,
				'front-end' => [
					'account_confirmation' => 1,
					'account_registration_success_email' => 0,
				]
			)
		);

		self::restorePath('/Applications/', $applicationImport);

		self::restoreImageColumnConfig( );

		$usersConfig = array(
			'table' => \UserAccount::TableName,
			'fields' =>array(
				'id' => '\\Extasy\\Columns\\Index',
				'login' => '\\Extasy\\Users\\Columns\\Login',
				'email' => '\\Extasy\\Users\\Columns\\Email',
				'new_email' => '\\Extasy\\Columns\\Input',
				'name' => '\\Extasy\\Columns\\Input',
				'avatar' => array(
					'class' => '\\Extasy\\Columns\\Image',
					'base_dir' => 'users/',
					'images' => '',
				),
				'password' => '\\Extasy\\Columns\\Password',
				'rights' => '\\GrantColumn',
				'last_activity_date' => '\\Extasy\\Columns\\Datetime',
				'confirmation_code' => '\\Extasy\\Users\\Columns\\ConfirmationCode',
				'email_confirmation_code' => '\\Extasy\\Columns\\Input',
				'time_access' => '\\Extasy\\Users\\Columns\\TimeAccess',
				'social_networks' => '\\Extasy\\Users\\Columns\\SocialNetworks',
			)
		);
		Configure::write( \UserAccount::ModelConfigureKey, $usersConfig);

		$data = array(
			'cconfig' => array(
				'user_control_path' => array()
			),
		);
		self::restorePath( '/Applications/', $data );
		\SystemRegisterSample::createCache();
	}
	public static function restorePath( $path, $data ) {
		try {
			$register = new \SystemRegister( $path );
			\SystemRegisterHelper::import($register, $data );
		} catch (\Exception $e ) {

		}
	}
	protected static function restoreImageColumnConfig( ) {
		try {
			$data = array(
				'columns' => array(
					'image' => array(
						'max_size' => 10 * 1024 * 1024
					)
				),
				'tag' => array(
					'cacheLifeTime' => '3600',
					'cacheKey' => 'system_columns_tag',
					'tag_table' => 'tags',
					'cross_table' => 'document2tag'
				)
			);
			$register = new \SystemRegister( '/System/');
			\SystemRegisterHelper::import($register, $data );
		} catch (\Exception $e ) {

		}
	}
}