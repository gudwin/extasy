<?php
require_once CONTROL_PATH . 'image.php';
class CConfigControl_ImageField extends CConfigBaseControl {
	public function outputInForm() {
		$image = new CImage();
		$image->name = $this->name;
		$image->src = $this->value;
		//
		return $image->generate();
	}
	public function getXType() {
		return 'imagefield';
	}
	public static function getControlTitle() {
		return 'Изображение';
	}
}