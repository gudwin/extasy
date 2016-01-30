<?
use \Faid\UParser;

class CHTMLArea extends CControl {
	protected $szContent;
	protected $required = false;
	protected $title = false;
	public function __set($name,$value) {
		if ($name == 'name') {
			$this->szName = htmlspecialchars($value);
		}

		if (($name == 'content') || ($name == 'html') || ($name == 'value')) {
			$this->szContent  = $value;
		}
		if ( $name == 'required') {
			$this->required = $value;
		}
		if ( $name == 'title') {
			$this->title = $value;
		}

	}

	function postHTMLArea() {
		if (empty($_REQUEST['id'])) {
			trigger_error('Не найдены все необходимые параметры',E_USER_ERROR);
		}
		if (!isset($_REQUEST['content'])) {
			trigger_error('Не найдены все необходимые параметры',E_USER_ERROR);
		}
		// выводим скрипт который закроет попап и сохранит контент
		$id = htmlspecialchars($_REQUEST['id']);

		$content = json_encode($_REQUEST['content']);
		
		$parseData = array(
			'id' => $id,
			'content' => $content,
		);
		print UParser::parsePHPFile(CONTROL_PATH.'tpl/htmlarea_saving.tpl',$parseData);
	}
	function showHTMLArea() {
		
		if (empty($_REQUEST['textarea'])) {
			trigger_error('Не найдены все необходимые параметры',E_USER_ERROR);
		}
		$id = htmlspecialchars($_REQUEST['textarea']);
		$cssPath = isset($_REQUEST['cssPath'])?$_REQUEST['cssPath']:'';
		$parseData = array(
			'id' => $id,
			'cssPath' => $cssPath
		);
		print UParser::parsePHPFile(CONTROL_PATH.'tpl/htmlarea.tpl',$parseData);

	}
	public function generate()
	{
		$parseData = array(
			'name' => $this->szName,
			'content' => $this->szContent,
			'title' => !empty( $this->title ) ? $this->title : $this->szName,
			'required' => $this->required
			);
			
		return UParser::parsePHPFile(CONTROL_PATH.'tpl/htmlarea-light.tpl',$parseData);
	}
}
?>