<?
require_once LIB_PATH.'sitemap/additional/pages.php';
require_once LIB_PATH.'sitemap/additional/lang.php';
class Sitemap_GoFirstChild_Controller extends extasyPage {
	public function main()
	{
		
		$aInfo = \Extasy\sitemap\Route::getCurrentUrlInfo();
		$aMenu = Sitemap_PagesOperations::selectChildWithoutAdditional($aInfo['id'],1,0);
		$url = ''; 
		if (empty($aMenu))
		{
			// Получаем предка
			if (!empty($aInfo['parent']))
			{

				$aParent = Sitemap_Sample::get($aInfo['parent']);
				
				$url .= $aParent['full_url'];
				$this->jump($url);
			}
			else
			{
				die('Parent empty');
			}
		}
		$url .=$aMenu[0]['full_url'];
		$this->jump($url);
	}
}
?>