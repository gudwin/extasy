<?
//
require_once LIB_PATH.'sitemap/additional/pages.php';
require_once LIB_PATH.'sitemap/additional/misc.php';
class SitemapMenu 
{
	/**
	 * Возвращает полное список предков с их соседями, включая текущий уровень
	 * @param int $nUntil указывает на каком предке остановится 
	 * @param bool $bWithAdditional обозначает необходимость подгрузки доп. информации из моделей
	 */
	public static function selectParentMenuToLevel($nUntil = -1,$bWithAdditional = false)
	{
		$aUrlInfo = self::getCurrentUrlInfo();
		
		$nCurrentId = $aUrlInfo['id'];
		$nOldId = 0;
		//
		$aResult = array();
		$aOld = array();
		// Пока не достигли конца (больше некуда подыматься, текущий уровень 0)
		while (1)
		{
			// Получаем детей к текущему уровню 
			if ($bWithAdditional)
			{
				$aData = Sitemap_PagesOperations::selectChildWithAdditional($nCurrentId);
			}
			else
			{
				$aData = Sitemap_PagesOperations::selectChildWithoutAdditional($nCurrentId);
			}
			
			// Добавляем к текущему уровню предыдущие вычисления
			foreach ($aData as $key=>$row)
			{
				if ($nOldId == $row['id'])
				{
					// Добавляем предыдущий результат в текущий слой меню
					$aData[$key]['current'] = 1;
					$aData[$key]['aChild'] = $aOld;
				}
			}
			// 
			if (empty($aUrlInfo) || ($nCurrentId == $nUntil))
			{
				return $aData;
			} else 
			{
				// Получаем новый текущий уровень
				$nOldId = $aUrlInfo['id'];
				//
				$aUrlInfo = Sitemap_Sample::get($aUrlInfo['parent']);
				//
				$nCurrentId = $aUrlInfo['id'];
			}
			$aOld = $aData;

		}
		return array();
	}
	/**
	 * Возвращает дочернее меню для указанного элемента до указанного уровня
	 * @param bool $nId обозначает индекс страницы, от которой меню генерировать
	 * @param bool $nDepth глубина генерации меню
	 * @param bool $bWithAdditional обозначает необходимость подгрузки доп. информации из моделей
	 */
	public static function selectMenu($nId = 0,$nDepth = 1,$bWithAdditional = false)
	{
		require_once LIB_PATH . 'kernel/functions/integer.func.php';
		$nDepth = IntegerHelper::toNatural($nDepth);
		$aUrlInfo = self::getCurrentUrlInfo();
		if (empty($nDepth)) {
			return array();
		}
		$aResult = array();
		if ($bWithAdditional)
		{
			$aResult = Sitemap_PagesOperations::selectChildWithAdditional($nId);
		}
		else
		{
			$aResult = Sitemap_PagesOperations::selectChildWithoutAdditional($nId);
		}
		// Перебор по рекурсии, спускаемся ниже, чтобы найти следующие документы
		foreach ($aResult as &$row)
		{
			if (!emptY($row['count']))
			{
				$row['aChild'] = self::selectMenu($row['id'],$nDepth - 1,$bWithAdditional);
			}
			else
			{
				$row['aChild'] = array();
			}
			// Определяем, что текущий элемент активный
            if ( !empty( $aUrlInfo )) {
                $isCurrent = SitemapMisc::urlsMatch($aUrlInfo['full_url'],$row['full_url']);
                if ($isCurrent) {
                    $row['active'] = 1;
                }
                if ($row['id'] == $aUrlInfo['id']) {
                    $row['current'] = 1;
                }
            }


		}
		unset($row);
		return $aResult;
	}

	/**
	 * Возвращает полное меню для указанного документа
	 * @param bool $nId обозначает индекс страницы, от которой меню генерировать
	 * @param bool $bWithAdditional обозначает необходимость подгрузки доп. информации из моделей
	 */
	public static function selectFullMenu($nId = 0,$bWithAdditional = false,$aExcludeSitemapEntity = array())
	{
		$aUrlInfo = self::getCurrentUrlInfo();
		$aResult = array();
		if ($bWithAdditional)
		{
			$aData = Sitemap_PagesOperations::selectChildWithAdditional($nId);
		}
		else
		{
			$aData = Sitemap_PagesOperations::selectChildWithoutAdditional($nId);
		}
		// Перебор по рекурсии, спускаемся ниже, чтобы найти следующие документы
		$aResult = array();
		foreach ($aData as &$row)
		{
			// Определяем, что текущий элемент активный
			$isCurrent = SitemapMisc::urlsMatch($aUrlInfo['full_url'],$row['full_url']);
			if ($isCurrent) {
				$row['active'] = 1;
			}
			
			if ($row['full_url'] == $aUrlInfo['full_url'])
			{
				$row['active'] = 1;
			}
			// Определяем, что текущий элемент активный
			
			$excludeDocument = (!empty($row['document_name']) && in_Array($row['document_name'],$aExcludeSitemapEntity));
			$excludeScript = (!empty($row['script']) && in_Array($row['script'],$aExcludeSitemapEntity)); 
			if ($excludeDocument || $excludeScript)
			{
				// Не включаем дерево ненужные документы
				continue;
			} 
			
			if (!empty($row['count']))
			{
				$row['aChild'] = self::selectFullMenu($row['id'],$bWithAdditional,$aExcludeSitemapEntity);
			}
			else
			{
				$row['aChild'] = array();
			}
			$aResult[] = $row;

		}
		unset($row);
		return $aResult;

	}
	protected static function getCurrentUrlInfo() {
		return \Extasy\sitemap\Route::getCurrentUrlInfo();

	}

}
?>