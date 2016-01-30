<?
namespace Extasy\Columns {
	use \Faid\UParser;

	define( 'DRAW_TTF_BASE', 72 );

	class Graphical_text extends BaseColumn {
		protected $nLeft; // Отступ слева
		protected $nTop; // Отступ сверху
		protected $nRight; // Отступ справа
		protected $nBottom; // Отступ снизу
		protected $szTextColor; // Цвет текста
		protected $szBackgroundColor; // Фоновый цвет
		protected $szBackgroundImage; // Фоновое изображение
		protected $szFontFile; // Путь к файлу шрифтов
		protected $nFontSize; // Размер шрифта
		protected $szDir; // Хранит путь до иконок
		protected $szType; // Тип изображения
		protected $szExtension; // Расширение


		public function __construct( $szFieldName, $fieldInfo, $aValue ) {
			parent::__construct( $szFieldName, $fieldInfo, $aValue );
			if ( !empty( $this->fieldInfo[ 'dir' ] ) ) {
				$this->szDir = $this->fieldInfo[ 'dir' ];
			}
			$this->nLeft             = isset( $fieldInfo[ 'left' ] ) ? intval( $fieldInfo[ 'left' ] ) : 0;
			$this->nTop              = isset( $fieldInfo[ 'top' ] ) ? intval( $fieldInfo[ 'top' ] ) : 0;
			$this->nRight            = isset( $fieldInfo[ 'right' ] ) ? intval( $fieldInfo[ 'right' ] ) : 0;
			$this->nBottom           = isset( $fieldInfo[ 'bottom' ] ) ? intval( $fieldInfo[ 'bottom' ] ) : 0;
			$this->szBackgroundColor = isset( $fieldInfo[ 'background-color' ] ) ? $fieldInfo[ 'background-color' ] : '';
			$this->szBackgroundImage = isset( $fieldInfo[ 'background-image' ] ) ? $fieldInfo[ 'background-image' ] : '';
			$this->szTextColor       = isset( $fieldInfo[ 'text-color' ] ) ? $fieldInfo[ 'text-color' ] : '';
			$this->szFontFile        = isset( $fieldInfo[ 'font' ] ) ? $fieldInfo[ 'font' ] : 'arial.ttf';
			$this->nFontSize         = isset( $fieldInfo[ 'font-size' ] ) ? $fieldInfo[ 'font-size' ] : 18;
			$this->szType            = !empty( $fieldInfo[ 'image-type' ] ) ? $fieldInfo[ 'image-type' ] : 'image/jpeg';
			switch ( $this->szType ) {
				case 'image/jpeg':
					$this->szExtension = 'jpg';
					break;
				case 'image/png':
					$this->szExtension = 'png';
					break;
				case 'image/gif':
					$this->szExtension = 'gif';
					break;
				default:
					$this->szType      = 'image/jpeg';
					$this->szExtension = 'jpg';
			}
		}

		/**
		 *
		 * @param unknown $dbData
		 */
		public function onAfterSelect( $dbData ) {
			if ( isset( $dbData[ $this->szFieldName ] ) ) {
				$this->aValue = $dbData[ $this->szFieldName ];
			}
		}

		public function Insert(\Extasy\ORM\QueryBuilder $query ) {
			$query->setSet( $this->szFieldName,$this->aValue );
			$this->generateImage();
		}

		public function Update(\Extasy\ORM\QueryBuilder $query ) {
			$query->setSet( $this->szFieldName, ( $this->aValue ) );
			$this->generateImage();
		}

		public function getValue() {
			return FILE_PATH . $this->szDir . $this->document->id->getValue() . '.' . $this->szExtension;
		}

		public function getViewValue() {
			if ( file_exists( $this->getValue() ) ) {
				return '<img src="' . \Extasy\CMS::getFilesHttpRoot() . $this->szDir . $this->document->id->getValue() . '.' . $this->szExtension . '" alt="' . htmlspecialchars( $this->aValue ) . '"/>';
			}
		}

		public function getFormValue() {
			// формируем массив парсинга
			$aParse = array(
				'szFieldName' => $this->szFieldName,
				'szFontFile'  => $this->szFontFile,
				'szValue'     => $this->aValue,
				'szView'      => $this->getViewValue(),
			);
			// шаблон
			$szContent = UParser::parsePHPFile( __DIR__ . DIRECTORY_SEPARATOR  . 'graphical_text/form.tpl', $aParse );
			return $szContent;
		}

		protected function generateImage() {
			if ( empty( $this->aValue ) ) {
				$szPath = $this->getValue();
				if ( file_exists( $szPath ) ) {
					unlink( $szPath );
				}
				return;
			}
			setlocale( LC_ALL, 'ru-RU' );
			$szPath = $this->getValue();
			if ( file_exists( $szPath ) ) {
				unlink( $szPath );
			}

			$aBox = imagettfbbox( $this->nFontSize, 0, $this->szFontFile, $this->aValue );
			$aBox[ 0 ] += $this->nLeft; // Левый край X-координата
			$aBox[ 1 ] -= $this->nTop; // Левый край Y-координата
			$aBox[ 2 ] += $this->nRight; // Правый край X-координата
			$aBox[ 7 ] -= $this->nBottom; // Правый край Y-координата
			$aBox[ 3 ] = -$aBox[ 7 ];
			$aBox[ 1 ] = -$aBox[ 1 ];


			$im = imagecreatetruecolor( -1 * $aBox[ 0 ] + $aBox[ 2 ], ( -1 * $aBox[ 1 ] + $aBox[ 3 ] + 1 ) );

			imagefill( $im, 0, 0, $this->responseColor( $this->szBackgroundColor, $im ) );

			// подключаем фоновое изображение
			if ( $this->szBackgroundImage ) {
				$szImage = file_get_contents( $this->szBackgroundImage );
				$aImage  = getimagesize( $this->szBackgroundImage );
				$imBg    = imagecreatefromstring( $szImage );
				imagecopy( $im, $imBg, 0, 0, 0, 0, $aImage[ 0 ], $aImage[ 1 ] );
			}
			imagettftext( $im,
						  $this->nFontSize,
						  0,
						  $this->nLeft,
						  $aBox[ 3 ] - $this->nBottom,
						  $this->responseColor( $this->szTextColor, $im ),
						  $this->szFontFile,
						  $this->aValue );
//			self::win2uni($this->aValue));

			$this->outputToFile( $im );
		}

		protected function outputToFile( $im ) {

			switch ( $this->szType ) {
				case 'image/jpeg':
					imagejpeg( $im, $this->getValue() );
				case 'image/png':
					imagepng( $im, $this->getValue() );
				case 'image/gif':
					imagegif( $im, $this->getValue() );
			}
		}

		protected function responseColor( $szColor, $im ) {
			if ( ( strlen( $szColor ) < 6 ) || ( strlen( $szColor ) > 6 ) ) {
				$szColor = '000000';
			}

			return imagecolorallocate( $im,
									   hexdec( $szColor[ 0 ] . $szColor[ 1 ] ),
									   hexdec( $szColor[ 2 ] . $szColor[ 3 ] ),
									   hexdec( $szColor[ 4 ] . $szColor[ 5 ] )
			);
		}

		/**
		 * Draws TTF/OTF text on the destination image with best quality.
		 * The built in function imagettftext freaks out with small point
		 * size on some fonts, commonly OTF. Also fixes a position bug
		 * with imagettftext using imagettfbbox. If you just want the text
		 * pass a null value to 'Destination Image Resource' instead.
		 *
		 * @param resource Destination Image Resource
		 * @param int      Point Size (GD2), Pixel Size (GD1)
		 * @param int      X Position (Destination)
		 * @param int      Y Position (Destination)
		 * @param int      Font Color - Red (0-255)
		 * @param int      Font Color - Green (0-255)
		 * @param int      Font Color - Blue (0-255)
		 * @param string   TTF/OTF Path
		 * @param string   Text to Print
		 *
		 * @return null
		 */
		public function drawttftext( &$des_img,
									 $size,
									 $posX = 0,
									 $posY = 0,
									 $colorR,
									 $colorG,
									 $colorB,
									 $font = '',
									 $text = '' ) {
			//-----------------------------------------
			// Establish a base size to create text
			//-----------------------------------------
			if ( !is_int( DRAW_TTF_BASE ) ) {
				define( 'DRAW_TTF_BASE', 72 );
			}

			if ( $size >= DRAW_TTF_BASE ) {
				define( 'DRAW_TTF_BASE', $size * 2 );
			}

			//-----------------------------------------
			// Simulate text and get data.
			// Get absolute X, Y, Width, and Height
			//-----------------------------------------
			$text_data = imagettfbbox( DRAW_TTF_BASE, 0, $font, $text );
			$posX_font = min( $text_data[ 0 ], $text_data[ 6 ] ) * -1;
			$posY_font = min( $text_data[ 5 ], $text_data[ 7 ] ) * -1;
			$height    = max( $text_data[ 1 ], $text_data[ 3 ] ) - min( $text_data[ 5 ], $text_data[ 7 ] );
			$width     = max( $text_data[ 2 ], $text_data[ 4 ] ) - min( $text_data[ 0 ], $text_data[ 6 ] );
			//-----------------------------------------
			// Create blank translucent image
			//-----------------------------------------

			$im = imagecreatetruecolor( $width, $height );
			imagealphablending( $im, false );
			$trans = imagecolorallocatealpha( $im, 0, 0, 0, 127 );
			imagefilledrectangle( $im, 0, 0, $width, $height, $trans );
			imagealphablending( $im, true );
			//-----------------------------------------
			// Draw text onto the blank image
			//-----------------------------------------
			$m_color = imagecolorallocate( $im, $colorR, $colorG, $colorB );
			imagettftext( $im, DRAW_TTF_BASE, 0, $posX_font, $posY_font, $m_color, $font, $text );
			imagealphablending( $im, false );

			//-----------------------------------------
			// Calculate ratio and size of sized text
			//-----------------------------------------
			$size_ratio = $size / DRAW_TTF_BASE;
			$new_width  = round( $width * $size_ratio );
			$new_height = round( $height * $size_ratio );

			//-----------------------------------------
			// Resize text. Can't use resampled direct
			//-----------------------------------------
			$rimg = imagecreatetruecolor( $new_width, $new_height );
			$bkg  = imagecolorallocate( $rimg, 0, 0, 0 );
			imagecolortransparent( $rimg, $bkg );
			imagealphablending( $rimg, false );
			imagecopyresampled( $rimg, $im, 0, 0, 0, 0, $new_width, $new_height, $width, $height );
			if ( $des_img != null ) {
				//-----------------------------------------
				// Copy resized text to origoinal image
				//-----------------------------------------

				imagealphablending( $des_img, true );
				imagecopy( $des_img, $rimg, $posX, $posY, 0, 0, $new_width, $new_height );
				imagealphablending( $des_img, false );
				imagedestroy( $im );
				imagedestroy( $rimg );
			} else {
				//-----------------------------------------
				// Just return the resized image
				//-----------------------------------------

				$des_img = $rimg;
				imagedestroy( $im );
			}
		}

		protected static function win2uni( $s ) {
			$s = convert_cyr_string( $s, 'w', 'i' ); //  win1251 -> iso8859-5
			//  iso8859-5 -> unicode:
			for ( $result = '', $i = 0; $i < strlen( $s ); $i++ ) {
				$charcode = ord( $s[ $i ] );
				$result .= ( $charcode > 175 ) ? "&#" . ( 1040 + ( $charcode - 176 ) ) . ";" : $s[ $i ];
			}
			return $result;
		}
	}
}
?>