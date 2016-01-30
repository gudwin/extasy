<?php
require '../../../../_cfg/all_project.cfg.php';
define('TPL_PATH',SYS_ROOT.'_tpl/');
define('CFG_PATH',SYS_ROOT.'../../../../_cfg/');

require_once('../../../../_cfg/menu.php');

require_once(KERNEL_PATH.'services/project.class.php');

$Application->load(array('parser','trace','db','auth','data','filesystem','exportjs'),'class');

$Application->load(array('image','cms'),'class');


// Config
$Application->load(array('auth'),'cfg');
// ������
$Application->load(array('loader'),'mod');
// Librarys

$db->connect();
$isAuthorized = $auth->check();
if ( !$isAuthorized ) {
    die;
}
$db->post('SET NAMES `utf8`');
?>