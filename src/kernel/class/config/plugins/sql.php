<?
use \Faid\DB;
//************************************************************//
//                                                            //
//      Расширение класса CConfig осуществляет работу с       //
//           полем выбора по sql-запросу                      //
//       Copyright (c) 2006  ООО SmartDesign                  //
//               отдел/сектор                                 //
//       Email:   gisma@smartdesign.by                        //
//                                                            //
//  Разработчик: Gisma (30.04.2007)                           //
//  Модифицирован:  30.04.2007  by Gisma                      //
//                                                            //
//************************************************************//
class Config_Sql implements ExtConfigurable {
	var $szName;
	/**
	*   @var Хранит доп.
	*/
	var $szValue;
	/**
	*   @var Хранит доп. данные
	*/
	var $szAdditional;
	/**
	*   -------------------------------------------------------------------------------------------
	*   @desc Устанавливает данные
	*   @return
	*   -------------------------------------------------------------------------------------------
	*/
	public function setConfigData($szName,$szContent,$szAdditional = '',$szComment = '') {
		$this->szName = $szName;
		$this->szValue = $szContent;
		$this->szAdditional = $szAdditional;
		
	}
	/**
	*   -------------------------------------------------------------------------------------------
	*   @desc Возвращает html-код контрола
	*   @return
	*   -------------------------------------------------------------------------------------------
	*/
	public function getControl() {
		$data = DB::query( $sql ) 
		
		$szResult = '<select style="width:99%" name="'.$this->szName.'">'."\r\n";
		$szResult .= '<option value="0">Не выбрано</option>';
		for ($i = 0; $i < sizeof($aValue); $i++) {
			$szResult .= '<option value="'.$aValue[$i]['id'].'" '.($this->szValue  == $aValue[$i]['id']?'selected':'').'>'.$aValue[$i]['name'].'</option>'."\r\n";
		}

		$szResult .= '</select>';
		return $szResult;
	}
	public function toString($szValue) {
		return $szValue;
	}
}

?>