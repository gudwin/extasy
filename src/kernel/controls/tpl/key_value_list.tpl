<div id="keyValueList<? print $name?>"></div>
<script type="text/javascript" src="<?php print \Extasy\CMS::getDashboardWWWRoot()?>extasy/js/user_controls/key_value_list.js"></script>
<script type="text/javascript">
	jQuery(document).ready(function () {
		var list = new KeyValueList({
			name 		: <?php print json_encode($name)?>,
			title 		: <?php print json_encode($title)?>,
			values		: <?php print json_encode($values)?>,
			renderTo	: $('#keyValueList<?php print ($name)?>').get(0) 
			}); 
	});
</script>