<?php
use \Faid\UParser;
class CPhpSource extends CControl {
	/**
	 * Хранит код для тестирования
	 * @var string
	 */
	protected $phpSource = '';
	/**
	 * 
	 * Хранит код для тестовых данных
	 * @var array
	 */
	protected $initData = array();
	public function __set($key,$value) {
		switch ($key) {
			case 'name':
				$this->szName = $value;				 
				break;
			case 'source':
				$this->phpSource = $value;
				break;
			case 'init':
				$this->initData = $value;
				break;
		}
	}
	public function generate() {
		return UParser::parsePHPFile(CONTROL_PATH.'tpl/php_source.tpl', array(
			'name' => $this->szName,
			'phpSource' => $this->phpSource,
			'initData' => $this->initData,
		));
	}
	
}