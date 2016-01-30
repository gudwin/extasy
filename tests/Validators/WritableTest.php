<?php


namespace Extasy\tests\Validators;

use \Extasy\Validators\WritableFile;
use \DAO_FileSystem;
class WritableTest extends \Extasy\tests\BaseTest {
	const TestFolderName = '/writabletests/';

	const FolderNameFixture = 'my_folder';
	const FileNameFixture = 'filename';
	/**
	 * @var \DAO_FileSystem
	 */
	protected $fs;

	public function setUp( ) {
		$this->fs = DAO_FileSystem::getInstance();
		if ( file_exists( self::getBasePath() )) {
			$this->fs->delete( self::getBasePath() );
		}
		$this->fs->createPath( self::getBasePath() );
		$this->fs->chmod( self::getBasePath() );
	}
	public function tearDown( ) {
		$this->fs->delete( self::getBasePath() );
	}

	public function testWritableDirectory() {
		$folderPath = self::getBasePath() . self::FolderNameFixture ;
		$this->fs->createPath( $folderPath );
		$this->fs->chmod( $folderPath );

		$validator = new WritableFile( $folderPath );
		$this->AssertFalse( $validator->isValid() );
	}


	public function testWritableFile( ) {
		file_put_contents( self::getFilePath(), '');

		$validator = new WritableFile( self::getFilePath()  );
		$this->AssertTrue( $validator->isValid() );
	}
	public function testNotWritableFile() {
		file_put_contents( self::getFilePath(), '');
		$this->fs->chmod( self::getFilePath(), 0111);
		$validator = new WritableFile( self::getFilePath()  );
		$this->AssertFalse( $validator->isValid() );
	}
	public function testNonExistsFileInWritableFolder( ) {
		$validator = new WritableFile( self::getFilePath() );
		$this->AssertTrue( $validator->isValid() );
	}
	public static function getBasePath( ) {
		return __DIR__ . self::TestFolderName;
	}
	public static function getFilePath( ) {
		return self::getBasePath() . self::FileNameFixture;
	}
} 