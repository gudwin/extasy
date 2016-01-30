<?
if (!defined('DAO_IMAGE_LIB')) {
	define('DAO_IMAGE_LIB','1');
}
if (!defined('DAO_IMAGE_MAGICK_PATH')) {
	define('DAO_IMAGE_MAGICK_PATH', '/usr/local/bin/');
}
if (!defined('DAO_IMAGE_THUMBNAIL_X')) {
	define('DAO_IMAGE_THUMBNAIL_X',144);
}
if (!defined('DAO_IMAGE_THUMBNAIL_Y')) {
	define('DAO_IMAGE_THUMBNAIL_Y',0);
}
        /**
                *        @desc Класс для работы с изображениями
                *        @desc 26.10.2005
                *        @author Gisma
                *        @package Exstasy
        */
        class DAO_Image extends DAO_Service {
			private static $instance;
                var $iData;
                var $szType;
				var $aSizes;
					public function push() {

	}
	public function pop() {

	}
				function __copy($dst_x,$dst_y,$src_x,$src_y,$dst_w,$dst_h,$src_w,$src_h)
				{

					$iDst = imagecreatetruecolor($dst_w,$dst_h);
					imagecopyresampled($iDst,$this->iData,$dst_x,$dst_y,$src_x,$src_y,$dst_w,$dst_h,$src_w,$src_h);
					return $iDst;
				}
                function __resize($dst_w,$dst_h)
                {
                        $x = imagesx($this->iData);
                        $y = imagesy($this->iData);
                        $this->iData = $this->_copy(0,0,0,0,$dst_h,$dst_w,$x,$y);
                }
                /**
                        @desc Создает изображение по файлу
                */
		function __create($szFileName,$szType = '') {
			if (file_exists($szFileName)) {
				$aImageInfo = getimagesize($szFileName);
				if ($aImageInfo === false)
				{
					throw new Excpetion ('Incorrect image type');
				}
				if ($szType == '') {
					$szType = $this->__returnTypebyId($aImageInfo[2]);
				}
				$this->szType = $szType;

				$result = $this->__imageCreateFromFile($szFileName,$szType);

				if ($result == null) {
					trigger_error('DAO_Image::__imageCreate ошибка создания изображния. Входящие параметры : $szFileName='.$szFileName.' $szType='.$szType,E_USER_ERROR);
				}
				$this->iData = $result;
				$this->aSizes = $aImageInfo;
				return $result;
			} else {
				trigger_error('DAO_Image::__imageCreate Файл $szFileName='.$szFileName,' не существует',E_USER_ERROR);
			}
		}
                /**
                        @desc Преобразует целочисленный идентификатор типа изображения в строковый
                        @param $nType int идентификатор
                        @return sting строковое имя типа
                */
                function __returnTypebyId($nType) {

                        switch ($nType) {
                                case 1:        return 'gif';
                                case 2:        return 'jpg';
                                case 3:        return 'png';
                                case 4:        return 'swf';
                                case 5:        return 'psd';
                                case 6:
                                        return 'bmp';
                                break;
                                case 7:        return 'tiff';
                                case 8:        return 'tiff';
                                case 9:        return 'jpc';
                                case 10:return 'jp2';
                                case 11:return 'jpx';
                                case 12:return 'jb2';
                                case 13:return 'swc';
                                case 14:return 'iff';
                                case 15:return 'wbmp';
                                case 16:return 'xbm';
                        }
                }

                function __imageCreateFromFile($file, $type) {
                	$old = set_error_handler(array('DAO_Image','emptyFunc'),E_ALL | E_WARNING | E_STRICT);	
					$result = imagecreatefromstring(file_get_contents($file));
					set_error_handler('Extasy_errorHandler',E_ALL);
					if ( empty( $result )) {
						throw new Exception('Failed to load image from given file:'.$file);
					}
					return $result;
                }
                function __convert_type_to_func($type) {

                        if ($type =='jpg') {
                                return 'jpeg';
                        } elseif ($type=='bmp') {
                                return 'wbmp';
                        }
                        return $type;

                }

                /**
                        @desc
                */
                function __imagefile($szFileName = '',$szType = '') {
                        $iData = $this->iData;
                        if ($iData == null) {
                                trigger_error('DAO_Image::__imagefile - $iData равно null',E_USER_ERROR);
                        }
                        if ($szType == '') {
                                if ($szFileName == '') {
                                        trigger_error('DAO_Image::__imagefile - $szFilename и $szType не определены',E_USER_ERROR);
                                }
                                $aPath = pathinfo($szFileName);
                                $szType = $this->szType;
                        }
						switch ($szType) {
							case 'jpg':
								imagejpeg($iData,$szFileName,100);
								break;
							case 'png':
								imagepng($iData,$szFileName,100);
								break;
							case 'gif':
								imagegif($iData,$szFileName);
								break;
							case 'bmp':
								imagewbmp($iData,$szFileName);
								break;
							default:
								trigger_error('DAO_Image::__imagefile uknown image type :'.htmlspecialchars($szType),E_USER_ERROR);
						}

                }

				/**
				 * Возвращает действительное расширение файла
				 * @return string  (.jpg|.png|.gif)
				 */
				public function getExtension($szFileName)
				{
					$aInfo = getimagesize($szFileName);
					$szResult = $this->__returnTypebyId($aInfo[2]);
					if (!empty($szResult))
					{
						return '.'.$szResult;
					}
				}
				function xCopy($szDestination, $szSource, $szSize,$bSaveLittle = false)
				{
					if (!file_exists($szDestination))
					{
						//trigger_error('DAO_Image::Copy файл $szDestination='.$szDestination.' не существует',E_USER_ERROR);
					}
					$aInfo = getimagesize($szSource);
					if ($aInfo === false) throw new Exception('Image incorrect');

					$this->__Create($szSource,$this->__returnTypebyId($aInfo[2]));
					list($w2, $h2) = explode('x', $szSize);

					$w1 = $aInfo[0];
					$h1 = $aInfo[1];
					if ($bSaveLittle) {
						if ($w1 < $w2) {
							$w2 = $w1;
						}
						if ($h1 < $h2) {
							$h2 = $h1;
						}
					}

					$k1 = $h1 / $w1;
					$k2 = $h2 / $w2;

					// Образаем слева-справа
					if ($k1 < $k2)
					{
						$lishn = $w1 - ($w2 * $h1) / $h2;
						$part1 = abs(round($lishn / 2));
					}
					// Обрезаем сверху-снизу
					else
					{
						$lishn = $h1 - ($h2 * $w1) / $w2;
						$part1 = abs(round($lishn / 2));
					}
					if (DAO_IMAGE_LIB== 2)
					{
						copy($szSource, $szDestination);
						//chmod($szDestination, '0777');
						$cmd = DAO_IMAGE_MAGICK_PATH."mogrify -resize ".$w2."x".$h2." ".$szDestination;
						$res = `$cmd 2>&1`;
						return ;
					}

					//__copy($dst_x,$dst_y,$src_x,$src_y,$dst_w,$dst_h,$src_w,$src_h)
					if ($k1 < $k2)
					{
						$this->iData = $this->__copy(0, 0, $part1, 0, $w2, $h2, round($w2 * ($h1 / $h2)), $h1);
					}
					else
					{
						$this->iData = $this->__copy(0, 0, 0, $part1, $w2, $h2, $w1, round($h2 * ($w1 / $w2)));
					}
					$this->__imagefile($szDestination,$this->__returnTypebyId($aInfo[2]));
				}

                function Copy($szDestination,$szSource,$dst_w,$dst_h) {
                        if (!file_exists($szSource)) {
                                trigger_error('DAO_Image::Copy файл $szDestination='.$szSource.' не существует',E_USER_ERROR);
                        }
                        $aInfo = getimagesize($szSource);
                        if ($aInfo == false) {
                                throw new Exception('Image incorrect') ;
                        }
                        $this->__Create($szSource,$this->__returnTypebyId($aInfo[2]));

                        if ($dst_w == 0) {
                                if ($dst_h < $aInfo[1])
                                    $dst_w = $dst_h/$aInfo[1] * $aInfo[0];
                                else {
                                     $dst_w = $aInfo[0];
                                     $dst_h = $aInfo[1];
                                }
                        } elseif($dst_h == 0) {
                                if ($dst_w < $aInfo[0])
                                    $dst_h = $dst_w/$aInfo[0] * $aInfo[1];
                                else {
                                     $dst_w = $aInfo[0];
                                     $dst_h = $aInfo[1];
                                }
                        }
                        if (DAO_IMAGE_LIB== 2){
                                copy($szSource, $szDestination);
                                //chmod($szDestination, '0777');
                                $cmd = DAO_IMAGE_MAGICK_PATH."mogrify -resize ".$dst_w."x".$dst_h." ".$szDestination;
                                $res = `$cmd 2>&1`;
                                return ;
                        }
                        $this->iData = $this->__copy(0,0,0,0,$dst_w,$dst_h,$aInfo[0],$aInfo[1]);
                        $this->__imagefile($szDestination,$this->__returnTypebyId($aInfo[2]));
                }
		/**
		@desc Создает иконку для данного файла, кладет ее в той же директории делает приставку _t
		*/
		function CreateThumbnail($szFilename,$x = 0,$y = 0) {
			if (!file_exists($szFilename)) {
				trigger_error('DAO_Image::CreateThumbnail файл $szFilname='.$szFilename.' не существует',E_USER_ERROR);
				return;
			}
			$aInfo = getimagesize($szFilename);
			if ($aInfo == false) {
				throw new Exception('Image incorrect');
				return ;
			}
			if ($x * $y == 0) {
				if (DAO_IMAGE_THUMBNAIL_X == 0) {
					$x = DAO_IMAGE_THUMBNAIL_Y / $aInfo[1] *  $aInfo[0];
					$y = DAO_IMAGE_THUMBNAIL_Y;
				} elseif (DAO_IMAGE_THUMBNAIL_Y == 0 ) {
					$x = DAO_IMAGE_THUMBNAIL_X;
					$y = DAO_IMAGE_THUMBNAIL_X / $aInfo[0] *  $aInfo[1];
				} else {
					$x = DAO_IMAGE_THUMBNAIL_X;
					$y = DAO_IMAGE_THUMBNAIL_Y;
				}
			}

			if ($x > $aInfo[0]) {
				$x = $aInfo[0];
			}
			if ($y > $aInfo[1]) {
				$y = $aInfo[1];
			}
			// получаем путь к тумбе
			$aPath = pathinfo($szFilename);
			$nPos = strrpos($aPath['basename'],'.');
			// проверяем найдена ли .
			if ($nPos !== false) {

				$szThumbPath = $aPath['dirname'].'/'.substr($aPath['basename'],0,$nPos).'_t'.substr($aPath['basename'],$nPos);
			} else {
				$szThumbPath = $szFilename.'_t';

			}
			//
			if (DAO_IMAGE_LIB == 1) {
				$this->__Create($szFilename,$this->__returnTypebyId($aInfo[2]));
				$this->iData = $this->__copy(0,0,0,0,
					$x,$y,
					$aInfo[0],$aInfo[1]);

				$this->__imagefile($szThumbPath,$this->szType );

				//chmod($szThumbPath,0777);
				// Расчет получения thumbnail
			} elseif (DAO_IMAGE_LIB == 2) {
				copy($szFilename, $szThumbPath);
				//chmod($szThumbPath, '0777');
				$cmd = DAO_IMAGE_MAGICK_PATH."mogrify -resize ".$x."x".$y." ".$szThumbPath;
				$res = `$cmd 2>&1`;
			} else {
				trigger_error('DAO_Image::CreateThumbnail IMAGE_LIB='.DAO_IMAGE_LIB.' неизвестное значение',E_USER_ERROR);
			}
		}
		/**
			@desc Возвращает ширину текущего изображения
		*/
		function GetHeight() {
			if ($this->iData != null) {
				return imagesy($this->iData);
			} else {
				trigger_error('DAO_Image::GetHeight $this->iData не определено',E_USER_ERROR);
				return ;
			}
		}
		/**
			@desc Возвращает ширину текущего изображения
		*/
		function GetWidth() {
			if ($this->iData != null) {
				return imagesx($this->iData);
			} else {
				trigger_error('DAO_Image::GetWidth $this->iData не определено',E_USER_ERROR);
				return ;
			}
		}
		/**
			@desc Поворачивает список изображений, или одно изображение, или внутреннее изображение на заданный угол
			@desc с заданным цветом закраски
			@param $fAngle float угол поворота
			@param $Data data данные
			@param $bgdcolor int цвет закрасски
		*/
		function Rotate($fAngle = 0,$Data = null,$bgdcolor = 0) {
			if (!is_float($fAngle)) {
				trigger_error('DAO_Image::Rotate $fAngle не является вещественным :'.$fAngle,E_USER_ERROR);
			}
			if ($Data == null) {
				$Data = &$this->iData;
			}
			if ($Data == null) {
				trigger_error('DAO_Image::Rotate данные не определены, $Data,$this->iData',E_USER_WARNING);
				return ;
			}
			if (is_array($Data)) {
				foreach ($Data as $key => $value ) {
					$iResult = @imagerotate($value,$fAngle,$bgdcolor);
					if ($iResult == null) {
						trigger_error('DAO_Image::Rotate некорректные данные в $Data => $value='.$value.' функция завершается',E_USER_WARNING);
						return ;
					} else {
						$Data[$key] = $iResult;
					}
				}
			} else {
				$Data = @imagerotate($Data,$fAngle,$bgdcolor);
			}
			return $Data;
		}
		public static function emptyFunc() {
			
		}
		public static function getInstance() {
			if (!is_object(self::$instance)) {
				self::$instance = new DAO_Image();
			}
			return self::$instance;
		}
	}
?>
