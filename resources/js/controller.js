/**
*   -------------------------------------------------------------------------------------------
*   @desc Описание objKey
*   @return 
*   -------------------------------------------------------------------------------------------

$objKey =  {
	keyCode:0,
	shiftKey:0,
	altKey:0,
	ctrlKey:0
}
*/

/**
*   @desc Редакти
*/
function onController() {
	this.bCode = '';
	this.$aMethodQuery = new Array();
}
/**
*   @desc Принимает HTML и целевую функцию
function addCodeToEvent() 
*/
onController.prototype.addCodeToEvent = function (target_Obj,event,code) {
	if (target_Obj[event])
	{
		var szFunction = target_Obj[event].toString();
		szFunction = szFunction.replace(RegExp("^[\s\n]*function[^{]*\\{",'gi'),'');
		szFunction = szFunction.replace(RegExp("\\}[\s\n]*$",'gi'), "");
	} else {
		szFunction = '';
	}
	
	szFunction = code  + szFunction;

	target_Obj[event] = new Function('event1',szFunction);
}
/**
*   @desc Добавляет новый обработчик горячей клавиши к указанному id 
function addToId() {
*/
onController.prototype.addToId = function (target,event,code) {
	this.addCodeToEvent (document.getElementById(target),event,code)

}
/**
*   @desc
function onKey() {
*/
onController.prototype.onKey = function (target,method,objKey,code)
{
	szCode = '';
	szCode += ''
	szCode += 'if (event1 != null) event = event1;' + "\r\n" ;
//	szCode += 'var_dump(event,1);'
//	szCode += 'return 1;';
	szCode += 'var $keyCode = event.keyCode == 0?event.charCode:event.keyCode;' + "\r\n" ;
//	szCode += 'alert($keyCode);alert(event.shiftKey);alert(event.altKey);alert(event.ctrlKey);';
	szCode += "bResult = ($keyCode == " + objKey.keyCode + ");\r\n"
	szCode += "bResult = (bResult && (event.shiftKey == " + objKey.shiftKey + "));\r\n"
	szCode += "bResult = (bResult && (event.altKey == " + objKey.altKey + "));\r\n"
	szCode += "bResult = (bResult && (event.ctrlKey == " + objKey.ctrlKey + "));\r\n"
	szCode += "if (bResult) { \r\n" + code + "\r\n}"
	if (typeof(target) == 'string') {
		this.addCodeToEvent (document.getElementById(target),method,szCode);
	} else {
		this.addCodeToEvent (target,method,szCode);
	}
	
	
}/**
*   @desc Подвешивает на onKeyDown реакцию на указанный id=target
function onKeyDown() {
*/
onController.prototype.onKeyDown = function (target,objKey,code) {
	this.onKey(target,'onkeydown',objKey,code)
}
/**
*   @desc Подвешивает на onKeyUp реакцию на указанный id=target
function onKeyUp() {
*/
onController.prototype.onKeyUp = function (target,objKey,code) {
	this.onKey(target,'onkeyup',objKey,code);
}
/**
*   @desc Подвешивает на onKeyPress реакцию на указанный id=target
function onKeyPress() {
*/
onController.prototype.onKeyPress = function (target,objKey,code) {
	this.onKey(target,'onkeypress',objKey,code);
}
/**
*   @desc Подвешивает проверку контрола на onSubmit
function onSubmitMatch()
*/
onController.prototype.onSubmitMatch = function (target,regexp,message) 
{

	// получаем форму
	var $obj;
	$obj = document.getElementById(target);
	var $form;
	if (!$obj) {
		alert('Error onSubmitCompare . Control-"'+ target +'" not found');
		return ;
	}
	if ($obj.form == null)
	{
		// если формы нет, то выход с объевлением об ошибке 
		alert('Error onSubmitMatch. Control-"'+ target +'" form not found');
		return;
	}
	// формируем код
	var code = '';
	code += 'if (!document.getElementById("'+ target + '").value.match('+ regexp.toString() + ')) {' + "\r\n";

	code += "\talert(\""+ message + "\");\r\n"
	code += "\t" + 'document.getElementById("'+ target + '").focus()' + "\r\n";
	code += "\t" + 'return false;' + "\r\n";
	code += '}' + "\r\n";
	this.addCodeToEvent($obj.form,'onsubmit',code);
//	alert($obj.form.onsubmit);
}
/**
*   @desc Подвешивает проверку Eval на работу 
function onSubmitMatch()
*/
onController.prototype.onSubmitEval = function (target,eval,message) 
{

	// получаем форму
	var $obj;
	$obj = document.getElementById(target);
	var $form;
	if (!$obj) {
		alert('Error onSubmitEval . Control-"'+ target +'" not found');
		return ;
	}
	if ($obj.form == null)
	{
		// если формы нет, то выход с объевлением об ошибке 
		alert('Error onSubmitEval. Control-"'+ target +'" form not found');
		return;
	}
	// формируем код
	var code = '';
	code += 'var $bResult = 0;' + "\r\n";
	code += '$bResult = (' + eval +'?1:0);' + "\r\n";
	code += 'if (!$bResult) {' + "\r\n";
	code += "\talert(\""+ message + "\");\r\n"
	code += "\t" + 'document.getElementById("'+ target + '").focus()' + "\r\n";
	code += "\t" + 'return false;' + "\r\n";
	code += '}' + "\r\n";

	this.addCodeToEvent($obj.form,'onsubmit',code);
}
/**
*   @desc Подвешивает проверку контрола на onSubmit
function onSubmitCompare() {
*/
onController.prototype.onSubmitCompare = function (target,target_confirm,message) 
{
	
	// получаем форму
	var $obj = document.getElementById(target);
	var $form;
	if (!$obj) {
		alert('Error onSubmitCompare . Control-"'+ target +'" not found');
		return ;
	}
	if ($obj.form == null)
	{
		// если формы нет, то выход с объевлением об ошибке 
		alert('Error onSubmitCompare . Control-"'+ target +'" form not found');
		return;
	}
	// формируем код
	var code = '';
	code = 'if ((document.getElementById("'+ target + '").value.length == 0) || (document.getElementById("'+ target + '").value != document.getElementById("'+ target_confirm + '").value)) {' + "\r\n";
	code += "\talert(\""+ message + "\");\r\n"
	code += "\t" + 'document.getElementById("'+ target + '").focus()' + "\r\n";
	code += "\t" + 'return false;' + "\r\n";
	code += '}' + "\r\n";

	this.addCodeToEvent($obj.form,'onsubmit',code);
}
/**
*   -------------------------------------------------------------------------------------------
*   @desc Метод подключает реакцию контрола на событие, при этом вызывая метод определенного 
*   объекта. 
*   ВАЖНО!
*   Обычно вызов функци-реакции на событие происходит в контексте вызываюшего контрола 
*   (input, div и т.д.), поэтому если подключать к реакции метод принадлежащий объекту, то 
*   контекст объяекта заменялся (терялся ) контекстом HTML-объекта. Метод решает указанную 
*   проблему
*   @algorithm Метод создает внутреннюю очередь внутри объекта controller, в нее добавляет и 
*   метод и объект. В функцию-реакцию записывает вызов соответствующего метода у очереди
*   @return 
*   -------------------------------------------------------------------------------------------
*/
onController.prototype.applyMethod = function($idTarget,$szEvent,$oObject,$fncMethod,$aAddParams)  {
	var $obj;
	var $szCode = '';
	var $aNewElement = new Array();

	$obj = document.getElementById($idTarget);
	if (!$obj)
	{
		// если формы нет, то выход с объевлением об ошибке 
		alert('Error applyMethod. Control-"'+ $idTarget +'"  not found');
		return;
	}

	// подключение к указанному контрола вызова метода у контрола
	$aNewElement['object'] = $oObject;
	$aNewElement['method'] = $fncMethod;
	if ($aAddParams) {
		$aNewElement['params'] = $aAddParams;
	} else {
		$aNewElement['params'] = {};
	}

	this.$aMethodQuery.push($aNewElement);

	// формируем код

	$szCode = 'controller.execQueryMethod(' + (this.$aMethodQuery.length - 1) +',this);';

	// подключение кода
	this.addToId($idTarget,$szEvent,$szCode);
}

/**
*   -------------------------------------------------------------------------------------------
*   @desc Вызывает определенный ($nMethodId) метод в очереди контроллера
*   @param $nMethodId Индекс метода в очереди методов
*   @param $oObject объект в контексте которого вызвано событие
*   @return 
*   -------------------------------------------------------------------------------------------
*/
onController.prototype.execQueryMethod = function($nMethodId,$oObject) {
	if (this.$aMethodQuery[$nMethodId]) {
		if (this.$aMethodQuery[$nMethodId]['object'] != null) 
			this.$aMethodQuery[$nMethodId]['method'].call(
				this.$aMethodQuery[$nMethodId]['object'],
				$oObject,
				this.$aMethodQuery[$nMethodId]['params']);
	} else {
		alert('Method not found');
	}
}
var controller = new onController();
