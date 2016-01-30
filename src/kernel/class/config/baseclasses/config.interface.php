<?
//************************************************************//
//                                                            //
//         Интерфейс конфигурируемого контрола                //
//       Copyright (c) 2006  ООО SmartDesign                  //
//               отдел/сектор                                 //
//       Email:   gisma@smartdesign.by                        //
//                                                            //
//  Разработчик: Gisma (19.03.2007)                           //
//  Модифицирован:  19.03.2007  by Gisma                      //
//                                                            //
//************************************************************//

interface ExtConfigurable {
	/**
	*   -------------------------------------------------------------------------------------------
	*   @desc Устанвливает данные полученные из конфига в класс
	*   @return
	*   -------------------------------------------------------------------------------------------
	*/
	public function setConfigData($szName,$szContent,$szAdditional = '',$szComment = '');
	/**
	*   -------------------------------------------------------------------------------------------
	*   @desc Возвращает контрол
	*   @return
	*   -------------------------------------------------------------------------------------------
	*/
	public function getControl();
	/**
	*   -------------------------------------------------------------------------------------------
	*   @desc Форматируем в строку данные поступившие на котрол
	*   @return
	*   -------------------------------------------------------------------------------------------
	*/
	public function toString($szValue);
}
?>