<?
use \Faid\UParser;
//************************************************************//
//                                                            //
//            Контрол чекбокса                                //
//       Copyright (c) 2006  ООО Extasy-CMS                   //
//               отдел/сектор                                 //
//       Email:   gisma@ext-cms.com                           //
//                                                            //
//  Разработчик: Gisma (29.06.2008)                           //
//  Модифицирован:  29.06.2008  by Gisma                      //
//                                                            //
//************************************************************//

class CCheckbox extends CControl {
	protected $szTitle;
	protected $szId;
	protected $szValue;
	protected $szChecked;
	public function __set($name,$value) {
		if ($name == 'value') {
			$this->szValue = htmlspecialchars($value);
		}
		if ($name == 'name') {
			$this->szName = htmlspecialchars($value);
		}
		if ($name == 'title') {
			$this->szTitle = htmlspecialchars($value);
		}

		if ($name == 'id') {
			$this->szId = htmlspecialchars($value);
		}
		if ($name == 'checked') {
			$this->szChecked  = $value;
		}

	}
	public function __get($name) {
		return NULL;
	}
	public function generate() {
		$parseData = array(
			'name' => $this->szName,
			'id' => !empty($this->szId)?$this->szId:('checkbox_'.$this->szName),
			'checked' => 	$this->szChecked,
			'value' => $this->szValue,
			'labelTitle' => $this->szTitle,
		);
		$result = UParser::parsePHPFile(CONTROL_PATH.'tpl/checkbox.tpl',$parseData);
		return $result;
	}

}
?>