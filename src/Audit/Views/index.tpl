<?php

?>
<div class="bootstrap audit">
	<nav class="navbar navbar-default" ng-controller="NavigateController">
		<ul class="nav navbar-nav nav-pills">
			<li ng-class="{ active : 'records' == menu.activeRoute}">
				<a href="#/records">Записи аудита</a>
			</li>
			<li ng-class="{ active : 'events' == menu.activeRoute}">
				<a href="#/events">Все события</a>
			</li>
			<? if ( \CMSAuth::getInstance()->isSuperAdmin( \UsersLogin::getCurrentSession() )):?>

			<li ng-class="{ active : 'settings' == menu.activeRoute}">
				<a href="#/settings">Настройка</a>
			</li>
			<?endif;?>
		</ul>
		<form class="navbar-form navbar-left" role="search" ng-submit="onSearch()">
			<div class="form-group">
				<input type="text" class="form-control" placeholder="Search" ng-model="searchRequest.search_phrase">
			</div>
			<button type="submit" class="btn btn-default">Submit</button>
		</form>
	</nav>
	<div class="view-container">
		<div ng-view class="view-frame">
			<div class="loading"><!-- --></div>
		</div>
	</div>
</div>
<!-- PLACE JS HERE, PLEASE--><script type="text/javascript" src="/resources/extasy/Dashboard/administrate/audit/Application.js"></script><script type="text/javascript" src="/resources/extasy/Dashboard/administrate/audit/NavigateController.js"></script><script type="text/javascript" src="/resources/extasy/Dashboard/administrate/audit/RecordsController.js"></script><script type="text/javascript" src="/resources/extasy/Dashboard/administrate/audit/EventsController.js"></script><script type="text/javascript" src="/resources/extasy/Dashboard/administrate/audit/SettingsController.js"></script><!-- THANK YOU FOR JS -->