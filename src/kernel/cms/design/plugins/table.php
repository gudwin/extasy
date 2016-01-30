<?php
use \Extasy\CMS;
/**
 * Класс для отображения таблиц. Пример использования:<br/>
 * <pre>
 * $table = new CMSDesignTable();
 * $table->begin('tableId');
 * $table->header(array(array('I',50),array('I * 2 ',50)));
 * for ($i = 0; $i < 5; $i++) {
 * 		$table->rowBegin();
 * 		$table->listCell($i);
 * 		$table->listCell($i * 2);
 * 		$table->rowEnd();
 * }
 * $table->hr();
 * $table->row2cell('6',12);
 * $table->end();
 * </pre>
 * @author Gisma
 *
 */
class CMSDesignTable {
	/**
	 * Если передан параметр $id то блоку с таблицей будет присвоен идентификатор
	 */
	public function begin($id = '') {
		?>
		<div class="ContentBlock OpList" <?php if (!empty($id)):?>id="<?=$id?>"<?php endif;?>>
		<table style="width:100%" class="OpTable" >
		<?
	}
	/**
	 * Конец таблицы
	 */
	public function end() {
		?></table>
		</div>
		<?
	}
	/**
	 * Заголовок таблицы. 
	 * Пример аргумента <strong>$aHeader</strong>:
	 * 	array(
	 * 		array('Title1','25'),
	 * 		array('Title2','5'),
	 * 		...
	 * 		array('TitleN','5'),
	 *  )
	 * @param array $aHeader
	 */
	public function header($aHeader) {
		// Если не массив
		if (!is_array($aHeader)) {
			throw new Exception('CMS :: TableHeader должен быть передан массив заголовка таблицы. Значение - имена колонок');
		}
		$szResult = '<thead><tr>'."\r\n";
		foreach ($aHeader as $key=>$value) {
			if (is_array($value)) {
				$szResult .= '<th style="width:'.(substr($value[1],-2) == 'px'?$value[1]:($value[1].'%')).'"><b>'.$value[0].'</b></th>'."\r\n";
			} else {
				$szResult .= '<th>'.$value.'</th>'."\r\n";
			}

		}
		$szResult .= '</tr></thead>'."\r\n";
		$szResult .= <<<EOD
							<tr>
								<td colspan="100" class="HeaderSplitter"><div><!-- --></div></td>
							</tr>

EOD;
		print $szResult;
	}
	/**
	 * Горизонтальная линия в таблице
	 */
	public function hr() {
?>
							<tr>
								<td colspan="100" class="HeaderSplitter"><div><!--  --></div></td>
							</tr>

<?
	}
	/**
	 * Начало ряда таблицы
	 * @param string $className Класс ряда
	 */
	function rowBegin($className = '') {
		?> <tr class="<?=$className?>">
		<?
	}
	/**
	 * Конец ряда
	 */
	function rowEnd() {
		?>
		</tr>
		<?
	}
	/**
	 * Старт вывода табличной ячейки
	 * @param int $width ширина ячейки в прочентах
	 */
	public function cellBegin($width = 10) {
		?>
		<td width="<?=$width?>%">
		<?
	}
	/**
	 * Конец вывода ячейки
	 */
	public function cellEnd() {
		?></td><?
	}
	/**
	 * Вывод ячейки таблицы
	 * @param string $value
	 * @param string $class
	 */
	public function listCell($value,$class='') {
		?>
		<td class="<?=$class?>"><?=$value?></td>
		<?
	}
	/**
	 * Выводит ячейку со ссылкой редактирования
	 * @param string $link
	 * @param bool $return Если установлен в true, то функция не выведет, но вернет код табличной ячейки 
	 */
	public function editCell($link = '#',$return = false) {
		return $this->buttonCell('edit',$link,$return);
	}
	/**
	 * 
	 * @param string $link
	 * @return string
	 */
	public function orderCell( $link, $return = false ) {
		$kernelPath = CMS::getResourcesUrl();
		$result = <<<HTML
		<a href="#{$link}" rel="{$link}" class="order_move_up"><img alt="" src="{$kernelPath}extasy/img/navigation/col-move-bottom.gif"/></a>
		<br/>
		<a href="#{$link}" rel="{$link}" class="order_move_bottom"><img alt="" src="{$kernelPath}extasy/img/navigation/col-move-top.gif"/></a>
HTML;
		if ( $return ) {
			return $result;
		} else {
			$this->listCell( $result );
		}
	}
	/**
	 * Выводит ячейку со ссылкой просмотра
	 * @param string $link
	 * @param bool $return Если установлен в true, то функция не выведет, но вернет код табличной ячейки 
	 */
	public function viewCell($link = '#',$return = false) {
		return $this->buttonCell('view',$link,$return);
	}
	
	/**
	 * Выводит ячейку со ссылкой удаления
	 * @param string $link
	 * @param bool $return Если установлен в true, то функция не выведет, но вернет код табличной ячейки 
	 */
	public function deleteCell($link = '#',$return = false) {
		return $this->buttonCell('delete',$link,$return);
	}	
	protected function buttonCell($type,$link,$return = false) {
		$strings = CMS_Strings::getInstance();
		$deleteConfirm = '';
		switch ($type) {
			case 'delete':
				$deleteConfirm = sprintf( "onclick='return confirm(%s)' " ,
					json_encode($strings->getMessage('CMS_CONFIRM_DELETE'))
				);
				$image = 'delete.gif';
				$message = $strings->getMessage('CMS_DELETE');
				break;
			case 'edit':
				$image = 'edit.gif';
				$message = $strings->getMessage('CMS_EDIT');
				break;
			case 'view':
				$image = 'edit.gif';
				$message = $strings->getMessage('CMS_VIEW');
				break;
		}
		$resultTpl = '<td><nobr><img alt="%s" src="%sextasy/pic/icons/%s" /><a href="%s" %s>%s</a></nobr></td>'."\r\n";
		$result = sprintf($resultTpl,
			$message,
			CMS::getResourcesUrl(),
			$image,
			$link,
			$deleteConfirm,
			$message
		);
		
		if ($return) {
			return $result;
		} else {
			print $result;
		}
	}
	/**
	 * Метод генерирующий ряд таблицы, состоящий из 2 ячеек: подписи и значения. Дополнительно, может быть сгенерерирована ссылка помощи
	 * @param string $name
	 * @param string $value
	 * @param string $helpHeader
	 * @param string $helpContent
	 */
	function row2cell($name,$value = NULL,$helpHeader = '',$helpContent = '') {
		// Генерируем код для всплывающего окна помощи
		if (!empty($helpHeader) || !empty($helpContent)) {
			$name .= CMSDesign::getInstance()->decor->getHintCode( $helpHeader, $helpContent );
		}
		// Выводим ячейку
		print <<<EOD
	<tr>
		<td class="FieldName" style="width:20%"> {$name} </td>
		<td class="Field" style="width:80%"> {$value} </td>
	</tr>
EOD;

	}
	/*
	 * Отображает ряд из одной ячейки
	 * @param string $value 
	 */
	public function fullRow($value = '') {
		
		print <<<EOD
				<tr>
					<td class="list" colspan="100"> {$value} </td>
				</tr>
EOD;
	}
}