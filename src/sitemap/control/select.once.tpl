<?
use \Extasy\CMS;
$config = array(
	'name' => $name,
	'urlInfo' => $urlInfo,
	'filter' => $filter
);
?>
<div id="sitemapOnce<? print $name?>"><!-- --></div>
<link rel="stylesheet" href="<?php print CMS::getResourcesUrl()?>extasy/css/user_controls/sitemap.once.css" type="text/css" media="screen" />
<script type="text/javascript" src="<?php print CMS::getResourcesUrl()?>extasy/js/user_controls/sitemap.once.js"></script>
<script type="text/javascript">
	jQuery(function () {
		var $ = jQuery;
		$('#sitemapOnce<? print $name?>').sitemapOnce(<?=json_encode( $config )?>);
	});
</script>