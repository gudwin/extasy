<?php
use \Faid\UParser;
/**
 * Контрол для генерации редактируемого списка "ключ-значение"
 * @author Gisma (info@gisma.ru)
 *
 */
class CKeyValueList extends CControl
{
	/**
	 * Хранит массив значений списка для отображения
	 * @var array
	 */
	protected $value = array();
	/**
	 * Хранит значение заголовка-подписи к контролу
	 * @var string
	 */
	protected $title = '';
	public function __set($name,$value)
	{
		if ($name == 'name')
		{
			$this->szName = $value;
		}
		elseif ($name == 'values')
		{
			$this->values = $value;
		}
		elseif ($name == 'title')
		{
			$this->title = $value;
		}
	}
	/**
	 * Генерирует html-представление контрола
	 */
	public function generate()
	{
		$values = array();
		foreach ($this->values as $key=>$row) {
			$values[] = array($key,$row);
		}
		$aParse = array(
			'name' => $this->szName,
			'title' => $this->title,
			'values' => $values
		);
		return UParser::parsePHPFile(CONTROL_PATH.'tpl/key_value_list.tpl',$aParse);
	}
}