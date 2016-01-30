<?
use \Extasy\CMS;
$constants = [
	'statuses' => $statuses
];
?>
<script type="text/javascript">
	var appConstants = <?=json_encode( $constants );?>;
</script>
<script type="text/javascript" src="<?=CMS::getResourcesUrl() . 'extasy/js/extasyApi.js'?>"></script>
<script type="text/javascript" src="<?=CMS::getResourcesUrl() . 'extasy/Schedule/LatestsTasksApp.js'?>"></script>

<div class="schedule-app bootstrap">
    <nav class="navbar navbar-default" role="navigation">
        <div class="container-fluid">
            <div class="collapse navbar-collapse" >
                <ul class="nav navbar-nav">
                    <li class="active"><a href="#/latests">Очередь</a></li>
                    <li><a href="#/add">Добавить задачу</a></li>
                </ul>

            </div>
        </div>
    </nav>
    <div ng-view>

    </div>
</div>