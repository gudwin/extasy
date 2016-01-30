<?php


namespace Extasy\ORM;

use \Trace;
class DB extends \Faid\DB {
    static public function get( $sql ) {
        Trace::addMessage( __CLASS__, sprintf('Get:<br>%s',$sql));
        $result = parent::get( $sql );
        Trace::addMessage( __CLASS__, sprintf('Get finished'));
        return $result;
    }
    static public function post( $sql, $bIgnore = false ) {
        Trace::addMessage( __CLASS__, sprintf('Post:<br>%s', $sql ));
        $result = parent::post( $sql, $bIgnore );
        Trace::addMessage( __CLASS__, sprintf('Post finished'));
        return $result;
    }
    static public function query( $sql, $bUserFetchAssoc = true ) {
        Trace::addMessage( __CLASS__, sprintf('Query:<br>%s', $sql ));
        $result = parent::query( $sql, $bUserFetchAssoc );
        Trace::addMessage( __CLASS__, sprintf('Query finished'));
        return $result;
    }
    static function getField( $sql, $field ) {
        Trace::addMessage( __CLASS__, sprintf('Getting field:"%s", with query:<br>%s', $field, $sql ));
        $result = parent::getField( $sql, $field );
        Trace::addMessage( __CLASS__, sprintf('getField Finished'));
        return $result;
    }
    static public function getAutoIncrement( $table ) {
        Trace::addMessage( __CLASS__, sprintf('Getting auto_increment from table:"%s"', $table));
        $result = parent::getAutoIncrement( $table );
        Trace::addMessage( __CLASS__, sprintf('Auto_increment fetched'));
        return $result;
    }
} 