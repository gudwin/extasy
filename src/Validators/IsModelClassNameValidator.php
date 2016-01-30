<?php
namespace Extasy\Validators;

class IsModelClassNameValidator extends BaseValidator{

	protected $modelName = '';
	public function __construct( $modelName ) {
		$this->modelName = $modelName;
	}
	public function getFieldsInfo() {
		return call_user_func( [ $this->modelName, 'getFieldsInfo']);
	}
	protected function test() {
        $result = class_exists( $this->modelName ) && is_subclass_of( $this->modelName, '\\Extasy\\Model\\Model');
		return $result;
	}
} 