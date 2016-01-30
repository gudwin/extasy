<?php


namespace Extasy\Columns;

use \CSelect;

class StaticSelect extends BaseColumn {
	public function __construct( $fieldName, $fieldInfo, $value ) {
		if ( !isset( $fieldInfo[ 'values' ] ) ) {
			throw new \DAO_Exception( '`values` options must be defined', $fieldName );
		}
		parent::__construct( $fieldName, $fieldInfo, $value );
	}
	public function getViewValue() {
		return $this->getValue();
	}
	public function getAdminViewValue() {
		foreach ( $this->fieldInfo[ 'values' ] as $key=>$row ) {
			if ($this->aValue == $key ) {
				return $row;
			}
		}
	}
	public function onAfterSelect( $dbData ) {
		if ( isset( $dbData[ $this->szFieldName ] ) ) {
			$this->aValue = $dbData[ $this->szFieldName ];
		}
	}

	public function getAdminFormValue() {
		$select         = new CSelect();
		$select->name   = $this->szFieldName;
		$select->current  = $this->aValue;

		$select->values = $this->getValuesForSelectComponent();
		return $select;
	}

	protected function getValuesForSelectComponent() {
		$result = array();
		foreach ( $this->fieldInfo[ 'values' ] as $id => $value ) {
			$result[ ] = array(
				'id'    => $id,
				'name' => $value
			);
		}
		return $result;
	}
	public function onCreateTable( \Extasy\ORM\QueryBuilder $queryBuilder ) {
		$queryBuilder->addFields( sprintf( '`%s` int not null default 0', $this->szFieldName ) );
	}

} 