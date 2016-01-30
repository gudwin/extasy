<?php

namespace Extasy\sitemap\controller;


class ModelEditor extends \CMS_DataManage {
	protected $modelName = '';
	protected $szEditScript = './edit';
	protected $szScript = './index';
	public function __construct() {
		$_GET[ 'type' ] = $_GET[ 'typeName' ] = $_POST[ 'typeName' ] = $this->modelName;
		parent::__construct();

		$this->addGet('create', 'add');
	}
	public function add( ) {

		$model = new $this->modelName();
		$model->insert( );

		$url = $this->szEditScript . '?id='. $model->id->getValue();
		$this->jump( $url );
	}
	protected function setupModelName( $modelName ) {
		$this->typeInfo = call_user_func( array( $this->modelName, 'getFieldsInfo' ) );
	}

	protected function generateEditUrl() {

		$szResult = $this->szEditScript;
		$szResult .= '?id=' . $this->nId;
		return $szResult;
	}
}