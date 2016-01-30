<?php
use \Faid\DBSimple;
class ACLMisc {
	public static function export($parentId = 0) {
		$parentId = IntegerHelper::toNatural($parentId);
		$result = array();
		$data = DBSimple::select(ACL_TABLE,"parentId = '{$parentId}'",'`name` ASC');
		foreach ($data as $row) {
			$children = self::export($row['id']);
			$add = array(
				'id' => $row['id'],
				'name' => $row['name'],
				'title' => $row['title'],	
				'fullPath' => $row['fullPath']
			);
			if (!empty($children)) {
				$add['children'] = $children;
			}
			$result[] = $add;
		}
		return $result;
	}

}