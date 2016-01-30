<?php
/**
 * Класс для отображения вкладок. 
 * Для работы этого класса также нужна js-библиотека: /resources/extasy/js/cms/main.js
 * Порядок вызова методов:
 * $tabs = new CMSDesignTabs();
 * $tabs->sheetsBegin($sheetsConfig);
 * 	$tabs->contentBegin($tabId);
 * 		...
 * 	$tabs->contentEnd();
 * 
 *  $tabs->contentBegin($tabId2);
 * 		...
 * 	$tabs->contentEnd();
 *  ... 
 * $tabs->sheetsEnd();
 * @author Gisma
 * 
 */
class CMSDesignTabs {
	protected $tabCurrentId;
	/**
	 * Стартует вывод блока вкладок. На вход поступает массив, следующего формата:
	 * aData = array(
	 * 	array(
	 * 		'id' => 'tab_id', // Индекс вкладки
	 * 		'title' => 'Sample title' // Название вкладки, оно отображается пользователю 
	 * 	),
	 * 	array(
	 * 		'id' => 'tab_other',
	 * 		'titile' => 'Sample title2',
	 * 	),
	 * );
	 * @param array $aData конфигурация вкладок
	 */
	public function sheetsBegin($aData) {
		$this->tabCurrentId = $aData[0]['id'];
		?>
		<table style="width:100%" class="tabCnt" cellpadding="0" cellspacing="0">
			<tr>
				<td id="tabCntTitle">
					<?for($i=0; $i<count($aData); $i++):?>
						<?if ($i == 0):?>
							<div class="tabCurrent tab_title" onclick="cms.tabSetCurrent(this,'<?=$aData[$i]['id']?>');return false;" id="title_<?=$aData[$i]['id']?>"><a href="#"><?=$aData[$i]['title']?></a></div>
						<?else:?>
							<div class="tabAfterCurrent tab_title" onclick="cms.tabSetCurrent(this,'<?=$aData[$i]['id']?>');" id="title_<?=$aData[$i]['id']?>"><a href="#" onclick="return false;"><?=$aData[$i]['title']?></a></div>
						<?endif;?>
					<?endfor?>
			  	</td>
			</tr>
			<tr>
				<td  id="tabCntContent">
		<?

	}
	/**
	 * Завершает вывод вкладок редактирования
	 */
	public function sheetsEnd() {
		?>
					</td>
			</tr>
		</table>
		<script type="text/javascript">
			cms.tabSetCurrent(null,'<?=$this->tabCurrentId?>');
		</script>
		<?
	}
	/**
	 * Старт вывода контент вкладки с указанным $tabId
	 * @param string $tabId Индекс вкладки, контент, которой выводится
	 */
	public function contentBegin($tabId) {
		?>
			<div class="panel tab_content" id="<?=$tabId?>">
		<?
	}
	/**
	 * Заканчивает вывод вкладки
	 */
	public function contentEnd() {
		?>
			</div>
		<?
	}
	
}