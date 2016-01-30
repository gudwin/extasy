<?php
/**
 * Класс для отображения форм. Для работы нужна библиотека /resources/extasy/js/controller.js
 * @author Gisma
 *
 */
class CMSDesignForms {
	/**
	 * Стартует код формы 
	 * @param string $action
	 * @param string $method
	 * @param string $id
	 * @param string $enctype
	 */
	function begin($action = '',$method = 'post',$id ='user_form',$enctype='multipart/form-data') {
		?><form method="<?=$method?>" action="<?=$action?>" id="<?=$id?>" enctype="<?=$enctype?>">
		<div class="Form"><?;
	}
	/**
	 * Завершает код формы
	 */
	function end() {
		?></div></form>

<?	}
	/**
	 * Выводит скрытое поле внутри формы
	 * @param string $name имя поля
	 * @param string $value значение поля
	 */
	function hidden($name,$value) {
		$name = htmlspecialchars($name);
		$value = htmlspecialchars($value);
		?><input type="hidden" name="<?=$name?>" value="<?=$value?>"><?
	}
	protected function setSubmitOnKey($id) {?>
		<script type="text/javascript">
		var $submit_code = '';
		$submit_code += "var $element = event.srcElement?event.srcElement:event.target;";
		$submit_code += "var $bAllOk = ($element.nodeName != 'INPUT' && $element.nodeName != 'SELECT' && $element.nodeName != 'TEXTAREA');";
		$submit_code += 'if ($bAllOk) {$("<?=$id?>").click();return false;}';
		var $objKey =  {
			keyCode:115,
			shiftKey:0,
			altKey:0,
			ctrlKey:0
		};
		controller.onKeyPress(document,$objKey,$submit_code);
		var $objKey =  {
			keyCode:115,
			shiftKey:0,
			altKey:0,
			ctrlKey:1
		};
		$submit_code = '$("<?=$id?>").click();return false;';
		controller.onKeyPress(document,$objKey,$submit_code);
		</script>

	<?}
	/**
	 * Отображает набор кнопок. Пример наполнения:
	 * array(
	 * 	array('name' => 'submit','title' =>'Submit!'),
	 * 	array('name' => 'submit2','title' =>'Submit it with alert!','alert("Wow!");'),
	 * )
	 * @param array $aData
	 */
	public function moreSubmits($aData = array()) {
?>

				<div class="ContentSpacer"><!-- --></div>

				<div class="ContentBlock Buttons">
<?
			foreach ($aData as $value) {
			if (empty($value['code'])) {
			?>
				<input type=submit  class="SaveButton" name="<?=$value['name']?>" id="<?=$value['name']?>" style="float: none;" value="<?=$value['title']?>" />
<?
			} else {
				?>
				<input type="submit"  class="SaveButton" name="<?=$value['name']?>" id="<?=$value['name']?>" style="float: none;" value="<?=$value['title']?>" onclick="<?=$value['code'];?>"/>
				<?
			}
			if (!empty($value['save'])) {
				$this->setSubmitOnKey($value['name']);
			}
		}
				?>
				</div>
				<?
	}
	/**
	 * Отображает кнопку для отсылки формы
	 * @param string $name имя кнопки
	 * @param string $title надпись на кнопке
	 * @param string $alert если этот параметр передан, то перед отправкой форму нужно будет подтвердить. А этот параметр - текст для формы подтверждения
	 */
	public function submit($name = 'submit',$title = 'Submit',$alert = '') {
		if ($alert == '') {
			$szResult = <<<EOD

				<div class="ContentSpacer"></div>

				<div class="ContentBlock Buttons">
					<input type="submit"  class="SaveButton" id="{$name}" name="{$name}" style="float: none;" value="{$title}"/>
				</div>
EOD;

		} else {
			$szResult = <<<EOD
				<div class="ContentSpacer"></div>
				<div class="ContentBlock Buttons">
					<input type="submit" id="{$name}" class="SaveButton" name="{$name}" value="{$title}"
					style="float: none"  onclick="return confirm('{$alert}')" />
				</div>
EOD;
		}

		print $szResult;
		$this->setSubmitOnKey($name);
	}
	
	
}