<?php
namespace Extasy\Columns\Controllers {
	use \CHTMLArea;

	class HtmlareaPage extends \adminPage {

		public function __construct() {
			parent::__construct();
			$this->addPost( 'content', 'store' );
		}

		public function store( $content ) {
			$area = new CHtmlarea();
			$area->postHTMLArea();
		}

		public function main() {
			$area = new CHtmlarea();
			$area->showHTMLArea();
			$this->output();
		}
	}
}