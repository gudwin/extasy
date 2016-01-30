<?
use \Extasy\Audit\Record;
define('CMS_LOG_TABLE','cms_log');
class CMSLog {
	const RuntimeErrors = 'Errors.Runtime';
	static protected $instance;
	static public function addMessage($category,$szMessage) {
		Record::add( $category, $szMessage, $szMessage );
	}
	static public function getInstance() {
		if (!is_object(self::$instance)) {
			self::$instance = new CMSLog();
		}
		return self::$instance;
	}
}
class CMS_Log extends CMSLog {

}
?>