<?
use \Extasy\CMS;
?>
		<div id="dateControl<?php print $name?>"></div>
		<script type="text/javascript" src="<?php print CMS::getResourcesUrl()?>extasy/js/user_controls/date.js"></script>
		<script type="text/javascript">
		$(function () {
			new dateControl({ name : "<?php print $name?>", value:"<?php print $value?>"});
		});
		</script>