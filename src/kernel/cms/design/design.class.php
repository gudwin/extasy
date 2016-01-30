<?php
require_once LIB_PATH . 'kernel/cms/design/plugins/layout.php';
require_once LIB_PATH . 'kernel/cms/design/plugins/popup.php';
require_once LIB_PATH . 'kernel/cms/design/plugins/forms.php';
require_once LIB_PATH . 'kernel/cms/design/plugins/tabs.php';
require_once LIB_PATH . 'kernel/cms/design/plugins/table.php';
require_once LIB_PATH . 'kernel/cms/design/plugins/text.php';
require_once LIB_PATH . 'kernel/cms/design/plugins/decor.php';
require_once LIB_PATH . 'kernel/cms/design/plugins/messages.php';
require_once LIB_PATH . 'kernel/cms/design/plugins/links.php';

/**
 * @todo Пересобрать для ускорения сборки страниц админки
 * @author Gisma
 *
 */
class CMSDesign {
	/**
	 * 
	 * @var CMSDesignLayout
	 */
	public $layout = null;
	/**
	 * 
	 * @var CMSDesignLinks
	 */
	public $links = null;
	/**
	 * @var CMSDesignDecor
	 */
	public $decor = null;
	/**
	 * @var CMSDesignTabs
	 */
	public $tabs = null;
	/**
	 * @var CMSDesignText
	 */
	public $text = null;
	/**
	 * @var CMSDesignTable
	 */
	public $table = null;
	/**
	 * @var CMSDesignForms
	 */
	public $forms = null;
	/**
	 * @var CMSDesignMessages
	 */
	public $messages = null;
	/** 
	 * @var CMSDesignPopup
	 */
	public $popup = null;

	protected function __construct() {
		$this->layout = new CMSDesignLayout();
		$this->links = new CMSDesignLinks();
		$this->decor = new CMSDesignDecor();
		$this->tabs = new CMSDesignTabs();
		$this->text = new CMSDesignText();
		$this->table = new CMSDesignTable();
		$this->forms = new CMSDesignForms();
		$this->messages = new CMSDesignMessages();
		$this->popup = new CMSDesignPopup();
	}
	
	
	/**
	 * Поддержка старых методов. Создан для обратной совместимости
	 * @param string $method
	 * @param mixed $arguments
	 */
	public function __call($method,$arguments) {
		$map = array(
			'begin' => array($this->layout,'begin'),
			'end' => array($this->layout,'end'),
			'setsubmitonkey' => array($this->forms,'setSubmitOnKey'),
			'hidden' => array($this->forms,'hidden'),
			'documentbegin' => array($this->layout,'documentBegin'),
			'documentend' => array($this->layout,'documentEnd'),
			'leftmenu' => array($this->layout,'leftMenu'),
			'error' => array($this->messages,'error'),
			'message' => array($this->messages,'message'),
			'header' => array($this->text,'header'),
			'header2' => array($this->text,'header2'),
			'formbegin' => array($this->forms,'begin'),
			'formend' => array($this->forms,'end'),
			'tablebegin' => array($this->table,'begin'),
			'tableend' => array($this->table,'end'),
			'tableheader' => array($this->table,'header'),
			'tablehr' => array($this->table,'hr'),
			'rowbegin' => array($this->table,'rowBegin'),
			'rowend' => array($this->table,'rowEnd'),
			'cellbegin' => array($this->table,'cellBegin'),
			'cellend' => array($this->table,'cellEnd'),
			'listcell' => array($this->table,'listCell'),
			'editcell' => array($this->table,'editCell'),
			'viewcell' => array($this->table,'viewCell'),
			'deletecell' => array($this->table,'deleteCell'),
			'geteditlink' => array($this->links,'editLink'),
			'getdeletelink' => array($this->links,'deleteLink'),
			'row2cell' => array($this->table,'row2cell'),
			'fullrow' => array($this->table,'fullRow'),
			'buttons' => array($this->decor,'buttons'),
			'br' => array($this->decor,'br'),
			'moresubmits' => array($this->forms,'moreSubmits'),
			'submit' => array($this->forms,'submit'),
			'popupbegin' => array($this->popup,'begin'),
			'popupend' => array($this->popup,'end'),
			'popupheader' => array($this->popup,'header'),
			'tabsheetbegin' => array($this->tabs,'sheetsBegin'),
			'tabcontentbegin' => array($this->tabs,'contentBegin'),
			'tabcontentend' => array($this->tabs,'contentEnd'),
			'tabsheetend' => array($this->tabs,'sheetsEnd'),
			'contentbegin' => array($this->decor,'contentBegin'),
			'contentend' => array($this->decor,'contentEnd'),
			'paging' => array($this->decor,'paging'),
			'createpopuplink' => array($this->links,'popupLink'),
			
		);
		$method = strtolower($method);
		if (isset($map[$method])) {
			call_user_func_array($map[$method],$arguments);
		} else {
			throw new Exception('CMSDesign method `'.$method.'` not found!');
		}
	}
	/**
	 * Возвращает объект дизайна 
	 * @return CMSDesign
	 */
	public static function getInstance() {
		static $instance = null;
		if (is_null($instance))
		{
			$instance = new CMSDesign();
		}
		return $instance;
	}
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $scriptUrl
	 */
	public static function insertScript( $scriptUrl ) {
		?>
		
	<script type="text/javascript" src="<? print htmlspecialchars( $scriptUrl );?>"></script>
			
		<?php 	
	}
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $cssUrl
	 */
	public static function insertCSS ( $cssUrl ) {
		?>

<link rel="stylesheet" type="text/css" href="<? print htmlspecialchars( $cssUrl )?>" />
		
		<?php 
	}

}

?>