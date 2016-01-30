<?php
use \Faid\DB;
use \Faid\DBSimple;
/**
 * 
 * Основной класс для управления доступом
 * @author Gisma
 *
 */
class ACL {
	/**
	 * 
	 * Создает объект права 
	 * @param string $path строка набора объектов разделенная символом "/"  
	 */
	public static function create($path,$title = '') {
		// Разрезаем путь
		$path = self::getPath($path);
		// Для каждого элемента пути
		$parentId = 0;
		$fullPath = '';
		foreach ($path as $key=>$row) {
			// Получаем каждый элемент (имя, parent)
			$condition = array(
				'parentId' => $parentId,
				'name' => $row,
			);
			if (empty($fullPath)) {
				$fullPath .= $row;
			} else {
				$fullPath .= '/'.$row;
			}
			$el = DBSimple::get(ACL_TABLE, $condition);
			// Если элемент не существует, создаем его
			if (empty($el)) {
				$insertCondition = $condition;
				$insertCondition['fullPath'] = $fullPath; 
				$parentId = DBSimple::insert(ACL_TABLE, $insertCondition);
			} else {
				$parentId = $el['id'];
			}
			
		}
		/**
		 * Если указан тайтл, то сохраняем его 
		 */
		if (!empty($title)) {
			$whereCondition = array(
				'id' => $parentId
			);
			$setCondition = array(
				'title' => $title
			);
			DBSimple::update(ACL_TABLE, $setCondition, $whereCondition);
		}
		return $parentId;
			
	}
	/**
	 * 
	 * Удаляет объект права
	 * @param unknown_type $path
	 */
	public static function remove($path) {
		$el = self::get($path);
		self::removeChild($el);
	}
	
	/**
	 * 
	 * Дает доступ к определенному объекту для определннной сущности 
	 * @param string $path
	 * @param string $entity
	 */
	public static function grant($path,$entity) {
		if (empty($entity)) {
			throw new ACLException('entity empty!');
		}
		// Получаем элемент
		$el = self::get($path);
		// Добавляем в бд
		$sql = 'INSERT INTO `%s` SET `actionId`="%d",`entity`="%s" ON DUPLICATE KEY UPDATE `actionId`="%d" ';
		$sql = sprintf($sql,ACL_GRANT_TABLE,$el['id'],\Faid\DB::escape($entity),$el['id']);
		DB::post($sql);
	}
	/**
	 * 
	 * Удаляет доступ для указанной сущности к указанному объекту
	 * @param string $path
	 * @param string $entity
	 */
	public static function unGrant($path,$entity) {
		// Получаем элемент
		$el = self::get($path);
		// Удаляем из бд
		$whereCondition = array(
			'actionId' => $el['id'],
			'entity' => $entity
		);
		DBSimple::delete(ACL_GRANT_TABLE, $whereCondition);
	}
	/**
	 * 
	 * Проверяет, есть ли доступ к определенному объекту у указанной сущности. 
	 * Данный метод рекурсивно обходит вверх по дереву прав, если на пути 
	 * @param unknown_type $path
	 * @param unknown_type $entity
	 */
	public static function isGranted($path,$entity) {

		// Получаем элемент
		$el = self::get($path);
		// Посылаем запрос на проверку
		$condition = array(
			'actionId' => $el['id'],
			'entity' => $entity
		);
		$data = DBSimple::get(ACL_GRANT_TABLE, $condition);

		return !empty( $data );
	}
	/**
	 * 
	 * Удаляет все записи из таблицы
	 * @param string $entity
	 */
	public static function removeEntity($entity) {
		// Посылаем запрос
		DBSimple::delete(ACL_GRANT_TABLE, array(
			'entity' => $entity
		));
	}
	public static function selectAllGrantsForEntity($entity) {

		$sql = <<<SQL
		select a.fullPath FROM `%s` AS g 
		INNER JOIN `%s` AS a 
		ON a.id = g.actionId
		WHERE g.entity = "%s"
SQL;
		$sql = sprintf($sql,ACL_GRANT_TABLE,ACL_TABLE,\Faid\DB::escape($entity));
		$data = DB::query($sql);
		$result = array();
		foreach ($data as $row) {
			$result[$row['fullPath']] = true;  
		}

		return $result;
	}
	protected static function get($path) {
		// Разрезаем путь
		$pathList = self::getPath($path);
		// Получаем элемент пути
		$condition = array(
			'fullPath' => $path
		);
		$data = DBSimple::get(ACL_TABLE, $condition);
		
		if (empty($data)) {
			throw new ACLException('Path "'.$path.'" not found');
		}
		
		return $data;
	}
	protected static function getPath($path) {
		$pathList = explode('/',$path);
		foreach ($pathList as $row) {
			if (empty($row)) {
				throw new ACLException('Empty key in path :'.$path);
			}
		}
		return $pathList;
	}
	protected static function removeChild($el) {
		// Получаем дочерние элементы
		$childList = DBSimple::select(ACL_TABLE,array('parentId' => $el['id']));
		
		foreach ($childList as $row) {
			// Вызов удаления для дочерних
			self::removeChild($row);
		}
		// Удаляем гранты на текущие элементы
		DBSimple::delete(ACL_GRANT_TABLE, array('actionId' => $el['id']));
		// Удаляем саму запись 
		DBSimple::delete(ACL_TABLE, array('id' => $el['id']));
	}
}