<?
//************************************************************//
//                                                            //
//      Расширение класса CConfig осуществляет работу с       //
//           выбором изображения                              //
//       Copyright (c) 2006  ООО SmartDesign                  //
//               отдел/сектор                                 //
//       Email:   gisma@smartdesign.by                        //
//                                                            //
//  Разработчик: Gisma (19.03.2007)                           //
//  Модифицирован:  19.03.2007  by Gisma                      //
//                                                            //
//************************************************************//
class Config_Image implements ExtConfigurable {
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
		// Высчитываем величины
		if (file_exists(SYS_ROOT.$this->szContent)
			&& is_file(SYS_ROOT.$this->szContent)) {
			$aInfo = getimagesize(SYS_ROOT.$this->szContent);
			switch ($aInfo[2]) {
				case 1:$szType = 'GIF'; break;
				case 2:$szType = 'JPG'; break;
				case 3:$szType = 'PNG'; break;
				case 6:$szType = 'BMP'; break;
				default : $szType = 'unknown';
			}
			$aImageSize = array(
				'height'  => $aInfo[1],
				'width'   => $aInfo[0],
				'size'    => filesize(SYS_ROOT.$this->szContent),
				'type'    => $szType,
				);
		} else {
			$aImageSize = array(
				'height'  => 0,
				'width'   => 0,
				'size'    => 0,
				'type'    => '',
				);
		}
		$szResult = include CONFIG_PATH . 'plugins/image/form.tpl';
		return $szResult;
	}
	public function toString($szValue) {
		return $szValue;
	}
}
?>