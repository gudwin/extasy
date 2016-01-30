<?
$map = [
	'vkontakte' => 'vkontakteAuthHelper',
	'twitter' => 'twitterAuthHelper',
	'odnoklassniki' => 'odnoklassnikiAuthHelper'
];
$helperName = $map[ $type ];
?>
<!DOCTYPE html>
<html>
<head>
</head>
<body>
	<script type="text/javascript">
		var response = <?=json_encode( $response );?>

		window.opener.<?=($helperName)?>.authCallback( response );
		window.close();
	</script>
</body>
</html>

