<?
/**
 * @author  d@dd-team.org
 * @created 02.11.2006
 *         ChangeLog
 * - 27.07.2013 moved to extasy5, refactored
 * - 28.09.2008 Добавлены методы setInsertValues и exec
 */
namespace Extasy\ORM;

class QueryBuilder {

	protected $template = '';

	/**
	 * @var array
	 */
	protected $flagList = array();
	protected $fields = array();
	/**
	 * This fields used only in "create" templates
	 * @var string
	 */
	protected $tableName = '';
	protected $tableOptions = null;

	function __construct( $template = '') {
		$this->flagList = array(
			'select'    => false,
			'from'      => false,
			'distinct'  => false,
			'join'      => false,
			'where'     => false,
			'group'     => false,
			'order'     => false,
			'having'    => false,
			'limit'     => false,
			'procedure' => false,
			'insert'    => false,
			'into'      => false,
			'set'       => false,
			'update'    => false
		);
		//

		$this->loadTemplate( $template );
	}
	protected function loadTemplate( $template ) {
		if ( !in_array( $template, array( 'select', 'insert', 'update', 'delete','create' ) ) ) {
			$error = sprintf( 'Unknown template file `%s`', $template );
			throw new \Exception( $error );
		}
		//
		$path = sprintf( '%s/templates/%s.tpl', __DIR__, $template );
		//
		$this->template = file_get_contents( $path );

	}

	protected function flush() {
		$this->tableName = null;
		$this->fields    = null;
	}

	public function setTableName( $tableName ) {
		$this->tableName = $tableName;
	}

	public function setTableOptions( $options ) {
		$this->tableOptions = $options;
	}
	public function innerJoin( $condition ) {
		$this->parse( 'INNER_JOIN', sprintf( 'INNER JOIN %s ', $condition ));
	}
	/**
	 *
	 */
	protected function parse( $blockName, $data = '' ) {
		$this->template = str_replace( '{' . $blockName . '}', $data . '{' . $blockName . '}', $this->template );
	}

	/**
	 * Generates query string
	 * @return string
	 */
	public function prepare() {
		if ( !empty( $this->flagList[ 'select' ] ) ) {
			$this->template = str_replace( '{SELECT}', '', $this->template );
		} else {
			$this->template = str_replace( '{SELECT}', '*', $this->template );
		}
		$this->template = str_replace( '{FROM}', '', $this->template );
		$this->template = str_replace( '{INSERT}', '', $this->template );
		$this->template = str_replace( '{DISTINCT_ALL}', '', $this->template );
		$this->template = str_replace( '{JOIN}', '', $this->template );
		$this->template = str_replace( '{SQL_CALC_FOUND_ROWS}', '', $this->template );

		if ( !$this->flagList[ 'where' ] ) {
			$this->template = str_replace( 'WHERE {WHERE}', '', $this->template );
		} else {
			$this->template = str_replace( '{WHERE}', '', $this->template );
		}
		if ( !$this->flagList[ 'group' ] ) {
			$this->template = str_replace( 'GROUP BY {GROUP}', '', $this->template );
		} else {
			$this->template = str_replace( '{GROUP}', '', $this->template );
		}
		if ( !$this->flagList[ 'order' ] ) {
			$this->template = str_replace( 'ORDER BY {ORDER}', '', $this->template );
		} else {
			$this->template = str_replace( '{ORDER}', '', $this->template );
		}
		if ( !$this->flagList[ 'having' ] ) {
			$this->template = str_replace( 'HAVING {HAVING}', '', $this->template );
		} else {
			$this->template = str_replace( '{HAVING}', '', $this->template );
		}
		if ( !$this->flagList[ 'limit' ] ) {
			$this->template = str_replace( 'LIMIT {LIMIT}', '', $this->template );
		} else {
			$this->template = str_replace( '{LIMIT}', '', $this->template );
		}

		if ( !$this->flagList[ 'procedure' ] ) {
			$this->template = str_replace( 'PROCEDURE {PROCEDURE}', '', $this->template );
		} else {
			$this->template = str_replace( '{PROCEDURE}', '', $this->template );
		}
		if ( !$this->flagList[ 'into' ] ) {
			$this->template = str_replace( 'INTO OUTFILE {INTO}', '', $this->template );
		} else {
			$this->template = str_replace( '{INTO}', '', $this->template );
		}
		if ( $this->flagList[ 'insert' ] ) {
			$this->template = str_replace( '{INSERT}', '', $this->template );
		}
		if ( !$this->flagList[ 'update' ] ) {
			$this->template = str_replace( 'UPDATE {UPDATE}', '', $this->template );
		} else {
			$this->template = str_replace( '{UPDATE}', '', $this->template );
		}
		if ( !$this->flagList[ 'set' ] ) {
			// Заплата для пустых update-запросов
			$this->template = str_replace( 'SET {SET}', 'SET `id` = `id` ', $this->template );
		} else {
			$this->template = str_replace( '{SET}', '', $this->template );
		}
		$this->template = str_replace( '{INNER_JOIN}', '', $this->template );

		$this->parseKey( 'tableName', $this->tableName );
		$this->parseKey( 'tableOptions', $this->tableOptions );
		$this->parseKey( 'fields', implode( ",\r\n", $this->fields ) );

		$this->flush();
		return $this->template;
	}

	protected function parseKey( $key, $paste ) {
		$this->template = str_replace( '{' . $key . '}', $paste, $this->template );
	}

	/**
	 * @param Второй параметр заставляет игнорирвать обратные кавычки в которые заключается параметр.Таким образом можно запихнуть, что хошь )
	 */
	public function setSelect( $select, $ignore = false ) {
		if ( !$ignore ) {
			$select = '`' . $select . '`';
		}
		if ( $this->flagList[ 'select' ] == true ) {
			$select = ',' . $select;
		} else {
			$this->flagList[ 'select' ] = true;
		}
		$this->parse( 'SELECT', $select );

		//
		return $this;
	}

	/**
	 * @param $order
	 *
	 * @return $this
	 */
	public function setOrderFunction( $order ) {
		if ( $this->flagList[ 'order' ] == true ) {
			$order = ',' . $order;
		} else {
			$this->flagList[ 'order' ] = true;
		}
		$this->parse( 'ORDER', $order );

		return $this;
	}

	public function setOrder( $order, $asc = true ) {
		$order = trim( $order );
		// Обработка ситуации, когда поле сразу пойдет в кавычках (Киляем их)
		if ( $order[ 0 ] == '`' ) {
			$order = substr( $order, 1, -1 );
		}

		if ( $this->flagList[ 'order' ] == true ) {
			$order = ',`' . $order . '`';
		} else {
			$this->flagList[ 'order' ] = true;
			$order                     = '`' . $order . '`';
		}
		$order .= $asc ? ' ASC ' : ' DESC ';
		$this->parse( 'ORDER', $order );

		return $this;
	}

	public function setLimit( $limit ) {
		if ( $this->flagList[ 'limit' ] == true ) {
			$limit = ',' . intval( $limit ) . '';
		} else {
			$this->flagList[ 'limit' ] = true;
			$limit                     = intval( $limit );
		}
		$this->parse( 'LIMIT', $limit );

		return $this;
	}

	public function setFrom( $from = "" ) {
		if ( $this->flagList[ 'from' ] == true ) {
			$from = ',`' . $from . '`';
		} else {
			$this->flagList[ 'from' ] = true;
			$from                     = '`' . $from . '`';
		}

		$this->parse( 'FROM', $from );

		return $this;
	}
	public function setFromCondition( $from ) {
		if ( empty( $this->flagList[ 'from' ]) ) {
			$this->flagList[ 'from' ] = true;
		} else {
			$from = ', ' . $from ;
		}
		$this->parse( 'FROM', $from );
	}

	public function addFields( $fields ) {
		$this->fields[ ] = $fields;
	}

	public function enablePaging() {
		$this->parse( 'SQL_CALC_FOUND_ROWS', 'SQL_CALC_FOUND_ROWS' );
	}

	public function setWhere( $fieldName = '', $where = '' ) {
		if ( $this->flagList[ 'where' ] == true ) {
			$where = ' and `' . $fieldName . '`="' . DB::escape( $where ) . '"';
		} else {
			$this->flagList[ 'where' ] = true;
			$where                     = '`' . $fieldName . '`="' . DB::escape( $where ) . '"';
		}
		$this->parse( 'WHERE', $where );

		return $this;
	}

	/**
	 * @param $cond
	 *
	 * @return $this
	 */
	public function setWhereCond( $cond ) {
		if ( $this->flagList[ 'where' ] == true ) {
			$where = ' and ' . $cond . ' ';
		} else {
			$this->flagList[ 'where' ] = true;
			$where                     = $cond;
		}
		$this->parse( 'WHERE', $where );

		return $this;
	}

	/**
	 * @param string $into
	 *
	 * @return $this
	 */
	public function setInsertInto( $into = "" ) {
		$into = '`' . $into . '`';
		if ( $this->flagList[ 'insert' ] == true ) {
			$into = ',' . $into;
		} else {
			$this->flagList[ 'insert' ] = true;
		}
		$this->parse( 'INSERT', $into );

		return $this;
	}

	public function setUpdate( $update = '' ) {
		$update = '`' . $update . '`';
		if ( $this->flagList[ 'update' ] == true ) {
			$update = ',' . $update;
		} else {
			$this->flagList[ 'update' ] = true;
		}
		$this->parse( 'UPDATE', $update );

		return $this;
	}

	public function setSet( $name, $set = '', $isSQL = false ) {
		if ( !$isSQL ) {
			$name = DB::$connection->real_escape_string( $name );
			$set  = DB::$connection->real_escape_string( $set );
			$name = '`' . $name . '`';
			if ( $this->flagList[ 'set' ] == true ) {
				$name = ',' . $name;
			} else {
				$this->flagList[ 'set' ] = true;
			}
			$this->parse( 'SET', $name . "='" . ( $set ) . "'" );
		} else {
			if ( $this->flagList[ 'set' ] == true ) {
				$name = ',' . $name;
			} else {
				$this->flagList[ 'set' ] = true;
			}
			$this->parse( 'SET', $name );
		}

		return $this;
	}
}