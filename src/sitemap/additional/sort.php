<?
use \Faid\DB;
/**
 * В данной либе находится модуль помощник в сортировке элементов
 */
// Необходимые либы
require_once LIB_PATH . 'kernel/functions/integer.func.php';
// Сам класс
class SitemapSorter 
{
	/**
	 * Осуществляет сортировку по определенному полю у конкретного документа
	 */
	public static function sortByField($nId,$szDocumentName,$szFieldName,$bAsc = true)
	{
		// 
		$nId = IntegerHelper::toNatural($nId);
		$szDocumentName = \Faid\DB::escape($szDocumentName);
		$szFieldName = \Faid\DB::escape($szFieldName);
		$bAsc = intval($bAsc);
		//
		// Получаем всех детей элемента, которые имеют указанный документ в кач. имени
		//

		$szTable = call_user_func([$szDocumentName, 'getTableName']);
		$sql = <<<SQL
	SELECT `sitemap`.id,`sitemap`.document_id
	FROM `%s` as `sitemap` 
	INNER JOIN `%s` as `document` 
	ON `sitemap`.`parent` = %d and `sitemap`.document_id = `document`.id and `sitemap`.document_name = "%s" 
	ORDER BY `document`.%s %s
SQL;
		$sql = sprintf($sql,
			SITEMAP_TABLE,
			$szTable,
			$nId,
			$szDocumentName,
			$szFieldName,
			$bAsc?' ASC':' DESC'
			);

		$aDocumentsData = DB::query($sql);
		// Получаем всех остальных детей
		$sql = 'SELECT * FROM `%s` WHERE `parent`="%d" and STRCMP(`document_name`,"%s") <> 0 ORDER by `order`';
		$sql = sprintf($sql,SITEMAP_TABLE,$nId,$szDocumentName);
		$aOtherData = DB::query($sql);

		// Выстраиваем детей совмещаем массивы, неотсортированные элементы остаются на своих местах 
		$aResult = array();
		$nOrder = 0;
		while (sizeof($aDocumentsData) > 0 || sizeof($aOtherData) > 0)
		{
			if (!empty($aOtherData) && ($aOtherData[0]['order'] == $nOrder))
			{
				$aElement = array_shift($aOtherData);
				$aResult[] = $aElement['id'];
			}
			else
			{
				//
				$aElement = array_shift($aDocumentsData);
				$aResult[] = $aElement['id'];
			}
			$nOrder++;
		}
		
		//

		Sitemap::manualOrder($nId,$aResult);
	}
}
?>