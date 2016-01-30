<?
//************************************************************//
//            Функции для работы с HTTP протоколом            //
//       Copyright (c) 2011  Extasy Team                      //
//       Email:   dmitrey.schevchenko@gmail.com               //
//                                                            // 
//  Разработчик: Gisma (14.03.2011)                           //
//                                                            //
//************************************************************//
class httpHelper {
	/**
	*   @desc Выставляет 403:)
	*/
	public static function error403() {
		header ("HTTP/1.0 403 Forbidden"); 
	}
	/**
	*   @desc Выставляет 404:)
	*/
	public static function error404() {
		header("HTTP/1.0 404 Not Found");
	}
	/**
	 * Returns current url 
	 */
	public static function getCurrentUrl() {
		$pageURL = 'http';
		$isHttps = !empty( $_SERVER['HTTPS']) && ("on" == $_SERVER["HTTPS"] );
		if ( $isHttps ) { $pageURL .= "s"; }
		
		$pageURL .= "://";
		
		if ($_SERVER["SERVER_PORT"] != "80") {
			$pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
		} else {
			$pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
		}
		return $pageURL;	
	}
	/**
	 *   Отсылает заголовки последнего изменения страницы
	 *   @return
	*/
	public static function setLastModified($date) {
		$szEtag = md5($date);
		$szDate = date('U');

		if ((!empty($_SERVER['HTTP_IF_NONE_MATCH'])) && ($_SERVER['HTTP_IF_NONE_MATCH'] == $szEtag)) {
			header('HTTP/1.1 304 Not Modified');
			header('ETag: "'.$szEtag.'"');
			die();
		}
		header('HTTP/1.1 200 OK');
		header('Etag: "'.$szEtag.'"');
		header('Last Modified: '.$szDate.' GMT');
		header('Content-Type: text/html; charset=utf-8');
	}
	public static function sendFile($szFileName,$szMimeType,$szContent) {
		// We'll be outputting a PDF
		header('Content-type: '.$szMimeType);

		// It will be called downloaded.pdf
		header('Content-Disposition: attachment; filename="'.$szFileName.'"');
		header("Content-Transfer-Encoding: binary");
	    header("Content-Length: ".strlen($szContent)); 
		ob_clean(); 
		// The PDF source is in original.pdf
		print $szContent;
		die();
	}
	
	/**
	 * Осуществляет 302-й редирект
	 * @param $url string
	 */
	public static function jump($url = '') {
		if (empty($url)) {
			die('Page:jump Url empty');
		}

		if (!headers_sent())
		{
			/** высылаем Header */
			header( 'HTTP/1.1 302 Found' );
			header( "Location: ". $url );
		} else {
			echo "<script>document.location.href='$url';</script>\n";
		}
		die();
	}
}

?>