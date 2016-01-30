<?php
use \Faid\DB;
/**
 * Базовый контрол конфига
 * @author Gisma
 * 18.04.2011 Добавил аттрибут объекта schema, хранит схему, которой принадлежит контрол
 *
 */
abstract class CConfigBaseControl {
	protected $id = 0;
	protected $name = '';
	protected $title = '';
	protected $config = array();
	protected $value = '';
	/**
	 * Схема, к которой принадлежит данная колонка
	 * @var CConfigSchema
	 */
	protected $schema = null;
	/**
	 * 
	 * @param int $id индекс
	 * @param string $name системное имя контрола
	 * @param string $title тайтл в формах администрирования
	 * @param array $additionalConfig доп. данные для донастройки конфига
	 * @param mixed $value значение контрола с которым он инициализируется
	 * @param CConfigSchema $schema хранит объект текущей схемы
	 */
	public function __construct($id,$name,$title,$additionalConfig,$value,$schema) {
		$this->id = IntegerHelper::toNatural($id);
		$this->name = $name;
		$this->title = $title;
		$this->schema = $schema;
		$this->config = $additionalConfig;
		$this->value = $value;
	}
	abstract public function getXType();
	public function getId() {
		return $this->id;
	}
	/**
	 * Возвращает системное имя контрола
	 */
	public function getName() {
		return $this->name;
	}
	/**
	 * Возвращает подпись к контролу
	 */
	public function getTitle() {
		return $this->title;
	}
	/**
	 * Возвращает значение из контрола
	 */
	public function getValue() {
		return $this->value;
	}
	/**
	 * Возвращает данные для отображения
	 */
	public function getViewValue() {
		return $this->getValue();
	}
	/**
	 * 
	 * Возвращает дополнительный конфиг контрола
	 */
	public function getConfig() {
		return $this->config;
	}
	/**
	 * Сохраняет значение в контроле
	 */
	public function setValue($data) {
		
		$this->value = $data;
		$sql = 'update `%s` set `value`="%s" where `id` = "%d"';
		$sql = sprintf($sql,
						CCONFIG_CONTROL_TABLE,
						\Faid\DB::escape($data),
						$this->id);
		DB::post($sql);
	}
	public function delete() {
		$sql = 'delete from `%s` where `id`="%d"';
		$sql = sprintf($sql,CCONFIG_CONTROL_TABLE,$this->id);
		DB::post($sql);
	}
	/**
	 * Возвращает код для отображения в админке
	 */
	public function outputInForm() {
		
	}
	/**
	 * Данный метод вызывается после того, как контрол был вставлен в бд
	 * @param $config array
	 * @param $name string
	 * @param $value mixed
	 * @param $title string
	 */
	public static function afterCreate($config,$name,$value,$title) {
		
	}
	/**
	 * Возвращает код для администрирования контрола в админке
	 */
	public static function outputAdminForm() {
		
	}
	/**
	 * Возвращает наименование контрола в базе данных
	 */
	public static function getControlTitle() {
		return __CLASS__;
	}
}