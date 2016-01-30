<?php
namespace Extasy\Columns;


use Extasy\ORM\QueryBuilder;

class Serializeable extends BaseColumn
{
    public function onInsert( QueryBuilder $queryBuilder)
    {
        $queryBuilder->setSet( $this->szFieldName,serialize( $this->aValue ) );
    }
    public function onUpdate( QueryBuilder $queryBuilder ) {
        $queryBuilder->setSet( $this->szFieldName, serialize( $this->aValue ));
    }

    /**
     *
     * @param unknown $dbData
     */
    public function onAfterSelect($dbData)
    {
        if (isset($dbData[$this->szFieldName])) {
            $this->aValue = unserialize($dbData[$this->szFieldName]);
        }
    }

    public function onCreateTable(\Extasy\ORM\QueryBuilder $queryBuilder)
    {
        $queryBuilder->addFields(sprintf('`%s` text null', $this->szFieldName));
    }
} 