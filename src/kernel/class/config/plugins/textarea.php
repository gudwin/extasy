<?
//************************************************************//
//                                                            //
//      Расширение класса CConfig осуществляет работу с       //
//           текстовым полем                                  //
//       Copyright (c) 2006  ООО SmartDesign                  //
//               отдел/сектор                                 //
//       Email:   gisma@smartdesign.by                        //
//                                                            //
//  Разработчик: Gisma (19.03.2007)                           //
//  Модифицирован:  19.03.2007  by Gisma                      //
//                                                            //
//************************************************************//
class Config_Textarea implements ExtConfigurable {
	/**
	*   @var Хранит имя директивы
	*/
	private $szName = '';
	/**
	*   @var Хранит значение директивы
	*/
	private $szContent = '';
	/**
	*   -------------------------------------------------------------------------------------------
	*   @desc Сохраняет данные для расширения
	*   @return
	*   -------------------------------------------------------------------------------------------
	*/
		public function setConfigData($szName,$szContent,$szAdditional = '',$szComment = '') {
		$this->szName = $szName;
		$this->szContent = $szContent;
	}
	public function getControl() {
		return '<textarea style="width:99%" rows=20 name="'.$this->szName.'">'.htmlspecialchars($this->szContent).'</textarea>';
	}
	public function toString($szValue) {
		return $szValue;
	}
}
?>