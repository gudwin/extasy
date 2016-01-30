<?php

class basicTestDocument extends \Extasy\Model\Model {
	const ModelName = 'basicTestDocument';

	protected function check() {
		if ( isset( $this->aData[ 'name' ] ) && ( $this->aData[ 'name' ] == 'check failed' ) ) {
			return false;
		}
		return true;
	}

	public static function getFieldsInfo() {
		// Устанавливаем тип
		return array(
			'table'  => 'basic_document',
			'fields' => array(
				'id'      => '\\Extasy\\Columns\\Index',
				'name'    => array(
					'class'  => '\\Extasy\\Columns\\Input',
					'title' => 'Базовый тайтл',
				),
				'content' => array(
					'class'  => '\\Extasy\\Columns\\Html',
					'title' => ''
				),
			),
		);

	}
}