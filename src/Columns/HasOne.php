<?php

namespace Extasy\Columns;


class HasOne extends BaseColumn {
	/**
	 * @var \Extasy\Model\Model
	 */
	protected $linkedModel = null;
	public function __construct( $fieldName, $fieldInfo, $value ) {
		if ( !class_exists( $fieldInfo['model'])) {
			throw new \NotFoundException('`model` attribute not found');
		}
		parent::__construct( $fieldName, $fieldInfo, $value );
	}
    public function onAfterSelect( $dbData ) {
        if ( isset( $dbData[ $this->szFieldName ])) {
            $this->setValue( $dbData[ $this->szFieldName ]);
        }
    }
	public function onInsert( \Extasy\ORM\QueryBuilder $queryBuilder) {
		$this->autoCreate();
		parent::onInsert( $queryBuilder );
	}
	public function onUpdate( \Extasy\ORM\QueryBuilder $queryBuilder ) {
		parent::onUpdate( $queryBuilder );
	    $this->autoCreate();
	}
    public function getLinkedModel() {
        if ( empty( $this->linkedModel )) {
            $this->autoCreate();
        }
        return $this->linkedModel;
    }
	protected function autoCreate( ) {
        $modelClass = $this->fieldInfo['model'];
        if ( empty( $this->aValue ) ) {
            $this->linkedModel = new $modelClass( );
            $this->linkedModel->insert( );
        } else {
            $this->linkedModel = call_user_func( [$modelClass,'getById'], $this->aValue );
        }
	}
    public function onCreateTable( \Extasy\ORM\QueryBuilder $queryBuilder ) {
        $queryBuilder->addFields( sprintf( '`%s` int not null default 0', $this->szFieldName ) );
        if ( !empty( $this->fieldInfo['index'])) {
            $queryBuilder->addFields( sprintf( 'index `search_%s` (`%s`)', $this->szFieldName,$this->szFieldName ) );
        }

    }
} 