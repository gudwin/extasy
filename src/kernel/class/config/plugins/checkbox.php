<?
//************************************************************//
//                                                            //
//      Расширение класса CConfig осуществляет работу с       //
//           полем выбора                                     //
//       Copyright (c) 2006  ООО SmartDesign                  //
//               отдел/сектор                                 //
//       Email:   gisma@smartdesign.by                        //
//                                                            //
//  Разработчик: Gisma (19.03.2007)                           //
//  Модифицирован:  19.03.2007  by Gisma                      //
//                                                            //
//************************************************************//
class Config_Checkbox implements ExtConfigurable {
	/**
	*   @var Хранит имя директивы
	*/
	private $szName = '';
	/**
	*   @var Хранит значение директивы
	*/
	private $szContent = '';
	private $szLabel = '';
	/**
	*   -------------------------------------------------------------------------------------------
	*   @desc Сохраняет данные для расширения
	*   @return
	*   -------------------------------------------------------------------------------------------
	*/
	public function setConfigData($szName,$szContent,$szAdditional = '',$szComment = '') {
		$this->szName = $szName;
		$this->szContent = $szContent;
		$this->szLabel = $szAdditional;
	}
	public function getControl() {
		$szResult = '<input type="hidden" id="'.$this->szName.'" name="'.$this->szName.'" value="'.(!empty($this->szContent)?'1':'0').'">'."\r\n";
		$szResult .= '<input type=checkbox id="'.$this->szName.'_checkbox"  '.(!empty($this->szContent)?'checked':'').' onclick="document.getElementById(\''.$this->szName.'\').value = 1 - document.getElementById(\''.$this->szName.'\').value">';
		$szResult .= '<label for="'.$this->szName.'_checkbox">'.$this->szLabel.'</label>';
		return $szResult;
	}
	public function toString($szValue) {
		return $szValue;
	}
}
?>