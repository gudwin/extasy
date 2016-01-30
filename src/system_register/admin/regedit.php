<?
use \Extasy\CMS;
/**
 * Редактор реестра
 * @date 29.04.2010 
 * @author Gisma (info@gisma.ru)
 */
class SystemRegisterAdministrate extends AdminPage
{
	public function __construct()
	{
		parent::__construct();
		$this->addGet('json','getData');
		$this->addPost('json','getData');
		$this->addPost('node','getData');
		$this->addPost('name,type,comment,value,parent,create','create');
		$this->addPost('id,name,comment,value,update','update');
		$this->addPost('delete','delete');
		
	}
	public function main()
	{
		$szTitle = 'Редактор реестра';
		$aBegin = array($szTitle => '#');
		//
		$aScripts = array(
			CMS::getResourcesUrl().'extasy/ext3/ux/treegrid/TreeGridSorter.js',
			CMS::getResourcesUrl()."extasy/ext3/ux/treegrid/TreeGridColumnResizer.js",
			CMS::getResourcesUrl()."extasy/ext3/ux/treegrid/TreeGridNodeUI.js",
			CMS::getResourcesUrl()."extasy/ext3/ux/treegrid/TreeGridLoader.js",
			CMS::getResourcesUrl()."extasy/ext3/ux/treegrid/TreeGridColumns.js",
			CMS::getResourcesUrl()."extasy/ext3/ux/treegrid/TreeGrid.js",
			CMS::getResourcesUrl().'extasy/Dashboard/administrate/regedit.js'
			);
		$css = CMS::getResourcesUrl()."extasy/ext3/ux/treegrid/treegrid.css";
		$this->outputExtJSHeader($aBegin,$szTitle,$aScripts, $css );

		$design = CMSDesign::getInstance();
		$design->contentBegin();
		?>
		<div id="regedit_layer"><!-- --></div>
		<?
		$design->contentEnd();
		//
		$this->outputFooter();
		$this->output();
	}
	public function getData()
	{
		$aResult = SystemRegisterHelper::export();
		$aResult = $this->appendData($aResult);

		print json_encode($aResult);
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
	public function create($name,$type,$comment,$value,$parent)
	{

		$node = SystemRegisterHelper::createById($parent);

		$nId = $node->insert($name,$value,$comment,$type);
		print $nId;
		SystemRegisterSample::createCache();
		die();
	}
	public function update($id,$name,$comment,$value,$update)
	{
		$node = SystemRegisterHelper::createById($id);
		if (is_subclass_of($node,'SystemRegister') || (get_class($node) == 'SystemRegister'))
		{
			$node->setupAttr($name,$comment);
		}
		else
		{
			$node->name = $name;
			$node->value = $value;
			$node->comment = $comment;
		}
		SystemRegisterSample::createCache();
		print 'Ok';
		die();
	}
	public function delete($id)
	{
		$node = SystemRegisterHelper::createById($id);
		if (is_subclass_of($node,'SystemRegister') || (get_class($node) == 'SystemRegister'))
		{
			$aPath = explode('/',$node->getFullPath());
			$szKey = $aPath = $aPath[sizeof($aPath) - 1];
			$node->getParent()->delete($szKey);
		}
		else
		{
			$szKey = $node->name;
			$node->parent->delete($szKey);
		}
		SystemRegisterSample::createCache();
		die();
		
	}
}
?>