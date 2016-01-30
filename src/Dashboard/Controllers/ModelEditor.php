<?php

namespace Extasy\Dashboard\Controllers;


class ModelEditor extends \CMS_DataPage {
    const UrlKey = './edit';
    protected $listUrl = '';
    protected $listClassName = '';
    protected $modelName = '';
    protected $model = null;

    public function __construct() {

        parent::__construct();

        $this->addGet( 'id', 'showEdit' );
        $this->addPost( 'id,fieldName,action', 'ajaxEditColumn' );
        $this->addPost( 'id,fieldName,action,data', 'ajaxEditColumn' );
        $this->addPost( 'id', 'postEdit' );
        $this->addGet( 'create', 'add' );
    }

    public function add() {

        $this->model = new $this->modelName();
        $this->model->insert();

        $this->jump( $this->generateEditUrl() );
    }

    public function showEdit( $nId ) {

        $nId = intval( $nId );

        $this->model = new $this->modelName;
        $found       = $this->model->get( $nId );

        if ( empty( $found ) ) {
            $this->addError( 'Item with id="' . $nId . '" not found' );
            $this->jump( $this->generateBackUrl() );
        }
        if ( empty( $this->typeInfo[ 'edit_fields' ] ) ) {
            $this->model->getAdminUpdateForm();
            parent::output();
        } else {
            // Вывод информации
            $szTitle                                                                             = 'Редактирование документа';
            $this->viewBeginPath[ $this->model->getLabel( \Extasy\Model\Model::labelAllItems ) ] = $this->generateBackUrl();
            $this->viewBeginPath[ $szTitle ]                                                     = '#';
            //
            $this->output( $this->model, $szTitle, true );
        }


    }

    public function postEdit( $id ) {
        $this->model = new $this->modelName();
        $this->model->get( $id );
        $this->model->updateFromPost( $_POST );

        $this->addAlert( _msg( 'Документ был успешно обновлен' ) );
        $this->nId = $id;
        $jumpTo    = $this->generateEditUrl();
        $this->jump( $jumpTo );
    }

    public function ajaxEditColumn( $type, $id, $columnName, $action, $data = array() ) {
        $id          = intval( $id );
        $this->model = new $this->modelName();
        $found       = $this->model->get( $id );
        if ( !$found ) {
            $error = sprintf( ' Document <%s,%s> not found', $type, $id );
            throw new \Exception( $error );
        }
        //
        $column   = $this->model->attr( $columnName, true );
        $response = $column->ajaxCall( $action, $data );
        print json_encode( $response );
        die();
    }

    protected function setupModelName( $modelName ) {
        $this->typeInfo = call_user_func( array( $this->modelName, 'getFieldsInfo' ) );
    }

    protected function generateEditUrl() {
        $result = static::UrlKey . '?id=' . $this->model->id->getValue();
        return $result;
    }

    protected function generateBackUrl() {
        return $this->listUrl;
    }

}