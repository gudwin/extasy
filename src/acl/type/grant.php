<?php

class GrantColumn extends \Extasy\Columns\BaseColumn {
	/**
	 * Because update of the column pretty heavy, this flag used to prevent extra update sequences (when they wasn`t requested)
	 * @var bool
	 */
	protected $changed = false;

	protected $loaded = false;

	/**
	 * @var array
	 */
	protected $customHtml = array();

	public function __construct( $fieldName, $fieldInfo, $value ) {
		parent::__construct( $fieldName, $fieldInfo, $value );
		if ( empty( $this->aValue ) ) {
			$this->aValue = array();
		}
	}

	public function setValue( $newValue ) {
		$this->changed = true;
		parent::setValue( $newValue );

	}
	public function onInsert( \Extasy\ORM\QueryBuilder $queryBuilder  ) {
	}


	public function onAfterInsert() {
		foreach ( $this->aValue as $path => $row ) {
			if ( !empty( $row )) {
				ACL::grant( $path, $this->getEntity() );
			}

		}
	}

	public function onUpdate( \Extasy\ORM\QueryBuilder $queryBuilder ) {
		if ( $this->changed ) {

			$this->onDelete( $queryBuilder );
			$this->onAfterInsert();
		}
		$this->changed = false;
	}

	public function onDelete( \Extasy\ORM\QueryBuilder $queryBuilder  ) {
		ACL::removeEntity( $this->getEntity() );
	}

	public function getEntity() {
		return $this->document->getModelName() . $this->document->getId();
	}

	public function onAfterSelect( $dbData ) {
		if ( !$this->isRightsLoaded()) {
			$this->refreshRights();
		} elseif ( !empty( $dbData[ $this->szFieldName ]) ) {
			$this->aValue = $dbData[ $this->szFieldName ];
			$this->loaded = true;
		}

	}
	public function refreshRights() {

		$this->aValue = ACL::selectAllGrantsForEntity( $this->getEntity() );
//		_debug( $this->aValue, $this->getEntity() );
		$this->loaded = true;
	}

	public function getViewValue() {
		$result = array();
		foreach ( $this->aValue as $key => $row ) {
			$result[ $row ] = true;
		}
		return $result;
	}

	public function getAdminFormValue() {
		$control         = new CACLGrant();
		$control->name   = $this->szFieldName;
		$control->entity = $this->getEntity();
		$control->customHtml = $this->customHtml;

		return $control->generate();
	}

	public function onAdminFormValue( $rightName, $value ) {
		$this->customHtml[ $rightName ] = $value;
	}
	protected function isRightsLoaded() {
		return $this->loaded && ( $this->document->getId() > 0 ) ;
	}
}
