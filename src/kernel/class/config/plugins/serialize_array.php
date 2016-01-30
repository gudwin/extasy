<?
//************************************************************//
//                                                            //
//      Расширение класса CConfig осуществляет работу с       //
//       массивом данных                                      //
//           текстовым полем                                  //
//       Copyright (c) 2006  ООО SmartDesign                  //
//               отдел/сектор                                 //
//       Email:   gisma@smartdesign.by                        //
//                                                            //
//  Разработчик: Gisma (07.07.2007)                           //
//  Модифицирован:  07.07.2007  by Gisma                      //
//                                                            //
//************************************************************//
// Расширение создано, для хранения массивов в конфиге
// Массив сохранятся в сериализованном виде, в директиве
// При редактирование выводится textarea
class Config_Serialize_array implements ExtConfigurable {
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
		$szContent = (!empty($this->szContent))?$this->szContent:'';
		return '<textarea style="width:99%" rows=10 name="'.$this->szName.'">'.$szContent.'</textarea>';
	}
	public function toString($szValue) {
		return $szValue;
	}
}
?>