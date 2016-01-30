<?
class adminTemplatePage extends extasyPage{
	protected $tplFile = '';
	protected $title = '';
	protected $aBegin = array();
	protected $aButton = array();
	public function __construct($tplFile,$szTitle,$aBegin = array(),$aButton = array()) {
		parent::__construct();
		$this->tplFile = $tplFile;
		$this->title = $szTitle;
		$this->aBegin = $aBegin;
		$this->aButton = $aButton;
		$this->addPost('file,content','post');
	}
	public function main() {
		$design = CMSDesign::getInstance();
		$FileContent = htmlspecialchars(file_get_contents($this->tplFile));
		$date = date('Y-m-d H:i',filemtime($this->tplFile));
		$design->begin($this->aBegin);
		$design->documentBegin();
		if (!empty($this->aButton))
		{
			$design->buttons($this->aButton);
		}
		$design->formBegin();
		$design->header(_msg('Редактировать шаблон').' :'.$this->title.' <br/> <span style="color:#AAAAFF">'._msg('FILE_TEMPLATE_DATE_MODIFIED').' : '.$date.'</span>');
		$design->submit('submit',_msg('Применить'));
		$design->br();
		$this->outputComment();
		$design->tableBegin();
		?>
		<tr>
			<td width="80%" valign="top">
				<textarea style="width:100%;align:center; height:500px;" name="content"><?=$FileContent?></textarea>
			</td>
		</tr>
		<?
		$design->tableEnd();
		$design->br();

		$design->submit('submit',_msg('Применить'));
		$design->formEnd();
		$design->documentEnd();
		$design->end();
		$this->output();
	}
	public function post($file,$content) {
		file_put_contents($this->tplFile,$content);
		$this->jump($_SERVER['REQUEST_URI']);
	}
	/*
	* @desc Переопределите этот метод, в дочерних классах, чтобы выводить комментаррии к шаблону
	*/
	protected function outputComment() {
		
	}
}
?>