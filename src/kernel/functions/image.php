<?php
class imageHelper {
	/**
	 * Returns timthumb image url 
	 * @param string $url image URL
	 * @param int $w width 
	 * @param int $h height
	 * @return string  
	 */
	public static function getTimthumbUrl( $url, $w = 0, $h = 0) {
		$src = sprintf('%sextasy/timthumb/timthumb.php?src=%s', \Extasy\CMS::getResourcesUrl(),urlencode( $url ) );
		if  ( !empty ( $w )) {
			$src .= sprintf( '&w=%d', $w );
		}
		if ( !empty( $h )) {
			$src .= sprintf( '&h=%d', $h );
		}
		return $src;
	}
	public static function addSignToImage( $path, $signPath ) {
		// вычислем размеры значка
		$sizes = getimagesize($signPath);
		$nSignWidth = $sizes[0];
		$nSignHeight = $sizes[1];
		// вычисляем размер текущего файла + отступы куда положить знак
		$sizes = getimagesize($path);
		$nOffsetX = $sizes[0] - 10 - $nSignWidth;
		$nOffsetY = $sizes[1] - 10 - $nSignHeight;
		
		// на отступ накладываем изображение
		$resImage = imagecreateFromString(file_get_contents($path));
		$resSign = imagecreateFromString(file_get_contents($signPath));
		imagecopy($resImage,$resSign,$nOffsetX,$nOffsetY,0,0,$nSignWidth,$nSignHeight);
		// сохраняем
		self::storeImageObject($path, $resImage);
		
	}
	protected static function storeImageObject( $path, $resource ) {
		$info = pathinfo( $path );
	
		switch (strtolower($info['extension']) ) {
			case 'jpg':
			case 'jpeg':
				imagejpeg($resource,$path,100);
				break;
			case 'png':
				imagepng($resource,$path,100);
				break;
			case 'gif':
				imagegif($resource,$path);
				break;
			default:
				throw new Exception( 'Unknown image type');
		}
	}
}