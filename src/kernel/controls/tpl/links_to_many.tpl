<div id="<? print $name ?>"></div>
<script type="text/javascript" src="<?php print \Extasy\CMS::getDashboardWWWRoot()?>extasy/js/user_controls/links_to_many.js"></script>
<script type="text/javascript">
	$(document).ready(function () { 
		var select_<?php $name?> = new LinksToManyControl({
			name : <?php print json_encode($name)?>,
			values : <?php print json_encode($values);?>,
			searchUrl : <?php print json_encode(\Extasy\CMS::getDashboardWWWRoot().$url)?>
			}); 
	});
</script>