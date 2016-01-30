<?
//************************************************************//
//                                                            //
//         Сервис работы с файловой системой                  //
//       Copyright (c) 2006  ООО SmartDesign                  //
//               отдел/сектор                                 //
//       Email: support@smartdesign.by                        //
//                                                            //
//  Разработчик: Gisma                                        //
//  Модифицирован:  2006.03.07  by Gisma                      //
//                                                            //
//************************************************************//
class DAO_FileSystem extends DAO_Service{
	private static $instance;
	public function push() {

	}
	public function pop() {

	}
/**
*   @desc Ищет в указанной директории по указанной маске файлы
*   @param $szDirName string имя директории
*   @param $szMask string маска поиска файлов
*/
	function getDirContent($szDirName = './') {
		$szDirName = realpath($szDirName);

		if (!is_dir($szDirName))
			throw new Exception('DAO_FileSystem:getDirContent $szDirName='.$szDirName.' не является именем каталога');

		if ($szDirName[strlen($szDirName) - 1] != '/')
			$szDirName.= '/';

		$dir = dir($szDirName);
		$aResult = array();
		while (($szFileName = $dir->read()) !== false )
			if (($szFileName != '.') && ($szFileName != '..'))
				$aResult[] = $szFileName;

		$dir->close();
		return $aResult;
	}
/**
*   @desc Ищет в указанной директории директории
*   @param $szDirName string имя директории
*/
	function getDirList($szDirName = './') {
		$szDirName = str_replace('\\','/',$szDirName);
		if (!is_dir($szDirName))
			trigger_error('DAO_FileSystem:getDirContent $szDirName="'.$szDirName.'" не является именем каталога',E_USER_ERROR);
		if ($szDirName[strlen($szDirName) - 1] != '/')
			$szDirName.= '/';
		$dir = dir($szDirName);
		$aResult = array();
		while (($szFileName = $dir->read()) !== false )
			if (($szFileName != '.') && ($szFileName != '..') && (is_dir($szDirName.$szFileName))) {
				$aResult[] = $szFileName;
		}
		$dir->close();
		return $aResult;
	}
/**
*   @desc Ищет в указанной директории файлы
*   @param $szDirName string имя директории
*/
	function getFileList($szDirName = './',$szPattern ='#.+#') {

		if (!is_dir($szDirName))
			trigger_error('DAO_FileSystem:getDirContent $szDirName="'.$szDirName.'" не является именем каталога',E_USER_ERROR);
		if ($szDirName[strlen($szDirName) - 1] != '/')
			$szDirName.= '/';

		$dir = dir($szDirName);
		$aResult = array();

		while (($szFileName = $dir->read()) !== false )

			if (($szFileName != '.') && ($szFileName != '..') && (is_file($szDirName.$szFileName))) {
				if (preg_match($szPattern,$szFileName)) {
					$aResult[] = $szFileName;
				}
			}
		$dir->close();
		return $aResult;
	}

/**
*   @desc $szUploadName имя закачиваемого файла
*/
	function upload($szUploadName,$szTo,$nMaxSize = 0) {

		if ((!empty($_FILES[$szUploadName])) && is_uploaded_file($_FILES[$szUploadName]['tmp_name'])) {
			// Проверяем по размеру
			$maxCondition = ($nMaxSize != 0) 
				&& ($nMaxSize < intval($_FILES[$szUploadName]['size']));
			
			if ($maxCondition) return false;
			move_uploaded_file($_FILES[$szUploadName]['tmp_name'], $szTo);
			if (!file_exists($szTo)) {
				throw new \ForbiddenException('DAO_FileSystem Can`t upload file "'.$szTo.'"');
			}
			return true;
		} else
			return false;
	}
	/**
	*   @desc Удаляет указанный путь
	*   @return
	*/
	function delete($szPath,$bIgnoreError = false) {
		if (is_dir($szPath)) {
			$szLast = $szPath[strlen($szPath) - 1];
			if (( $szLast != '/') && ($szLast != '\\')) {
				$szPath .= '/';
			}
			$this->deleteDir($szPath,$bIgnoreError);
		}
		elseif( is_file($szPath))
			@unlink($szPath);
		else
			if (!$bIgnoreError) {
				trigger_error('DAO_FileSystem путь ("'.$szPath.'") не существует',E_USER_ERROR);
			}


	}
	function chmod($szPath,$nMod = 0777) {
		@chmod($szPath,$nMod);
		if (@is_dir($szPath))  {
			$szLast = $szPath[strlen($szPath) - 1];
			if (( $szLast != '/') && ($szLast != '\\')) {
				$szPath .= '/';
			}
			return $this->chmoddir($szPath,$nMod);
		}
		elseif(@is_file($szPath))
			@chmod($szPath,$nMod);
		else {
			trigger_error('DAO_FileSystem путь ("'.$szPath.'") не существует',E_USER_ERROR);
		}

	}

	function chmoddir($szPath,$nMod = 0777) {
		$aData = $this->getDirContent($szPath);
		$bResult = true;
		$bResult = $bResult && chmod($szPath,$nMod);
		if (count($aData) > 0) {
			foreach ($aData as $szFileName) {
				if (@is_file($szPath.$szFileName)) {
					$bResult = $bResult && chmod($szPath.$szFileName,$nMod);
				}elseif (@is_dir($szPath.$szFileName)) {
					$bResult = $bResult && $this->chMod($szPath.$szFileName.'/',$nMod);
				}
			}
		}

		return $bResult;
	}
	function deleteDir($szPath = '') {
		$aData = $this->getDirContent($szPath);

		if (count($aData) > 0) {
			foreach ($aData as $szFileName) {
				if (is_file($szPath.$szFileName)) {
					@unlink($szPath.$szFileName);
				}elseif (is_dir($szPath.$szFileName)) {
					$this->deleteDir($szPath.$szFileName.'/');
				}
			}
		}
		@rmdir($szPath);
	}
	/**
	*   @desc Копирует объект по указанному пути, если базовая часть пути $to не существует, то путь
	*   создается
	*/
	function copy($from,$to) {
		$from = realpath($from);
		//$to = realpath($to);
		
		$bResult = true;
		// Разбираем путь по слешам и криэтим директорию
		$this->createPath(dirname($to));
		if (is_dir($from)) {
			// Если директория не существует, то создает папку, где будет лежать файл
			if (!file_exists($to)) {
				$bResult = $bResult && mkdir($to);
			}
			$bResult = $bResult && $this->__copy($from,$to);
		} else {
			$bResult = $bResult && copy($from,$to);

		}

		return $bResult;
	}
	/**
	*   @desc Копирует директории
	*/
	function __copy($from,$to) {

		$aItems = $this->getDirContent($from);

		$bResult = true;
		for ($i = 0; $i < sizeof($aItems); $i++) {
			// Игнорируем служебные (.svn)
			if ($aItems[$i] == '.svn') continue;

			// Это папка, копируем папку 
			if (is_dir($from.'/'.$aItems[$i])) {
				if (!file_exists($to.'/'.$aItems[$i])) {
					if (is_file($to.'/'.$aItems[$i])) {
						trigger_error('DAO_FileSystem:copy $to='.$to.'/'.$aItems[$i].'  является файлом, но не каталогом',E_USER_ERROR);
					}

					$bResult = $bResult && mkdir($to.'/'.$aItems[$i]);
				}
				$bResult = $bResult && $this->__copy($from.'/'.$aItems[$i],$to.'/'.$aItems[$i]);
			} else {
				$bResult = $bResult && copy($from.'/'.$aItems[$i],$to.'/'.$aItems[$i]);
			}
		}
		return $bResult;
	}
	/**
	*   @desc Проверяет находится ли один путь($testPath) в другом $path
	*   @var $path -
	*/
	function pathInPath($path,$testPath) {

		$szPath = realpath($path);
		$szTest = realpath($testPath);
		if (is_bool($szPath) || is_bool($szTest) || !file_exists($szTest)) {
			return false;
		}
		return (substr($szTest,0,strlen($szPath)) == $szPath);
	}
	/**
	*   @desc Отрезает часть пути относительно базового
	*/
	function truncPath($basePath,$path) {
		$basePath = realpath($basePath);
		$path = realpath($path);

		if ($basePath != $path)
			return substr($path,strlen($basePath));
		else
			return '';


	}
	/**
	*   @desc Создает директории, указанные в пути
	*/
	function createPath($path) {
		$aTo = preg_split('#[/\\\]#',$path);
		//
		$prefix = stristr(php_uname(), 'windows') !== false ? '' : '/';
		$error_handler = set_error_handler('filesystem_void_func',E_ALL);
		for ($i = 0; $i < sizeof($aTo);$i++)  {
			if ( @file_exists($prefix) && (!empty($aTo[$i])) && !@file_exists($prefix.$aTo[$i]) ) {
				mkdir($prefix.$aTo[$i]);
			}
            if ( !empty( $aTo[$i])) {
                $prefix .= $aTo[$i].'/';
            }

		}
		set_error_handler(create_function('','return false;'),E_ALL);
	}
	function __getDirSize($target) {
		$aData = $this->getDirContent($target);
		$nSum = 0;

		for ($i = 0; $i < sizeof($aData); $i++) {
			if (is_dir($target.'/'.$aData[$i].'/')) {
				$nSum += $this->__getDirSize($target.'/'.$aData[$i].'/');
			} elseif (is_file($target.'/'.$aData[$i])) {
				$nSum += filesize($target.'/'.$aData[$i]);
			} else {
				trigger_error('DAO_FileSystem <span style="color:red">Неопределенный путь : "'.$target.'/'.$aData[$i].'"</span>',E_USER_WARNING);
			}

		}
		return $nSum;
	}

	/**
	*   @desc Возвращает объем физической памяти занимаемой target
	*/
	function getSize($target) {
		$target = str_replace('\\','/',realpath($target));
		if (file_exists($target)) {
			if (is_file($target)) {
				$nSize = filesize($target);
			} else {
				$nSize = $this->__getDirSize($target);
			}
		} else {

			trigger_error('<span style="color:red">DAO_FileSystem::getSize не является объектом файловой систем</span>',E_USER_WARNING);
			return -1;
		}
		return $nSize;
	}
	/**
	*   @desc Записываем $content в указанный файл $filename
	*/
	function writeFile($filename,$content ='') {
		file_put_contents($filename,$content);
	}
	/**
	*   @desc Обращает имя файла в каноническое
	*   @return
	*/
	function toCanonical($szPath) {
		if (empty($szPath)) return '';
		$szPath = realpath($szPath);
		if (is_dir($szPath)) {
			$szLast = $szPath[strlen($szPath) - 1];
			if (( $szLast != '/') && ($szLast != '\\')) {
				$szPath .= '/';
			}
		}
		$szPath = str_replace('\\','/',$szPath);
		return $szPath;
	}
	public static function getInstance() {
		if (!is_object(self::$instance)) {
			self::$instance = new DAO_FileSystem();
		}
		return self::$instance;
	}
}
function filesystem_void_func() {
}
?>