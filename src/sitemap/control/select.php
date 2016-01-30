<?php
use \Faid\UParser as UParser;
/**
 * Генерирует контрол для выбора страницы из БД модуля sitemap
 * @author Gisma (info@gisma.ru)
 *
 */
class CSitemapSelectControl extends CControl {
	/**
	 * Хранит массив индексов на элементы карты сайта
	 * @var unknown_type
	 */
	protected $values = array();
	public function __set($name,$value) {
		if ($name == 'name') {
			$this->szName = $value;
		} elseif ($name == 'values') {
			$this->values = $value;
		} 
	}
	public function generate() {
		// Получаем сайтмап данные
		$displayValues = array();
		foreach ($this->values as $id) {
			
			$sitemap = Sitemap_Sample::get($id);
			if (!empty($sitemap)) {
				$displayValues[] = array($sitemap['id'],$sitemap['name'],$sitemap['full_url']);
			}
		}
		$parseData = array(
			'name' => $this->szName,
			'values' => $displayValues
		);
		return UParser::parsePHPFile(LIB_PATH.'sitemap/control/select.tpl',$parseData);
	} 
}