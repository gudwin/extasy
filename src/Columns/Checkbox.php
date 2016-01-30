<?
namespace Extasy\Columns {
use \Faid\UParser;
use \Faid\DB;
use \DAO_Exception;
//************************************************************//
//                                                            //
//                 Элемент Input                              //
//       Copyright (c) 2006  ООО SmartDesign                  //
//               отдел/сектор                                 //
//       Email: support@smartdesign.by                        //
//                                                            //
//  Разработчик: Gisma                                        //
//  Модифицирован:   2006.02.20 by Gisma                      //
//                                                            //
//************************************************************//
class Checkbox extends BaseColumn  {
	var $nId;
	function __construct($szFieldName,$fieldInfo,$Value) {
		if (empty($fieldInfo['label'])) {
			throw new DAO_Exception('Нет параметра label',$szFieldName);
		}
		parent::__construct($szFieldName,$fieldInfo,intval($Value));
		if ( empty( $this->aValue )) {
			$this->aValue = 0;
		}
	}
	/**
	 * 
	 * @param unknown $dbData
	 */
	public function onAfterSelect( $dbData ) {
		if ( isset( $dbData[ $this->szFieldName ] )) {
			$this->aValue = intval($dbData[ $this->szFieldName ]);
		}
	}	
	function onInsert(\Extasy\ORM\QueryBuilder $query ) {
		$query->setSet( $this->szFieldName,$this->aValue );
	}
	function onUpdate(\Extasy\ORM\QueryBuilder $query ) {
		if (empty($this->fieldInfo['disabled'])) {
			$query->setSet($this->szFieldName,htmlspecialchars($this->aValue));
		}
	}
	function getFormValue() {
		$szResult = UParser::parsePHPFile(__DIR__ . DIRECTORY_SEPARATOR .'checkbox/form.tpl',array(
			'fieldname' =>  $this->szFieldName,
			'default_value' =>  1,
			'value'     =>  $this->aValue,
			'checked'   =>  !empty($this->aValue)?'checked':'',
			'label'     =>  $this->fieldInfo['label'],
			));

		return $szResult;
	}
	function getValue() {
		return $this->aValue;
	}
	function getViewValue() {
		return $this->aValue;
	}
}
}
?>