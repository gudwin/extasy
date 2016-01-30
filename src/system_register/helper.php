<?
use \Faid\DB;
/**
 * Помощник в управлении путями
 * @package exst3/modules/system_register
 * @author Gisma (info@gisma.ru)
 * @date 13.10.2009
 */
class SystemRegisterHelper 
{
	/**
	 * Преобразует путь в канонический 
	 */
	public static function responsePath($szPath)
	{
		$aResult = array();
		$aData = explode('/',$szPath);
		foreach ($aData as $row)
		{
			if (strlen($row) != 0)
			{
				$aResult[] = $row;
			}
		}
		return $aResult;
	}
	/**
	 * Возвращает по ID его предка
	 */
	public static function createById($nId)
	{
		$nId = IntegerHelper::toNatural($nId);
		//
		$sql = 'SELECT * FROM `%s` WHERE `id`="%d"';
		$sql = sprintf($sql,SYSTEMREGISTER_TABLE,$nId);
		//
		$aResult = DB::get($sql);
		if (empty($aResult))
		{
			throw new SystemRegisterException('Key with id="'.$nId.'" not found');
		}

		// Нашли корневой элемент
		if ($aResult['parent'] == 0)
		{
			$result = new SystemRegister($aResult['name']);

		}
		else
		{
			$result = self::createById($aResult['parent'])->get($aResult['name']);	
		}
		return $result;
	}
	/**
	 * Определяет является ли элемент ветвью
	 */
	public static function isBranch($value,$type)
	{
		if (empty($type) || ($type == SYSTEMREGISTER_BRANCH_TYPE))
		{
			if (empty($value))
			{
				
				return true;
			}
			else
			{
				if ($type == SYSTEMREGISTER_BRANCH_TYPE)
				{
					throw new SystemRegisterException('Conflict found, branch with non-empty `value` attribute');
				}
			}
		}
		return false;
	}
	/**
	 * На основе двух массивов формирует путь
	 */
	public static function createPath($aCurrentPath,$aAdd) {
		Return implode('/',array_merge($aCurrentPath,$aAdd));
	}
	/**
	 * Осуществляет делегированный вызов
	 */
	public static function delegate($aCurrentPath,$aNewPath,$method,$aData) {
		// то доверяем задачу сыну
		$szKey = array_pop($aNewPath);
		$szNewPath = SystemRegisterHelper::createPath($aCurrentPath,$aNewPath);

		// Получаем новый узел
		$branch = new SystemRegister($szNewPath);
		//
		array_unshift($aData,$szKey);
		return call_user_func_array(array($branch,$method),$aData);
	}
	/**
	 * Импортируемт значение (может быть массивом) в реестр
	 * @param $path SystemRegister узел дерева реестра
	 * @param $aData mixed значение для вставки
	 */
	public static function import(SystemRegister $path,$aData)
	{
		
		foreach ($aData as $key=>$row)
		{
			if (!isset($path->$key))
			{
				// Создаем ключ
				// Если это массив
				if (!is_Array($row)) 
				{
					$path->insert($key,$row,'',gettype($row));
				}
				else
				{
					// Если значение не является массив
					$path->insert($key,'','',SYSTEMREGISTER_BRANCH_TYPE);
					self::import($path->$key,$row);
				}
			}
			else
			{
				// Существует ключ
				// и он  массив
				if (is_array($row)) 
				{
				// Если данные массив
					// Проверяем, является ли массивом значение и является ли веткой данный узел
					if ($path->$key instanceof SystemRegister)
					{
						// Да, значит спукаемся по рекурсии
						self::import($path->$key,$row);
					}
				}
				else 
				{
					// Нет, не массив
					// Обновляем значени 
					$path->$key = $row;
				}
			}
		}
		
	}

	/**
	 * Возвращает дерево реестра в виде массива, с полной информацией об узлах дерева
	 */
	public static function export($nId = 0)
	{
		$aResult = SystemRegisterSample::selectChild($nId);
		foreach ($aResult as $key=>$row)
		{
			if ($row['type'] == SYSTEMREGISTER_BRANCH_TYPE)
			{
				$aResult[$key]['children'] = self::export($row['id']);

			}
			else
			{
			}
		}
		if (empty($aResult))
		{
			$aResult = array();
		}
		return $aResult;
	}
	/**
	 * Возвращает дерево реестра в виде ассоциативного массива
	 */
	public static function exportData($nId = 0)
	{
		$aData = SystemRegisterSample::selectChild($nId);
		$aResult = array();
		foreach ($aData as $row)
		{
			if ($row['type'] == SYSTEMREGISTER_BRANCH_TYPE)
			{
				$aResult[$row['name']] = self::exportData($row['id']);

			}
			else
			{
				$aResult[$row['name']] = $row['value'];
			}
		}
		
		return $aResult;
	}

	/**
	 * Возвращает значение указанного элемента системного реестра
	 * @param $path
	 * @return string
	 */
	public static function getValue( $path ) {
		$register = new SystemRegister('/');
		return $register->get( $path )->value;
	}

}
?>