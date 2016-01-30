<?php
use \Faid\UParser;
class CFileSelect extends CControl
{
	/**
	 * Хранит текущий выбор файла
	 * @var string
	 */
	var $szValue = '';
	public function __set($key,$value)
	{
		if ($key == 'name') {
			$this->szName = $value;
		} elseif ($key == 'value')	{
			$this->szValue = $value;
		}
	}
	public function generate()
	{
		$aParse = array(
			'id' => $this->szName.'_id',
			'name' => $this->szName,
			'value' => $this->szValue
		);
		return UParser::parsePHPFile(CONTROL_PATH.'tpl/file_select.tpl',$aParse);
	}
}