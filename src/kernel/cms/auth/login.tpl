<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN""http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" lang="ru" xml:lang="ru">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title><?php print SITE_NAME ?> &rsaquo; Вход</title>
	<link rel="stylesheet" type="text/css" href="<?= \Extasy\CMS::getResourcesUrl() ?>extasy/css/auth/style.css"/>
</head>
<body>
<div id="Enter">
	<form method="post" action="">
		<h1> Здравствуйте! </h1>
		<?php if ( !empty( $techMessage ) ): ?>
			<h2><?php print $techMessage; ?></h2>
		<?php endif; ?>
		<div>
			<input type="text" name="login" autofocus/> &nbsp;&nbsp;Логин<br/>
			<input type="password" name="password"/> &nbsp;&nbsp;Пароль<br/>
			<input type="submit" name="sm" value="Войти" style="width: 252px;"/>
			<input type="hidden" name="cms_auth" value="1"/>
		</div>

	</form>
</div>
<div class="Footer" style="position: absolute; top: 95%; width: 98%;">Система управления Belsmi.by</div>
</body>
</html>