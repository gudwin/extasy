<?php
namespace Extasy\Columns;


class FilePath extends Input {
	public function setValue( $newValue ) {
		$this->aValue = realpath( $newValue );
	}
	public function getAdminViewValue() {
		if ( empty( $this->aValue ) || !file_exists( $this->aValue )) {
			return '';
		}
		$validator = new \Faid\Validators\FileInSecuredFolder( FILE_PATH );
		if ( !$validator->isValid( $this->aValue )) {
			return '';
		}
		$validator = new \Faid\Validators\FileInSecuredFolder( WEBROOT_PATH );
		$linkText = $validator->getOffset( $this->aValue );

		return sprintf( '<a href="/%s" target="_blank">%s</a>', $linkText, $linkText);
	}
} 