<?php

namespace Extasy\Schedule\Columns;


class ClassName extends \Extasy\Columns\BaseColumn {
	public function setValue( $newValue ) {

	}
	public function setDocument( $document ) {
		parent::setDocument( $document );
		$this->aValue = get_class( $document );
	}
	public function getViewValue() {
		return $this->aValue;
	}

	public static function validateClassName( $className ) {
		if ( empty( $className ) || !class_exists( $className )) {
			throw new \NotFoundException(sprintf( 'Class `%s` not found', $className ));
		}
		if ( !is_subclass_of( $className, \Extasy\Schedule\Job::ModelName )) {
			throw new \InvalidArgumentException( sprintf('Must be a job class - "%s"', $className ));
		}
		return true;
	}
	public function onCreateTable( \Extasy\ORM\QueryBuilder $queryBuilder ) {
		$queryBuilder->addFields( sprintf( '`%s` varchar(255) not null default ""', $this->szFieldName ) );
	}
} 