<?php
use \Extasy\CMS;
?>
	<div id="<?php print $name?>_image_target"></div>
	<script type="text/javascript" src="<?=CMS::getResourcesUrl()?>js/user_controls/image.js"></script>
	<script type="text/javascript">
	jQuery(function () {
		<?
		?>
		$('#<?php print $name?>_image_target').imageControl({
			path_to_fm : <?php print json_encode( '/extasy/Dashboard/fm/index.php');?>,
			name       : <?php print json_encode($name)?>,
			current    : <?php print json_encode($value)?>,
			image	   : <?php print json_encode($imageInfo)?>
		});
	});
	
	</script>
