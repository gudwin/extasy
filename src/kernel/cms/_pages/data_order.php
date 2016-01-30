<?
class DataListOrderPage extends AdminOrderPage
{
	public function __construct() 
	{
		$validator = new \Extasy\Validators\IsModelClassNameValidator( $_REQUEST['type']);
		if ( !$validator->isValid() ) {
			throw new InvalidArgumentException('Not a valid model ');
		}
		$modelName = $_REQUEST['type'];
		$aType = call_user_func([$modelName,'getFieldsInfo']);
		$aBegin = array(
			'К списку "'.(!empty($aType['title'])?$aType['title']:'').'" ' => 'list.php?type='.$modelName,
			'Сортировка' => '#'
		);
		$this->typeName = $modelName;
		$this->back = 'list.php?type='.$modelName;
		parent::__construct($aBegin,'Сортировка');
	}
}

?>