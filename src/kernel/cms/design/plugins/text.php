<?php
/**
 * Класс для отображения текста
 * @author Gisma
 *
 */
class CMSDesignText {
	/**
	 * Отображает типовой заголовок текста
	 * @param string $value Текст заголовка
	 * @param string $id идентификатор заголовка
	 */
	public function header($value,$id='') {
		CMSDesign::getInstance('design')->messages->showAlerts();
		?>
							<div class="ContentBlock Actions">
								<table style="width:100%">
									<tr>
										<td valign="top">
											<ul>
												<li><h3 <?php if (!empty($id)):?> id="<?=$id?>"<?php endif?>><?=$value?></h3></li>
											</ul>
										</td>
									</tr>
								</table>
							</div>
<?
	}
	/**
	 * Отображает типовой уменьшенный заголовок, заголовок второго уровня
	 * @todo Выровнять всё до банального h2
	 * @param string $value Текст заголовка 
	 */
	function header2($value) {
		?>
							<h4><?=$value?></h4>
<?
	}
}