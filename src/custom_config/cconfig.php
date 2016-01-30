<?php
use \Faid\DB;
use \Faid\DBSimple;
/**
 * Класс-фабрика для получения схем
 * @author Gisma (dmitrey.schechevchenko@gmail.com)
 * @version 0.1
 *
 */
class CConfig {
	/**
	 * Создает новую схему
	 * @param string $schemaName имя схемы
	 * @return CConfigSchema $schemaName
	 */
	public static function createSchema($schemaName) {
		if (empty($schemaName)) {
			throw new CConfigException('Empty schemaName');
		}
		// Проверяем, что такая схема существует
		$exists = self::checkSchemaExists($schemaName);
		if ($exists) {
			throw new CConfigException('Duplicate schema');
		}
		// Запрос на создание схемы
		$sql = 'INSERT INTO `%s` SET `name`="%s"';
		$sql = sprintf($sql,
			CCONFIG_SCHEMA_TABLE,
			\Faid\DB::escape($schemaName));
		DB::post($sql);
		$id = DB::$connection->insert_id;
		// Получаем объект схемы
		$result = new CConfigSchema(array('id' => $id,'name' => $schemaName));
		return $result;
	}
	/**
	 * Возвращает объект схемы на основе индекса из бд
	 * @param int $id
	 * @return CConfigSchema
	 * @throws CConfigException
	 */
	public static function getSchemaById($id) {
		$id = IntegerHelper::toNatural($id);
		$sql = 'select * from `%s` where `id`="%d"';
		$sql = sprintf($sql,CCONFIG_SCHEMA_TABLE,$id);
		$result = DB::get($sql);
		if (empty($result)) {
			throw new CConfigException('Schema with id="'.$id.'" not found');	
		}
		$result = new CConfigSchema($result);
		return $result;
		
		
	} 
	/**
	 * Возвращает объект существующей схемы
	 * @param string $schemaName имя схемы
	 * @return CConfigSchema $schemaName
	 */
	public static function getSchema($schemaName) {
		
		// Проверяем существование схемы
		$exists = self::checkSchemaExists($schemaName);
		if (!$exists) {
			throw new CConfigException('Unknown schema');
		}
		// Существует, загружаем
		$result = new CConfigSchema($exists);
		return $result;
	}
	
	/**
	 * Возвращает все существующие схемы
	 */
	public static function selectAll() {
		$table = CCONFIG_SCHEMA_TABLE;
		$data = DBSimple::select( $table,'','`title`');

		$result = array();
		foreach ( $data as $row ) {
			// Создаем объект на основе схемы
			$result[] = new CConfigSchema($row);
		}
		return $result;
	}
	
	protected static function checkSchemaExists($schemaName) {
		
		// Делаем запрос, на проверку существования записей в контролах
		$sql = 'select * from `%s` WHERE `name`="%s"';
		$sql = sprintf($sql,CCONFIG_SCHEMA_TABLE,\Faid\DB::escape($schemaName));
		$result = DB::get($sql);
		if (!empty($result)) { 
			return $result;
		} else {
			return false;
		}
		
	}
}