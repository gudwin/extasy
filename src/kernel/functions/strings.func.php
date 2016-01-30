<?php
/**
*   @desc Переводит \r\n в <br>
*/
function rn2br($content) {
	return str_replace("\n",'<br>',$content);
}
/**
*   @desc Переводит  <br> в \r\n
*/
function br2rn($content) {
	return str_replace('<br>',"\r\n",$content);
}

/**
*   @desc Слэширует символы поиска
*/
function makeSearchString($str) {
	return to_search_string($str);
}
/**
*   -------------------------------------------------------------------------------------------
*   @desc Создает строку для поиска в MySQL таблице
*   @return
*   -------------------------------------------------------------------------------------------
*/
function to_search_string($str) {
	return addCslashes(str_replace('\\','\\\\',htmlspecialchars(\Faid\DB::escape(trim($str)))),'_%');
}
function to_ajax_string($str) {
	$str = str_replace("'","\'",$str);
	$str = str_replace('"',"\"+String.fromCharCode(34) + \"",$str);
	$str = str_replace("\r\n",'\r\n',$str);
	$str = str_replace("\r",'\r',$str);
	$str = str_replace("\n",'\n',$str);
	return '"'.$str.'"';
}
/**
*   @desc Обрезает текст на указанное кол-во символов
*   @return
*/
function cuttext($text, $numchar = 255)
{
	$text=strip_tags($text);
	$text=  htmlspecialchars($text);
	$text = trim(str_replace('  ',' ',$text));
	$nLength  = strlen($text);
	if ($nLength >$numchar)
	{
		$text=mb_substr($text,0,$numchar,'utf-8');
		
		$text .= '...';
	}

	return $text;
}
function addParamToUrl($Field = '',$Add = '') {
    $szResult = $_SERVER['SCRIPT_NAME'].'?';
	if (sizeof($_GET) > 0) {
		$_GET[$Field] = $Add;
		$tmp = array();
		foreach ($_GET as $key=>$value) {
			$tmp[] = $key.'='.$value;
		}
		$szResult .= implode('&',$tmp);
	} else {
		$szResult .= $Field.'='.$Add;
	}
    return $szResult;
}
/**
	*   -------------------------------------------------------------------------------------------
	*   Конвертирует строки из кирилицы в её аналог на латинице
	*   @return
	*   -------------------------------------------------------------------------------------------
	*/

	function convert2lat_url_key($str)
	{
		$szResult = '';
		$mm=iconv_strlen($str,'utf-8');
		for ($i=0;$i<$mm;$i++)
		{
$ss=iconv_substr($str,$i,1,'utf-8');
			switch ($ss)

			{

			case "щ":
					$szResult .=  "sch";
					break;

			case "ч":
					$szResult .=  "ch";
					break;

			case "ш":
					$szResult .=  "sh";
					break;

			case "я":
					$szResult .=  "ja";
					break;

			case "ю":
					$szResult .=  "ju";
					break;

			case "ё":
					$szResult .=  "jo";
					break;

			case "ж":
					$szResult .=  "zh";
					break;

			case "э":
					$szResult .=  "e";
					break;

			case "Щ":
					$szResult .=  "Sch";
					break;

			 case "Ч":
					$szResult .=  "Ch";
					break;

			 case "Ш":
					$szResult .=  "Sh";
					break;

			 case "Я":
					$szResult .=  "Ja";
					break;

			case "Ю":
					$szResult .=  "Ju";
					break;

			case "Ё":
					$szResult .=  "Jo";
					break;

			case "Ж":
					$szResult .=  "Zh";
					break;

			case "Э":
					$szResult .=  "E";
					break;

			case "ь":
					$szResult .=  "";
					break;

			case "ъ":
					$szResult .=  "'";
					break;

			case "а":
					$szResult .=  "a";
					break;

			 case "б":
					$szResult .=  "b";
					break;

			case "ц":
					$szResult .=  "c";
					break;

			case "д":
					$szResult .=  "d";
					break;

			case "е":
					$szResult .=  "e";
					break;

			case "ф":
					$szResult .=  "f";
					break;

			case "г":
					$szResult .=  "g";
					break;
			case "х":
					$szResult .=  "h";
					break;

			case "и":
					$szResult .=  "i";
					break;

			case "й":
					$szResult .=  "j";
					break;

			case "к":

				 {

					if ($str[$i+1]=="с" ) {
				   $szResult .=  "x";
				   $i=$i+1; break;}

					$szResult .=  "k";
					break;

				   }

			case "л":
					$szResult .=  "l";
					break;

			case "м":
					$szResult .=  "m";
					break;
			case "н":
					$szResult .=  "n";
					break;
			case "о":
					$szResult .=  "o";
					break;
			case "п":
					$szResult .=  "p";
					break;

			 case "р":
					$szResult .=  "r";
					break;

			case "с":
					$szResult .=  "s";
					break;

			case "т":
					$szResult .=  "t";
					break;

			case "у":
					$szResult .=  "u";
					break;

			case "в":
					$szResult .=  "v";
					break;

			case "ы":
					$szResult .=  "y";
					break;

			case "з":
					$szResult .=  "z";
					break;

			case "Ь":
					$szResult .=  "'";
					break;

			case "Ъ":
					$szResult .=  "'";
					break;

			case "А":
					$szResult .=  "A";
					break;

			case "Б":
					$szResult .=  "B";
					break;

			case "Ц":
					$szResult .=  "C";
					break;

			case "Д":
					$szResult .=  "D";
					break;

			 case "Е":
					$szResult .=  "E";
					break;

			case "Ф":
					$szResult .=  "F";
					break;

			case "Г":
					$szResult .=  "G";
					break;

			case "Х":
					$szResult .=  "H";
					break;

			case "И":
					$szResult .=  "I";
					break;

			case "Й":
					$szResult .=  "J";
					break;

			case "К":

				 {

				  if ($str[$i+1]=="С" ) {
				   $szResult .=  "X";
				   $i=$i+1; break;}

				  if ($str[$i+1]=="с" ) {
				   $szResult .=  "X";
				   $i=$i+1; break;}

				  $szResult .=  "K";
				   break;

				   }

			case "Л":
					$szResult .=  "L";
					break;

			 case "М":
					$szResult .=  "M";
					break;

			case "Н":
					$szResult .=  "N";
					break;

			case "О":
					$szResult .=  "O";
					break;

			case "П":
					$szResult .=  "P";
					break;

			case "Р":
					$szResult .=  "R";
					break;

			case "С":
					$szResult .=  "S";
					break;

			case "Т":
					$szResult .=  "T";
					break;

			 case "У":
					$szResult .=  "U";
					break;

			 case "В":
					$szResult .=  "V";
					break;

			case "Ы":
					$szResult .=  "Y";
					break;

			 case "З":
					$szResult .=  "Z";
					break;
			case ' ':
			case '-':
				$szResult .= '-';
				break;
			case '/':
				$szResult .= '/';
				break;
			case '«':
			case '»':
			case '"':
			case '?':
			case '$':
			case '#':
			case '%':
			case '!':
			case ':':
			case '+':
			case '&':
				$szResult .= '';
				break;
			default:
					$szResult .=  $ss;
			}
		}
		$szResult = strtolower($szResult);
		return $szResult;
	}

function array_strip_slashes( &$data) {
	foreach ( $data as $key=>$row) {
		if ( !is_scalar( $row )) {
			$data[ $key ] = array_strip_slashes( $row );
		} else {
			$data[ $key ] = stripslashes( $row );
		}
	}
	return $data;
}
?>