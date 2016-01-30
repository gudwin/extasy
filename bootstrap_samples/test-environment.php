<?php
use \Faid\Configure\Configure;
use \Extasy\CMS;

\Extasy\tests\ErrorHandlers::setUp();
\Faid\Debug\Debug::enable();


Configure::write ( CMS::SaltConfigureKey, 'extasySalt' );

Configure::write ( \Extasy\Schedule\Runner::TimeoutConfigureKey, 30 );

Configure::write(CMS::DashboardConfigureKey,
    array(
        'url' => '/',
        'Domain' => 'extasy'
    ));
Configure::write(CMS::MainDomainConfigureKey,'extasy');


// Собственно, имя проекта
define ( 'SITE_NAME', 'Extasy Framework' );
// Путь до закачиваемых пользователем файлов
define ( 'FILE_PATH', EXTASY_PATH . 'tests/data/' );

Configure::write ( \Extasy\CMS::FileConfigureKey, EXTASY_PATH. 'tests/data/' );
Configure::write ( \Extasy\CMS::FilesHttpRoot, '/tests/data/' );

Configure::write ( 'UParser.tmp_dir', FILE_PATH );


Configure::write ( 'Sitemap', [
    'Menu' => [
        'title' => 'Меню сайта',
        'depth' => 2
    ]
] );

Configure::write ( \UserAccount::ModelConfigureKey, [
    'table' => UserAccount::getTableName (),
    'api' => [
        'profileUpdateFields' => 'name,surname'
    ],
    'fields' => [
        'id' => '\\Extasy\\Columns\\Index',
        'login' => '\\Extasy\\Users\\Columns\\Login',
        'password' => '\\Extasy\\Columns\\Password',
        'rights' => '\\GrantColumn',
        'time_access' => '\\Extasy\\Users\\Columns\\TimeAccess',
        'registered' => '\\Extasy\\Columns\\Datetime',
        'last_activity_date' => '\\Extasy\\Columns\\Datetime',
        'confirmation_code' => '\\Extasy\\Users\\Columns\\ConfirmationCode',
        'email_confirmation_code' => '\\Extasy\\Columns\\Input',
        'email' => '\\Extasy\\Users\\Columns\\Email',
        'new_email' => '\\Extasy\\Columns\\Input',
        'name' => '\\Extasy\\Columns\\Input',
        'surname' => '\\Extasy\\Columns\\Input',
        'social_networks' => [
            'class' => '\\Extasy\\Users\\Columns\\SocialNetworks',
            'parse_field' => 1
        ],
        'avatar' => [
            'class' => '\\Extasy\\Columns\\Image',
            'base_dir' => 'users/',
            'images' => ''
        ]
    ]

] );

// Режим отладки
define('DEBUG', 1);



$db = array(
    'host' => 'localhost',
    'user' => 'extasy',
    'password' => '',
    'database' => 'extasy',
);
$cacheConfig = array(
    'Engine' => '\\Faid\\Cache\\Engine\\FileCache',
    'FileCache' => array(
        'BaseDir' => EXTASY_PATH . 'tests/data/'
    ),
);

Configure::write('DB', $db);
Configure::write('SimpleCache', $cacheConfig);
