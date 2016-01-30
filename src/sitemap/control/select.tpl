<div id="<? print $name ?>"></div>
<script type="text/javascript" src="<?php print \Extasy\CMS::getResourcesUrl()?>extasy/js/user_controls/sitemap_select.js"></script>
<script type="text/javascript">
	$(document).ready(function () { 
		var select_<?php $name?> = new SitemapSelectControl({
			name : <?php print json_encode($name)?>,
			values : <?php print json_encode($values);?>,
			searchUrl : <?php print json_encode(\Extasy\CMS::getDashboardWWWRoot().'sitemap/search.php')?>
			}); 
	});
</script>