<?php
use \Faid\UParser;
/**
 * That Column show results of page testing 
 * @author Gisma
 * @package extasycms.testSuite
 *
 */
class extasyTestResultColumn extends \Extasy\Columns\BaseColumn {
	const STATUS_OK = 'OK';
	const STATUS_ERROR = 'ERROR';
	protected $status = 0;
	protected $errorMessage = '';
	protected $loadTime = 0;
	public function __construct( $fieldName, $fieldInfo, $value ) {
		parent::__construct( $fieldName, $fieldInfo, $value );
		// trying to decode incoming data 
		if ( !empty( $this->aValue )) {
			if ( is_string( $this->aValue )) {
				$this->aValue = unserialize( $this->aValue ); 
			}
			$this->status = isset( $this->aValue['status'] ) ? $this->aValue['status'] : '';
			$this->errorMessage = isset( $this->aValue['errorMessage'] ) ? $this->aValue['errorMessage'] : '';
			$this->loadTime = isset( $this->aValue['loadTime'] ) ? $this->aValue['loadTime'] : '';
		}
	}
	/**
	 * 
	 */
	public function onInsert( \Extasy\ORM\QueryBuilder $query ) {
		$value = $this->getSerializedValue();
		$query->setSet( $this->szFieldName,$this->aValue );
	}
	/**
	 * 
	 */
	public function onUpdate( \Extasy\ORM\QueryBuilder $query ) {
		$value = $this->getSerializedValue();
		$query->setSet($this->szFieldName, $value );

	}
	public function setValue( $value) {
		if ( is_string( $value )) {
			$value = unserialize( $value );
		}
		$this->status = $value ['status'];
		$this->loadTime = $value ['loadTime'];
		$this->errorMessage = $value ['errorMessage'];
	}
	/**
	 * 
	 * @param unknown $dbRow
	 */
	public function onAfterSelect( $dbRow ) {
		if ( !empty( $dbRow[ $this->szFieldName ])) {
		 	$this->aValue = unserialize( $dbRow[ $this->szFieldName ] );
		 	$this->status = $this->aValue ['status'];
		 	$this->loadTime = $this->aValue ['loadTime'];
		 	$this->errorMessage = $this->aValue ['errorMessage'];
		 	
		} 
	}
	/**
	 * 
	 * @param unknown $statusInfo
	 */
	public function setStatus( $statusInfo = 'OK',  $errorMessage ) {
		$this->status = $statusInfo;
		$this->errorMessage = $errorMessage;
	}
	public function setExecutionTime( $time ) {
		$this->loadTime = $time; 
	}
	public function getAdminFormValue() {
		return $this->getAdminViewValue();
	}
	public function getAdminViewValue() {

		$parseVariables = array(
				'name'				=> $this->szFieldName,
				'status'			=> $this->status,
				'loadTime'			=> $this->loadTime,
				'errorMessage'		=> $this->errorMessage
				);

		return UParser::parsePHPFile( dirname(__FILE__).'/tpl/resultView.tpl', $parseVariables);
	}
	protected function getSerializedValue( ) {
		$parseVariables = array(
				'status'			=> $this->status,
				'loadTime'			=> $this->loadTime,
				'errorMessage'		=> $this->errorMessage
		);
		return serialize( $parseVariables );
	}
}