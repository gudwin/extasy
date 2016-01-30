<?php
use \Faid\UParser;

class CACLGrant extends CControl {
	protected $entity;
	protected $customHtml = array();

	public function __set( $name, $value ) {
		if ( $name == 'name' ) {
			$this->szName = htmlspecialchars( $value );
		} elseif ( $name == 'entity' ) {
			$this->entity = htmlspecialchars( $value );
		} elseif ( $name == 'customHtml' ) {
			$this->customHtml = $value;
		}
	}

	public function generate() {

		$fullGrantList = $this->getAllGrantsMap();
		$grantList     = ACL::selectAllGrantsForEntity( $this->entity );
		$tpl           = __DIR__ . DIRECTORY_SEPARATOR . 'form.tpl';
		$parseData     = array(
			'name'          => $this->szName,
			'grantList'     => $grantList,
			'fullGrantList' => $fullGrantList,
		);
		return UParser::parsePHPFile( $tpl, $parseData );
	}

	protected function getAllGrantsMap() {
		$result = ACLMisc::export();
		$result = self::addCustomHtml( $result ) ;

		return $result;
	}
	protected function addCustomHtml( $result ) {
		foreach ( $result as $key=>$row ) {
			$isMatch = isset( $this->customHtml[ $row['fullPath']]);
			if ( $isMatch ) {
				$result[ $key ]['title'] .= $this->customHtml[ $row['fullPath']];
			}
			if ( !empty($row['children'])) {
				$result[$key]['children'] = $this->addCustomHtml( $row['children']);
			}
		}
		return $result;
	}
}