<?
require_once __DIR__ . DIRECTORY_SEPARATOR . '../../_lib/loader.php';
$_loader = new Loader();
function check() {
	if (empty($_REQUEST['path'])) {
		die();
	}
	$szPath = toCanonical(realpath(FILE_PATH.$_REQUEST['path']));
	$szErrorMessage = checkpath($szPath);
	if (!empty($szErrorMessage)) {
		die($szErrorMessage);
	}
	$szPath = \Extasy\CMS::getFilesHttpRoot().$_REQUEST['path'];
	return $szPath;
}
/**
*   @desc ������� ��������� �� ��������� ������������
*   @return 
*/
function main() {
}
/**
*   @desc ��������� �� ����� ����� ��� �����
*   @return 
*/
function getThumbnailPath() {
	$szSourcePath = toCanonical(realpath(FILE_PATH.$_REQUEST['path']));
	$aPath = pathinfo($szSourcePath);
	$nPos = strrpos($aPath['basename'],'.');
	// ��������� ������� �� .
	if ($nPos >= 0) {
		$szThumbPath = $aPath['dirname'].'/'.substr($aPath['basename'],0,$nPos).'_t'.substr($aPath['basename'],$nPos);
	} else {
		$szThumbPath = $szThumbPath.'_t';
	}
	return $szThumbPath;

}
/**
*   @desc ������� �����
*   @return 
*/
function post() {

	$image = DAO::GetInstance('image');
	$fs = DAO::getInstance('fs');
	// ��������� ����
	check();
	$szThumbPath = getThumbnailPath();
	// ��������� ����� �� �� �������� �����������
	if (!empty($_FILES['file']) && is_uploaded_file($_FILES['file']['tmp_name'])) {
		// ���� �� �� �������� ������ ��������
		$fs->upload('file',$szThumbPath);
	} else {
		// �������
		$image->createThumbnail(realpath(FILE_PATH.$_REQUEST['path']),$_POST['width'],$_POST['height']);
	}
		

	// ��������
	header('Location: image.php?path='.$_REQUEST['path']);
	die();
}
/**
*   @desc ������� �������� ����� 
*   @return 
*/
function show() {
	// ��������� ����
	check();
	$aInfo = getimagesize(FILE_PATH.$_REQUEST['path']);
	// ���������� ������ ���� �� �����
	$szThumbPath = getThumbnailPath();
	$bThumb = false;
	$aThumbInfo = array();
	if (!empty($szThumbPath) && file_exists($szThumbPath)) { 
		$bThumb = true;
		// ���������� �� �����
		$aThumbInfo = getimagesize($szThumbPath);
		$aThumbInfo['width'] = $aThumbInfo[0];
		$aThumbInfo['height'] = $aThumbInfo[1];
		// ����������� � http path;
		$szThumbPath = substr($szThumbPath,strlen(realpath(FILE_PATH)) +1);
	} else {
		$aThumbInfo = array(
			'width'   =>  DAO_IMAGE_THUMBNAIL_X,
			'height'  =>DAO_IMAGE_THUMBNAIL_Y
			);
	}
	extract(array(
		'path'  =>  $_REQUEST['path'],
		'aInfo' =>  $aInfo,
		'bThumb'=>  $bThumb,
		'aThumbInfo'=>  $aThumbInfo,
		'szPathToThumb' =>  $szThumbPath,
		));
	include dirname(__FILE__).'/_tpl/image.tpl';
}
$_loader->addPost('path,width,height','post');
$_loader->addGet('path','show');
$_loader->setDefault('main');
$_loader->process();
?>