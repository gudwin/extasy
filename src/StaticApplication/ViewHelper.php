<?php


namespace Extasy\StaticApplication;


class ViewHelper {
    public static function safeInclude( $path ) {
        $path = SYS_ROOT . Writer::AppFolderName . $path;
        if ( file_exists( $path )) {
            include $path;
        }
        return '';
    }
    public static function getInclude() {

    }
    public static function getRequire() {

    }
} 