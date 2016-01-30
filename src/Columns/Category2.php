<?
namespace Extasy\Columns {
	use Extasy\ORM\QueryBuilder;
	use \Faid\DBSimple, \Faid\DB;
	use \CSelect;


//************************************************************//
//                                                            //
//                 Элемент Category                           //
//       Copyright (c) 2006  ООО SmartDesign                  //
//               отдел/сектор                                 //
//       Email: support@smartdesign.by                        //
//                                                            //
//  Разработчик: Gisma                                        //
//  Модифицирован:   2006.02.20 by Gisma                      //
//                                                            //
//************************************************************//
	/**
	 * @name TCategory
	 * @author Gisma
	 * @desc <p>Тип категории применяется в деревьяв для индексирования по элементам дерева.
	 *        Этот тип хранится в базе данных как тип INT, т.к. фактически указывает на индекс элемента
	 *        к которому он являетя дочерним.</p>
	 *        <p><strong style="color:red">Важное ! это поле не может быть индексом.</strong></p>
	 *        <h3>Параметры:</h3>
	 *        <ul>
	 *        <li>        <b>cross_table</b> - таблица в которой будет организована выборка;
	 *        <li>        <b>name_field</b>        - поле по-которому будет организована выборрка
	 *        <li>        <b>index</b>                - поле индекс элементов в таблице
	 *        </ul>
	 */
	class Category2 extends BaseColumn {
		use \Extasy\ORM\Columns\IntegerColumn;

		/**
		 * @var Хранит строковое имя текущего значения
		 */
		var $szName;

		function __construct( $szFieldName, $fieldInfo, $Value ) {

			parent::__construct( $szFieldName, $fieldInfo, $Value );
			if ( empty( $this->fieldInfo[ 'model' ] ) ) {
				throw new \ForbiddenException( 'не опеределен поле model:' . $this->szFieldName );
			}
			if ( !class_exists( $this->fieldInfo[ 'model' ] ) ) {
				throw new \NotFoundException( 'Model not found  :' . $this->fieldInfo[ 'model' ] );
			}

			if ( empty( $Value ) ) {
				$this->aValue = 0;
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

		function onInsert(\Extasy\ORM\QueryBuilder $query ) {
			$query->setSet( $this->szFieldName,$this->aValue );
		}

		function onUpdate(\Extasy\ORM\QueryBuilder $query ) {
			if ( empty( $this->fieldInfo[ 'disabled' ] ) ) {
				$query->setSet( $this->szFieldName, $this->aValue );
			}
		}

		public function getValue() {

			return $this->aValue;
		}

		/**
		 * Возвращает подключенный ряд. Данный метод кеширует результаты своей работы
		 */
		public function getLinkedRow() {
			$model = new $this->fieldInfo[ 'model' ]();
			$found = $model->get( $this->aValue );
			if ( empty( $found ) ) {
				return null;
			}
			$result = $model->getData();
			return $result;

		}

		/**
		 * @desc
		 * @return
		 */
		function getViewValue() {
			return $this->getValue();
		}

		/**
		 * @desc Вывод на  админской форме
		 * @return
		 */
		function getAdminFormValue() {
			return $this->getFormValue();
		}

		function getAdminViewValue() {
			return $this->getViewValue();
		}

		/**
		 * @desc Вывод на обычной форме
		 * @return
		 */
		function getFormValue() {
			$builder    = new QueryBuilder( 'select' );
			$fieldsInfo = call_user_func( array( $this->fieldInfo[ 'model' ], 'getFieldsInfo' ) );
			$nameField  = isset( $this->fieldInfo[ 'cross_name' ] ) ? $this->fieldInfo[ 'cross_name' ] : 'name';
			$index      = 'id';
			$order      = isset( $fieldsInfo[ 'order' ] ) ? $fieldsInfo[ 'order' ] : 'id';

			$builder->setSelect( $index );
			$builder->setSelect( $nameField );
			$builder->setFrom( $fieldsInfo[ 'table' ] );
			$builder->setOrderFunction( $order );


			$data = \Extasy\ORM\DB::query( $builder->prepare() );
			array_unshift( $data,
						   [ 'id'   => 0,
							 'name' => 'Не выбрано'
						   ] );

			$select           = new CSelect();
			$select->name     = $this->szFieldName;
			$select->current  = $this->aValue;
			$select->required = !empty( $this->fieldInfo[ 'required' ] ) ? $this->fieldInfo[ 'required' ] : false;

			$select->values = $data;
			return $select->generate();
		}

		public function onCreateTable( \Extasy\ORM\QueryBuilder $queryBuilder ) {
			$queryBuilder->addFields( sprintf( '`%s` int not null default 0', $this->szFieldName ) );
		}
	}
}
?>