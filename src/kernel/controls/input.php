<?
//************************************************************//
//                                                            //
//            Контрол Изображения                             //
//       Copyright (c) 2006  ООО Extasy-CMS                   //
//               отдел/сектор                                 //
//       Email:   gisma@ext-cms.com                           //
//                                                            //
//  Разработчик: Gisma (10.02.2008)                           //
//  Модифицирован:  10.02.2008  by Gisma                      //
//                                                            //
//************************************************************//

class CInput extends CControl {
	protected $szContent;
	protected $szStyle;
	protected $nRows = 0;
	public function __set($name,$value) {
		if ($name == 'name') {
			$this->szName = htmlspecialchars($value);
		} elseif ($name == 'content') {
			$this->szContent = htmlspecialchars($value);
		} elseif ($name == 'value') {
			$this->szContent = htmlspecialchars($value);
		} elseif ($name == 'style') {
			$this->szStyle = $value;
		} elseif ($name == 'rows') {
			$this->nRows = IntegerHelper::toNatural($value);
		}
	}
	public function generate() {
		$this->nRows = IntegerHelper::toNatural($this->nRows);
		if (!empty($this->nRows) && ($this->nRows > 1)) 
		{
			// то генерируем textarea
			$szTemplate = '<textarea rows="%d" name="%s" style="%s">%s</textarea>';
			$szResult = sprintf($szTemplate,$this->nRows,$this->szName,$this->szStyle,($this->szContent));
			return $szResult;
		}
		$szTemplate = '<input type="text" name="%s" value="%s" style="%s"/>';
		$szResult = sprintf(
			$szTemplate,
			$this->szName,
			$this->szContent,
			$this->szStyle);
		return $szResult;
	}
}
?>