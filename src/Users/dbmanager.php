<?
use \Faid\DB;
use \Faid\DBSimple;
//************************************************************//
//                                                            //
//              Выборки по базе данных                        //
//       Copyright (c) 2008 Ext-CMS (http://www.ext-cms.com/) //
//               отдел/сектор                                 //
//       Email:    info@gisma.ru (http://www.gisma.ru/)       //
//                                                            //
//  Разработчик: Gisma (14.01.2009)                           //
//  Модифицирован:  14.01.2009  by Gisma                      //
//                                                            //
//************************************************************//
//  Хранит все запросы к БД, которые содержит базовый         //
//  модуль пользователей. Также перехватывает ситуации, когда //
//  MySQL возвращает пустой результат там, где он быть не     //
//  может. Например: метод get всегда должен возвращать       //
//  найденный профайл.                                        //
//************************************************************//

class UsersDBManager {
	/**
	 * Хранит кэш данных аккаунтов пользователей 
	 * @var array
	 */
	protected static $userCache = array();
	public static $nPageCount = 0;
	public static $nItemCount = 0;

	/**
	*   Возвращает конкретную запись
	*   @return
	*/
	public static function get($nId) {
		$sql = 'SELECT * FROM `%s` WHERE `id` = "%d"';
		$sql = sprintf($sql,
			USERS_TABLE,
			intval($nId));
		$aResult = DB::get($sql);

		if (empty($aResult))
		{
			throw new NotFoundException('row '.$nId.' in users table not found');
		}

		return $aResult;
	}
	public static function findByEmail( $email ) {
		$result = DBSimple::get( UserAccount::getTableName(),array(
			sprintf( '`email` = "%s" ', DB::escape( $email), DB::escape( $email ))
		));
		return $result;
	}
	/**
	 * 
	 * Отыскивает запись о пользователе в БД по его логину
	 * @param string $login
	 * @throws Exception
	 * @return array
	 */
	public static function getByLogin($login) {

		$sql = 'select * from `%s` where `login`="%s" ';
		$sql = sprintf($sql,USERS_TABLE,\Faid\DB::escape($login));
		$found = DB::get($sql);
		if (empty($found)) {
			throw new \NotFoundException('User with login `'.$login.'` not found');
		}
		return $found;
	}
	/**
	*   Возвращает определенную страницу пейджинга
	*   @return
	*/
	public static function selectPaged($nCurrentPage,$nPageSize) {
		$nCurrentPage = intval($nCurrentPage);
		$nPageSize = intval($nPageSize);
		if ($nPageSize <= 0) {
			throw new Exception('Dimensions overflow. PageSize = "'.$nPageSize.'"');
		}
		if ($nCurrentPage < 0) {
			throw new Exception('Dimensions overflow. CurrentPage = "'.$nCurrentPage.'"');
		}
		$sql = 'SELECT SQL_CALC_FOUND_ROWS * FROM `%s` ORDER by `login` ASC LIMIT %d,%d ';
		$sql = sprintf($sql,
			USERS_TABLE,
			$nCurrentPage * $nPageSize,
			$nPageSize);
		$aData = DB::query($sql);
		$aFound = DB::get('SELECT FOUND_ROWS() as `found`');
		self::$nItemCount = $aFound['found'];
		self::$nPageCount = ceil($aFound['found'] / $nPageSize);
		// Проверяем на допустимые диапазоны поданные страницы
		if ((self::$nPageCount <= $nCurrentPage)
			&& ($nCurrentPage != 0))
		{
			throw new Exception('Incorrect page number "'.$nCurrentPage.'"');
		}
		return $aData;
	}
	/**
	*   Возвращает всех пользователей
	*   @return array
	*/
	public static function selectAll() {
		$sql = 'SELECT * FROM `%s` ORDER by `id` ASC';
		$sql = sprintf($sql,USERS_TABLE);
		return DB::query($sql);
	}
	/**
	*   Отыскивает пользователя по части его логина
	*   @return
	*/
	public static function searchByLogin($szKey) {
		$szKey = trim($szKey);
		if (strlen($szKey) < 2)
		{
			throw new Exception('Search key too small');
		}
		$sql = 'SELECT id,login,email FROM `%s` WHERE `login` LIKE "%%%s%%" order by `login` ASC';
		$sql = sprintf($sql,
			USERS_TABLE,
			to_search_string($szKey));;
		return DB::query($sql);

	}
	/**
	*   Возвращает количество пользователей в системе
	*   @return
	*/
	public static function getAccountCount() {
		$sql = 'SELECT count(*) as `count` FROM `%s`';
		$sql = sprintf($sql,USERS_TABLE);
		$aResult = DB::get($sql);
		return $aResult['count'];
	}
}
?>