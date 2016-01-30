<?php

namespace Extasy\ORM\Columns;

trait IntegerColumn {
	public function onCreateTable( \Extasy\ORM\QueryBuilder $queryBuilder ) {
		$queryBuilder->addFields( sprintf( '`%s` int not null default 0', $this->szFieldName ) );
	}
}
