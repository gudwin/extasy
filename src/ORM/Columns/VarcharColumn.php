<?php

namespace Extasy\ORM\Columns;

trait VarcharColumn {
	public function onCreateTable( \Extasy\ORM\QueryBuilder $queryBuilder ) {
		$queryBuilder->addFields( sprintf( '`%s` varchar(255) not null default ""', $this->szFieldName ) );
	}
}
