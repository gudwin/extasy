<?php

namespace Extasy\Collections;
use \Faid\DB;

class ModelCollection {
	protected $data = array();
	protected $sitemapInfoMap = array();
	protected $modelName = '';

	/**
	 * @param array $data
	 * @param string $modelName
	 */
	public function __construct( $data, $modelName ) {
		$this->data = $data;
		$this->modelName = $modelName;
	}

	/**
	 * Loads sitemap data for collected data
	 */
	public function loadSitemapData( ) {
		if ( empty( $this->data))  { return ;}
		//
		$sqlFilter = array();
		foreach ($this->data as $row) {
			$sqlFilter[] = intval($row['id']);
		}
		//
		$sql = 'SELECT * FROM `%s` WHERE `document_id` IN (%s) and STRCMP(`document_name`,"%s") = 0 and visible = 1';
		$sql = sprintf($sql,
			SITEMAP_TABLE,
			implode($sqlFilter,','),
			$this->modelName);
		$tmp = DB::query($sql);
		$this->sitemapInfoMap = array();
		foreach ($tmp as $row) {
			$this->sitemapInfoMap[$row['document_id']] = $row;
		}
		return $this;
	}

	/**
	 *
	 */
	public function getParseData( ) {
		$result = array();
		$models = $this->prepareModels( );
		foreach ( $models as $row ) {
			$result[] = $row->getPreviewParseData();
		}
		return $result;
	}

	/**
	 *
	 */
	public function getPreviewParseData( ) {
		$result = array();
		$models = $this->prepareModels( );
		foreach ( $models as $row ) {
			$result[] = $row->getPreviewParseData();
		}
		return $result;
	}
	private function prepareModels( ) {
		$result = array();
		//
		foreach ( $this->data as $modelData ) {
			$sitemapData = isset( $this->sitemapInfoMap[$modelData['id']] ) ? $this->sitemapInfoMap[$modelData['id']] : null;

			if ( !is_null( $sitemapData )) {
				$result[] = new $this->modelName( $modelData, $sitemapData);
			} else {
			}

		}
		//
		return $result;
	}
}