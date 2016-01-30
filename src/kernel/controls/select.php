<?

class CSelect extends CControl {
	protected $aValues;
	protected $nCurrent;
	protected $szId;
	protected $szStyle;
	protected $class = '';
	protected $nSize = 1;
	protected $titleField = 'name';
	protected $valueField = 'id';
	protected $required = false;
	/**
	 * Установленный в true заставляет контрол генерировать группы из <optgroup>
	 * @var bool
	 */
	protected $bUseGroups = false;

	public function __set( $name, $value ) {
		if ( ( $name == 'values' ) || ( $name == 'items' ) ) {
			if ( is_array( $value ) ) {
				$this->aValues = $value;
			} else {
				trigger_error( 'Property value must be an array', E_WARNING );
			}
		}
		if ( $name == 'name' ) {
			$this->szName = htmlspecialchars( $value );
		} else if ( $name == 'id' ) {
			$this->szId = htmlspecialchars( $value );
		} else if ( ( $name == 'current' ) || ( $name == 'selected' ) ) {
			$this->nCurrent = $value;
		} else if ( $name == 'style' ) {
			$this->szStyle = $value;
		} else if ( $name == 'size' ) {
			$this->nSize = $value;
		} else if ( $name == 'use_groups' ) {
			$this->bUseGroups = (bool)$value;
		} else if ( $name == 'titleField' ) {
			$this->titleField = $value;
		} else if ( $name == 'valueField' ) {
			$this->valueField = $value;
		} else if ( $name == 'class' ) {
			$this->class = $value;
		} else if ( $name == 'required' ) {
			$this->required = $value;
		}


	}

	public function __get( $name ) {
		return null;
	}

	public function generate() {


		$szResult = '<select class="%s" size="%s" name="%s" id="%s" style="%s">';
		$szResult = sprintf( $szResult,
							 $this->class,
							 $this->nSize,
							 $this->szName,
							 !empty( $this->szId ) ? $this->szId : $this->szName,
							 $this->szStyle
		);


		$szTemplateOptGroup = '<optgroup label="%s">%s</optgroup>';
		if ( !is_array( $this->aValues ) ) {
			throw new Exception( 'Select control values list must be an arrray' );
		}
		if ( $this->bUseGroups ) {
			foreach ( $this->aValues as $row ) {
				// Если перед нами группа, то добавляем её
				if ( isset( $row[ 'options' ] ) ) {
					$szOptions = '';
					foreach ( $row[ 'options' ] as $opt ) {

						$szOptions .= self::createOption( $opt[ $this->valueField ],
														  $opt[ $this->titleField ],
														  $this->nCurrent );
					}
					// Если хотя бы одна опция была вставлена в группу, то группа подается на вывод
					if ( !empty( $szOptions ) ) {
						$szResult .= sprintf( $szTemplateOptGroup,
											  htmlspecialchars( $row[ 'group-title' ] ),
											  $szOptions );
					}
				} else {
					// Просто вставляем код <otpion>
					$szResult .= self::createOption( $row[ $this->valueField ],
													 $row[ $this->titleField ],
													 $this->nCurrent );
				}
			}
		} else {
			foreach ( $this->aValues as $row ) {
				$szResult .= self::createOption( $row[ $this->valueField ],
												 $row[ $this->titleField ],
												 $this->nCurrent );
			}
		}
		$szResult .= '</select>';

		if ( !empty( $this->required ) ) {
			$selector = '#'. (!empty( $this->szId ) ? $this->szId : $this->szName );
			$errorMsg = $this->required;
			$szResult .= \Faid\UParser::parsePHPFile( __DIR__ . '/tpl/select.tpl', [
				'selector' => $selector,
				'errorMsg' => $errorMsg
			]);

		}

		return $szResult;
	}

	/**
	 * Создает html-код тега <option>
	 *
	 * @param int $optionId
	 *                        * @param string $optionTitle
	 * @param int $current_id индекс <option> у которого должен быть выставлен аттрибут selected
	 */
	protected static function createOption( $optionId, $optionTitle, $current_id ) {

		$szTemplateRow = '<option value="%s">%s</option>';
		$szTemplateRowActive = '<option selected="selected" value="%s">%s</option>';
        $szTemplate = ( strval($optionId) == $current_id ) ? $szTemplateRowActive : $szTemplateRow;
		return sprintf( $szTemplate, $optionId, htmlspecialchars( $optionTitle ) );
	}
}

?>