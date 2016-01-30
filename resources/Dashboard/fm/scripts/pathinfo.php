<?
require_once __DIR__.'/_lib/loader.php';

header( 'HTTP/1.1 200' );

/*$_POST['rpc'] = <<<EOD
<EXTASY>
<CALL>
<FUNCTION NAME="getPathInfo">
	<PARAM name="path" ><![CDATA[|/|]]></PARAM>
</FUNCTION>
</CALL>
</EXTASY>
EOD;*/
function FullPerms($szFileName) {

	$perms = fileperms($szFileName);

	if (($perms & 0xC000) == 0xC000) {
		// Сокет
		$info = 's';
	} elseif (($perms & 0xA000) == 0xA000) {
		// Символическая ссылка
		$info = 'l';
	} elseif (($perms & 0x8000) == 0x8000) {
		// Обычный
		$info = '-';
	} elseif (($perms & 0x6000) == 0x6000) {
		// Специальный блок
		$info = 'b';
	} elseif (($perms & 0x4000) == 0x4000) {
		// Директория
		$info = 'd';
	} elseif (($perms & 0x2000) == 0x2000) {
		// Специальный символ
		$info = 'c';
	} elseif (($perms & 0x1000) == 0x1000) {
		// Поток FIFO
		$info = 'p';
	} else {
		// Неизвестный
		$info = 'u';
	}

	// Владелец
	$info .= (($perms & 0x0100) ? 'r' : '-');
	$info .= (($perms & 0x0080) ? 'w' : '-');
	$info .= (($perms & 0x0040) ?
				(($perms & 0x0800) ? 's' : 'x' ) :
				(($perms & 0x0800) ? 'S' : '-'));

	// Группа
	$info .= (($perms & 0x0020) ? 'r' : '-');
	$info .= (($perms & 0x0010) ? 'w' : '-');
	$info .= (($perms & 0x0008) ?
				(($perms & 0x0400) ? 's' : 'x' ) :
				(($perms & 0x0400) ? 'S' : '-'));

	// Мир
	$info .= (($perms & 0x0004) ? 'r' : '-');
	$info .= (($perms & 0x0002) ? 'w' : '-');
	$info .= (($perms & 0x0001) ?
				(($perms & 0x0200) ? 't' : 'x' ) :
				(($perms & 0x0200) ? 'T' : '-'));

	return $info;

}
/**
*   @desc Собирает информацию о пути
*   @return Возвращает массив с тремя ключами 1 - директории 2 - файлы 3 - ошибка
*/
function getPathInfo($path,$szErrorMessage = '') {
	$fs = DAO::getInstance('fs');
	$szTestPath = FILE_PATH.$path;
	// Путь должен существовать
	$szTest = checkPath($szTestPath);

	if (!empty($szTest) || !is_readable($szTestPath) || !@is_dir($szTestPath)) {
		if (!is_readable($szTestPath)) {
			$szErrorMessage .= 'Путь не читаем'."\r\n";
		}
		if (!is_dir($szTest)) {
			$szErrorMessage .= 'Путь не является директорией'."\r\n";
		}

		$szErrorMessage .= (strlen($szTest) > 0)?($szTest."\r\n"):'';
		$szPath = FILE_PATH;
	} else {
		$szPath = $szTestPath;
	}

	$szPath = toCanonical(realpath($szPath));

	$aResult = array(
		'path'  =>  substr($szPath,strlen(realpath(FILE_PATH)) + 1)
		);

	if (empty($aResult['path']))
		$aResult['path'] = '';
	$aFile = array();
	// проверяем не находимся ли мы в FILE_PATH
	if (toCanonical(realpath($szPath)) != toCanonical(realpath(FILE_PATH))) {
		// если находимся то добавляем ссылочку ../
		$aFile[] = arraY(
			'name'          =>  '..',
			'is_directory'  =>  1,
			'fileSize'      =>  ' ',
			'owner'         =>  ' ',
			'rights'        =>  ' ',
			'date'          =>  ' ',
			);
	}
	$aList = $fs->getDirList($szPath);

	for ($i = 0;$i < sizeof($aList);$i++) {
		$szDate = date('Y.m.d h:i',filectime($szPath.$aList[$i]));
		$aFile[] = array(
			'name'  =>  $aList[$i],
			'is_directory'  =>  1,
			'fileSize'      =>  0,
			'owner'         =>  fileowner($szPath.$aList[$i]),
			'rights'        =>  FullPerms($szPath.$aList[$i]),
			'date'          =>  $szDate,
			);
	}
	$aList = $fs->getFileList($szPath);
	for ($i = 0;$i < sizeof($aList);$i++) {
		// получаем дату
		$szDate = date('Y.m.d h:i',filectime($szPath.$aList[$i]));
		// добавляем в массив
		$aFile[] = arraY(
			'name'  =>  $aList[$i],
			'is_directory'  =>  intval(is_dir($szPath.$aList[$i])),
			'fileSize'      =>  filesize($szPath.$aList[$i]),
			'owner'         =>  fileowner($szPath.$aList[$i]),
			'rights'        =>  FullPerms($szPath.$aList[$i]),
			'date'          =>  $szDate,
			);
	}
	$aResult['errorMessage'] = $szErrorMessage;
	$aResult['aFile'] = $aFile;
	return $aResult;
}
/**
*   @desc Устанавливает права на удаленный файл
*   @return
*/
function setFileMode($szFileName,$szMode,$bRecursive = 0) {
	$fs = DAO::getInstance('fs');
	// Проверяем доступность пути и пытаемся установить права
	$szFileName = FILE_PATH.$szFileName;
	$szErrorMessage = '';
	if (!file_exists($szFileName) || !$fs->pathInPath(FILE_PATH,$szFileName)) {
		$szErrorMessage = 'Неправильно задан путь'."\r\n";
	} else {
		// Устанавливаем права
		$nMode = octdec($szMode);
		$bResult = true;
		if (intval($szMode) > 0) {
			if ($bRecursive) {
				$bResult = $fs->chmod($szFileName,$nMode);
			} else {
				$bResult = chmod($szFileName,$nMode);
			}
			if (!$bResult) {
				$szErrorMessage = 'Ошибки при установке прав'."\r\n";
			}
		}
	}
	return getPathInfo(substr(dirname($szFileName).'/',strlen(FILE_PATH)),$szErrorMessage);
}
/**
*   @desc Переименует файл
*   @return
*/
function renameFile($szOldFile,$szNewFile) {
	$fs = DAO::getInstance('fs');
	// режем слеши
	$szNewFile = preg_replace('/[\\/\:\*\?\<\>\"|]/','',$szNewFile);
	// Проверяем доступность пути и пытаемся установить права
	$szFileName = toCanonical(realpath(FILE_PATH.$szOldFile));
	$szErrorMessage = checkPath($szFileName);
	if (!empty($szErrorMessage)) {
		// Если сообщение н
		return getPathInfo('',$szErrorMessage);
	}
	// Сохраняем старое имя
	$szOld = $szFileName;
	// Устанавливаем новое имя
	$szFileName = toCanonical(dirname($szOld).'/'.$szNewFile);
	// Проверяем что старый путь не равен FILE_PATH
	$bNotRoot = $szOld != toCanonical(realpath(FILE_PATH));
	// И что пути не одинаковы
	$bNotEqual = $szOld  != $szFileName;

	if ($bNotEqual && ($bNotRoot) ) {
		if ($fs->copy($szOld,$szFileName)) {
			$fs->delete($szOld);
		} else {
			$szErrorMessage .='Ошибки при копировании'."\r\n";
		}

	} else {
		$szErrorMessage .= !$bNotRoot?'Такое перемещение недоступно'."\r\n":'';
	}
	return getPathInfo(substr(dirname(FILE_PATH.$szOldFile).'/',strlen(FILE_PATH)),$szErrorMessage);
}
function unlinkFile($szFile) {
	$fs = DAO::getInstance('fs');
	// Проверяем доступность пути и пытаемся установить права
	$szFileName = toCanonical(realpath(FILE_PATH.$szFile));
	$szErrorMessage = checkPath($szFileName);
	if (!empty($szErrorMessage)) {
		// Если сообщение не пусто возвращаем ошибку
		return getPathInfo('',$szErrorMessage);
	}
	// Удалять корень нельзя, проверяем возможность его удаления
	$szFilePath = toCanonical(realpath(FILE_PATH));
	$bNotRoot = $szFileName != $szFilePath;
	if ($bNotRoot) {
		$fs->delete($szFileName);
	}
	$szPath = (substr(toCanonical(dirname($szFileName)),strlen($szFilePath)));
	return getPathInfo(is_bool($szPath)?'':$szPath,$szErrorMessage);
}
/**
*   @desc Создает каталог
*   @return
*/
function createFolder($basepath,$szName) {
	$fs = DAO::getInstance('fs');
	// проверяем путь на доступ
	$szBasePath = toCanonical(realpath(FILE_PATH.$basepath));
	$szErrorMessage = checkPath($szBasePath);
	if (!is_dir($szBasePath) || !empty($szErrorMessage)) {
		return getPathInfo('',$szErrorMessage);
	}
	// создаем каталог
	$szName= preg_replace('/[\\/\:\*\?\<\>\"|]/','',$szName);

	$fs->createPath(toCanonical($szBasePath.$szName));
	$fs->chmod(toCanonical($szBasePath.$szName));
	// вызываем список файлов в каталоге
	return getPathInfo($basepath,$szErrorMessage);
}
/**
*   @desc Замеряет размер указанного объекта
*   @return
*/
function folderSize($basePath) {
	$fs = DAO::getInstance('fs');
	// проверяем путь на доступ
	$szBasePath = toCanonical(realpath(FILE_PATH.$basePath));
	$szErrorMessage = checkPath($szBasePath);
	if (empty($szErrorMessage) ) {
		$nSize = $fs->getSize(toCanonical($szBasePath));
	} else {
		$nSize = 0;
	}
	$aResult = array(
		'error' =>  $szErrorMessage,
		'size'  =>  $nSize,
		);
	return $aResult;
}
header( 'HTTP/1.1 200' );
$export = new ExportJS();
$export->add('getPathInfo');
$export->add('setFileMode');
$export->add('renameFile');
$export->add('unlinkFile');
$export->add('createFolder');
$export->add('folderSize');
$export->setEncoding('utf-8');
$export->process();
?>