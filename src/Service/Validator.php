<?php
namespace Extasy\Service {
	class Validator extends FilesService {
		public function testFiles() {
			$this->loadMap();
			$failed = false;
			foreach ( $this->filesMap as $row ) {
				try {
					if ( $this->getCheckSum( $row[0]) != $row[1]) {
						printf( "\e[0;31m[FAILURE Invalid checksum]\e[0m '%s'\r\n", $row[0]);
						$failed = true;
					}
				} catch (\NotFoundException $e ) {
					printf("\e[0;31m[FAILURE Path not found]\e[0m `%s`\r\n", $row[0]);
					$failed = true;
				}
			}
			if ( !$failed ) {
				printf("[SUCCESS] All files valid\r\n");
			}
		}
		public function csv() {
			$this->loadMap();
			$reportPath = sprintf('%sFilesSecurityMap_%s.csv',FILE_PATH, date('Y-m-d_H:i:s'));
			$handler = fopen( $reportPath, 'w' );

			$header = array('Файл', 'Контрольная сумма MD5');
			$this->appendToCSV( $handler, $header );
			foreach ( $this->filesMap as $row ) {
				$this->appendToCSV( $handler, $row );
			}
			fclose( $handler );
			printf('File saved to "%s"', $reportPath);
		}
		protected function loadMap() {
			$validator = new \Extasy\Validators\ReadableFile( $this->getMapPath() );
			if ( !$validator->isValid() ) {
				print '[FAILURE] file map file not readable, try to fixture files first';
			}
			$this->filesMap = json_decode( file_get_contents($this->getMapPath() ));
		}
		public function appendToCSV( $handler, $data ) {
			array_map( function ( $item ) {
				iconv( 'utf-8', 'windows-1251',$item);
			}, $data );
			fputcsv( $handler, $data, ';' );
		}
	}
}