<?
/**
 * Класс примитивной ячейки в дереве реестра. Примитивная ячейка - запись не имеющая подчиненных записей, не ветвь, короче :)
 * @package exst3/modules/system_register
 * @author Gisma (info@gisma.ru)
 * @date 13.10.2009
 */
class SystemRegisterPrimitive
{
	protected $szName = '';
	protected $szValue = '';
	protected $szType = '';
	protected $szComment = '';
	protected $parent = null; 
	protected $nId = 0;
	public function __construct(SystemRegister $parent,$name,$value,$comment,$type,$id = 0) 
	{
		$this->parent = $parent;
		$this->szName = $name;
		$this->nId = $id;
		$this->szValue = $value;
		$this->szComment = $comment;
		$this->szType = $type;
	}
	public function __get($name) 
	{
		switch ($name)
		{
			case 'parent':
				return $this->parent;
				break;
			case 'id' :
				return $this->nId;
			break;
			case 'name' :
				return $this->szName;
			break;

			case 'type':
				return $this->szType;
				break;
			case 'comment':
				return $this->szComment;
				break;
			case 'value':
				return $this->szValue;
				break;
			default:
				throw new SystemRegisterException('Uknown property! Getting failed');
				break;
		}
		
	}
	public function __set($name,$value)
	{
		switch ($name)
		{
			case 'name' : 
				$this->szName = $value;
				break;
			case 'type':
				// Проверка, не попытка ли это сделать лист ветвью
				if ($value == 'folder')
				{
					throw new SystemRegisterException('Trying to access property `type` with inaccessable value!');
				}
				$this->szType = $value;
				break;
			case 'comment':
				$this->szComment = $value;
				break;
			case 'value':
				$this->szValue = $value;
				break;
			default:
				throw new SystemRegisterException('Uknown property! Setting failed');
				break;
		}
		// Любое обновление параметров, сразу вызывает обновление в бд
		$this->update();

	}
	public function __toString()
	{
		return $this->szValue;
	}
	/**
	 * Удаляет лист 
	 */
	public function delete()
	{
		if (empty($this->nId))
		{
			throw new SystemRegisterException('Deletion failed! Attribute `id` empty');
		}
		SystemRegisterSample::delete($this->parent,$this->nId,false);
	}
	/**
	 * Сохраняет новый лист
	 */
	public function insert()
	{
		if (!empty($this->nId))
		{
			throw new SystemRegisterException('Insert failed! Attribute $id not empty');
		}
		SystemRegisterSample::insert($this->parent,$this->szName,$this->szValue,$this->szComment,$this->szType);
	}
	/**
	 * Обновляет значение листа
	 */ 
	public function update()
	{
		if (empty($this->nId))
		{
			throw new SystemRegisterException('Update failed! Attribute $id  empty');
		}

		SystemRegisterSample::update($this->parent,$this->nId,$this->szName,$this->szValue,$this->szComment,$this->szType);
	}

}

class SystemRegisterException extends Exception
{
}
?>