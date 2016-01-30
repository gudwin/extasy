<?
//************************************************************//
//                                                            //
//         Класс работы с настройками                         //
//       Copyright (c) 2006  ООО SmartDesign                  //
//               отдел/сектор                                 //
//       Email:   gisma@smartdesign.by                        //
//                                                            //
//  Разработчик: Gisma (19.03.2007)                           //
//  Модифицирован:  19.03.2007  by Gisma                      //
//                                                            //
//************************************************************//
//
// 11.09.2009 Обновление класса конфигуратора, теперь без ошибок!;)
//
// Служит для организации форм быстрого редактирования
// конфигурационных файлов
// Работает с конфигами, создает PHP файлы, формовые элементы создаются
// с помощью контролов

define('CONFIG_PATH',CLASS_PATH.'config/');
require_once CONFIG_PATH . 'baseclasses/config.interface.php';

class Config {
	// Коды ошибок
	const FILE_NOT_FOUND = 1;
	const FILE_NOT_WRITEABLE = 2;

	public function __construct() {
	}
	/**
	*   -------------------------------------------------------------------------------------------
	*   @desc Загружает из все сущестсвующие в нем директивы
	*   @return
	*   -------------------------------------------------------------------------------------------
	*/
	public function load($szFileName) {
		if (!file_exists($szFileName) || !is_readable($szFileName)) {
			throw new Exception('CConfig :: loadConfig файл конфига "'.htmlspecialchars($szFileName).'" не найден либо нечитаем');
		}
		// получаем контент файла
		$szContent = file_get_contents($szFileName);

		// регулярное выражение
		$szPattern = <<<EOD
#\/\/\#(.*?)\s+define\(['"]([^'"]+)['"],['"]([^\n]*)['"]\);\n?#si
EOD;
		// вырезаем все связки
		preg_match_all($szPattern,$szContent,$aMatch,PREG_SET_ORDER);
		$aResult = array();
		// для каждой связки от первой до последней
		for ($i = 0; $i < sizeof($aMatch); $i++) {
			$tmp = array();
			$nPos1 = strpos($aMatch[$i][1],';');
			if ($nPos1 !== FALSE) {
				$nPos2 = strrpos($aMatch[$i][1],';');
				// Получаем тип
				
				$tmp['type'] = substr($aMatch[$i][1],0,$nPos1);
				// Получаем коммент
				$tmp['comment'] = substr($aMatch[$i][1],$nPos2 + 1);
				
				// Получаем доп данные
				$tmp['additional'] = substr($aMatch[$i][1],$nPos1 + 1,$nPos2 - $nPos1 - 1);
				

			} else {
				// Получаем тип
				$tmp['type'] = $aMatch[$i][1];

			}

			// Получаем текущее значение
			$tmp['value']= $aMatch[$i][3];
			
			$aReplace = array('\\','"',"\r","\n",'$');
			$aFind = array('\\\\','\\"','\\r','\\n','\\$');
			$tmp['value'] = str_replace($aFind,$aReplace,$tmp['value']);
				
			// Записываем результат

			$aResult[$aMatch[$i][2]] = $tmp;
		}
		
		// возвращаем результат
		return $aResult;
	}
	/**
	*   -------------------------------------------------------------------------------------------
	*   @desc Сохраняет в указанный файл, все переданные директивы
	*   @return
	*   -------------------------------------------------------------------------------------------
	*/
	public function save($szFileName, array $aData) {
		if (!file_exists($szFileName)) {
			return self::FILE_NOT_FOUND;
		}
		if (!is_writeable($szFileName)) {
			return self::FILE_NOT_WRITEABLE;
		}
		// грузим файл
		$aInfo = $this->load($szFileName);
		// получаем контент файла
		$szContent = file_get_contents($szFileName);

		// для всего массиво дефайнов
		foreach ($aData as $key=>$value) {
				
			// формируем значение строк
			if (!is_string($value)) {

				// если массив посылаем на обработку
				if (!empty($aInfo[$key]) && isset($aInfo[$key]['type']))
				{
					$szClassName = $this->getClassName($aInfo[$key]['type']);
					if (!class_exists($szClassName)) {
						$this->loadConfigClass($aInfo[$key]['type']);
					}
					$extension = new $szClassName();
					$value = $extension->toString($value);
				}
				else
				{
					// Игнорируем
					continue;
				}
			}
			$szValue = $value;
	
			$aFind = array('\\','"',"\r","\n",'$');
			$aReplace = array('\\\\','\\"','\\r','\\n','\\$');
			$szValue = str_replace($aFind,$aReplace,$szValue);
#			$szValue = str_replace("\r\n",'\r\n',$szValue);
#			$szValue = str_replace('"','\"',$szValue);
#			$szValue = str_replace("\r",'\r',$szValue);
#			$szValue = str_replace("\n",'\n',$szValue);

			// заменяем старый дефайн на новый
			$szPattern = '/define\([\'"]'.addcslashes($key,'\"').'[\'"],[\'"](.*?)[\'"]\);(\s)/i';
			$szReplace = 'define(\''.addcslashes($key,'\"').'\',"::fill_now::");${2}';
			

			$szContent = preg_replace($szPattern,$szReplace,$szContent);

			$szContent = str_replace('::fill_now::',$szValue,$szContent);

		}

		// сохраняем файл
		$f_header = fopen($szFileName,'w+');
		fputs($f_header,$szContent);
		fclose($f_header);

		return 0;
	}
	/**
	*   -------------------------------------------------------------------------------------------
	*   @desc Создает объекты  контролов
	*   @return
	*   -------------------------------------------------------------------------------------------
	*/
	public function exec($aData) {
		// инфициализация массива результатов
		$aResult = array();
		// перебор массив входящих данных и формирование объектов
		foreach ($aData as $key => $row) {
			if (empty($row['type'])) {
				trigger_error('','Config::exec Для директивы "'.htmlspecialchars($key).'" не найден тип',E_USER_ERROR);
				continue;
			}
			$szClassName = $this->getClassName($row['type']);
			if (!class_exists($szClassName)) {
				$this->loadConfigClass($row['type']);
			}
			// проверяем поддерживает ли расширение интерфейс ExtConfigurable
			$aImplements = class_implements($szClassName);
			if (!isset($aImplements['ExtConfigurable'])) {
				trigger_error('CConfig :: расширение "'.htmlspecialchars($szClassName).'" не поддерживает интерфейс ExtConfigurable',E_USER_ERROR);
			}
			// создаем объект расширения
			$extension = new $szClassName();
			$extension->setConfigData($key,$row['value'],!empty($row['additional'])?$row['additional']:'',$row['comment']);

			// запись результата
			$aResult[$key] = $extension->getControl();
		}
		return $aResult;

	}
	/**
	*   -------------------------------------------------------------------------------------------
	*   @desc
	*   @return
	*   -------------------------------------------------------------------------------------------
	*/
	function generate($aData) {
		return $this->exec($aData);
	}
	public function loadConfigClass($szType) {
		$szPath = CONFIG_PATH.'plugins/'.$szType.'.php';
		// если файл существует, инклюдим его и создаем объект класса
		if (file_exists($szPath)) {
			require_once $szPath;
		} else {
			throw new Exception('CConfig::exec Конфиг "'.htmlspecialchars($szType).'" не найден файл с типом : '
									.htmlspecialchars($szType));
			continue;
		}

	}
	protected function getClassName($szType) {
		return 'Config_'.$szType;
	}
}


?>