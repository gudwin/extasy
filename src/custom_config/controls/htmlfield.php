<?php
require_once CONTROL_PATH . 'htmlarea.php';
class CConfigControl_HTMLField extends CConfigBaseControl {
	public function outputInForm() {
		$area = new CHTMLArea();
		$area->name = $this->name;
		$area->content = $this->value;
		//
		return $area->generate();
	}
	public function getXType() {
		return 'htmlfield';
	}
	public static function getControlTitle() {
		return 'Визуальный редактор';
	}
}