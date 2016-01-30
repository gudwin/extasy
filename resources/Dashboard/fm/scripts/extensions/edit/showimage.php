<?

require_once __DIR__ . DIRECTORY_SEPARATOR . '../../_lib/loader.php';
error_reporting(E_ALL);
/**
*   @desc ������� ������ �����������
*   @return 
*/
function outputPreview($szContent,$szMime) {
	$source = imagecreatefromstring($szContent);
	$nSourceHeight = imagesy($source);
	$nSourceWidth = imagesx($source);
	
	// �������� ������ � ������
	if (DAO_IMAGE_THUMBNAIL_X == 0) {
		$szHeight = DAO_IMAGE_THUMBNAIL_Y;
		$szWidth = intval(($nSourceWidth * DAO_IMAGE_THUMBNAIL_Y) / $nSourceHeight);
	} elseif (DAO_IMAGE_THUMBNAIL_Y == 0) {
		$szWidth  = DAO_IMAGE_THUMBNAIL_X;
		$szHeight = intval(($nSourceHeight * DAO_IMAGE_THUMBNAIL_X) / $nSourceWidth);
	} else {
		$szWidth = DAO_IMAGE_THUMBNAIL_X;
		$szHeight = DAO_IMAGE_THUMBNAIL_Y;
	}
	
	$thumb = imagecreatetruecolor($szWidth, $szHeight);
	imagecopyresized($thumb,$source,0,0,0,0,$szWidth,$szHeight,$nSourceWidth,$nSourceHeight);
	switch ($szMime) {
		case 'image/jpeg':   
			imagejpeg($thumb);
		break;
		case  'image/png':   
			imagepng($thumb);
		break;
		case 'image/gif':   
			imagegif($thumb);
		break;
		default:
			imagejpeg($thumb);
	}
}
if (empty($_GET['path'])) {
	die();
}

$szPath = toCanonical(realpath(FILE_PATH.$_GET['path']));
$szErrorMessage = checkpath($szPath);
if (!empty($szErrorMessage) || !is_readable($szPath)) {
	die($szErrorMessage);
}

$aInfo = getimagesize ($szPath);
header("Content-type: {$aInfo['mime']}");

$szContent = file_get_contents($szPath);
// ���������� ���������� �� ��� �������� ������
if (isset($_GET['preview'])) {
	// ���� �� �� �������

	outputPreview($szContent,$aInfo['mime']);
} else {
	print $szContent;
}
// ������� ���������� �����
	

?>