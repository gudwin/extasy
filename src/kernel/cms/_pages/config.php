<?
require_once CLASS_PATH . 'config/config.class.php';
class AdminConfig extends AdminPage {
	protected $config = null;
	protected $path = '';
	protected $aBegin = array();
	protected $szTitle = '';
	protected $aButton = array();
	function __construct($aBegin,$szTitle,$szConfigPath,$aButton = array()) {
		parent::__construct();

		$this->addPost('submit','post');
		$this->config = new Config();

		$this->path = $szConfigPath;
		$aResultBegin = array();
		foreach ($aBegin as $key => $row) {
			$aResultBegin[_msg($key)] = $row;
		}
		$this->aBegin = $aResultBegin;
		$this->szTitle = _msg($szTitle);
		$this->aButton = $aButton;
	}
	public function main() {
		$design = CMSDesign::getInstance();
		$strings = CMS_Strings::getInstance();
		$aData = $this->config->load($this->path);
		$aGenerated = $this->config->generate($aData);
		$aBegin = $this->aBegin;
		$szTitle = $this->szTitle;
		$design->begin($aBegin,$szTitle);
		$design->documentBegin();
			$design->header($szTitle);
			if (!empty($this->aButton))
			{
				$design->buttons($this->aButton);
			}
			$this->outputComment();
			$design->formBegin();
			$design->submit('submit',$strings->getMessage('APPLY'));
			$design->tableBegin();
			foreach ($aGenerated as $key=>$row) {
				$design->row2cell($aData[$key]['comment'],$row);
			}

			$design->tableEnd();
			$design->submit('submit',$strings->getMessage('APPLY'));
			$design->formEnd();
		$design->documentEnd();
		$design->End();
		$this->output();
	}
	public function post() {
		$this->config->save($this->path,$_POST);
		$this->afterPost();
		$this->jump($_SERVER['REQUEST_URI']);
	}
	protected function afterPost()
	{

	}
	/**
	*   -------------------------------------------------------------------------------------------
	*   @desc Функция для вывода комментариев по конфигу
	*   @return
	*   -------------------------------------------------------------------------------------------
	*/
	protected function outputComment() {

	}

}
?>
