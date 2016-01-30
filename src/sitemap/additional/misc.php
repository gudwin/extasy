<?php
/**
 * Прочие фунции
 *
 */
class SitemapMisc { 
	/**
	 * Сравнение того, что один адрес находится в другом
	 * @param string $url1
	 * @param string $url2
	 */
	public static function urlsMatch($url1,$url2) {
		$subUrl = substr($url1,0,strlen($url2));
		if ($subUrl == $url2) {
			return true;
		} else {
			return false;
		}
	}
}