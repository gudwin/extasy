<?php
class AdminTemplateListPage extends AdminTemplatePage {
	public function __construct($aTemplates,$aBegin) {
		if (empty($_GET['template'])) {
		}
		$template = $_GET['template'];
		if (!empty($aTemplates[$template])) {
			$szTitle = $aTemplates[$template]['title'];
			$szTemplatePath = $aTemplates[$template]['path'];
			$aBegin[$szTitle] = '#';

		} else {
			throw new Exception('Template info not found');
		}
		parent::__construct($szTemplatePath,$szTitle,$aBegin);
	}
}
?>