<?php
use \Faid\UParser;
/**
 * Выводит контрол даты
 * @author Gisma (info@gisma.ru)
 *
 */
class CDate extends CControl
{
	/**
	 * Хранит дату котрола
	 * @var string
	 */
	protected $value = null;
	public function __set($name,$value)
	{
		$value = htmlspecialchars($value);
		//
		if ($name == 'name')
		{
			$this->szName = $value;
		}
		elseif ($name == 'value')
		{
			$this->value = $value;
		}
	}
	/**
	 * Генерирует код для вызова js/users_controls/date.js и создания нового поля ввода даты 
	 */
	public function generate()
	{
		
		$aParse = array(
			'name' => $this->szName,
			'value' => $this->value,
		);
		return UParser::parsePHPFile(CONTROL_PATH.'tpl/date.tpl',$aParse); 
	}
}