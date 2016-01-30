<?php
namespace Extasy\sitemap\controller;

class ModelList extends \CMS_Page_DataList {
	const title = '';

	protected $modelName = '';
	protected $szDeleteUrl = './index';
	protected $szScript = './index';
	protected $szEditScript = './edit';

	public function __construct() {
		$_GET[ 'type' ] = $_GET[ 'typeName' ] = $_POST[ 'typeName' ] = $this->modelName;
		$this->szTitle  = static::title;
		$this->aBegin   = array(
			$this->szTitle => '#'
		);
		parent::__construct();
	}
	public function main() {
		$this->show( '' );
	}

	public function getButton() {
		$result = array();
		if ( !$this->bBlockAdd ) {
			$label = call_user_func( array( $this->modelName, 'getLabel' ),
									 \Extasy\Model\Model::labelAddItem );
			$result[ $label ] = $this->szEditScript . '?create=1';
		}

		if ( isset( $this->typeInfo[ 'cms_buttons' ] ) ) {
			if ( is_array( $this->typeInfo[ 'cms_buttons' ] ) ) {
				$result = array_merge( $result, $this->typeInfo[ 'cms_buttons' ] );
			} else {
				throw new \InternalErrorException( 'getButton directive cms_buttons muste an array' );
			}
		}
		if ( !empty( $this->buttons ) ) {
			$result = array_merge( $result, $this->buttons );
		}

		return $result;
	}
}