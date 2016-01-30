<?
use \Faid\DB;
use \Faid\Configure\Configure;
use \Faid\Configure\ConfigureException;

define( 'SITEMAP_TEMPLATE_TABLE', 'sitemap_template' );

class SitemapTemplateHelper {
	const ConfigureKey = 'Sitemap.Templates.ViewPath';
	public static function getViewPath() {
		try {
			$result = Configure::read( self::ConfigureKey );
		} catch ( ConfigureException $e ) {
			$result = '';
		}

		if ( empty( $result )) {
			$result = VIEW_PATH;
		}
		return $result;
	}
	/**
	 * Создает шаблон
	 *
	 * @param $tpl string имя шаблона
	 */
	public static function create( $tpl ) {
		self::checkPathCorrect( $tpl );
		//
		$szPath = self::createTemplatePath( $tpl );
		// Проверяем, что директория существует
		$szDirname = dirname( $szPath );
		if ( file_exists( $szDirname ) && is_dir( $szDirname ) ) {
			if ( !file_exists( $szPath ) ) {
				// Создаем пустой файл
				file_put_contents( $szPath, '' );
			} else {
				throw new SitemapTemplateException( 'Template `' . $tpl . '` already exists' );
			}
		} else {
			throw new SitemapTemplateException( 'Directory `' . $szDirname . '` not exists' );
		}
	}

	/**
	 * Обновляет информацию о шаблоне
	 *
	 * @param $tpl       string имя шаблона
	 * @param $szContent string содержимое шаблона
	 * @param $szComment string комментарий к шаблону (в HTML-виде)
	 */
	public static function update( $tpl, $szContent, $szComment ) {
		self::checkPathCorrect( $tpl );
		//
		$szPath = self::createTemplatePath( $tpl );
		// Проверяем, что шаблон существует
		if ( !file_exists( $szPath ) ) {
			throw new SitemapTemplateException( 'Template `' . $tpl . '` not found' );
		}
		// Проверяем существование записи в базе
		$aDB = self::getFromDB( $tpl );
		if ( empty( $aDB ) ) {
			$tpl       = \Faid\DB::$connection->real_escape_string( $tpl );
			$szComment = \Faid\DB::$connection->real_escape_string( $szComment );
			//
			$sql = 'INSERT INTO `%s` SET `path`="%s",`comment`="%s"';
			$sql = sprintf( $sql, SITEMAP_TEMPLATE_TABLE, $tpl, $szComment );
			DB::post( $sql );
		} else {
			$aDB[ 'id' ];
			// Сохраняем коммент
			$sql = 'UPDATE `%s` SET `comment`="%s" WHERE `id`="%d"';
			$sql = sprintf( $sql, SITEMAP_TEMPLATE_TABLE, $szComment, $aDB[ 'id' ] );
			DB::post( $sql );
		}
		// Сохраняем содержимое файла
		file_put_contents( $szPath, $szContent );

	}

	/**
	 *
	 * @param $tpl string имя шаблона
	 */
	public static function delete( $tpl ) {
		self::checkPathCorrect( $tpl );
		//
		$szPath = self::createTemplatePath( $tpl );
		//
		if ( file_exists( $szPath ) || is_file( $szPath ) ) {
			unlink( $szPath );
		} else {
			throw new SitemapTemplateException( 'Template `' . $tpl . '` not exists' );
		}
	}

	/**
	 *
	 * @param dir string имя директории
	 */
	public static function createDirectory( $dir ) {
		self::checkPathCorrect( $dir );
		//
		$szPath = self::createDirectoryPath( $dir );
		//
		if ( file_exists( $szPath ) ) {
			throw new SitemapTemplateException( 'Directory `' . $szPath . '` already exists' );
		}
		DAO_FileSystem::getInstance()->createPath( $szPath );
	}

	/**
	 * Удаляет директорию
	 *
	 * @param dir string имя директории
	 */
	public static function deleteDirectory( $dir ) {
		self::checkPathCorrect( $dir );
		//
		$szPath = self::createDirectoryPath( $dir );
		//
		if ( !file_exists( $szPath ) ) {
			throw new SitemapTemplateException( 'Direcotry `' . $szPath . '` not exists' );
		}
		rmdir( $szPath );
	}

	/**
	 * Возвращает информацию о шаблоне
	 *
	 * @param $tpl string имя шаблона
	 *
	 * @return array Ассоциативный массив с информацией о шаблоне: его содержимое, комментарий и привязанные функции
	 */
	public static function getInfo( $tpl ) {

		self::checkPathCorrect( $tpl );
		//
		$szPath = self::createTemplatePath( $tpl );
		//
		if ( !file_exists( $szPath ) ) {
			throw new SitemapTemplateException( 'Template `' . $tpl . '` not found' );
		}
		$aResult              = array( 'content' => '', 'comment' => '', 'functions' => '' );
		$aResult[ 'content' ] = file_get_contents( $szPath );
		$aDB                  = self::getFromDB( $tpl );
		$aResult[ 'comment' ] = !empty( $aDB ) ? $aDB[ 'comment' ] : '';
		//
		return $aResult;
	}

	/**
	 * Возвращает все шаблоны (файлы с расширением .tpl) находящиеся в папке _cfg/view
	 * @return array массив шаблонов
	 */
	public static function selectTemplates( $path = '' ) {
		$szPath = self::getViewPath(). $path;
		$fs     = DAO_FileSystem::getInstance();
		// Перебор всех файлов и папок
		$aResult = array();
		$aData   = $fs->getDirContent( $szPath );

		foreach ( $aData as $row ) {
			// Если это папка, опускаемся по перебору
			if ( is_dir( $szPath . $row ) ) {
				$child   = self::selectTemplates( $path . $row . '/' );
				$aResult = array_merge( $aResult, $child );
			} else {
				$aPath = pathinfo( self::getViewPath(). $path . $row );

				if ( !empty( $aPath[ 'extension' ] ) && ( $aPath[ 'extension' ] == 'tpl' ) ) {
					$aResult[ $path . basename( $row, '.tpl' ) ] = 1;


				}


			}
		}
		return $aResult;

	}

	/**
	 * Проверяет корректность пути
	 */
	protected static function checkPathCorrect( $tpl ) {
		if ( empty( $tpl ) ) {
			throw new SitemapTemplateException( 'Empty path' );
		}

		$realFile  = realpath( self::getViewPath() . dirname( $tpl ) );
		$view_path = realpath( self::getViewPath() );

		if ( empty( $realFile ) || ( substr( $realFile, 0, strlen( $view_path ) ) != $view_path ) ) {
			throw new SitemapTemplateException( 'Path incorrect' );
		}
		return true;
	}

	/**
	 * Создает по имени шаблона полный путь
	 */
	protected static function createTemplatePath( $tpl ) {
		return self::getViewPath() . $tpl . '.tpl';
	}

	/**
	 *  Создает имя директории
	 */
	protected static function createDirectoryPath( $dir ) {
		return self::getViewPath() . $dir . '/';
	}

	/**
	 * Возвращает ряд с комментарием о шаблоне из бд
	 */
	protected static function getFromDB( $tpl ) {
		$tpl = \Faid\DB::$connection->real_escape_string( $tpl );
		//
		$sql = 'SELECT * FROM `%s` WHERE `path`="%s"';
		$sql = sprintf( $sql, SITEMAP_TEMPLATE_TABLE, $tpl );
		//
		return DB::get( $sql );
	}

}

class SitemapTemplateException extends Exception {
}

?>