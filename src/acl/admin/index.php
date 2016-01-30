<?php
use \Extasy\CMS;
class ACLAdminManageActions extends AdminPage {
	public function __construct() {
		parent::__construct();
		$this->addPost('node','getData');
		$this->addPost('method,path,title','call');
		$this->addPost('method,path','call');
	}
	public function main() {
		$design = CMSDesign::getInstance();
		$scripts = array(
			CMS::getResourcesUrl().'extasy/ext3/ux/treegrid/TreeGridSorter.js',
			CMS::getResourcesUrl()."extasy/ext3/ux/treegrid/TreeGridColumnResizer.js",
			CMS::getResourcesUrl()."extasy/ext3/ux/treegrid/TreeGridNodeUI.js",
			CMS::getResourcesUrl()."extasy/ext3/ux/treegrid/TreeGridLoader.js",
			CMS::getResourcesUrl()."extasy/ext3/ux/treegrid/TreeGridColumns.js",
			CMS::getResourcesUrl()."extasy/ext3/ux/treegrid/TreeGrid.js",
			CMS::getResourcesUrl().'extasy/Dashboard/administrate/acl.js'
			);
		$title = 'Редактирование списка прав';
		$begin = array($title => '#');
		$this->outputHeader($begin, $title,$scripts);
		?>
		<div id="actionLayout"></div>
		<?php 
		$this->outputFooter();
		$this->output();
	}
	public function getData() {
		$data = ACLMisc::export();
		$data = $this->appendData($data);
		print json_encode($data);
		die();
	}
	protected function appendData($aData)
	{
		foreach ($aData as $key=>$row)
		{
			if (!empty($row['children'])) {

				$aData[$key]['children'] = $this->appendData($row['children']);
			}
			else
			{
				$aData[$key]['leaf'] = true;
			}
		}
		return $aData;
	}
	public function call($method,$path,$title = '') {
		
		switch ($method) {
			case 'create':
				print ACL::create($path,$title);
				
				break;
			case 'remove':
				ACL::remove($path); 
				break; 
				
		}
		die();
	}
}