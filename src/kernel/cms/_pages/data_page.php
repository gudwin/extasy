<?
/**
 * Class CMS_DataPage
 * @package extasycms.Dashboard
 */
class CMS_DataPage extends AdminPage {
	protected $buttons = array();
	protected $modelName;
	protected $typeName;
	protected $typeInfo;
	public function addButtons( $buttons ) {
		$this->buttons = array_merge( $this->buttons, $buttons);
	}
	protected  function getType($type)
	{
		return call_user_func( [ $type, 'getFieldsInfo']);

	}
	protected function getButton( ) {
		return $this->buttons;
	}
	protected function setupModelName ( $modelName ) {
		if ( empty( $this->modelName )) {
			$this->modelName = $modelName;
		}
		if ( !empty( $this->typeName )) {
			$typeName = $this->typeName;
		} else {
			$typeName = $this->modelName;
		}
		$this->typeInfo = call_user_func( [ $typeName, 'getFieldsInfo']);
	}
}
?>