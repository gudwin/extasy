<?php
namespace Extasy\Columns {
    use \Faid\DBSimple;
    use \Faid\DB;
    use \Faid\View\View;

    use \IntegerHelper;

    class HasMany extends BaseColumn {
        protected $tableName;
        protected $innerKey = '';
        protected $foreighnKey = '';
        protected $linkedModel = '';

        public function __construct( $fieldName, $fieldInfo, $fieldValue = array() ) {
            if ( empty( $fieldInfo[ 'table' ] ) ) {
                throw new \ForbiddenException( 'Table key not defined' );
            }
            if ( empty( $fieldInfo[ 'foreighnKey' ] ) ) {
                throw new \ForbiddenException( 'Foreighn key not defined' );
            }
            if ( empty( $fieldInfo[ 'innerKey' ] ) ) {
                throw new \ForbiddenException( 'Inner key not defined' );
            }
            if ( empty( $fieldInfo[ 'model' ] ) ) {
                throw new \ForbiddenException( 'Model key not defined' );
            }
            $this->tableName   = $fieldInfo[ 'table' ];
            $this->innerKey    = $fieldInfo[ 'innerKey' ];
            $this->foreighnKey = $fieldInfo[ 'foreighnKey' ];
            $this->linkedModel = $fieldInfo[ 'model' ];

            parent::__construct( $fieldName, $fieldInfo, $fieldValue );
        }

        public function onAfterSelect( $dbData ) {
            if ( $this->document->id->getValue() > 0 ) {
                $this->loadData();
            }
        }

        public function onUpdate( \Extasy\ORM\QueryBuilder $queryBuilder ) {
            $this->onDelete( new \Extasy\ORM\QueryBuilder( 'delete' ) );
            $this->onInsert( new \Extasy\ORM\QueryBuilder( 'insert' ) );
        }

        public function onInsert( \Extasy\ORM\QueryBuilder $queryBuilder ) {
            if ( empty( $this->aValue ) ) {
                return;
            }

            foreach ( $this->aValue as $row ) {
                $row = IntegerHelper::toNatural( $row );
                DBSimple::insert( $this->tableName,
                                  array(
                                      $this->innerKey    => $this->document->id->getValue(),
                                      $this->foreighnKey => $row
                                  ) );
            }
        }

        public function onDelete( \Extasy\ORM\QueryBuilder $queryBuilder ) {
            DBSimple::delete( $this->tableName,
                              array(
                                  $this->innerKey => $this->document->id->getValue()
                              ) );
        }

        public function getViewValue() {
            return $this->aValue;
        }

        public function getAdminFormValue() {
            $view = new View( __DIR__ . DIRECTORY_SEPARATOR . 'HasMany/admin.tpl' );
            $view->set( 'name', $this->szFieldName );
            $view->set( 'values', $this->loadLinkedData() );
            return $view->render();
        }

        public function loadLinkedData() {
            $sql  = <<<SQL
		select %s as valueId, linkedModel.* from %s as linkedModel
		left join %s as map
		on linkedModel.id = map.%s and map.%s = "%d"
		order by linkedModel.name asc
SQL;
            $sql  = sprintf( $sql,
                             $this->innerKey,
                             call_user_func( array( $this->linkedModel, 'getTableName' ) ),
                             $this->tableName,
                             $this->foreighnKey,
                             $this->innerKey,
                             $this->document->id->getValue()
            );
            $data = DB::query( $sql );

            $result = array();
            foreach ( $data as $row ) {
                $model = new $this->linkedModel( $row );

                $result [ ] = array(
                    'checked' => !empty( $row[ 'valueId' ] ),
                    'model'   => $model->getParseData()
                );
            }
            return $result;
        }

        protected function loadData() {
            $data         = DBSimple::select( $this->tableName,
                                              array(
                                                  $this->innerKey => $this->document->id->getValue()
                                              ),
                                              'id asc' );
            $this->aValue = array();
            foreach ( $data as $row ) {
                $this->aValue[ ] = $row[ $this->foreighnKey ];
            }

        }

        public function onCreateTable( \Extasy\ORM\QueryBuilder $queryBuilder ) {
            $sql = <<<SQL
  create table %s (
    `id` int not null auto_increment,
    `%s` int not null default 0,
    `%s` int not null default 0,
    primary key (`id`),
    index `search` (`%s`)
  )

SQL;
            $sql = sprintf( $sql, $this->innerKey, $this->foreighnKey, $this->innerKey );
            DB::post( $sql );
        }
    }
}