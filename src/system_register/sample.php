<?php
use \Faid\DB;
use \Faid\debug;
use \Faid\Cache\Exception as SimpleCacheException;
use \Faid\SimpleCache;

/**
 * Класс по работе с БД и кэшем
 * @package exst3/modules/system_register
 * @author Gisma (info@gisma.ru)
 * @date 14.10.2009

 */
class SystemRegisterSample 
{
	public static $bUseCache = true;
	public static $aGetCache = array();
	public static $aChildCache = array();
	public static function startup( ) {
		// Загружаем по дефолту всю либу
		SystemRegisterSample::loadAll();
	}
	/**
	 * По ключу (имени) элемента и индексу предка, получаем информацию об элементе
	 */
	public static function get($szKey = 0,$nParent = 0)
	{
		// Проверяем данные в хеше 

		if (self::hasGetCache($szKey,$nParent)) 
		{
			return self::returnGetCache($szKey,$nParent);
		}
		
		$szKey = \Faid\DB::escape($szKey);
		$nParent = intval($nParent);
		// Формируем запрос в БД
		$sql = 'SELECT * FROM `%s` WHERE `parent`="%d" and `name`="%s" ';
		$sql = sprintf($sql,
			SYSTEMREGISTER_TABLE,
			$nParent,
			$szKey);
		//
		// Получаем данные
		$aData = DB::get($sql);
		// если, что-нибудь найдено, то сохраняем это
		if (!empty($aData)) {
			self::storeGetCache($szKey,$nParent,$aData);
		}
		
		return $aData;
	}

	/**
	 * По ключу элемента находим его детей
	 */
	public static function selectChild($nId)
	{
		// Проверяем данные в хеше
		if (self::hasChildCache($nId)) 
		{
			return self::returnChildCache($nId);
		} else {
			
		}
		
		$nId = intval($nId);
		//
		$sql = 'SELECT * FROM `%s` WHERE `parent`="%d" ORDER by `name` ASC';
		$sql = sprintf($sql,
			SYSTEMREGISTER_TABLE,
			$nId);
		// СОхраняем кеш
		$aData = DB::query($sql);
		self::storeChildCache($nId,$aData);
		
		return $aData;
	}
	/**
	 * Удаляет ряд из бд
	 * @param int $nId Индекс ряда
	 * @param boolean $bRecursive удалять ли детей данного элемента. ВНИМАНИЕ!!! Устанавливайте значение в false, только если вы точно знаете, что делаете!
	 */
	public static function delete($parent,$nId,$bRecursive = true)
	{
		$nId = intval($nId);

		// Определяем установлен ли флаг рекурсивного удаления
		if ($bRecursive)
		{
			$aChild = self::selectChild($nId);

			foreach ($aChild as $row)
			{
				
				self::delete(null,$row['id']);
			}
		}
		// 
		$sql = 'SELECT * FROM `%s` WHERE `id`="%d"';
		$sql = sprintf($sql,
			SYSTEMREGISTER_TABLE,
			$nId);
		$aInfo = DB::get($sql);
		if (!empty($aInfo)) 
		{
			// Запрос в бд
			$sql = 'DELETE FROM `%s` WHERE `id`="%d"';
			$sql = sprintf($sql,
				SYSTEMREGISTER_TABLE,
				$nId);
			DB::post($sql);
			
			self::updateGetCache($aInfo['name'],$parent->getId(),true);
		} else {
			
		}
		if (is_object($parent))
		{
			// Обновляем кеш для предка
			self::updateChildCache($parent->getId());
			self::updateChildCache($nId,true);
			
		}
		
	}
	/**
	 * Вставляет новый лист в таблицу
	 *
	 */
	public static function insert(SystemRegister $parent,$szName,$szValue,$szComment,$szType)
	{
		// Экранируем переменные

		$szName = \Faid\DB::escape($szName);
		$szValue = \Faid\DB::escape($szValue);
		$szComment = \Faid\DB::escape($szComment);
		$szType = \Faid\DB::escape($szType);
		// запрос
		$sql = 'INSERT INTO `%s` SET `parent`="%d",`name`="%s",`value`="%s",`comment`="%s",`type`="%s"';
		$sql = sprintf($sql,
			SYSTEMREGISTER_TABLE,
			$parent->getId(),
			$szName,
			$szValue,
			$szComment,
			$szType);
		// 
		DB::post($sql);
		// Обновляем кеш предка
		$nResult = DB::$connection->insert_id;

		self::updateChildCache($parent->getId());
		self::updateGetCache($szName,$parent->getId());
		return $nResult;
	}
	/**
	 * Обновляет лист в бд
	 */
	public static function update(SystemRegister $parent,$id,$szName,$szValue,$szComment,$szType)
	{
		// Экранируем переменные
		$id = intval($id);

		$szName = \Faid\DB::escape($szName);
		$szValue = \Faid\DB::escape($szValue);
		$szComment = \Faid\DB::escape($szComment);
		$szType = \Faid\DB::escape($szType);

		// запрос
		$sql = 'UPDATE `%s` SET `name`="%s",`value`="%s",`comment`="%s",`type`="%s" WHERE `id`="%s"';
		$sql = sprintf($sql,
			SYSTEMREGISTER_TABLE,
			$szName,
			$szValue,
			$szComment,
			$szType,
			$id);
		// 
		DB::post($sql);
		// обновляем кеш предка
		self::updateChildCache($parent->getId());
		self::updateGetCache($szName,$parent->getId());
		return $id;
	}
	/**
	 * Блокирует загрузку из кеша. НО! Данные по-старому будут записываться в кеш
	 */
	public static function disableCache()
	{
		self::$bUseCache = false;
	}
	/**
	 * Активирует загрузку из кеша (если данные в нем присуствует)
	 */
	public static function enableCache()
	{
		self::$bUseCache = true;
	}
	/**
	 * Проверяет сохранены ли данные в хеше записей
	 */
	public static function hasGetCache($key,$parent) {
		$key = self::generateGetCacheKey($key,$parent);
		if ((self::$bUseCache) && (isset(self::$aGetCache[$key])))
		{
			return true;
		}
		return false;
	}
	public static function generateGetCacheKey($key,$parent) 
	{
		return md5(md5($key).md5($parent));
	}
	/**
	 * Проверяет сохранены ли данные в хеше детей
	 */
	public static function hasChildCache($id) {
		if ((self::$bUseCache) && (isset(self::$aChildCache[$id])))
		{
			return true;
		}
		return false;
	}
	
	/**
	 * Возвращает данные из кеша
	 */
	public static function returnGetCache($key,$parent) 
	{
		$key = self::generateGetCacheKey($key,$parent);
		return self::$aGetCache[$key];
	}
	/**
	 * Возвращает данные из кеша
	 */
	public static function returnChildCache($id) 
	{
		return self::$aChildCache[$id];
	}
	
	/**
	 * Очищает кэш нахрен
	 */ 
	public static function clearCache() 
	{
		self::$aChildCache = array();
		self::$aGetCache = array();
		try {
			SimpleCache::clear(SYSTEM_REGISTER_GET_CACHE);
			SimpleCache::clear(SYSTEM_REGISTER_CHILD_CACHE);
		} catch (Exception $e) {
			
		}
	}
	/**
	 * Загружает всю таблицу в кеш сразу
	 */
	public static function loadAll()
	{
		try {
			// Если есть кеш, то грузим его
			$getCache = SimpleCache::get(SYSTEM_REGISTER_GET_CACHE);
			$childCache = SimpleCache::get(SYSTEM_REGISTER_CHILD_CACHE);
			self::$aGetCache = $getCache;
			self::$aChildCache = $childCache;
			return ;
		} catch (SimpleCacheException $e) {

		}

		self::createCache();
	}
	public static function createCache() {
		self::clearCache();
		self::disableCache();
		// Получаем все ряды, выстроенные по предку
		$sql = 'SELECT * FROM `%s` ORDER by `parent` ASC,`id` ASC';
		$sql = sprintf($sql,SYSTEMREGISTER_TABLE);
		$aData = DB::query($sql);

		// Перебор всех рядов
		$parent = -1;
		$aChild = array();
		foreach ($aData as $row)
		{
			// Если предок изменился, то генерируем другой childCache
			if ($row['parent'] != $parent)
			{
				if (!empty($aChild)) 
				{
					self::storeChildCache($parent,$aChild);
					$aChild = array();
				}
				$parent = $row['parent'];
				$aChild[] = $row;
			} 
			else 
			{
				$aChild[] = $row;
			}
			// Добавляем в getCache
			self::storeGetCache($row['name'],$parent,$row);
		}
		self::enableCache();
		SimpleCache::set(SYSTEM_REGISTER_GET_CACHE,self::$aGetCache);
		SimpleCache::set(SYSTEM_REGISTER_CHILD_CACHE,self::$aChildCache);
	}
	/**
	 * Возвращает данные из кеша
	 */
	protected static function storeGetCache($key,$parent,$aData) {
		$key = self::generateGetCacheKey($key,$parent);
		self::$aGetCache[$key] = $aData;
		if ( self::$bUseCache ) {
			SimpleCache::set(SYSTEM_REGISTER_GET_CACHE,self::$aGetCache);
		}
		
	}
	/**
	 * Возвращает данные из кеша
	 */
	protected static function storeChildCache($id,$aData) {
		self::$aChildCache[$id] = $aData;
		if ( self::$bUseCache ) {
			SimpleCache::set(SYSTEM_REGISTER_CHILD_CACHE,self::$aChildCache);
		}
	}
	/**
	 * Обновляет кеш детей
	 * @param $bDelete boolean если установлен этот флаг, то хеши просто удаляются
	 */
	protected static function updateChildCache($id,$bDelete = false)
	{
		if ($bDelete)
		{
			if (isset(self::$aChildCache[$id]))
			{
				unset(self::$aChildCache[$id]);
			}
			return;
		}
		self::disableCache();
		// Обновляем кеш детей
		self::selectChild($id);
		self::enableCache();
		// Сохраняем кеш в файл
		SimpleCache::set(SYSTEM_REGISTER_CHILD_CACHE,self::$aChildCache);
	}
	/**
	 * Обновляет кэш детей
	 * @param $bDelete boolean если установлен этот флаг, то хеши просто удаляются
	 */
	protected static function updateGetCache($key,$parent,$bDelete = false)
	{
		if ($bDelete)
		{
			$key = self::generateGetCacheKey($key,$parent);
			if (isset(self::$aGetCache[$key]))
			{
				unset(self::$aGetCache[$key]);
			}
	
			return;
		}
		self::disableCache();
		// Обновляем кеш детей
		self::get($key,$parent);
		self::enableCache();
		// Сохраняем кеш в файл
	
	}
}
?>