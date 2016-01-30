<?php
use \Faid\DB;
class sitemapPageIdColumn extends \Extasy\Columns\BaseColumn  {
	public function __construct( $fieldName, $fieldInfo, $value) {
		parent::__construct( $fieldName, $fieldInfo, intval( $value ));
	}
	public function onAfterSelect( $dbData ) {
		if ( isset( $dbData[$this->szFieldName ])) {

			$this->aValue = intval( $dbData[$this->szFieldName]);
		}
	}
	public function getAdminFormValue() {
		
		$control = new CSitemapSelectOnce();
		if ( !empty( $this->fieldInfo['filter'])) {
			$control->setFilter( $this->fieldInfo['filter']);
		}
		$control->name = $this->szFieldName;
		$control->value = $this->aValue;

		return $control->generate();
	}
	public function getValue() {
		return $this->aValue;
	}
	/**
	*   @desc
	*   @return
	*/
	public function getViewValue() {
		$sitemapInfo = Sitemap_Sample::get( $this->aValue);
		if ( !empty( $sitemapInfo )) {
			return $sitemapInfo['name'];
		}
	}
	public function getAdminViewValue() {
		$sitemapInfo = Sitemap_Sample::get( $this->aValue);
		$result = sprintf(' %s <a href="http:%s" target="_blank">[На сайте]</a>', $sitemapInfo['name'], $sitemapInfo['full_url']);
		$result .= sprintf( ' <a href="%ssitemap/edit.php?id=%d" target="_blank">[К администрированию]</a>',
			\Extasy\CMS::getDashboardWWWRoot(),
			$sitemapInfo['id']
		);
		return $result;
	}

	/**
	 * @return \RegisteredDocument
	 */
	public function getModel() {
		return RegisteredDocument::autoload( $this->aValue );

	}
	public function getPreviewParseData() {
		$document = $this->getModel();
		if  ($document->id->getValue() > 0 ) {
			$result = $document->getSitemapData();
			$result['additional'] = $document->getPreviewParseData();
			return $result;
		}
	}
	public function onCreateTable( \Extasy\ORM\QueryBuilder $queryBuilder ) {
		$queryBuilder->addFields( sprintf( '`%s` int not null default 0', $this->szFieldName ) );

	}
}
?>