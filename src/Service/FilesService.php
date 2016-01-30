<?php

namespace Extasy\Service;


class FilesService {
	const FileMap = 'application/cache/production/file_map.json';

	protected $filesMap = [];
	protected function getCheckSum( $path ) {
		$path = SYS_ROOT . $path;
		$validator = new \Extasy\Validators\ReadableFile( $path );
		if ( !$validator->isValid() ) {
			throw new \NotFoundException("Path - \"{$path}\" not readable");
		}
		return md5_file( $path );
	}
	protected function getMapPath() {
		return SYS_ROOT . self::FileMap;
	}
} 