<?php
use \Faid\UParser;
/**
 * Генерирует контрол с выборки каких-либо элементов. 
 * На вход принимает список текущих знанений, адрес скрипта возвращающего результаты поиска и имя контрола
 * @author Gisma (info@gisma.ru)
 *
 */
class CLinksToManyControl extends CControl {
	/**
	 * Хранит массив индексов на элементы карты сайта
	 * @var unknown_type
	 */
	protected $values = array();
	/**
	 * Адрес скрипта, к которому будет обращаться
	 * @var string
	 */
	protected $url = '';
	public function __set($name,$value) {
		if ($name == 'name') {
			$this->szName = $value;
		} elseif ($name == 'values') {
			$this->values = $value;
		} elseif ($name == 'url') {
			$this->url = $value;
		} 
	}
	public function generate() {
		// Проверяем есть ли адрес скрипта
		if (empty($this->url)) {
			throw new Exception('Url not defined');
		} 
		
		$parseData = array(
			'name' => $this->szName,
			'values' => $this->values,
			'url' => $this->url
		);
		return UParser::parsePHPFile(dirname(__FILE__).'/tpl/links_to_many.tpl',$parseData);
	} 
}