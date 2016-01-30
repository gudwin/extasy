<?php
$initConfig = array(
	'targetId' => $name
);
if (!empty($currentUser)) {
	$initConfig['currentValue'] = $currentUser['id'];
	$initConfig['currentTitle'] = $currentUser['login'];
}
?>
<link rel="stylesheet" type="text/css" href="<?=\Extasy\CMS::getResourcesUrl()?>extasy/Dashboard/users/userselect.css" />
<div id="<?php print $name?>"></div>
<script type="text/javascript" src="<?=\Extasy\CMS::getResourcesUrl()?>extasy/Dashboard/users/userselect.js"></script>
<script type="text/javascript">
var <?php print $name?> = null;
jQuery(function () {
	<?php print $name?> = new UserSelect(<?php print json_encode($initConfig)?>);
});
</script>
