<?
use \Extasy\CMS;
use \Faid\DB;
use \Faid\Dispatcher\Dispatcher;
use \Faid\Request\Request;

require_once '../Vendors/Extasy/src/bootstrap.php';

$request    = new Request();
$dispatcher = new Dispatcher( $request );


new CMS( $dispatcher );

set_time_limit( 0 );
$nLevel = !empty($_GET['level'])?($_GET['level']):die('Setup level!');

require_once LIB_PATH.'kernel/cms/cms.php';
require_once LIB_PATH.'sitemap/additional/project_helper.php';
switch ($nLevel)
{
	case '1':

		DB::post('TRUNCATE `'.SITEMAP_TABLE.'`');
		DB::post('TRUNCATE `'.SITEMAP_SCRIPT_CHILD_TABLE.'`');

		break;
	case '-999': // Это блок для копипаста
		// Главная баннеры
		//Sitemap::addScript('Главная','scripts/index.php','',0,'index/page.php',1);

		//SiteMap::setupScriptChildDocuments('scripts/go-child.php',array('textpage'));

		//$sql = <<<EOD
		//	ALTER TABLE `textpage` 
		//	ADD COLUMN `text_class` varchar(255) not null,
		//	ADD COLUMN `background_class` varchar(255) not null,
		//	ADD COLUMN `hover_class` varchar(255) not null;
		//EOD;
		//DB::post($sql);
		
		//EventController::addEvent('get_portfolio_template_data');
		//EventController::addEventListener('get_portfolio_template_data','selectSizes','PortfolioTemplate','_lib/portfolio.php'); 


		break;
	default:
		$path = sprintf('%smigration/%s.php',SYS_ROOT,$nLevel);
		if ( file_exists( $path )) {
			include $path;
		} else {
			throw new NotFoundException( $path );
		}
		break;

}
printf('Level %s finished',$nLevel);
print '<br/>';
print '<a href="/admin/">Перейти в контрольную панель?</a>';
?>