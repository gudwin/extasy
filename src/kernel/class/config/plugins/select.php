<?
//************************************************************//
//                                                            //
//      Расширение класса CConfig осуществляет работу с       //
//           выбором значения из списка                       //
//       Copyright (c) 2010  ООО Ext-team                     //
//               отдел/сектор                                 //
//       Email:   info@gisma.ru                               //
//                                                            //
//  Разработчик: Gisma (29.06.2010)                           //
//  Модифицирован:  29.06.2010  by Gisma                      //
//                                                            //
//************************************************************//
require_once CONTROL_PATH . 'select.php';
class Config_Select implements ExtConfigurable {
	/**
	*   @var Хранит имя директивы
	*/
	private $szName = '';
	/**
	*   @var Хранит значение директивы
	*/
	private $szContent = '';
	/**
	*   @var Хранит значения для вывода
	*/
	private $aValues = ''; 
	/**
	*   @var Хранит подпись к селекту
	*/
	private $szTitle = ''; 
	/**
	*   -------------------------------------------------------------------------------------------
	*   @desc Сохраняет данные для расширения
	*   @return
	*   -------------------------------------------------------------------------------------------
	*/
	public function setConfigData($szName,$szContent,$szAdditional = '',$szComment = '') {
		$this->szName = $szName;
		$this->szContent = $szContent;
		$this->aValues = explode(';',$szAdditional);
		$this->szTitle = $szComment;
	}
	public function getControl() {
		$select = new CSelect();
		$select->name = $this->szName;
		$select->current = $this->szContent;
		//
		$aItem = array();
		foreach ($this->aValues as $row)
		{
			$aValue = explode('@',$row);
			$aItem[] = array('id' => $aValue[0],'name' => $aValue[1]);
		}
		$select->values = $aItem;
		return $select->generate();
	}
	public function toString($szValue) {
		return $szValue;
	}
}
?>