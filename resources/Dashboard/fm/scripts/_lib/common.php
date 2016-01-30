<?
function toCanonical($szPath) {
	if (empty($szPath)) return '';
	if (is_dir($szPath)) {
		$szLast = $szPath[strlen($szPath) - 1];
		if (( $szLast != '/') && ($szLast != '\\')) {
			$szPath .= '/';
		}
	}
	$szPath = str_replace('\\','/',$szPath);
	return $szPath;
}
function checkPath($szTestPath) {
	$fs = DAO::getInstance('fs');
	$szErrorMessage = '';
	
	if (!file_exists($szTestPath)) {
		$szErrorMessage .= 'Путь не найден'."\r\n";
	} else {
		if (!$fs->pathInPath(FILE_PATH,$szTestPath)) {
			$szErrorMessage .= 'Путь нарушает систему безопасности'."\r\n";
		}
	}
	return $szErrorMessage;
}
?>