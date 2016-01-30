<?php
use \Faid\DBSimple;
use \Faid\DB;
class CConfigSchema {
	const EventName = 'CConfig.Update';
	/**
	 * Хранит данные об контролах данной схемы
	 * @var array
	 */
	protected $controls = array();
	/**
	 * Хранит вкладки текущей схемы
	 * @var array
	 */
	protected $tabSheets = array();
	protected $id;
	protected $name; 
	protected $title;
	/**
	 * Индекс в таблице sitemap, означает, что страница подключена к sitemap
	 * @var int 
	 */
	protected $sitemapId = 0;
	/**
	 * Загружает класс данными на основе хеш-массива
	 * @param array $data
	 */
	public function __construct($data) {
		$this->name = $data['name'];
		$this->title = !empty($data['title'])?$data['title']:'';
		$id = !empty($data['id'])?$data['id']:0;		
		$this->id = IntegerHelper::toNatural($id);
		//
		$sitemapId = isset($data['sitemapId'])?$data['sitemapId']:0;
		$sitemapId = IntegerHelper::toNatural($sitemapId);
		$this->sitemapId = $sitemapId;
		if (!empty($this->id)) {
			$this->loadControls();
		}
		
		
	}
	public function __get( $key) {
		return $this->getControlByName( $key ); 
	}
	public function __set( $key, $value ) {
		$control = $this->getControlByName( $key );
		$control->setValue( $value );
	}
	public function getId() {
		return $this->id;
	}
	/**
	 * Возвращает системное имя конфига
	 */
	public function getName() {
		return $this->name;	
	}
	/**
	 * Возвращает подпись к конфигу
	 */
	public function getTitle() {
		return $this->title;
	}	
	/**
	 * Устанавливает вкладки редактирования у конфига
	 * @param array $data
	 */
	public function setTabsheets($data = array()) {
		$this->getTabSheets();
		// Перебор вкладок, проверяем что это массив
		if (!is_array($data)) {
			throw new CConfigException('setTabsheets empty argument ');
		}
		// удаляем все вкладки, которых нету в этом массиве
		$tabsheetsTitle = array_keys($data);
		foreach ($this->tabSheets as $key=>$row) {
			$pos = array_search($row['title'],$tabsheetsTitle);
			// Если вкладка не найден в новых, киляем её
			if ($pos === false) {
				$sql = 'delete from `%s` where `id`="%d"';
				$sql = sprintf($sql,
								CCONFIG_TABSHEETS_TABLE,
								$row['id']);
				DB::post($sql);
				unset($this->tabSheets[$key]);
				break;
			}
		}
		// Удаляем все ссылки на вкладки у котролов
		DBSimple::update(CCONFIG_CONTROL_TABLE, array('tabId' => 0), array('schemaId' => $this->id));
		
		// Проверяем, что каждый элемент массива существует как контрол
		$order = 1;
		$controlOrder = 1;
		foreach ($data as $key=>$row) {
			$tabsheetId = 0;
			$pos = false;
			foreach ($this->tabSheets as $tabInfo) {
				if ($tabInfo['title'] == $key) {
					$tabsheetId = $tabInfo['id'];
					break;
				}
			}
			if (empty($tabsheetId)) {
				// Нету?
				$sql = 'INSERT INTO `%s` SET `title`="%s",`schemaId`="%d",`order`="%d"';
				$sql = sprintf($sql,
								CCONFIG_TABSHEETS_TABLE,
								\Faid\DB::escape($key),
								$this->id,
								$order);				
				// Создаем
				DB::post($sql);
				// Индекс вкладки сохраняем				
				$tabsheetId = DB::$connection->insert_id;
			} else {
				$sql = 'UPDATE `%s` SET `order`="%d" WHERE `id`="%d"';
				$sql = sprintf($sql,
								CCONFIG_TABSHEETS_TABLE,
								$order,
								$tabsheetId);
				DB::post($sql);
				
			}
			if (is_string($row)) {
				$row = explode(',',$row);
			}
			// Перебор элементов
			foreach ($row as $controlName) {
				// Проверяем, что элемент существует
				$control = $this->searchControlByName($controlName);
				// Устанавливаем значение вкладки
				$sql = 'UPDATE `%s` SET `tabId`="%d",`order`="%d" where `id`="%d"';
				$sql = sprintf($sql,
								CCONFIG_CONTROL_TABLE,
								$tabsheetId,
								$controlOrder,
								$control->getId());
				DB::post($sql);
				$controlOrder++;
			}
			$order++;
		}
		$this->tabSheets = array(); 
	}
	public function getTabSheets() {
		if (empty($this->tabSheets)) {
			$sql = 'select * from `%s` where `schemaId`="%d" order by `order` ASC';
			$sql = sprintf($sql,
				CCONFIG_TABSHEETS_TABLE,
				$this->id);
			$this->tabSheets = DB::query($sql);
		}
		// Возвращаем вкладки простым массивом имен
		$result = array();
		foreach ($this->tabSheets as $row) {
			$result[] = array(
				'id' => $row['id'],
				'title' => $row['title'],
			);
		}
		return $result;
		
	}
	public function selectTabbedControls() {
		$this->getTabSheets();
		$result = array();
		foreach ($this->tabSheets as $row) {
			// Получаем все котролы данной вкладки
			$sql = 'select `name` from `%s` where `tabId`="%d" order by `order` ASC';
			$sql = sprintf($sql,CCONFIG_CONTROL_TABLE,$row['id']);
			$controls = DB::query($sql);
			$add = array();
			foreach ($controls as $record) {
				$control = $this->searchControlByName($record['name']);
				$add[$record['name']] = $control;
			}
			$result[$row['title']] = $add;
		}
		return $result;
	}
	/**
	 * Возвращает список всех контролов в схеме. Контролы не сортируются
	 * @return array
	 */
	public function returnControlList() {
		return $this->controls;
	}
	/**
	 * Возвращает контрол из текущей темы, контрол отыскивается по имени ($name)
	 * @param string $name
	 * @throws CConfigException
	 * @return CConfigBaseControl
	 */
	public function getControlByName($name) {
		foreach ($this->controls as $row) {
			if ($row->getName() == $name) {
				return $row;
			}
		}
		throw new CConfigException(sprintf('Control with name=`'.htmlspecialchars($name).'` not found'));
	}
	/**
	 * Добавляет в текущий конфиг новый контрол
	 * @param string $name
	 * @param string $xtype
	 * @param string $title
	 * @param mixed $value
	 * @param array $additionalConfig
	 */
	public function addControl($name,$xtype,$title,$additionalConfig = array(),$value = null) {
		try {
			$this->searchControlByName($name);
			$notFound = false;
		} catch (CConfigException $e) {
			$notFound = true;
		}
		// Если контрол уже есть, бросаем исключение
		if (!$notFound) {
			throw new CConfigException('Control with name="'.$name.'" already exists');
		}
		
		$sql = 'INSERT INTO `%s` SET `schemaId`="%d",`tabId`="0",`name`="%s",`xtype`="%s",`title`="%s",`value`="",`config`="%s",`order`="%d"';
		$sql = sprintf($sql,
						CCONFIG_CONTROL_TABLE,
						$this->id,
						\Faid\DB::escape($name),
						\Faid\DB::escape($xtype),
						\Faid\DB::escape($title),
						!empty($additionalConfig)?\Faid\DB::escape(serialize($additionalConfig)):'',
						sizeof($this->controls) + 1);
		DB::post($sql);
		$id = DB::$connection->insert_id;
		//
		// Вызываем after create у контрола
		$class = CConfigControlManager::loadControl($xtype);
		call_user_func(array($class,'afterCreate'),$additionalConfig,$name,$value,$title);
		//
		$control = CConfigControlManager::createControl($id,$name,$xtype,$title,$additionalConfig, $value,$this);
		$control->setValue( $value );
		$this->loadControls();
		return $control;
	}
	/**
	 * Удаляет контрол
	 * @param string $name 
	 */
	public function removeControl($name) {
		$control = $this->searchControlByName($name);
		$control->delete();
		
		$this->loadControls();
	}
	/**
	 * 
	 * @param array $data хеш массив в формате имя котрола, значение
	 */
	public function setValues($data) {
		foreach ($data as $key=>$row) {
			try {
				$control = $this->searchControlByName($key);
			}
			catch (Exception $e) {
				continue;
			}
			$control->setValue($row);
		}
		EventController::callEvent( self::EventName, $this );
		
	}
	public function getValues() {
		$result = array();
		foreach ($this->controls as $control) {
			$result[$control->getName()] = $control->getValue();
		}	
		return $result;
	}
	/**
	 * Возвращает данные из схемы, подготовленные для вывода
	 */
	public function getViewValues() {
	
		$result = array();
		foreach ($this->controls as $control) {
			$result[$control->getName()] = $control->getViewValue();
		}
		return $result;
	}
	public function updateSchema($name,$title) {
		if (empty($name)) {
			throw new CConfigException('Empty schemaName');
		}
		// Проверяем есть ли уже такая вкладка
		$sql = 'select * from `%s` where `name`="%s" and `id` <> "%d"';
		$sql = sprintf($sql,
						CCONFIG_SCHEMA_TABLE,
						\Faid\DB::escape($name),
						$this->id);
		$found = DB::get($sql);
		if ($found) {
			throw new CConfigException('Duplicate schema name `'.$name.'`');
		}
		$this->name = $name;
		$this->title = $title;
		$this->store();
	}
	/**
	 * Удаляет данную схему
	 */
	public function delete() {
		$sqlTemplate = 'delete from `%s` where `%s`="%d"';
		$sql = sprintf($sqlTemplate,CCONFIG_SCHEMA_TABLE,'id',$this->id);
		DB::post($sql);
		$sql = sprintf($sqlTemplate,CCONFIG_TABSHEETS_TABLE,'schemaId',$this->id);
		DB::post($sql);
		$sql = sprintf($sqlTemplate,CCONFIG_CONTROL_TABLE,'schemaId',$this->id);
		DB::post($sql);
	}
	/**
	 * Устанавливает sitemap-индекс в бд
	 * @param int $sitemapId
	 */
	public function setupSitemapLink($sitemapId) {
		$this->sitemapId = IntegerHelper::toNatural($sitemapId);
		$this->store();
	}
	/**
	 * Возвращает индекс sitemap-элемента
	 */
	public function getSitemapLink() {
		return $this->sitemapId;
	}
	/**
	 * 
	 * @param string $name
	 * @return CConfigBaseControl
	 */
	protected function searchControlByName($name) {
		foreach ($this->controls as $control) {
			if ($control->getName() == $name) {
				return $control;
			}
		}
		throw new CConfigException('Control `'.$name.'` not found');
	}
	protected function loadControls() {
		// Загружаем контролы
		$sql = 'select * from `%s` WHERE `schemaId`="%s" ORDER by `order` ASC';
		$sql = sprintf($sql,
			CCONFIG_CONTROL_TABLE,
			$this->id);
		$data = DB::query($sql);
		// Конвертируем в объекты
		$result = array();
		foreach ($data as $row) {
			$result[] = CConfigControlManager::createControl($row['id'],$row['name'],$row['xtype'],$row['title'],$row['config'],$row['value'],$this);
		}
		$this->controls = $result;	
	}
	/**
	 * Сохраняет в бд данные
	 */
	protected function store() {
		// Сохраняем вкладки
		$sql = 'update `%s` set `name`="%s",`title`="%s",`sitemapId`="%d" where `id`="%d"';
		$sql = sprintf($sql,
					CCONFIG_SCHEMA_TABLE,
					\Faid\DB::escape($this->name),
					\Faid\DB::escape($this->title),
					\Faid\DB::escape($this->sitemapId),
					$this->id);
		DB::post($sql);					
		
		
	}
} 