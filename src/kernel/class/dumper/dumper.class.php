<?php
//************************************************************//
//                                                            //
//                     Модуль Dumper                          //
//           Copyright (c) 2006  ООО SmartDesign              //
//                     отдел/сектор                           //
//            Email: support@smartdesign.by                   //
//                                                            //
//  Разработчик: Midgard   17.01.2006                         //
//  Модифицирован:  20.07.2006  by Midgard                    //
//                                                            //
//************************************************************//
use \Faid\DB;
// 13.10.2009 Gisma - добавил метод импорта файлов
//
class CDumper{
	/**
	 * @author Gisma 
	 * Импортирует файл-дампа в бд
	 */
	public static function importFile($szFileName)
	{
		$dumper = new CDumper();
		$dumper->import(file_get_contents($szFileName));
	}
	public static function exportFile( $szFileName,$dbase = '', $db_table = '' ) {
		$dumper = new CDumper();
		$result = $dumper->export( $dbase, $db_table );
		file_put_contents( $szFileName, $result );
	} 
	/**
	 * @author Midgard
	 * @desc Вспомогательная функцияю. Возращает массив запросов MySQL.
	 * @param &$ret Адрес возвращаемого массива.
	 * @param $sql  Строка, содержащая MySQL запросы.
	 */
	function __splitSqlFile(&$ret, $sql)
	{
		$sql = trim($sql);
		$sql_len = strlen($sql);
		$char = '';
		$string_start = '';
		$in_string = FALSE;
		$time0 = time();

		for ($i = 0; $i < $sql_len; ++$i)
		{
			$char = $sql[$i];
			if ($in_string)
			{
				for (;;)
				{
					$i = strpos($sql, $string_start, $i);
					if (!$i)
					{
						$ret[] = $sql;
						return TRUE;
					}
					elseif ($string_start == '`' || $sql[$i-1] != '\\')
					{
						$string_start = '';
						$in_string = FALSE;
						break;
					}
					else
					{
						$j = 2;
						$escaped_backslash     = FALSE;
						while ($i-$j > 0 && $sql[$i-$j] == '\\')
						{
							$escaped_backslash = !$escaped_backslash;
							$j++;
						}

						if ($escaped_backslash)
						{
							$string_start = '';
							$in_string = FALSE;
							break;
						}
						else
						{
							$i++;
						}
					}
				}
			}
			else if ($char == ';')
			{
				$ret[] = substr($sql, 0, $i);
				$sql = ltrim(substr($sql, min($i + 1, $sql_len)));
				$sql_len = strlen($sql);
				if ($sql_len)
				{
					$i = -1;
				}
				else
				{
					return TRUE;
				}
			}
			else if (($char == '"') || ($char == '\'') || ($char == '`'))
			{
				$in_string    = TRUE;
				$string_start = $char;
			}
			else if ($char == '#' || ($char == ' ' && $i > 1 && $sql[$i-2] . $sql[$i-1] == '--'))
			{
				$start_of_comment = (($sql[$i] == '#') ? $i : $i-2);
				$end_of_comment   = (strpos(' ' . $sql, "\012", $i+2))
								? strpos(' ' . $sql, "\012", $i+2)
								: strpos(' ' . $sql, "\015", $i+2);

				if (!$end_of_comment)
				{
					if ($start_of_comment > 0)
					{
						$ret[] = trim(substr($sql, 0, $start_of_comment));
					}
					return TRUE;
				}
				else
				{
					$sql = substr($sql, 0, $start_of_comment).ltrim(substr($sql, $end_of_comment));
					$sql_len = strlen($sql);
					$i--;
				}
			}
			$time1 = time();
			if ($time1 >= $time0 + 30)
			{
				$time0 = $time1;
				header('X-pmaPing: Pong');
			}
		}
		if (!empty($sql) && preg_match('@[^[:space:]]+@', $sql))
		{
			$ret[] = $sql;
		}
		return TRUE;
	}

	/**
	 * @author Midgard
	 * @desc Функция импортирует строку с запросами MySQL в базу.
	 * @param $sql_query Строка запросов.
	*/
	function import($sql_query)
	{
		if (!empty($sql_query))
		{
			$pieces = array();
			$this->__splitSqlFile($pieces,$sql_query);
			foreach ($pieces as $value)
			{
				DB::post($value);
			}
		}
	}

	/**
	 * @name Функция export
	 * @author Midgard
	 *
	 * @desc Функция экспортирует .sql файл из базы.
	 * Параметр $db_table
	 * Если указан массив таблиц, то делается дамп указанных таблиц.
	 * Если $db_table - строка (имя таблицы), то дамп делается только этой таблицы.
	 *
	 * @param $dbase Экспортируемая база данных.
	 * @param $db_table Необязательный параметр. пспользуется при экспорте выбранных(-ой) * таблиц(-ы)
	 * @param $storeTableData bool - Если флаг равен true то делается также дамп данных  
	 */
	function export($dbase = '', $db_table = '', $storeTableData = true )
	{
		if ( empty( $db_table )) {
			// выбираю все таблицы из базы
			$aTable =array();
			if (!empty($dbase)) {
				$aTable = $this->execQuery('SHOW TABLES FROM `'.$dbase.'`',false);
			} else {
				$aTable = $this->execQuery('SHOW TABLES',false);
			}
			foreach ($aTable as $key=>$row) {
				$aTable[$key] = $row[0];
			}
		} else {
			$aTable= array( $db_table );
		}
		// получаем список create table;
		$aTableCreate = array();
		foreach ($aTable as $row) {
			if (!empty($dbase)) {
				$sql = 'SHOW CREATE TABLE `'.\Faid\DB::escape($dbase).'`.`'.\Faid\DB::escape($row).'`';
			} else {
				$sql = 'SHOW CREATE TABLE `'.\Faid\DB::escape($row).'`';
			}
			$aTmp = $this->execQuery($sql,false);
			$aTableCreate[$row] = $aTmp[0][1];
		}
		// пишем заголовки и с учетом дропа таблицы
		$szSQLCreate = '';
		$szSQLTemplate = 'DROP TABLE IF EXISTS `%s` ;'."\r\n";
		foreach ($aTableCreate as $szTableName => $szTableCreate) {
			$szSQLCreate .= sprintf($szSQLTemplate,
				\Faid\DB::escape($szTableName)
			);
			$szSQLCreate.= $szTableCreate.';'."\r\n";
		}
		$szResult = $szSQLCreate;
		// получаем все данные для каждой таблицы
		if ( $storeTableData ) {
			$aTableData = array();
			foreach ($aTable as $row) {
				if (!empty($dbase)) {
					$sql = 'SELECT * FROM `'.\Faid\DB::escape($dbase).'`.`'.\Faid\DB::escape($row).'`';
				} else {
					$sql = 'SELECT * FROM `'.\Faid\DB::escape($row).'`';
				}
				$aTableData[$row] = $this->execQuery($sql,true);
			}
			// формируем SQL-запросы
			$sqlInsert ='';
			$sqlTemplate = 'INSERT INTO `%s` SET %s;'."\r\n";
			foreach ($aTableData as $szTableName=>$aCurrentTableData) {
				foreach ($aCurrentTableData as $row) {
					$aTmp = array();
					foreach ($row as $field_name =>$field_value) {
						$aTmp[] = '`'.\Faid\DB::escape($field_name).'`="'.\Faid\DB::escape($field_value).'"';
					}
					$szTmp = implode(',',$aTmp);
					$sqlInsert .= sprintf(
						$sqlTemplate,
						\Faid\DB::escape($szTableName),
						$szTmp
						);
				}
			}
			$szResult .= "\r\n".$sqlInsert;
		}
		return $szResult;
	}
	protected function execQuery($sql,$bUseAssoc =true) {
		$aResult = DB::query( $sql, $bUseAssoc  );
		return $aResult;
	}
}
?>