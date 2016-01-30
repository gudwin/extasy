<?php
namespace Extasy\Columns\Controllers {
	class Tags extends \adminPage {
		public function __construct() {
			parent::__construct();
			$this->addGet( 'document,index,fieldname,displayLimit', 'showByCount' );
		}

		public function showByCount( $document, $index, $fieldName, $displayLimit ) {

		}
	}
}