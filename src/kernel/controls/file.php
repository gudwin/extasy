<?
//************************************************************//
//                                                            //
//            Контрол Вставки закачки файла                   //
//       Copyright (c) 2006  ООО Extasy-CMS                   //
//               отдел/сектор                                 //
//       Email:   info@gisma.ru                               //
//                                                            //
//  Разработчик: Gisma (09.03.2010)                           //
//  Модифицирован:  09.03.2010  by Gisma                      //
//                                                            //
//************************************************************//

class CFile extends CControl {

	protected $szStyle;
	public function __set($name,$value) {
		if ($name == 'name') {
			$this->szName = $value;
		} elseif ($name == 'style') {
			$this->szStyle = $value;
		}
	}
	public function generate() {
		$szTemplate = '<input type="file" name="%s" style="%s"/>';
		$szResult = sprintf(
			$szTemplate,
			$this->szName,
			$this->szStyle);
		return $szResult;
	}
}
?>