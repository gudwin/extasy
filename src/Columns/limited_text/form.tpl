<?
$name = $this->szFieldName;
$nLength = intval($this->fieldInfo['max_size']);
$value = htmlspecialchars($this->aValue);
// Все коды $ слешируются :)
$szCode = <<<EOD
Всего символов осталось <span id="{$name}_numchar" style="background-color:lightgray">0</span>
<textarea id="{$name}" name="{$name}" >{$value}</textarea>
<script type="text/javascript">
limited_textarea = function (\$id,\$nLength) {
	var \$szCode = '';
	var \$obj = document.getElementById(\$id);
	var \$szId_numchar = \$id + '_numchar';
	// формируем код проверки
	\$szCode += 'if (this.value.length > ' + \$nLength + ') { ' + "\\r\\n";
	\$szCode += ' this.value = this.value.substring(0,' + \$nLength + ');' + "\\r\\n";
	\$szCode += '}' + "\\r\\n";
	\$szCode += 'document.getElementById("' + \$szId_numchar + '").innerHTML = ' + \$nLength + ' - this.value.length' + "\\r\\n" ;
	\$szCode += 'return true;';

	// подвешиваем обработчик
	var \$nValue = \$nLength - \$obj.value.length;
	// сравниваем значение с нулем
	if (\$nValue < 0) {
		\$obj.value = \$obj.value.substring(0,$nLength);
	}
	document.getElementById(\$szId_numchar).innerHTML = \$nValue;
	\$obj.onkeyup = new Function(\$szCode);
}
limited_textarea('{$name}',{$nLength});
</script>

EOD;
return $szCode;
?>