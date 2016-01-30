<link href="<?=\Extasy\CMS::getResourcesUrl()?>extasy/css/dashboard_toolbar.css" rel="stylesheet" />
<!-- PLACE JS HERE, PLEASE--><script type="text/javascript" src="/resources/extasy/DashboardMenu/audit.js"></script><script type="text/javascript" src="/resources/extasy/DashboardMenu/search.js"></script><!-- THANK YOU FOR JS -->
<div class="DashboardMenu bootstrap">
	<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
		<div class="navbar-inner">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#dashboard-nav">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
			</div>
			<div class="collapse navbar-collapse" id="dashboard-nav">
				<ul class="nav navbar-nav navButtons">
					<? if ( !empty( $adminUrl )):?>
						<li><a href="<?=$adminUrl?>" class="button">Редактировать</a></li>
					<? endif;?>

					<? if ( !empty( $tplFile )):?>
						<li><a href="<?=$tplFile?>" class="button">Шаблон</a></li>
					<? endif;?>

					<? if ( !empty( $traceOutput )):?>
						<li><a href="#" onclick="showTrace();return false; "class="button">Отладка</a></li>
					<? endif;?>
					<? if ( !empty( $auditMessages)):?>
						<li class="audit"><a href="<?=\Extasy\CMS::getDashboardWWWRoot()?>administrate/audit">Аудит</a></li>
					<? endif;?>
					<? if ( !empty($menuItems)):?>
					<?=$this->menuRenderer->renderMenuItemsRecursive( $menuItems  )?>

					<?endif?>
				</ul>

				<ul class="nav navbar-nav navbar-right">
                    <li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown"><?=$currentUser['login']?><b class="caret"></b></a>
						<ul class="dropdown-menu">
							<? if ( !empty( $inDashboard)):?>

								<li><a href="<?=\Extasy\CMS::getWWWRoot()?>">На главную</a></li>
							<? else:?>

							<li><a href="<?=\Extasy\CMS::getDashboardWWWRoot()?>">В админку</a></li>
							<? endif?>

							<? if ( !empty( $showAdministrativeMenu)):?>
							<li class="divider"></li>
								<?
								$administrativeScripts = array(
									array('href' => \Extasy\CMS::getDashboardWWWRoot() . 'administrate/security', 'name' => 'Общая безопасность'),
									array('href' => \Extasy\CMS::getDashboardWWWRoot() . 'administrate/audit', 'name' => 'Журнал аудита'),
									array('href' => \Extasy\CMS::getDashboardWWWRoot() . 'cconfig/index.php', 'name' => 'Конфиги'),
									array('href' => \Extasy\CMS::getDashboardWWWRoot() . 'administrate/acl.php', 'name' => 'Права'),
									array('href' => \Extasy\CMS::getDashboardWWWRoot() . 'administrate/sql.php', 'name' => 'SQL-консоль'),
									array('href' => \Extasy\CMS::getDashboardWWWRoot() . 'administrate/schedule', 'name' => 'Очередь задач'),
									array('href' => \Extasy\CMS::getDashboardWWWRoot() . 'users/index.php', 'name' => 'Пользователи'),
									array('href' => \Extasy\CMS::getDashboardWWWRoot() . 'users/group_permissions/', 'name' => 'Установка прав на группу'),
									array('href' => \Extasy\CMS::getDashboardWWWRoot() . 'email/index.php', 'name' => 'Почта'),
									array('href' => \Extasy\CMS::getDashboardWWWRoot() . 'administrate/regedit.php', 'name' => 'Редактор реестра'),
									array('href' => \Extasy\CMS::getDashboardWWWRoot() . 'administrate/setup_events.php', 'name' => 'События'),
									array('href' => \Extasy\CMS::getDashboardWWWRoot() . 'administrate/testSuite/index.php', 'name' => 'Тесты'),
									array('href' => \Extasy\CMS::getDashboardWWWRoot() . 'administrate/template-manager.php', 'name' => 'Шаблоны'),
								);
								foreach ( $administrativeScripts as $row) {
									printf('<li><a href="%s">%s</a></li>', $row['href'], htmlspecialchars($row['name']));
								}
								?>
							<? endif?>

					<li class="divider"></li>
							<li><a href="<?=\Extasy\CMS::getDashboardWWWRoot()?>logout.php">Выйти</a></li>
						</ul>
					</li>
				</ul>
                <form class="navbar-form navbar-right" role="search" id="dashboardSearch" ng-controller="Search">
                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="Поиск" ng-model="search"
                               typeahead="match as match.title for match in getSearchResults($viewValue)"
                               typeahead-template-url="search-item.html"
                               typeahead-loading="loadingLocations"
                            >
                        <i ng-show="loadingLocations" class="glyphicon glyphicon-refresh"></i>
                    </div>
                    <script type="text/ng-template" id="search-item.html">
                        <a ng-href="{{match.model.link}}">
                            <span bind-html-unsafe="match.model.title | typeaheadHighlight:query"></span>
                        </a>
                    </script>
                </form>
            </div>
		</div>
	</nav>
</div><!-- / dashboard toolbar container -->
