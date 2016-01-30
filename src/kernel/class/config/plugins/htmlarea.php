<?
?><?
//************************************************************//
//                                                            //
//      Расширение класса CConfig осуществляет работу с       //
//           визуальным редактором                            //
//       Copyright (c) 2006  ООО SmartDesign                  //
//               отдел/сектор                                 //
//       Email:   gisma@smartdesign.by                        //
//                                                            //
//  Разработчик: Gisma (19.03.2007)                           //
//  Модифицирован:  19.03.2007  by Gisma                      //
//                                                            //
//************************************************************//
class Config_Htmlarea implements ExtConfigurable {
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
		require_once CONTROL_PATH . 'htmlarea.php';
		$control = new CHTMLArea();
		$control->name = $this->szName;
		$control->content = $this->szContent;
		return $control->generate();
	}
	public function toString($szValue) {
		return $szValue;
	}
}
?>