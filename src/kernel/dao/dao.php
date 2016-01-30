<?
//************************************************************//
//                                                            //
//             Extasy-Dao                                     //
//       Copyright (c) 2006  ООО SmartDesign                  //
//               отдел/сектор                                 //
//       Email:   gisma@smartdesign.by                        //
//                                                            //
//  Разработчик: Gisma (30.06.2007)                           //
//  Модифицирован:  30.06.2007  by Gisma                      //
//                                                            //
//************************************************************//
class DAO {
	static function getInstance($szName) {
		switch ($szName) {
			case 'fs':
				return DAO_FileSystem::getInstance();
				break;
			case 'image':
				return DAO_Image::getInstance();
				break;
			default:
				trigger_error('DAO::getInstance. instance `'.htmlspecialchars($szName).'` not found',E_USER_ERROR);
				break;
		}
	}
	/**
	*   -------------------------------------------------------------------------------------------
	*   @desc Cохраняет и сериализует состояние объекта в памяти
	*   @return
	*   -------------------------------------------------------------------------------------------
	*/
	static function push($obj) {
		$obj->push();
	}
	/**
	*   -------------------------------------------------------------------------------------------
	*   @desc Восстанавливает состояние сервиса
	*   @return
	*   -------------------------------------------------------------------------------------------
	*/
	static function pop(&$obj) {
		$obj->pop();
	}

}
abstract class DAO_Service {
	protected $aStack = array();
	abstract public function push();
	abstract public function pop();
}
class DAO_Exception extends Exception {
	protected $target;
	/**
	 * 
	 * @param string $message - сообщение
	 * @param string $fieldName - имя поля сгенерировшего сообщение
	 */
	public function __construct($message,$fieldName) {
		parent::__construct($message,E_USER_ERROR);
		CMS_log::addMessage(__CLASS__ ,$fieldName);
		CMS_log::addMessage(__CLASS__ ,$message);

	}
	public function getTarget() {
		return $this->target;
	}
}

?>