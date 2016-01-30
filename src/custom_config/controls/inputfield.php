<?php
require_once CONTROL_PATH . 'input.php';
class CConfigControl_InputField extends CConfigBaseControl {
	public function getXType() {
		return 'inputfield';
	}
	public function outputInForm() {
	
		$input = new CInput();
		$input->name = $this->name;
		$input->value = $this->value;
		$input->rows = isset($this->config['rows'])?$this->config['rows']:1;
		$input->style = 'width:500px';
		//
		return $input->generate();
	}
	/**
	 * Выводит форму редактирования кол-ва строк в поле
	 */
	public static function outputAdminForm() {
		$input = new CInput();
		$input->name = 'config[rows]';
		$input->value = 1;
		$design = CMSDesign::getInstance();
		$design->table->begin();
		$design->table->row2cell('Кол-во рядов в поле',$input);
		$design->table->end();
	}
	public static function getControlTitle() {
		return 'Текстовое поле';
	}
}