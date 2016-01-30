<?
namespace Extasy\Model;

use \EventController;
use \Extasy\ORM\DB;
use \Extasy\ORM\QueryBuilder;


class Model extends \Faid\Model {

	const ModelName = '\\Extasy\\Model\\Model';

	const IndexColumnClass = '\\Extasy\\Columns\\Index';
	/**
	 * @var string
	 */
	const PermissionName = 'Administrator/Sitemap';

	const StandalonePageParseVariable = 'parse_field';
	const PreviewParseVariable        = 'preview_field';
	/**
	 * Document labels
	 */
	const labelAddItem      = 'add_item';
	const labelAllItems     = 'all_items';
	const labelEditItem     = 'edit_item';
	const labelName         = 'name';
	const labelNotFound     = 'not_found';
	const labelSearchItems  = 'search_items';
	const labelSingularName = 'singular_name';
	/**
	 * Document errors
	 */
	const errorTplFailedInitializeFields = '%s: Failed to initialize document fields ';
	const errorTplTypeNameNotFound       = 'Document `%s` has empty typeName constant';

	public function __construct( $initialData = array() ) {
		if ( is_array( $initialData ) ) {
			$this->setData( $initialData );
		}
		parent::__construct( $this->columns );
	}


	/**
	 * Adds support for retreiving columns
	 * @see Document::__get()
	 */
	public function __get( $key ) {

		if ( preg_match( '#^obj\_#', $key ) ) {
			$key = substr( $key, 4 );
		}
		return $this->attr( $key );

	}

	public function __set( $key, $value ) {
		$column = $this->attr( $key );
		$column->setValue( $value );
	}

	public function getIndexKey() {
		return $this->index;
	}

	/**
	 *
	 * Enter description here ...
	 *
	 * @param unknown_type $index
	 */
	public function get( $index ) {
		$this->columns[ $this->index ]->setValue( $index );
		// Загружаем данные из бд

		$queryBuilder = new QueryBuilder( 'select' );
		$queryBuilder->setFrom( static::getTableName() );
		$this->columns[ $this->index ]->onSelect( $queryBuilder );
		$sql    = $queryBuilder->prepare();
		$dbData = DB::get( $sql );
		// If return data empty, than exit
		if ( empty( $dbData ) ) {
			return false;
		}
		// Вызываем у всех колонок onAfterSelect, чтобы они могли восстановить свои значения 
		foreach ( $this->columns as $key => $value ) {
			$value->onAfterSelect( $dbData );

		}

		return true;
	}

	public function reload() {
		return $this->get( $this->id->getValue() );
	}

	public function insert() {
		$queryBuilder = new QueryBuilder( 'insert' );
		$queryBuilder->setInsertInto( static::getTableName() );
		foreach ( $this->columns as $key => $column ) {
			//  Подключаем обычные переданные поля
			$column->onInsert( $queryBuilder );
		}
		$sql = $queryBuilder->prepare();
		DB::post( $sql );
		$this->columns[ $this->index ]->setValue( DB::getInsertId() );
		foreach ( $this->columns as $key => $column ) {
			$column->onAfterInsert();
		}
		EventController::callEvent( 'document_create', $this );

		return true;
	}

	public function update() {

		$queryBuilder = new QueryBuilder( 'update' );
		$queryBuilder->setUpdate( static::getTableName() );
		foreach ( $this->columns as $column ) {
			$column->onUpdate( $queryBuilder );
		}
		$sql = $queryBuilder->prepare();
		DB::post( $sql );
		foreach ( $this->columns as $key => $column ) {
			$column->onAfterUpdate();
		}

		EventController::callEvent( 'document_update', $this );

		return true;
	}

	public function delete() {
		$queryBuilder = new QueryBuilder( 'delete' );
		$queryBuilder->setFrom( static::getTableName() );
		foreach ( $this->columns as $column ) {
			$column->onDelete( $queryBuilder );
		}
		$sql = $queryBuilder->prepare();
		DB::post( $sql );

		EventController::callEvent( 'document_delete', $this );
		// unset all columns 
		foreach ( $this->columns as $key => $row ) {
			unset( $this->columns[ $key ] );
		}
		$this->columns = [ ];
	}

	public function getViewValue() {
		$result = array();
		foreach ( $this->columns as $key => $column ) {
			$result[ $key ] = $column->getViewValue();
		}
		return $result;
	}

	/**
	 * Выводит форму вставки документа
	 */
	public function getAdminInsertForm() {
	}

	/**
	 * Выводит форму редактирования документа
	 */
	public function getAdminUpdateForm() {
	}

	/**
	 * @desc Возвращает внутренние данные документ
	 * @return
	 */
	public function getData() {
		//
		$result = parent::getData();
		foreach ( $result as $key => $row ) {
			$result[ $key ] = $row->getValue();
		}
		return $result;
	}

	/**
	 * (non-PHPdoc)
	 * @see Document::getId()
	 */
	public function getId() {
		return $this->columns[ $this->getIndex() ]->getValue();
	}

	/**
	 * Returns model table name
	 * @return string
	 */
	public static function getTableName() {
		$fieldInfo = static::getFieldsInfo();
		return $fieldInfo[ 'table' ];
	}

	/**
	 * Returns label. Every label displayed ( if it exists) in specific situation, listing below describes them
	 * Default labels list:
	 * array(
	 *        'name' => 'Type general name',
	 *        'singular_name' => 'post type singular name',
	 *        'add_item' => 'Add new item',
	 *        'edit_item' => 'Edit item',
	 *        'all_items' => 'All items',
	 *        'search_items' => 'Search items',
	 *        'not_found' => 'Item not found',
	 * ),
	 *
	 * @param string $labelName
	 * @param bool   $useDefault
	 */
	public static function getLabel( $labelName, $useDefault = true ) {
		$typeInfo = static::getFieldsInfo();
		if ( isset( $typeInfo[ 'labels' ] ) ) {
			if ( isset( $typeInfo[ 'labels' ][ $labelName ] ) ) {
				return $typeInfo[ 'labels' ][ $labelName ];
			}
		}
		if ( !empty( $useDefault ) ) {
			if ( isset( $typeInfo[ 'title' ] ) ) {
				return $typeInfo[ 'title' ];
			} else {
				return static::ModelName;
			}
		} else {
			return '';
		}
	}

	/**
	 * Для статического возврата информации по колонкам
	 * @return NULL
	 */
	public static function getFieldsInfo() {
		return null;
	}

	/**
	 * @param $newData
	 *
	 * @return bool
	 */
	public function updateFromPost( $newData ) {
		foreach ( $this->columns as $key => $row ) {
			if ( isset( $newData[ $key ] ) ) {
				$this->columns[ $key ]->setValue( $newData[ $key ] );
			}
		}
		return $this->update();
	}

	/**
	 * @desc Устанавливает значение внутренних данных
	 * @return
	 */
	public function setData( array  $newData ) {

		if ( empty( $this->columns ) ) {
			$this->initializeColumns();
		}

		foreach ( $this->columns as $fieldName => $row ) {
			$isColumn = ( isset( $newData[ $fieldName ] ) && is_object( $newData[ $fieldName ] ) );
			if ( $isColumn ) {
				$this->columns[ $fieldName ] = $newData[ $fieldName ];
			} else {
				$this->columns[ $fieldName ]->setDocument( $this );
				$this->columns[ $fieldName ]->onAfterSelect( $newData );
			}
		}
	}

	protected function initializeColumns() {
		$getClassName = function ( $value ) {
			$value = ( is_array( $value ) && !empty( $value[ 'class' ] ) ) ? $value[ 'class' ] : $value;
			return $value;
		};
		$indexField   = null;
		$fieldsInfo   = static::GetFieldsInfo();
        if ( !is_array( $fieldsInfo['fields'])) {
            throw new \RuntimeException(sprintf('Fields must be array, current - `%s`. Model - `%s` ', print_r( $fieldsInfo, true ), static::ModelName));
        }
		foreach ( $fieldsInfo[ 'fields' ] as $key => $value ) {
			$value = $getClassName( $value );
			if ( $value == self::IndexColumnClass ) {
				$indexField = $key;
				break;
			}
		}
		if ( empty( $indexField ) ) {
			throw new \RuntimeException( 'No index field found' );
		}
		foreach ( $fieldsInfo[ 'fields' ] as $fieldName => $fieldsInfo ) {
			$className                   = $getClassName( $fieldsInfo );
            if ( !is_string( $className ) || !class_exists( $className )) {
                throw new \RuntimeException(sprintf('Column class `%s` for field `%s` not found. Model name - `%s`',
                                            print_r( $className, true ),
                                            $fieldName,
                                            static::ModelName
                                            ));
            }
			$this->columns[ $fieldName ] = new $className( $fieldName, $fieldsInfo, null );
		}
	}

	/**
	 *   Возвращает данные для парсинга документа, данные берутся на основе данных в типе
	 *   Метод работы - обход всех полей в типе, определение есть ли необходимость в подаче данных
	 *   на парсинг (на основе св-ва parsed_field), определение методов парсинга, которые будут
	 *   применены к данному полю (так же на основе значение св-ва parsed_field). Данные возвращаюся
	 *   в виде хеш-массива. Также определеяет стандартные seo-поля (seo_title, seo_keywords,
	 *   seo_description) и группирует их в массив aMeta.
	 *   <h2>ВАЖНО!</h2>
	 *   <pre>Для того, чтобы поле попало в результат парсинга, оно должно иметь в описнии
	 *   типа установленное свойство parsed_field. Возможны слудующие варианты значение:
	 *   - true, который указывает просто вернуть значение через метод getViewValueж
	 *   - хеш-массив, где ключи это имя переменной, а значение это метод. </pre>
	 * @return array массив для парсинга. Ключ - имя поля, значение - данные полученные от методов
	 *   парсинга

	 */
	public function getParseData() {
		$aResult = self::getParseDataByKey( self::StandalonePageParseVariable );

		if ( isset( $aResult[ 'seo_title' ] )
			&& isset( $aResult[ 'seo_description' ] )
			&& isset( $aResult[ 'seo_keywords' ] )
		) {
			//
			$aResult[ 'aMeta' ] = array(
				'title'       => $aResult[ 'seo_title' ],
				'description' => $aResult[ 'seo_description' ],
				'keywords'    => $aResult[ 'seo_keywords' ],
			);
			if ( !empty( $aResult[ 'seo_news_keywords' ] ) ) {
				$aResult[ 'aMeta' ][ 'news_keywords' ] = $aResult[ 'seo_news_keywords' ];
			}
		}
		return $aResult;

	}

	/**
	 *   Возвращает данные для отображения документа в списке
	 * @return
	 */
	public function getPreviewParseData() {
		$aResult = self::getParseDataByKey( self::PreviewParseVariable );
		return $aResult;
	}

	/**
	 *
	 */
	public function getParseDataByKey( $szFieldKey ) {
		$column   = null;
		$safeCall = function ( $methodName ) use ( &$column ) {
			if ( is_callable( $methodName ) ) {
				$callback = $methodName;
			} else {
				$callback = array( $column, $methodName );
			}

			if ( !is_callable( $callback ) ) {
				throw new \InvalidArgumentException( sprintf( 'Column method not callable. Column - "%s", method - "%s"',
															  $column->getFieldName(),
															  $methodName ) );
			}
			return call_user_func( $callback, $column );
		};
		// Вызов методов по найденным полям
		$result     = [ ];
		$fieldsInfo = static::getFieldsInfo();
		foreach ( $fieldsInfo[ 'fields' ] as $fieldName => $fieldInfo ) {
			$skip = !is_array( $fieldsInfo ) || empty( $fieldInfo[ $szFieldKey ] );
			if ( $skip ) {
				continue;
			}
			$column = $this->columns[ $fieldName ];

			$methods = $fieldInfo[ $szFieldKey ];
			if ( is_scalar( $methods ) ) {
				$methodName   = is_string( $methods ) && !empty( $methods ) ? $methods : 'getViewValue';
				$columnResult = $safeCall( $methodName );
			} else if ( is_array( $methods ) ) {
				$columnResult = array();
				foreach ( $methods as $name => $methodName ) {
					$columnResult[ $name ] = $safeCall( $methodName );
				}
			} else if ( is_callable( $methods ) ) {
				$columnResult = call_user_func( $methods, $column );
			} else {
				throw new \RuntimeException( sprintf( 'Unable to process parse data for column `%s`, unknown methods in model fields declaration  %s',
													  $fieldName,
													  print_r( $methods, true ) ) );
			}
			$result[ $fieldName ] = $columnResult;
		}
		return $result;
	}


	public static function getById( $id ) {
		$class = static::ModelName;
		$model = new $class();
		$found = $model->get( $id );
		if ( empty( $found ) ) {
			throw new \NotFoundException( sprintf( 'Model `%s` with id="%d" not found ',static::ModelName,  $id ) );
		}

		return $model;
	}

	/**
	 *
	 */
	public static function getPermissionName() {
		return static::PermissionName;
	}

	public static function isModel( $modelName ) {
		return class_exists( $modelName ) && is_subclass_of( $modelName, __CLASS__ );
	}

	public function createDatabaseTable( $dropTable = false ) {

		$fields = static::getFieldsInfo();
		if ( $dropTable ) {
			$sql = sprintf( 'DROP TABLE IF EXISTS `%s`', $fields[ 'table' ] );
			DB::post( $sql );
		}

		$queryBuilder = new \Extasy\ORM\QueryBuilder( 'create' );
		$queryBuilder->setTableName( $fields[ 'table' ] );
		foreach ( $this->columns as $column ) {
			$column->onCreateTable( $queryBuilder );
		}

		$sql = $queryBuilder->prepare();
		DB::post( $sql );
	}

}

?>