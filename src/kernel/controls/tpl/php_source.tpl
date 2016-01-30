<?php
use \Extasy\CMS;
// В данном шаблоне, формируется вывод php-контрола для редактирования сорцов
$scripts = array(
		'lib/codemirror.js',
		'lib/util/dialog.js',
		'lib/util/search.js',
		'lib/util/foldcode.js',
		'lib/util/matchbrackets.js',
		'mode/htmlmixed/htmlmixed.js',
		'mode/xml/xml.js',
		'mode/javascript/javascript.js',
		'mode/css/css.js',
		'mode/php/php.js',
		'mode/clike/clike.js'
		) 
?>
<script type="text/javascript" src="<?php print CMS::getDashboardWWWRoot()?>extasy/js/user_controls/phpSource.js"></script>
<link rel="stylesheet" href="<?php print CMS::getDashboardWWWRoot()?>extasy/vendors/codeMirror/lib/codemirror.css">

<?php
foreach ( $scripts as $row ) {
	$path = sprintf( '%sextasy/vendors/codeMirror/%s', CMS::getDashboardWWWRoot(), $row );
	printf( "<script src=\"%s\"></script>", $path );	
} 
?>
<div class="phpSource">
	<textarea name="<?php print $name?>" id="phpsource-<?php print $name?>"><?php print htmlspecialchars($phpSource);?></textarea>	
</div>
<script type="text/javascript">
jQuery(function () {
	initPhpSource("phpsource-<?php print $name?>",<?php print json_encode($initData)?>);
});

</script>