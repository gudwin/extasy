<?php
use \Faid\DBSimple;
use \Extasy\Model\Model as extasyDocument;

/**
 * Class extasyTestModel
 * @package extasycms.testSuite
 */
class extasyTestModel extends extasyDocument {
	const ModelName = 'extasyTestModel';

	/**
	 *
	 */
	public function execute() {
		$ch       = $this->getCurl();
		$start    = microtime( true );
		$result   = curl_exec( $ch );
		$httpCode = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
		$finish   = microtime( true );
		$this->obj_lastResult->setExecutionTime( $finish - $start );
		$this->lastTestDate = date( 'Y-m-d H:i:s' );
		if ( 200 != $httpCode ) {
			$errorDescription = 'HTTP Response status:' . $httpCode;
			$this->obj_lastResult->setStatus( extasyTestResultColumn::STATUS_ERROR, $errorDescription );

		} else if ( curl_error( $ch ) ) {
			$errorDescription = curl_error( $ch );
			$this->obj_lastResult->setStatus( extasyTestResultColumn::STATUS_ERROR, $errorDescription );
		} else {
			$this->scanForErrors( $result );
		}
		$this->update();
	}

	/**
	 *
	 * @param unknown $url
	 */
	public static function exists( $url ) {
		$found = DBSimple::get( self::getTableName(),
								array(
									'url' => $url,
								) );
		return !empty( $found );
	}

	/**
	 *
	 * @return multitype:extasyTestModel
	 */
	public static function selectAll() {
		$list   = DBSimple::select( self::getTableName(), '', '`id` asc' );
		$result = array();
		foreach ( $list as $key => $row ) {
			$result[ ] = new extasyTestModel( $row );
		}
		return $result;
	}

	/**
	 *
	 * @return multitype:string multitype:string  multitype:string multitype:string
	 */
	public static function getFieldsInfo() {
		return array(
			'table'       => 'extasy_test_model',
			'list_fields' => 'created,lastTestDate,url,method,comment',
			'edit_fields' => 'created,url,method,parameters,errorRegexps,comment',
			'labels'      => array(
				extasyDocument::labelAllItems     => 'Все тесты',
				extasyDocument::labelAddItem      => 'Добавить тест',
				extasyDocument::labelSingularName => 'ExtasyCMS. Тест',
				extasyDocument::labelName         => 'url'
			),
			'fields'      => array(
				'id'           => '\\Extasy\\Columns\\Index',
				'url'          => array(
					'class' => '\\Extasy\\Columns\\Input',
					'title' => 'URL',
				),
				'method'       => array(
					'class'  => '\\Extasy\\Columns\\Listvalue',
					'values' => 'GET;POST',
					'title'  => 'Метод отправки',
				),
				'parameters'   => array(
					'class'    => '\\Extasy\\Columns\\Text',
					'title'    => 'Параметры',
					'cms_help' => 'Параметры применяются <b>только</b> к POST запросу. Формат комментариев - JSON'
				),
				'errorRegexps' => array(
					'class'   => '\\Extasy\\Columns\\Table',
					'title'   => 'Регулярные выражения',
					'columns' => 'regExp=Регулярное выражение'
				),
				'comment'      => array(
					'title' => 'Комментарий',
					'class' => '\\Extasy\\Columns\\Html',
				),
				'lastResult'   => array(
					'class' => '\\extasyTestResultColumn',
					'title' => 'Последний результат',
				),
				'created'      => array(
					'class' => '\\Extasy\\Columns\\Datetime',
					'title' => 'Создано',
				),
				'lastTestDate' => array(
					'class' => '\\Extasy\\Columns\\Datetime',
					'title' => 'Дата последнего тестирования',
				)
			)
		);
	}

	/**
	 *
	 * @param unknown $pageCode
	 */
	protected function scanForErrors( $pageCode ) {
		$checkList = array();
		$tmp       = $this->errorRegexps->getValue();
		if ( !empty( $tmp ) ) {
			foreach ( $tmp as $row ) {
				$checkList[ ] = $row[ 'fieldRegExp' ];
			}
		}

		$checkList[ ] = '#Error source#';
		$checkList[ ] = '#Fatal\s+error#msi';
		$checkList[ ] = '#class\=\"error\"#';
		$checkList[ ] = '#xdebug\-error#';
		$checkList[ ] = '#Parse error#';

		foreach ( $checkList as $pattern ) {

			if ( preg_match( $pattern, $pageCode, $match ) ) {

				$errorMsg = 'Find error by regular expression. Matched patter - "%s" ';
				$errorMsg = sprintf( $errorMsg, $pattern );

				if ( sizeof( $match ) > 1 ) {
					$errorMsg .= sprintf( "\r\n<br/>%s", $match[ 1 ] );
				}
				$this->obj_lastResult->setStatus( extasyTestResultColumn::STATUS_ERROR, $errorMsg );
				return ;
			}
		}
		$this->obj_lastResult->setStatus( extasyTestResultColumn::STATUS_OK, '' );
	}

	/**
	 *
	 * @return resource
	 */
	protected function getCurl() {
		$url     = $this->url->getValue();
		$urlInfo = parse_url( $url );
		if ( empty( $urlInfo[ 'host' ] ) ) {
			$url = \Extasy\CMS::getWWWRoot() . substr( $url, 1 );
		}
		$ch = curl_init();
		curl_setopt_array( $ch,
						   array(
							   CURLOPT_RETURNTRANSFER => 1,
							   CURLOPT_FOLLOWLOCATION => 1,
							   CURLOPT_URL            => $url
						   ) );
		if ( !empty( $this->method->getValue() ) ) {
			curl_setopt( $ch, CURLOPT_POST, true );
			$parameters = trim( $this->parameters->getValue() );
			if ( !empty( $parameters ) ) {
				$postFields = json_decode( $parameters, JSON_OBJECT_AS_ARRAY );
				curl_setopt( $ch, CURLOPT_POSTFIELDS, $postFields );
			}
		}
		return $ch;
	}
} 