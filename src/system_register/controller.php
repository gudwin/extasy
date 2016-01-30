<?
/**
 * Контроллер реестра, управляет обновлениями значений, сохраняет в кеше  
 * @package exst3/modules/system_register
 * @author Gisma (info@gisma.ru)
 * @date 13.10.2009

 */
class SystemRegister 
{
	protected $nId = 0;
	protected $name = '';
	protected $comment = '';
	protected $aCurrentPath = ''; // Путь на который указывает текущий элемент
	protected $parent = null; // Объект внутри которой работает объект
	public function __construct($key = '')
	{
		$this->aCurrentPath = SystemRegisterHelper::responsePath($key);

		// Получаем детей текущего элемента
		// + проверяем существует ли путь
		$nParent = 0; 
		if(!empty($this->aCurrentPath)) {
			$szCurrentPath = '';
			foreach ($this->aCurrentPath as $row) 
			{
				// Создаем предка
				$this->parent = new SystemRegister($szCurrentPath);

				$aInfo =  SystemRegisterSample::get($row,$nParent);

				// Проверяем, найден ли элемент
				if (empty($aInfo))
				{
					throw new SystemRegisterException('Element `'.$row.'` not found. Parent:'.$nParent);
				}
				// Является ли он веткой?
				elseif (!SystemRegisterHelper::isBranch($aInfo['value'],$aInfo['type'])) {
					throw new SystemRegisterException('Element `'.$row.'` not a branch. Parent:'.$nParent.'::'.$szCurrentPath);
				}
				// Значит повезло
				$nParent = $aInfo['id'];

				// Сохраняем полный путь (употребляется в начале цикла)
				$szCurrentPath = $this->parent->getFullPath().'/'.$row;
				$this->name = $aInfo['name'];
				$this->comment = $aInfo['comment'];	
				
			}
			$this->nId = $nParent;
			
		}
		else {
		}
	}
	public function __get($key)
	{
		// Вызов метода get
		return $this->get($key);
	}
	public function __set($key,$value)
	{
		// Вызов метода update
		return $this->update($key,$value);
	}
	public function __isset($key)
	{
		
		// Проверяем, есть ли уже данный ключ
		$aInfo =  SystemRegisterSample::get($key,$this->nId);
		
		return !empty($aInfo)?true:false;
	}
	public function __unset($key)
	{
		$this->delete($key);
	}
	public function insert($key,$value = null,$comment = '',$type = 'string')
	{
		//
		$aNewPath = SystemRegisterHelper::responsePath($key);
		// Если путь больше чем 1 
		if (sizeof($aNewPath) > 1) 
		{
			return SystemRegisterHelper::delegate($this->aCurrentPath,$aNewPath,'insert',array($value,$comment,$type));
		}
		else {
			// Если текущий уровень - первый, то вставки и обновления запрещены
			$this->checkFirstLevel();
			// Проверяем, что ключ уже существует
			$aElement = SystemRegisterSample::get($key,$this->nId);
			if (!empty($aElement)) {
				throw new SystemRegisterException('Insert failed! Element `'.$key.'` already exists');
			}
			// Добавляем в бд 
			return SystemRegisterSample::insert($this,$key,$value,$comment,$type);

		}

		// Всё
	}
	public function update($key,$value)
	{
		//
		$aNewPath = SystemRegisterHelper::responsePath($key);
		// Если путь больше чем 1 
		if (sizeof($aNewPath) > 1) 
		{
			return SystemRegisterHelper::delegate($this->aCurrentPath,$aNewPath,'update',array($value));
		}
		else {
			// Если текущий уровень - первый, то вставки и обновления запрещены
			$this->checkFirstLevel();

			$element = $this->get($key);
			if ($element instanceOf SystemRegisterPrimitive) {
				$element->value = $value;
			}
			else {
				throw new SystemRegisterException('Update failed! Key `'.$key.'` - branch. ');
			}
		}
	}
	public function delete($key)
	{
		//
		$aNewPath = SystemRegisterHelper::responsePath($key);

		// Если путь больше чем 1 
		if (sizeof($aNewPath) > 1) 
		{
			return SystemRegisterHelper::delegate($this->aCurrentPath,$aNewPath,'delete',array());
		}
		else {
			// Если текущий уровень - первый, то вставки и обновления запрещены
			$this->checkFirstLevel();

			// Получаем элемент
			$element = $this->get($key);
			// Если это просто лист
			if ($element instanceOf SystemRegisterPrimitive) {
				$element->delete();
			}
			// Эге... значит это папочка
			else {
				// Из папки 
				$aChilds = SystemRegisterSample::selectChild($element->getId());
				// Удаляем из бд
				foreach($aChilds as $key=>$element2) {
					$element->delete($element2['name']);
				}
				SystemRegisterSample::delete($this,$element->getId());
			}
		}
	}
	/**
	 * По ключу элемента ($key) отыскивает его детей и возвращает их
	 * @param $key string Путь от текущего элемента, к которому нужно получить доступ
	 */
	public function get($key)
	{
		//
		$aNewPath = SystemRegisterHelper::responsePath($key);
		// Если путь больше чем 1 
		if (sizeof($aNewPath) > 1) 
		{
			return SystemRegisterHelper::delegate($this->aCurrentPath,$aNewPath,'get',array());
		}
		else
		{
			$aChilds = SystemRegisterSample::selectChild($this->nId);
			// Ищем среди детей
			foreach ($aChilds as $row)
			{
				if ($row['name'] == $aNewPath[0])
				{
					// Имя совпало
					// Это ветвь?
					if (SystemRegisterHelper::isBranch($row['value'],$row['type']))
					{
						$szNewPath = SystemRegisterHelper::createPath($this->aCurrentPath,$aNewPath);
						return new SystemRegister($szNewPath);
					}
					else
					{
						$primitive = new SystemRegisterPrimitive($this,$row['name'],$row['value'],$row['comment'],$row['type'],$row['id']);
						Return $primitive;
					}
				}
			}
		}
		throw new SystemRegisterException('Element `'.$key.'` not found. Current element:'.$this->getFullPath());
	}
	/**
	 * Очищает от дочерних элементов
	 */
	public function clear()
	{
		$aChilds = SystemRegisterSample::selectChild($this->nId);
		
		foreach ($aChilds as $row)
		{
			$this->delete($row['name']);
		}
	}
	/**
	 * @return SystemRegister
	 */
	public function getParent()
	{
		//
		return $this->parent;
	}
	/** 
	 * Проверяет находится ли ветка на первом уровне (нету, предка)
	 */ 
	protected function checkFirstLevel() {
		if ($this->nId == 0) {
			throw new SystemRegisterException('Manipulation with first level denied');
		}
	}
	/**
	 * Возвращает полный путь до текущей ветви
	 */
	public function getFullPath()
	{
		//

		return '/'.implode('/',$this->aCurrentPath);
	}
	/**
	 * Возвращает индекс текущей ветви
	 */
	public function getId()
	{
		//
		return $this->nId;
	}
	public function getName() {
		return $this->name;
	}
	public function getComment() {
		return $this->comment;
	}
	/**
	 * Обновляет имя и комментарий текущего нода
	 */
	public function setupAttr($name,$comment)
	{
		SystemRegisterSample::update($this->parent,$this->nId,$name,'',$comment,SYSTEMREGISTER_BRANCH_TYPE);
		return $this;
	}

}
?>