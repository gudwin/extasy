<?php
use \Faid\UParser as UParser;
/**
 * Генерирует поле для выбора одного адреса из базы данных sitemap
 * @author Gisma
 *
 */
class CSitemapSelectOnce extends CControl {
	/**
	 * Хранит значение в индексе
	 * @var int
	 */
	protected $value;
	protected $filter = array();
	public function __set($key,$value) {
		switch ( $key ) {
			case 'name':
				$this->szName = htmlspecialchars($value);
				break;
			case 'value':
				$this->value = IntegerHelper::toNatural($value);
				break;
				
		}	
	}
	public function setFilter( array $docList ) {
		$this->filter = $docList;
	}
	
	public function generate() {
		if (!empty($this->value)) {
			$urlInfo = Sitemap_Sample::get($this->value);
		} else {
			$urlInfo = array();
		}

		$parseData = array(
			'name' => $this->szName,
			'filter' => $this->filter,
			'urlInfo' => $urlInfo,
		);
		return UParser::parsePHPFile(LIB_PATH.'sitemap/control/select.once.tpl',$parseData);
	}
}