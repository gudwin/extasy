<?php


namespace Extasy\ORM;

use Trace;

class DBSimple extends \Faid\DBSimple {

    public static function select($table, $where = '', $order = '') {
        Trace::addMessage(__CLASS__, sprintf( 'Select from table:"%s" with condition:"%s" and order:"%s"', $table, print_r( $where, true), $order) );
        $result = parent::select( $table, $where, $order );
        Trace::addMessage(__CLASS__, 'Finished selecting from: '. $table );
        return $result;
    }
    public static function get($table, $condition, $order = '') {
        Trace::addMessage(__CLASS__, sprintf('Get from table:"%s" with condition:"%s" and order:"%s"', $table, print_r( $condition, true),$order));

        $result = parent::get( $table, $condition, $order );
        Trace::addMessage(__CLASS__, sprintf('Getting from "%s" finished', $table));
        return $result;
    }
    public static function insert($table, $data) {
        Trace::addMessage(__CLASS__, sprintf('Inserting into table:"%s" with data:"%s"', $table, print_r( $data, true)));
        $result = parent::insert( $table, $data );
        Trace::addMessage(__CLASS__, sprintf('Insert into %s finished', $table));
        return $result;
    }
    public static function update($table, $setCondition, $whereCondition) {
        Trace::addMessage(__CLASS__, sprintf('Updating table:"%s" with setCondition:"%s" and whereCondition:"%s"', $table, print_r($setCondition,true), print_r( $whereCondition, true)));
        $result = parent::update( $table, $setCondition, $whereCondition );
        Trace::addMessage(__CLASS__, sprintf( 'Update of %s finished', $table ));
        return $result;
    }
    public static function delete($table, $whereCondition) {
        Trace::addMessage(__CLASS__, sprintf('Delete from table:"%s", condition:"%s" ', $table, print_r( $whereCondition, true)));
        $result = parent::delete( $table, $whereCondition);
        Trace::addMessage(__CLASS__, 'deletion from "'.$table.'" finished');
        return $result;
    }
    public static function getRowsCount($table, $condition = array()) {
        Trace::addMessage(__CLASS__, sprintf( 'getRowsCount. Table:"%s", $condition:%s', $table, print_r( $condition,true)));
        $result = parent::getRowsCount( $table, $condition );
        Trace::addMessage(__CLASS__, 'Rows counting finished');
        return $result;
    }
} 