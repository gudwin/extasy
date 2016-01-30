<?
//************************************************************//
//                                                            //
//            XML-парсер, генерирующий структуру XML-данных   //
//       Copyright (c) 2006  ООО Extasy-CMS                   //
//               отдел/сектор                                 //
//       Email:   info@gisma.ru                              //
//                                                            //
//  Разработчик: Gisma (05.12.2007)                           //
//  Модифицирован:  05.12.2007  by Gisma                      //
//                                                            //
//************************************************************//
define('XMLPARSER_E_EMPTY','1');
define('XMLPARSER_E_CORRUPTED','2');
class XMLParser {
	protected $szCode;
	protected $aStack = array();
	protected $aCurrent = null;
	public function __construct($szCode) {
		$this->szCode = $szCode;
	}
	/**
	*   -------------------------------------------------------------------------------------------
	*   @desc Парсит и возвращает структуру
	*   @return
	*   -------------------------------------------------------------------------------------------
	*/
	public function parse() {
		if (empty($this->szCode)) {
			throw new Exception('Empty XML-data',XMLPARSER_E_EMPTY);
		}
		// инициализируем парсер
		$this->aCurrent = new XMLNode();
		$this->aCurrent->child = array();
		$xml_parser = xml_parser_create();
		xml_parser_set_option($xml_parser, XML_OPTION_CASE_FOLDING, 0);
		xml_set_element_handler($xml_parser, array($this,"_parseXMLStartElement"),array($this, "_parseXMLEndElement"));
		xml_set_character_data_handler($xml_parser, array($this,"_parseXMLData"));
		xml_set_default_handler($xml_parser, array($this,"_parseXMLDefault"));

		// парсим
		if (!xml_parse($xml_parser, $this->szCode, true)) {
			throw new Exception(sprintf("XML error: %s at line %d\n",
				xml_error_string(xml_get_error_code($xml_parser)),
				xml_get_current_line_number($xml_parser)));
		}
			// освобождаем парсер
			xml_parser_free($xml_parser);
		// возращаем результата
		return $this->aCurrent->child[0];
	}
	/**
	*   -------------------------------------------------------------------------------------------
	*   @desc Мы попали на новый элемент
	*   @return
	*   -------------------------------------------------------------------------------------------
	*/
	protected function _parseXMLStartElement($parser, $name, $attribs) {
		array_push($this->aStack,$this->aCurrent);
		$this->aCurrent = new XMLNode();
		$this->aCurrent->name = $name;
		$this->aCurrent->value = '';
		$this->aCurrent->attr = $attribs;
		$this->aCurrent->child = array();
	}

	/**
	*   -------------------------------------------------------------------------------------------
	*   @desc Попали на конец элемента
	*   @return
	*   -------------------------------------------------------------------------------------------
	*/
	protected function _parseXMLEndElement($parser, $name) {
		$aData = array_pop($this->aStack);
		$aData->child[] = $this->aCurrent;
		$this->aCurrent = $aData;
	}
	/**
	*   -------------------------------------------------------------------------------------------
	*   @desc Попали на данные
	*   @return
	*   -------------------------------------------------------------------------------------------
	*/
	protected function _parseXMLData($parser, $data) {
		$this->aCurrent->value .= $data;
	}
	/**
	*   -------------------------------------------------------------------------------------------
	*   @desc Хрен знает что;)
	*   @return
	*   -------------------------------------------------------------------------------------------
	*/
	protected function _parseXMLDefault($parser, $data) {
	}
}
class XMLNode {
	public $name;
	public $attr;
	public $value;
	public $child = array();
	public function getElementsByTagName($szTagName,$ignoreCase = false) {
		if ($ignoreCase) {
			$szTagName = strtoupper($szTagName);
		}
		$aResult = array();
		foreach ($this->child as $row) {
			if ($ignoreCase) {
				if (strtoupper($row->name) == $szTagName) {
					$aResult[] = $row;
				}
			} elseif ($row->name == $szTagName) {
				$aResult[] = $row;
			}
		}
		return $aResult;
	}
	public function getTagByName($szTagName,$ignoreCase = false) {
		if ($ignoreCase) {
			$szTagName = strtoupper($szTagName);
		}
		$aResult = array();
		foreach ($this->child as $row) {
			if ($ignoreCase) {
				if (strtoupper($row->name) == $szTagName) {
					return $row;
				}
			} elseif ($row->name == $szTagName) {
				return $row;
			}
		}
		return null;
	}
	public function getTags($aTag) {
		$aResult = array();
		foreach ($this->child as $row) {
			if (in_array($row->name,$aTag)) {
				$aResult[$row->name] = $row;
			}
		}
		return $aResult;
	}
}

?>