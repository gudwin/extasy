<?php


namespace Extasy\tests\StaticApplication;

use Extasy\StaticApplication\Writer;
use \Faid\Configure\Configure;
class WriterTest extends BaseTest {
    const Fixture = 'Hello world';
    public function testOutputFolderConfigurable() {
        Configure::write( Writer::ConfigureKey, '');
        $result = SYS_ROOT . Writer::AppFolderName;
        $this->assertEquals( $result, Writer::getCacheFolderPath());

        $fixture = 'hello';
        Configure::write( Writer::ConfigureKey, $fixture );
        $this->assertEquals( $fixture, Writer::getCacheFolderPath());
    }
    public function testPathCreated() {
        $writer = new Writer('//aa/bb/cc');
        $writer->write( self::Fixture );

        $path = Writer::getCacheFolderPath() . 'aa/bb/cc';
        $this->assertTrue( file_exists( $path ));
        $contents = file_get_contents( $path );

        $this->AssertEquals( self::Fixture, $contents );
    }
    public function testIndexCreated() {
        $writer = new Writer('//1/2/');
        $writer->write( self::Fixture );
        $path = Writer::getCacheFolderPath() . '1/2/index.php';
        $this->assertTrue( file_exists( $path ));
        $contents = file_get_contents( $path );
        $this->AssertEquals( self::Fixture, $contents );
    }
    public function testUrlsSupported() {
        $writer = new Writer('http://3/4/5');
        $writer->write( self::Fixture );
        $path = Writer::getCacheFolderPath() . '3/4/5';
        $this->assertTrue( file_exists( $path ));
        $contents = file_get_contents( $path );
        $this->AssertEquals( self::Fixture, $contents );
    }
} 