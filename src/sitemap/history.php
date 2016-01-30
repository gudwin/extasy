<?
use \Faid\DB;
use \Faid\DBSimple;
//************************************************************//
//                                                            //
//             История изменения урлов                        //
//       Copyright (c) 2008 Ext-CMS (http://www.ext-cms.com/) //
//               отдел/сектор                                 //
//       Email:    info@gisma.ru (http://www.gisma.ru/)       //
//                                                            //
//  Разработчик: Gisma (12.12.2008)                           //
//  Модифицирован:  12.12.2008  by Gisma                      //
//                                                            //
//************************************************************//

class Sitemap_History
{
	/**
	*   -------------------------------------------------------------------------------------------
	*   Добавление истории дата
	*   @return
	*   -------------------------------------------------------------------------------------------
	*/
	public static function add($sitemap_id,$szName)
	{
		$sitemap_id = intval($sitemap_id);
		$szName = \Faid\DB::escape($szName);
		//
		$sql = 'SELECT * FROM `%s` WHERE id="%d"';
		$sql = sprintf($sql,SITEMAP_TABLE,$sitemap_id);

		$aFound = DB::Get($sql);
		if (empty($aFound))
		{
			throw new SitemapException('Page with id="'.htmlspecialchars($sitemap_id).'" not found');
		}

		//
		$sql = 'INSERT INTO `%s` SET `date`=NOW(),`name`="%s",`url`="%s",`page_id`="%d"';
		$sql = sprintf($sql,
			SITEMAP_HISTORY_TABLE,
			\Faid\DB::escape($szName),
			\Faid\DB::escape($aFound['full_url']),
			$sitemap_id);
		//
		DB::post($sql);
	}
	/**
	*   -------------------------------------------------------------------------------------------
	*   Возвращаем историю
	*   @return
	*   -------------------------------------------------------------------------------------------
	*/
	public static function foundByUrl($szUrl)
	{
		$sql = 'SELECT * FROM `%s` WHERE STRCMP(`url`,"%s") = 0 ORDER by `id` DESC LIMIT 0,1';
		$sql = sprintf($sql,
			SITEMAP_HISTORY_TABLE,
			$szUrl);
		$aFound = DB::get($sql);
		
		if (!empty($aFound))
		{
			$aFound = Sitemap_Sample::get($aFound['page_id']);
			if (!empty($aFound))
			{
				return $aFound['full_url'];
			}
			else
			{
				throw new SiteMapException('Page not found. Page="'.htmlspecialchars($aFound['page_id']).'"');
			}
		}
		else
		{
			throw new SiteMapException('Url not found. Url="'.htmlspecialchars($szUrl).'"');
		}
		
	}
	/**
	 * Добавляет к указанной странице доп. урл - алиас
	 * @param unknown_type $id
	 * @param unknown_type $newUrl
	 */
	public static function addAlias($id,$newUrl) {
		// Удаляем из алиасов все урлы, которые совпадают, новый урл их заместит
		DBSimple::delete(SITEMAP_HISTORY_TABLE,array(
			'url' => $newUrl
		));
		$page = Sitemap_Sample::get($id);
		if (empty($page)) {
			throw new SiteMapException('addAlias failed. Page with id="'.$id.'" not found');
		}
		$sql = 'INSERT INTO `%s` SET `date`=NOW(),`name`="%s",`page_id`="%s",`url`="%s"';
		$sql = sprintf($sql,
			SITEMAP_HISTORY_TABLE,
			\Faid\DB::escape($page['name']),
			$page['id'],
			\Faid\DB::escape($newUrl));
		DB::post($sql);
	}
	/**
	 * Возвращает все алиасы (пред версии урлов)
	 * @param int $id
	 */
	public static function selectById($id) {
		$id = IntegerHelper::toNatural($id);
		$sql = 'select * from `%s` where `page_id`="%d" order by `id` DESC';
		$sql = sprintf($sql,
			SITEMAP_HISTORY_TABLE,
			$id);
		$dbResult = DB::query($sql);
		return $dbResult;
	}
	public static function deleteAlias($id,$url) {
		$id = IntegerHelper::toNatural($id);
		$sql = 'DELETE FROM `%s` WHERE `page_id`="%d" and `url`="%s" ';
		$sql = sprintf($sql,
			SITEMAP_HISTORY_TABLE,
			$id,
			\Faid\DB::escape($url)
		);
		DB::post($sql);
	}
	public static function deleteAliasById($id,$aliasId) {
		DBSimple::delete( SITEMAP_HISTORY_TABLE, [
			'page_id' => IntegerHelper::toNatural( $id ),
			'id' => IntegerHelper::toNatural( $aliasId )
		]);
	}
}
?>