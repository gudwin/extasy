<?php
/**
 * Данный класс занимается работой с контролами
 * @author Gisma
 *
 */
class CConfigControlManager {
	/**
	 * Данный метод вызывается после того, как контрол был создан, возвращается объект созданного контрола
	 * @param int $id
	 * @param string $name
	 * @param string $type
	 * @param string $title
	 * @param mixed $config
	 * @param mixed $value
	 * @return CConfigBaseControl
	 */
	public static function createControl($id,$name,$type,$title,$config,$value,$schema) {
		$class = self::loadControl($type);
		// Если это строка, то идет выборка из бд 
		if (is_string($config)) {
			$config = unserialize($config);
		} else {			
		}
		$control = new $class($id,$name,$title,$config,$value,$schema);
		
		return $control;				
	}
	/**
	 * Загружает класс контрола и возвращает имя класса 
	 * @param string $type тип контролоа
	 * @return string
	 */
	public static function loadControl($type) {
		$type = escapeshellcmd($type);
		$class = sprintf( '%sCConfigControl', $type );
		if (class_exists( $class )) {
			return $class;
		}
		$class = 'CConfigControl_'.$type;
		if ( class_exists( $class )) {
			return $class;
		}
		// Пробиваем если такой файл в папке контролов
		$path = CCONFIG_CONTROLS_PATH.$type.'.php';
		if (file_exists($path)) {
			require_once $path;

		} else {
			// Добавляем предзагрузку из доб. путей
			$register = new SystemRegister('Applications/cconfig/user_control_path');
			if (isset($register->$type)) {
				$class = 'CConfigControl_'.$type;
				$possiblePath = $register->$type->value;
				$prefixes = array('',APPLICATION_PATH,LIB_PATH);
				foreach ( $prefixes as $key=>$row ){
					if ( file_exists( $row . $possiblePath )) {
						require_once $row. $possiblePath;
						if ( class_exists( $class )) {
							break;
						}
					}
				}

			} else {
				throw new CConfigException('Unknown control `'.$type.'`');
			}
		}
	
		if (!class_exists($class)) {
			throw new CConfigException('Control class `'.$class.'` not found. File included, but class not found');
		}
		return $class;
	}
	
	/**
     * Возвращает имена классов всех контролов, с которыми в данный момент работает CConfig
     * @return array
	 */
	public static function selectAll() {
		// Грузим все файлы в папке контролов
		$files = DAO::getInstance('fs')->getFileList(CCONFIG_CONTROLS_PATH);
		$result = array();
		
		foreach ($files as $row) {
			try {
				$row = basename($row,'.php');
				$result[$row] = self::loadControl($row);
			} catch (Exception $e) {
				
			}
		}
		// Дополняем классами, записанными в реестре
		$register = new SystemRegister('Applications/cconfig/user_control_path');
		$paths = SystemRegisterSample::selectChild($register->getId());
		foreach ($paths as $row) {
			try {
				$result[$row['name']] = self::loadControl($row['name']);
			} catch (Exception $e) {
				
			}
		}
		return $result;
	}
}