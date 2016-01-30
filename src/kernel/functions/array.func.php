<?
class ArrayHelper
{
	/**
	*   -------------------------------------------------------------------------------------------
	*   Проверяет, является ли массив, массивом целых чисел
	*   @return
	*   -------------------------------------------------------------------------------------------
	*/
	public static function checkArrayWithInt($aParam)
	{
		if (!is_array($aParam))
		{
			throw new Exception('Param isn`t array');
		}
		foreach ($aParam as &$row)
		{
			if (!is_scalar($row))
			{
				throw new Exception('Array contains not scalar items :'.$row);
			}
			$row = intval($row);
		}
		unset($row);
		return $aParam;
	}
	/**
	*   -------------------------------------------------------------------------------------------
	*   Проверяет существуют ли указанные индексы ($aKeys) в массив ($aSearch)
	*   @return
	*   -------------------------------------------------------------------------------------------
	*/
	public static function indexesExists($aKeys,$aSearch)
	{
		if (!is_array($aSearch))
		{
			throw new Exception('Param isn`t array');
		}
		foreach ($aKeys as $key)
		{
			if (!array_key_exists($key,$aSearch))
			{
				throw new Exception('key ("'.$key.'") not exists');
			}
		}
	}
	public static function arrayCleanFields($data,$fields) {
		foreach ($data as $key=>$row) {
			$key = strval($key);
			if (in_array($key,$fields)) {
				unset($data[$key]);
				continue;
			}
			if (is_array($data[$key])) {
				$data[$key] = self::arrayCleanFields($row,$fields);
			}
		}
		return $data;
	}
}
?>