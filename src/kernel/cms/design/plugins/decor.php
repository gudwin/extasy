<?php
use \Extasy\CMS;
/**
 * Класс для отображения элементов оформления
 * @author Gisma
 *
 */
class CMSDesignDecor {
	/**
	 * Отображает блок пейджинга (он идет вне блока таблицы)
	 * @param $nCurrentPage int Номер текущей страницы
	 * @param $nTotalPage int Общее количество страниц  
	 * @version 1.0 
	 */
	public function paging($nCurrentPage,$nTotalPage) 
	{
		if (empty($nTotalPage) || ($nTotalPage == 1)) { return ;}
		$aLink = array();
		for ($i = 0; $i < $nTotalPage;$i++ ) 
		{
			if ($i == $nCurrentPage) 
			{
				$aLink[] = ' <strong> '.($i + 1).' </strong> ';
			}
			else
			{
			
				$url = explode('?',$_SERVER['REQUEST_URI']);	
				
				$urlParams = $_GET;
				$urlParams['page'] = $i;
				
				$url = $url[0].'?'.http_build_query($urlParams);
				$aLink[] = sprintf(' <a href="%s"> %d </a> ',$url,($i + 1));
			}
		}
		$this->contentBegin();
		print ' Доступные страницы: '.implode(' | ',$aLink);
		$this->contentEnd();
	}	
	/**
	 * Начало блока для вывода производного HTML-контента
	 */
	public function contentBegin() {
		?>
		<div class="ContentBlock OpList">
			<div class="contentText">
		<?
	}
	/**
	 * Конец блока для вывода производного HTML-контента
	 */
	public function contentEnd() {
		?>
			</div>
		</div>
		<?
	}
	/**
	 * Отображает блок ссылок. Данные поступает в виде массива блоков ссылок. Ниже приведены варианты данных:
	 * array(
	 * 	'This is a button title' => 'http://example.com/button1',
	 * 	'Button2' => array(
	 * 		'code' => 'Wow! We can use HTML  '
	 * 	),
	 * 	'Button3' => array(
	 * 		'id'=> 'button_id', // HTML id="" attribute
	 * 		'value' => 'http://example.com/button3' 
	 * 	),
	 * );
	 * @param array $linkList
	 */
	public function buttons($linkList) {
?>
		<div class="ContentBlock Actions">
			<table style="width:100%">
				<tr>
					<td valign="top">
						<ul>
<?
		// Перебор ссылок
		foreach ($linkList as $key=>$value) {
			
			if (is_array($value)) {
				if (!empty($value['code'])) {
					printf($value['code']);
				} else {
					
					printf('<li><img alt="" src="%sextasy/pic/icons/back.gif" /> <a title="%s" href="%s" id="%s">%s</a></li>'."\r\n",
						   CMS::getResourcesUrl(),
							htmlspecialchars($key),
							$value['value'],
							$value['id'],
							htmlspecialchars($key));
				}
			} else {
				printf('<li><img alt="" src="%sextasy/pic/icons/back.gif" /> <a title="%s" href="%s">%s</a></li>'."\r\n",
					   CMS::getResourcesUrl(),
						htmlspecialchars($key),
						$value,
						htmlspecialchars($key));
			}
		}

		?>
						</ul>
					</td>
				</tr>
			</table>
		</div>
<?
	}

	/*
	 * Отображает перевод строки в дизайне админки 
	 */
	public function br() {
		?><div class="ContentSpacer"></div><?
	}
	public function getHintCode( $helpHeader, $helpContent = '') {
		$result = '';
		if (empty($helpContent)) {
			$helpContent = $helpHeader;
			$helpHeader = '';
		}
		$result = ' <a class="help_link"><img src="'.CMS::getResourcesUrl().'extasy/img/help_ico.gif" /></a>';
		$result .= '<div class="hideToolTip">';
		if (!empty($helpHeader)) {
			$result .= '<h3>'.htmlspecialchars($helpHeader).'</h3>';
		}
		$result .= $helpContent;
		$result .= '</div>';
		return $result;
	}
}