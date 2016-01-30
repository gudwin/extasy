<?php
use \Faid\DB;
class CMSUsers {
	/**
	 * Создает пользователя
	 * @param string $login
	 * @param string $password
	 * @param string $rights
	 */ 
	public static function createUser($login,$password,$rights = '') {

		$login = \Faid\DB::escape($login);
		$password = \Faid\DB::escape($password);
		$rights = \Faid\DB::escape(implode("\r\n",$rights));
		//
		$sql = 'INSERT INTO `%s` SET `login`="%s",`password`="%s",`rights`="%s"';
		$sql = sprintf($sql,
			CMS_AUTH_TABLE,
			$login,
			$password,
			$rights);
		DB::post($sql);
	}
	/**
	 * Обновляет данные о пользователе
	 * @param $id
	 * @param $login
	 * @param $password
	 * @param $rights
	 */
	public static function updateUser($id,$login,$password,$rights = '') {
		if (is_array($rights)) {
			$rights = implode("\r\n",$rights);
		}
		$id = intval($id);
		$login = \Faid\DB::escape($login);
		$password = \Faid\DB::escape($password);
		$rights = \Faid\DB::escape($rights);
		//
		$sql = 'UPDATE `%s` SET `login`="%s",`password`="%s",`rights`="%s" WHERE `id`="%d"';
		$sql = sprintf($sql,
			CMS_AUTH_TABLE,
			$login,
			$password,
			$rights,
			$id);
		DB::post($sql);
	}
	/**
	*   Удаляет пользователя
	*   @return
	*/
	public static function deleteUser($nUserId)
	{
		$nUserId = intval($nUserId);
		//
		$sql = 'DELETE FROM `%s` WHERE `id`="%d"';
		$sql = sprintf($sql,
			CMS_AUTH_TABLE,
			$nUserId);
		return DB::post($sql);

	}
	/**
	*   Возвращает пользователя с nUserId
	*   @return
	*/
	public static function getUser($nUserId)
	{
		$nUserId = intval($nUserId);
		//
		$sql = 'SELECT * FROM `%s` WHERE `id`="%d" LIMIT 0,1';
		$sql = sprintf($sql,CMS_AUTH_TABLE,$nUserId);
		return DB::get($sql);
	}
	/**
	 * Возвращает список всех пользователей системы
	 */
	public static function selectAllUsers() {
		$sql = 'select * from `%s` WHERE `id` > 2 order by `login` ';
		$sql = sprintf($sql,CMS_AUTH_TABLE);
		return DB::query($sql);
	}	
}