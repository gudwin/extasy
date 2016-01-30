
function Rights() {
	// Подключаем обработчик на текстовое поле ввода
	controller.addToId('rightsCalculator','onkeyup','ii.rights.calculatorChange(this)');
	// На чекбоксы
	controller.addToId('rightsOwnerRead','onchange','ii.rights.calculatorChange(this)');
	controller.addToId('rightsOwnerWrite','onchange','ii.rights.calculatorChange(this)');
	controller.addToId('rightsOwnerExecute','onchange','ii.rights.calculatorChange(this)');
	controller.addToId('rightsGroupRead','onchange','ii.rights.calculatorChange(this)');
	controller.addToId('rightsGroupWrite','onchange','ii.rights.calculatorChange(this)');
	controller.addToId('rightsGroupExecute','onchange','ii.rights.calculatorChange(this)');
	controller.addToId('rightsWorldRead','onchange','ii.rights.calculatorChange(this)');
	controller.addToId('rightsWorldWrite','onchange','ii.rights.calculatorChange(this)');
	controller.addToId('rightsWorldExecute','onchange','ii.rights.calculatorChange(this)');
	// кнопки

	controller.addToId('rightsButtonOK','onclick','ii.endDialogRights(1,document.getElementById(\'rightsCalculator\').value)');
	controller.addToId('rightsButtonCancel','onclick','ii.endDialogRights(0,document.getElementById(\'rightsCalculator\').value)');
	
	
	
}
/**
*   @desc Устаналвивает в диалоге права для текущего файла
*/
Rights.prototype.setDialogData = function (rights) {

//alert(document.getElementById('rightsOwnerRead'));
	document.getElementById('rightsOwnerRead').checked = rights.charAt(1) == 'r';
	document.getElementById('rightsOwnerWrite').checked = rights.charAt(2) == 'w';
	document.getElementById('rightsOwnerExecute').checked = rights.charAt(3) == 'x';
	document.getElementById('rightsGroupRead').checked = rights.charAt(4) == 'r';
	document.getElementById('rightsGroupWrite').checked = rights.charAt(5) == 'w';
	document.getElementById('rightsGroupExecute').checked = rights.charAt(6) == 'x';
	document.getElementById('rightsWorldRead').checked = rights.charAt(7) == 'r';
	document.getElementById('rightsWorldWrite').checked = rights.charAt(8) == 'w';
	document.getElementById('rightsWorldExecute').checked = rights.charAt(9) == 'x';
	this.calculatorChange(document.getElementById('rightsOwnerRead'));
	controller.addToId('popupWindowCloser','onclick',';ii.endDialogRights(0,document.getElementById("rightsCalculator").value);return false;');
	
}
Rights.prototype.calculatorInputChange = function($szGroup,$szValue) {
	var nPermission = Math.round($szValue);
	var $szRead = 'rights' + $szGroup + 'Read';
	var $szWrite = 'rights' + $szGroup + 'Write';
	var $szExecute = 'rights' + $szGroup + 'Execute';
	var nResult = 0;
	
	nResult += (document.getElementById($szRead).checked)?4:0;
	
	nResult += (document.getElementById($szWrite).checked)?2:0;
	nResult += (document.getElementById($szExecute).checked)?1:0;
	return nResult.toString();
}
Rights.prototype.calculatorColumnChange = function($szGroup,$szValue) {
	var nPermission = Math.round($szValue);
	var $szRead = 'rights' + $szGroup + 'Read';
	var $szWrite = 'rights' + $szGroup + 'Write';
	var $szExecute = 'rights' + $szGroup + 'Execute';
	
	if (nPermission % 2 == 1)
		// Значит есть права на запуск
		document.getElementById($szRead).checked = true;
	else 
		document.getElementById($szRead).checked = false;
	if ((nPermission >> 1) % 2 == 1)
		// Значит есть права на запуск
		document.getElementById($szWrite).checked = true;
	 else 
		document.getElementById($szWrite).checked = false;
	if ((nPermission >> 2) % 2 == 1)
		// Значит есть права на запуск
		document.getElementById($szExecute).checked = true;
	else 
		document.getElementById($szExecute).checked = false;
}
/**
*   @desc Реагирует на изменение значения в текстовом поле ввода прав
*/
Rights.prototype.calculatorChange = function(target) {
	if (target.type == 'checkbox')
	{
		var szString = '';
		szString += this.calculatorInputChange('Owner');
		szString += this.calculatorInputChange('Group');
		szString += this.calculatorInputChange('World');
		
		document.getElementById('rightsCalculator').value = szString;
	}
	if (target.type == 'text') 
	{
		
		var szString = target.value;
		// Перерасчитываем значения
		this.calculatorColumnChange('Owner',(szString.charAt(0) != null)?szString.charAt(0):0);
		this.calculatorColumnChange('Group',(szString.charAt(1) != null)?szString.charAt(1):0);
		this.calculatorColumnChange('World',(szString.charAt(2) != null)?szString.charAt(2):0);
	}
}