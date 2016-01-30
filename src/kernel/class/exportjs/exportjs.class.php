<?
define('EXPORTJS_PATH',dirname(__FILE__).'/');
class ExportJS {
	var $aFunction;
	var $szBasePath = '';
	var $bParam = false;
	var $szParamName = '';
	var $szParamData = '';
	var $szFunctionName = '';
	var $aArgument = array();
	var $szRPCResult = array();
	var $szEncoding = 'utf-8';
	var $aAddParam = array();
	function __construct() {
		$this->szBasePath = EXPORTJS_PATH.'plugins/';
	}
	function setEncoding($szEncoding) {
		$this->szEncoding = $szEncoding;
	}
	function addParam($name,$value) {
		if (is_array($value) || is_object($value) || is_array($name) || is_object($name)) {
			throw new Exception('CExportJS::addParam значение и имя могут быть только строкой');
			trigger_error('CExportJS::addParam один из параметров поступивших в функцию не является строкой $name="'.htmlspecialchars($name).'", $value="'.htmlspecialchars($value).'"',E_USER_ERROR);
		}
		$this->aAddParam[$name] = ($value);

	}
	function add($function) {

		if (is_callable($function)) {
			$this->aFunction[] = $function;
		} else {
			if (is_array($function)) {
				$obj = $GLOBALS[$function[0]];
				if (is_callable(array($obj,$function[1]))) {
					$this->aFunction[] = $function;
				} else {
					die('Function `'.print_r($function,true).'`does not exists');
				}
			} else
				die('Function `'.print_r($function,true).'`does not exists');
		}
	}
	function _parseXMLStartElement($parser, $name, $attribs) {
		$this->bParam = false;
		$this->szParamData = '';
		switch ($name) {
			case 'PARAM':
				$this->bParam = true;
				$this->szParamName = !empty($attribs['NAME'])?$attribs['NAME']:'';
				$this->szParamData = '';
				break;
			case 'FUNCTION':
				$this->szFunctionName = $attribs['NAME'];
				break;
			default:
				break;
		}
	}
	function _parseXMLEndElement($parser, $name) {
		switch ($name) {
			case 'PARAM':
				$this->attachParam($this->szParamName,$this->szParamData);
				$this->bParam = false;
				$this->szParamData = '';
				$this->szParamName = '';
				break;
			case 'FUNCTION':

				if (!empty($this->szFunctionName)) {
					$aFunction = explode('.',$this->szFunctionName);
					if (sizeof($aFunction) > 1) {
						for ($i = 0; $i < sizeof($this->aFunction);$i++) {
							if (is_array($this->aFunction[$i])
								&& ($aFunction[0] == $this->aFunction[$i][0])
								&& ($aFunction[1] == $this->aFunction[$i][1])) {
								$obj = $GLOBALS[$aFunction[0]];
								$this->RPCResult [$this->szFunctionName] = call_user_func_array(array($obj,$aFunction[1]),$this->aArgument);
							}
						}
					} else {
						if (empty($this->aFunction[$this->szFunctionName])) {
							$this->RPCResult[$this->szFunctionName] = call_user_func_array($aFunction[0],$this->aArgument);
						}

					}
					$this->aArgument = array();
//					_debug(1);
					$this->szFunctionName = '';
				}
				break;
			default:;
		}
	}
	function _parseXMLData($parser, $data) {

		if ($this->bParam ) {
			$this->szParamData .= $data;
		}
	}
	function _parseXMLDefault($parser, $data) {
	}
	function attachParam($name,$data) {
		if (!is_array($data)) {

			preg_match_all('#\[\'([^\]]+)\'\]#',$name,$aMatch);
			if (sizeof($aMatch[1]) > 0) {
				$path = substr($name,'[',strpos($name,'[') - 1);
				if (empty($this->aArgument[$path])) {
					$this->aArgument[$path] = array();
				}

				$aTarget = &$this->aArgument[$path];
				for ($i =0; $i < sizeof($aMatch[1]);$i++) {
					if (empty($aTarget[$aMatch[1][$i]])) {
						$aTarget[$aMatch[1][$i]] = array();
					}
					$aTarget = &$aTarget[$aMatch[1][$i]];
				}
				$aTarget = substr($data,1,strlen($data) -2);
			} else {
				$this->aArgument[$name] = substr($data,1,strlen($data) -2);
			}

		} else {
			foreach ($data as $aValue) {
				attachParam($name,$aValue);
			}
		}

	}
	function encodeResults($value,$name,$path) {
		if (empty($path)) {
			$path= $name;
		} else {
			$path .= '[\''.$name.'\']';
		}

		if (is_array($value)) {
			// проверка на !empty($value ) сделана, для того чтобы полученные в результатах пустые массивы тоже передавались
			$szResult = '';
			foreach ($value as $newName=> $newValue ) {
				$szResult .= $this->encodeResults($newValue,$newName,$path);
			}
			return $szResult;
		} else {
			return '<results name="'.$path.'"><![CDATA[|'.$value.'|]]></results>';
		}
	}
	function callRPC() {
		//file_put_contents('test.xml',$_POST['rpc']);

		if (!empty($_POST['rpc'])) {
			header( 'HTTP/1.1 200' );
			header('Content-type: text/xml; charset='.$this->szEncoding );
			$this->RPCResult = array();
			// инициализируем парсер
			$xml_parser = xml_parser_create();

			xml_parser_set_option($xml_parser, XML_OPTION_CASE_FOLDING, 1);

			xml_set_element_handler($xml_parser, array(&$this,"_parseXMLStartElement"),array(&$this, "_parseXMLEndElement"));

			xml_set_character_data_handler($xml_parser, array(&$this,"_parseXMLData"));
			xml_set_default_handler($xml_parser, array(&$this,"_parseXMLDefault"));

			// стартуем

			if (!xml_parse($xml_parser, $_POST['rpc'], true)) {
				die(sprintf("XML error: %s at line %d\n",
					xml_error_string(xml_get_error_code($xml_parser)),
					xml_get_current_line_number($xml_parser)));
			}
			// освобождаем парсер
			xml_parser_free($xml_parser);
			// теперь криэйтим результат
			$szResult = '<EXTASY><CALL_RESULT>';

			foreach ($this->RPCResult as $key=>$value) {
				$szResult .= sprintf('<FUNCTION name="%s">%s</FUNCTION>',$key,$this->encodeResults($value,'result',''));
			}
			$szResult .= '</CALL_RESULT></EXTASY>                                   ';
			//$szResult = iconv('UTF-8','WINDOWS-1251',$szResult);
			ob_clean();
			return '<?xml version="1.0" encoding="utf-8"?>'."\r\n".$szResult;
		}
	}
	function generate() {
		// считываем шаблоны
		$szObjectTemplate = file_get_contents($this->szBasePath.'object.tpl');
		$szFunctionTemplate = file_get_contents($this->szBasePath.'function.tpl');
		//
		$szFunctions = '';
		// формируем массив функций

		$szScriptName = isset($_SERVER['SCRIPT_URL'])?$_SERVER['SCRIPT_URL']:$_SERVER['REQUEST_URI'];

		foreach ($this->aFunction as $value) {
			if (is_array($value)) {
				$aReplace = array(
				'{szObject}'  =>  $value[0],
				'{szFunctionName}'  =>  $value[1],
				'{$szScriptName}' => $szScriptName
				);
				$szContent = $szObjectTemplate;
			} else {
				$aReplace = array(
					'{szFunctionName}'=> $value,
					'{$szScriptName}' => $szScriptName,
					);
				$szContent = $szFunctionTemplate;
			}
			$szFunctions .= str_replace(array_keys($aReplace),array_values($aReplace),$szContent);
		}
		
		// генерим доп параметры
		$tmp = array();
		foreach ($this->aAddParam as $key=>$value) {
			$tmp[] = $key.'='.urlencode($value);
		}
		$szAddParam = implode('&',$tmp);
		include $this->szBasePath.'script.tpl';
	}
	function process() {
		if (sizeof($_POST) > 0) {
			$szResult= $this->callRPC();
			print $szResult;
		} else {
			print $this->generate();
		}

	}
	function writeRequest($filename) {
		file_put_contents($filename,print_r($_REQUEST,true));
	}
}
?>
