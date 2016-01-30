<?php

class CConfigControl_PhpSourceField extends CConfigBaseControl {
	public function getXType() {
		return 'phpsourcefield';
	}
	public function outputInForm() {
	
		$input = new CPhpSource();
		$input->name = $this->name;
		$input->source = $this->value;
		$input->init = isset($this->config['initData'])?$this->config['initData']:array();
		//
		return $input->generate();
	}
	/**
	 * Выводит форму редактирования кол-ва строк в поле
	 */
	public static function outputAdminForm() {
		$input = new CInput();
		$input->name = 'config[initData]';
		$input->value = '<?php ?>';
		$input->rows = 6;
		$input->style="width:100%;";
		$design = CMSDesign::getInstance();
		$design->table->begin();
		$design->table->row2cell('Инициализация (формат PHP)',$input);
		$design->table->end();
	}
	public static function getControlTitle() {
		return 'PHP-шаблон';
	}
}