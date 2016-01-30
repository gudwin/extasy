<?php
/**
 * Данный класс содержит доп. функции модуля для работы с шаблонами и прочим
 * @author Gisma
 *
 */
class UsersMisc {
	public static function getCurrentUser() {
		if (!UsersLogin::isLogined()) {
			return;
		}
		return array(
			'currentUser' => UsersLogin::getCurrentSession()->getParseData()
		);
	}
} 
?>