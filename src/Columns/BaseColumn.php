<?
namespace Extasy\Columns {
	use \Faid\DB;
	/**
	 * @desc    21.10.2005 Модуль содержит прототип класса типов
	 * @package Exstasy
	 * @author  Gisma
	 */
	class BaseColumn {

		var $aValue;
		var $szFieldName;
		var $fieldInfo;


		/**
		 *
		 * Указатель на текущий объект
		 * @var \Extasy\Model\Model
		 */
		protected  $document;

		/**
		 * @param $szFieldName
		 * @param $fieldInfo
		 * @param $Value data from a client
		 */
		public function __construct( $szFieldName, $fieldInfo, $Value ) {
			$this->szFieldName = $szFieldName;
			$this->aValue      = $Value;
			if ( is_array( $fieldInfo ) ) {
				$this->fieldInfo = $fieldInfo;
			}
			else {
				$this->fieldInfo = array( 'type' => $fieldInfo );
			}
		}
		public function setDocument( $document ) {
			$this->document = $document;
		}
		public function getDocument() {
			return $this->document;
		}
		public function getFieldName() {
			return $this->szFieldName;
		}

		public function __toString() {
			return (string)$this->getValue();
		}

		/**
		 * If you need extends standart document "select" query, than you need to add your code here
		 */
		public function onSelect( \Extasy\ORM\QueryBuilder $query ) {
			// do nothing
		}

		/**
		 * Called after db data was fetched, in that method column may initialize his value after document inserted
		 *
		 * @params array $dbData data from data source
		 */
		public function onAfterSelect( $dbData ) {

		}

		/**
		 * Вызывается в процессе вставки
		 */
		public function onInsert(\Extasy\ORM\QueryBuilder $query ) {
			$query->setSet( $this->szFieldName,$this->aValue );
		}

		/**
		 * Вызывается сразу после вставки
		 */
		public function onAfterInsert() {

		}

		public function onUpdate( \Extasy\ORM\QueryBuilder $query ) {
			$query->setSet( $this->szFieldName, ( $this->aValue ) );
		}

		/**
		 *
		 */
		public function onAfterUpdate() {

		}

		public function onDelete( \Extasy\ORM\QueryBuilder $query ) {

		}

		/**
		 *
		 */
		public function onAfterDelete() {

		}
		public function onCreateTable( \Extasy\ORM\QueryBuilder $queryBuilder ) {

		}

		public function check( $value, $fieldInfo = array() ) {
			return true;
		}

		public function getValue() {
			return $this->aValue;
		}

		/**
		 *
		 * Изменяет значение колоки
		 *
		 * @param mixed $newValue data from client
		 */
		public function setValue( $newValue ) {
			$this->aValue = $newValue;
		}

		/**
		 * Загружает данные для колонки из массива post
		 */
		public function loadDataFromPost() {
			$this->aValue = isset( $_POST[ $this->szFieldName ] ) ? $_POST[ $this->szFieldName ] : null;
		}

		public function getViewValue() {
		}

		/**
		 *
		 */
		public function getAdminTitle() {
			if ( isset( $this->fieldInfo[ 'title' ] ) ) {
				return $this->fieldInfo[ 'title' ];
			} else {
				return $this->szFieldName;
			}
		}

		public function getAdminViewValue() {
			return $this->getViewValue();
		}

		public function getFormValue() {
		}

		public function getAdminFormValue() {
			return $this->getFormValue();
		}

		/**
		 * Use that method for receiving ajax-calls during editing
		 */
		public function ajaxCall( $action, $params ) {

		}

		/**
		 *   -------------------------------------------------------------------------------------------
		 *   Метод создан для возможности изменения списка полей конкретного типа. Метод должен быть
		 *   переопределен в субклассах, т.к. содержит только лишь проверку, допустимости установки типа
		 * @return
		 *   -------------------------------------------------------------------------------------------
		 */
		public static function addColumn( $szType, $szFieldName, $aAdditional ) {
			throw new \DAO_Exception( 'define me!', $szFieldName );
		}

		/**
		 * @desc Метод создан, для установки настроек сразу над всем типом колонок.
		 *   Через этот метод желательно регулировать настройку работы колонок
		 * @return
		 */
		public static function onSetting( $type, $fieldname, $key, $value ) {
		}

	}
}
?>