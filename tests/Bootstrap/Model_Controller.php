<?php
namespace Extasy\tests\Bootstrap {
    class Model_Controller extends \SitemapController {
        protected static $called = false;

        public function show( $id ) {
            self::$called = true;

        }
        public static function isCalled() {
            return self::$called;
        }
    }
}