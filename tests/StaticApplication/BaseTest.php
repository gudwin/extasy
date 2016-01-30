<?php


namespace Extasy\tests\StaticApplication;

use Extasy\tests\BaseTest as ExtasyBaseTest;
use \DAO_FileSystem;
use Extasy\StaticApplication\Writer;
use \Faid\Configure\Configure;
abstract class BaseTest extends ExtasyBaseTest{
    const CacheFolder = 'tests/data/cache/';
    public function setUp() {
        parent::setUp();
        $path = EXTASY_PATH . self::CacheFolder;
        //
        $fs = DAO_FileSystem::getInstance();
        $fs->createPath( $path );
        //
        Configure::write(Writer::ConfigureKey, $path );
        //
        $this->cleanStaticApplicationCacheFolder();
    }
    public function tearDown() {
        parent::tearDown();
        $this->cleanStaticApplicationCacheFolder();
    }
    protected function cleanStaticApplicationCacheFolder() {
        $fs = DAO_FileSystem::getInstance();
        $list = $fs->getDirContent( EXTASY_PATH. self::CacheFolder );
        foreach ( $list as $fileName ) {
            $fs->delete( Writer::getCacheFolderPath() . $fileName );
        }
    }

} 