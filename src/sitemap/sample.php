<?
use \Faid\DB;
use \Faid\DBSimple;
//************************************************************//
//                                                            //
//            Класс выборки по документам в системе           //
//       Copyright (c) 2008 Ext-CMS (http://www.ext-cms.com/) //
//               отдел/сектор                                 //
//       Email:    info@gisma.ru (http://www.gisma.ru/)       //
//                                                            //
//  Разработчик: Gisma (19.10.2008)                           //
//  Модифицирован:  19.10.2008  by Gisma                      //
//                                                            //
//************************************************************//
//  Новый метод - поиск по сохраненным документам Sitemap     //
//  Gisma - 03.12.2008                                        //
//************************************************************//

class Sitemap_Sample
{
	protected static $nItemCount = 0;
	public static function selectChild($nId)
	{
		$nId = intval($nId);
		//
		$sql = 'SELECT * FROM `%s` WHERE `parent`="%d" ORDER by `order` ASC';
		$sql = sprintf($sql,
			SITEMAP_TABLE,
			$nId);
		return DB::query($sql);
	}
	public static function selectChildDocumentId($nId,$szDocument = '')
	{
		$nId = intval($nId);
		$szDocument = \Faid\DB::escape($szDocument);
		//
		$sql = 'SELECT * FROM `%s` WHERE `parent`="%d" ';
		if (!empty($szDocument))
		{
			$sql .= ' and STRCMP(`document_name`,"%s")=0 ';
		}
		$sql .= 'ORDER by `order` ASC';
		$sql = sprintf($sql,
			SITEMAP_TABLE,
			$nId,
			$szDocument);
		return DB::query($sql);
	}
	/**
	*   -------------------------------------------------------------------------------------------
	*   Отыскивает скрипт по пути к нему
	*   @return
	*   -------------------------------------------------------------------------------------------
	*/
	public static function getScript($szPath,$szUrl = '')
	{
		$szPath = \Faid\DB::escape($szPath);
		$szUrl = \Faid\DB::escape($szUrl);
		
		$condition = array(
			sprintf( 'STRCMP(`script`,"%s") = 0', $szPath )   
		);
		if ( !empty( $szUrl)) {
			$condition[] = sprintf( 'STRCMP(`url_key`,"%s") = 0', $szUrl );
		}
		return DBSimple::get( SITEMAP_TABLE, $condition );
	}
	/**
	 * Возвращает информацию о скрипте на основании его файла и пути для редактирования в админке
	 */
	public static function getScriptByAdminInfo($szPath,$szAdminUrl = '')
	{
		$szPath = \Faid\DB::escape($szPath);
		$szAdminUrl = \Faid\DB::escape($szAdminUrl);
		
		$condition = array(
			sprintf('STRCMP(`script`,"%s") = 0',$szPath)
		);
		if (!empty($szAdminUrl)) {
			$condition[] =  sprintf('STRCMP(`script_admin_url`,"%s") = 0',$szAdminUrl);
		}
		return DBSimple::get(SITEMAP_TABLE, $condition);
	}
	public static function get($nId)
	{
		$nId = intval($nId);
		$sql = 'SELECT * FROM `%s` WHERE `id`="%d" ';
		$sql = sprintf($sql,
			SITEMAP_TABLE,
			$nId);
		return DB::get($sql);

	}
	/**
	*   -------------------------------------------------------------------------------------------
	*   Отыскивает страницу по его имени документа и его индексу
	*   @return
	*   -------------------------------------------------------------------------------------------
	*/
	public static function getByDocumentId($szDocument,$nDocumentId) {
		
		$szDocument = \Faid\DB::escape($szDocument);
		$nDocumentId = intval($nDocumentId);
		
		$sql = 'SELECT * FROM `%s` WHERE `document_name`="%s" and `document_id` = %d LIMIT 0,1';
		$sql = sprintf($sql,
			SITEMAP_TABLE,
			$szDocument,
			$nDocumentId);
		$result = DB::get($sql);
		
		return $result;
	}
	public static function seekByUrlInParent($szUrl,$nParent)
	{
		$szUrl = \Faid\DB::escape($szUrl);
		$nParent = intval($nParent);
		//
		$sql = 'SELECT * FROM `%s` WHERE STRCMP(`url_key`,"%s") = 0 and `parent`="%d" LIMIT 0,1';
		$sql = sprintf($sql,
			SITEMAP_TABLE,
			$szUrl,
			$nParent);
		return DB::get($sql);
	}
	/**
	 * 
	 * Возвращает элемент по его предку и имени
	 * @param int $nParent
	 * @param string $szName
	 * @return array 
	 */
	public static function getByNameAndParent($nParent,$szName)
	{
		$nParent = intval($nParent);
		$szName = \Faid\DB::escape($szName);
		//
		$sql = 'SELECT * FROM `%s` WHERE STRCMP(`name`,"%s") = 0 and `parent`=%d';
		$sql = sprintf($sql,
			SITEMAP_TABLE,
			$szName,
			$nParent);
		return DB::Get($sql);
	}
	public static function getCount($nId)
	{
		$nId = intval($nId);
		$sql = 'SELECT count(*) as `count` FROM `%s` WHERE `parent`="%d" LIMIT 0,1';
		$sql = sprintf(
			$sql,
			SITEMAP_TABLE,
			$nId);
		$aResult = DB::get($sql);
		return $aResult['count'];
	}
	public static function selectLastUpdatedParentNodes()
	{
		$sql = 'SELECT DISTINCT `parent` FROM `%s` WHERE `date_updated` > DATE_SUB(NOW(),INTERVAL 30 SECOND) ORDER by `id` ASC';
		$sql = sprintf($sql,
			SITEMAP_TABLE);
		return DB::query($sql);
	}
	public static function selectLastUpdated()
	{
		$sql = 'SELECT *,CEIL(TIME_TO_SEC(TIMEDIFF(NOW(),`date_updated`)) / 60) as `minute_diff` FROM `%s` WHERE `date_updated` > DATE_SUB(NOW(),INTERVAL 12 HOUR) and (`document_id` != 0 or LENGTH(`script_admin_url`) > 0) ORDER by `date_updated` DESC LIMIT 0,30';
		$sql = sprintf($sql,
			SITEMAP_TABLE);
		return DB::query($sql);
	}
	public static function getRowFromBD($nId)
	{
		$nId = intval($nId);
		$aResult = SiteMap_Sample::get($nId);
		if (empty($aResult))
		{
			throw new SiteMapException('Page not found');
		}
		return $aResult;
	}

	public static function selectScriptChild($szPath)
	{

		$szPath = \Faid\DB::escape($szPath);
		$sql = 'SELECT * FROM `%s` WHERE STRCMP(`path`,"%s") = 0 ORDER by `id` ASC';
		$sql = sprintf($sql,
			SITEMAP_SCRIPT_CHILD_TABLE,
			$szPath);
		return DB::query($sql);
	}
	
	public static function getByUrl($url)
	{
		$url = \Faid\DB::escape($url);
		$sql = 'SELECT * FROM `%s` WHERE `full_url` = "%s" LIMIT 0,1';
		$sql = sprintf($sql,
			SITEMAP_TABLE,
			$url);
		$aInfo = DB::get($sql);
		if (empty($aInfo))
		{
			throw new SiteMapException('Url `'.htmlspecialchars($url).'`not found');
		}
		return $aInfo;

	}
	
	/**
	*   Метод осуществляет поиск по карте сайте
	*   @param string $szKeyword строка поисковая ключа
	*   @return array
	*/
	public static function search($szKeyword,$nStart,$nLimit, $filter = array())
	{
		$szKeyword = to_search_string($szKeyword);

		$nStart = intval($nStart);
		$nLimit = intval($nLimit);
		//
		if (strlen($szKeyword) < 2)
		{
			throw new Exception('Can`t start search. Search keyword too small');
		}

		if ( !empty( $filter )) {
			foreach ( $filter as $key=>$row ) {
				$filter[$key] = '"'. DB::escape( $row ). '"';
			}
			$sqlFilter = sprintf( ' and `document_name` in (%s)', implode(',', $filter ));
		} else {
			$sqlFilter = '';
		}
		$sql = 'SELECT SQL_CALC_FOUND_ROWS *,UNIX_TIMESTAMP(`date_updated`) as `unixtimestamp`
		FROM `%s`
		WHERE
		( `name` LIKE "%%%s%%" or  `full_url` LIKE "%%%s%%" )
		and (`document_id` != 0 or LENGTH(`script_admin_url`) > 0) %s ORDER by `name` LIMIT %d,%d';
		$sql = sprintf($sql,
			SITEMAP_TABLE,
			$szKeyword,
			$szKeyword,
			$sqlFilter,
			$nStart,
			$nLimit);
		$aResult = DB::query($sql);
		$sql = 'SELECT FOUND_ROWS() as `totalcount`';
		$aFound = DB::Get($sql);
		self::$nItemCount = $aFound['totalcount'];
		return $aResult;
	}
	/**
	*   -------------------------------------------------------------------------------------------
	*   Метод осуществляет поиск по карте сайте
	*   @param string $szKeyword строка поисковая ключа
	*   @return array
	*   -------------------------------------------------------------------------------------------
	*/
	public static function selectPaged($nParent,$nStart,$nLimit)
	{
		$nParent = intval($nParent);
		$nStart = intval($nStart);
		$nLimit = intval($nLimit);
		//

		$sql = 'SELECT SQL_CALC_FOUND_ROWS * FROM `%s` WHERE `parent`="%d" ORDER by `order` LIMIT %d,%d';
		$sql = sprintf($sql,
			SITEMAP_TABLE,
			$nParent,
			$nStart,
			$nLimit);
		$aResult = DB::query($sql);
		$sql = 'SELECT FOUND_ROWS() as `totalcount`';
		$aFound = DB::Get($sql);
		self::$nItemCount = ceil($aFound['totalcount'] / $nLimit);
		return $aResult;
	}
	/**
	*   -------------------------------------------------------------------------------------------
	*   Возвращает количество страниц
	*   @return
	*   -------------------------------------------------------------------------------------------
	*/
	public static function getTotalCount()
	{
		return self::$nItemCount;
	}
}
?>