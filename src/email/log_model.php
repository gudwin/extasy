<?php
use \Faid\DB;
use \Faid\DBSimple;
/**
 * Объект отдельной записи в логе
 * @author Gisma
 *
 */
class EmailLogModel extends \Extasy\Model\Model {
	const ModelName = 'EmailLogModel';
	const TableName = 'email_log';
	public static function getLast() {

		$found = DBSimple::get(\EmailLogModel::getTableName(),'','order by id desc');
		if ( empty( $found ) ) {
			throw new \NotFoundException('Last log message not found. Log table empty');
		}
		$log = new \EmailLogModel( $found  );
		return $log;
	}
	/**
	 * Возвращает все записи из лога
	 */
	public static function selectAll() {
		$sql = 'select * from %s order by `id` desc';
		$sql = sprintf($sql,self::TableName);
		$data = DB::query($sql);
		$result = array();
		foreach ($data as $row) {
			$result[] = new EmailLogModel($row);
		}
		return $result;
	}
	/**
	 * Удаляет все записи из лога
	 */
	public static function deleteAll() {
		// Пока что достаточно полной очистки базы
		DB::post('TRUNCATE `'.self::TableName.'`');
	}
	public static function getFieldsInfo() {
		return array(
			'table' => self::TableName,
			'fields' => array(
				'id' => '\\Extasy\\Columns\\Index',
				'date' => arraY(
					'class' => '\\Extasy\\Columns\\Datetime',
					'title' => 'Дата отсылки',
				),
				'to' => arraY(
					'title' => 'Получатель',
					'class' => '\\Extasy\\Columns\\Input'
				),
				'subject' => arraY(
					'title' => 'Тема ',
					'class' => '\\Extasy\\Columns\\Input'
				),
				'status' => arraY(
					'title' => 'Статус отсылки',
					'class' => '\\Extasy\\Columns\\Input'
				),
				'content' => arraY(
					'title' => 'Текст письма',
					'class' => '\\Extasy\\Columns\\Html'
				),
				'attach1' => arraY(
					'title' => 'Аттач №1',
					'class' => '\\Extasy\\Columns\\File'
				),
				'attach2' => arraY(
					'title' => 'Аттач №2',
					'class' => '\\Extasy\\Columns\\File'
				),
				'attach3' => arraY(
					'title' => 'Аттач №3',
					'class' => '\\Extasy\\Columns\\File'
				),
				'attach4' => arraY(
					'title' => 'Аттач №4',
					'class' => '\\Extasy\\Columns\\File'
				),
				'attach5' => arraY(
					'title' => 'Аттач №5',
					'class' => '\\Extasy\\Columns\\File'
				),
			),
		);
	}
	
}