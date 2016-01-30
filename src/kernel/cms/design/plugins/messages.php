<?php
/**
 * Класс для отображения сообщений
 * @author Gisma
 *
 */
class CMSDesignMessages {
	public function showAlerts( ) {
		if (!empty($_SESSION['cms_error'])) {
			$this->error($_SESSION['cms_error']);
			unset($_SESSION['cms_error']);
		}
		if (!empty($_SESSION['cms_message'])) {
			$this->message($_SESSION['cms_message']);
			unset($_SESSION['cms_message']);
		}
	}
	/**
	 * Выводит список/одно сообщений об ошибке
	 * @param mixed $errorList Может быть как массивом сообщений, так и отдельным сообщением
	 */
	public function error($errorList =array()) {
		if (!is_array($errorList)) {
			$errorList = array($errorList);
		}
		?>
		<script type="text/javascript">
		<?foreach ($errorList as $row):?>
		jQuery( function () {
		dtError(<?php print json_encode( htmlspecialchars($row) ) ?>)
		<?endforeach?>
		});
		</script>
		<?
	}
	/**
	 * Выводит сообщения сгенерированные скриптом
	 * @param mixed $list Может быть как массивом сообщений, так и отдельным сообщением
	 */
	public function message($list =array()) {
		if (!is_array($list)) {
			$list = array($list);
		}
		?>
		<script type="text/javascript">
		jQuery( function ( $ ) {
		<?php foreach ($list as $row):?>		
			dtAlert(<?php print json_encode( htmlspecialchars( $row ) ) ?>)
		<?php endforeach; ?>
		})
		
		</script>
		<?php 
	}	
}