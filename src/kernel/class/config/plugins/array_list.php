<?
use \Faid\UParser;
class Config_Array_List implements ExtConfigurable {
	protected $szName = '';
	protected $szContent = '';
	protected $aTableHeader= '';
	protected $szAdditional = '';
	public function setConfigData($szName,$szContent,$szAdditional = '',$szComment = '') {
		$this->szName = $szName;
		$this->szContent = $szContent;
		$this->aTableHeader = explode(';',$szAdditional);
		$this->szComment = str_replace("\r",'',$szComment);

	}
	public function getControl() {
		$strings = CMS_Strings::getInstance();
		$aParse = array(
			'lang'         => $strings->language,
			'aTableHeader' => $this->aTableHeader,
			'aData'        => unserialize($this->szContent),
			'szName'       => $this->szName,
			'szComment'    => $this->szComment,
			);
		return UParser::parsePHPFile(CONFIG_PATH.'plugins/array_list/form.tpl',$aParse);
	}
	public function toString($aValue) {
		return serialize($aValue);
	}
}
?>