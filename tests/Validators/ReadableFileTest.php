<?php


namespace Extasy\tests\Validators;

use \Extasy\Validators\ReadableFile;
use \DAO_FileSystem;
class ReadableFileTest extends \Extasy\tests\BaseTest {
	const Filename = '/filename';
	public function tearDown() {
		if ( file_exists( self::getPath() )) {
			unlink( self::getPath() );
		}
	}
	protected function getPath( ) {
		return __DIR__ . self::Filename;
	}
	public function testFolder( ) {
		$validator = new ReadableFile( $this->getPath() );
		$this->assertFalse( $validator->isValid() );
	}
	public function testNotReadableFile( ) {
		file_put_contents( $this->getPath(), '');

		$fs = new DAO_FileSystem();
		$fs->chmod( $this->getPath(), 0333);

		$validator = new ReadableFile( $this->getPath() );
		$this->assertFalse( $validator->isValid() );
	}
	public function testReadableFile( ) {
		file_put_contents( $this->getPath(), '');

		$fs = new DAO_FileSystem();
		$fs->chmod( $this->getPath(), 0777);

		$validator = new ReadableFile( $this->getPath() );
		$this->assertTrue( $validator->isValid() );
	}
} 