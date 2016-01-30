// Здесь лежат системные функции для отладки и работы в javascript
function _debug(obj) {
	result = '';
	i =0;
	for ( field in obj )
	{
		result += field ;
		//alert(field +' = ' + obj[field])
		if (i % 10 == 0)
		{
			result += "\n";
		} else {
			result += " | ";
		}
		i++;
	}
	alert(result);
}
function _var_dump($obj,$nLevel) {
	if (($obj instanceof Object ) && ($nLevel > 0))
	{ 
		
		$szResult = '{' + "\r\n";

		for ($key in $obj)
		{
			try
			{
				$szResult += $key + ":" + _var_dump($obj[$key],$nLevel - 1) + "\r\n";
			}
			catch (e)
			{
			}
			
		}
		$szResult += "}\r\n";
		return $szResult;
	} else {
		return $obj;
	}
}
function var_dump($obj,$nLevel) {
	if (!$nLevel) {
		$nLevel = 20;
	}
	var szResult = _var_dump($obj,$nLevel)
	var width = 400;
	var height = 300;
	// Получаем элемент
	// грабим HTML
	var szHTML;
	szHTML = '<table border=1 width="'+ width +'px" height=100% style="border-collapse:collapse" cellpadding=0 cellspacing=0><tr><td width="'+ (width - 10) + 'px">Debug</td><td width="10px"><a href="#" onclick="document.body.removeChild(document.getElementById(\'debugWindow\'));">[X]</a></td></tr>';
	szHTML += '<tr><td colspan="2">'
	//szHTML = '<span style="background-color:white"> ' + title + ' </span><span style="position:absolute;left:'+ (width - 6) +'px;top:0px;text-align:right"></span><hr>' + "\r\n";
	szHTML += '<pre>' + szResult + '</pre>';
	szHTML += '</td></tr></table>';
	// создаем объект
	var obj = document.createElement('div');
	obj.id = 'debugWindow';
	obj.innerHTML = szHTML;
	
	// присваиваем стили и позиции
	obj.style.position = 'absolute';
	obj.style.cssText = 'position:absolute;background-color:white;';
	obj.style.width = width + 'px';
	obj.style.height = height + 'px';
	obj.style.marginLeft =  (Math.round(-width) / 2) + 'px';
	obj.style.marginTop = (Math.round(-height) / 2) + 'px';
	obj.style.left = '50%';
	obj.style.top = '50%';
	obj.style.zIndex = 500;
	obj.style.visibility = 'visible';
	// выводим
	
	document.body.appendChild(obj);
}
// Слеширует все спец. символы
function make_escape_string(str) {
	return str.replace(/([\/\:\/\=\?\.\&\'\"\+\-\[\]])/g,'\\$1');
}
/**
*   @desc Очищает дочерние элементы у элемента
*/
function clearChild($obj) {
	while($obj.childNodes.length > 0) {
		try
		{
			child = $obj.childNodes[0];
			$obj.removeChild(child)
			
		}
		catch (x) {
			alert('Error rendering document. Contact with developers (email : gisma@smartdesign.by).');
			return;
		}
	}
}
function sprintf() {
	var $pattern,$i,$position;
	$pattern = arguments[0]
	for ($i = 1; $i < arguments.length ; $i++)
	{
		$position = $pattern.indexOf('%s',0);
		if ($position != null)
		{
			$pattern = $pattern.substring(0,$position) + arguments[$i] + $pattern.substring($position + 2,$pattern.length);
		}
	}
	return $pattern;
}
function htmlspecialchars($str) {
	$oNode = document.createTextNode($str);
	return $oNode.data;
}
function startLoading() {
	// создаем див
	var obj = document.createElement('div');
	document.body.appendChild(obj);
	
	obj.id = 'LoadingWindow';
	// устанавливаем позиции
	obj.style.position = 'absolute';
	obj.style.cssText = 'position:absolute;';
	obj.style.width = 120;
	obj.style.height = 40;
	obj.style.marginLeft = '-60px';
	obj.style.marginTop = '-20px';
	obj.style.left = '50%';
	obj.style.top = '50%';
	obj.style.zIndex = 500;
	// забиваем содержимое
	
	obj.innerHTML = '<img src="/resources/extasy/ext4/resources/themes/images/default/grid/loading.gif">';
	
	// выводим
	obj.style.visibility = 'visible';
}
function stopLoading() {
	// ловим див и убиваем ;) 
	
	document.getElementById('LoadingWindow').visibility = 'hidden';
	document.body.removeChild(document.getElementById('LoadingWindow'));
	
}
function trim($str) {
	var $result = '';
	var $i = 0,$j = $str.length - 1;
	// начинаем рассмотр строки с начала

	while (($str[$i] == ' ') && ($i < $str.length)) {
		$i++;
	}

	// потом с конца проходим
	var $j 
	while (($str[$i] == ' ') && ($j >= 0)) {
		$о--;
	}
	if ($j > $i )
	{
		return $str.substring($i,$j);
	} else 
		return '';
}
function getInnerHTML($szId) {
	var $obj = document.getElementById($szId);
	if (!$obj) {
		alert('Element `' + $szId + '` not found');
		return;
	}
	return  $obj.innerHTML;
}
function _msg($string) {
	if ($aHash[$string]) {
		return $aHash[$string]
	} else {
		return $string;
	}
}
function createHidden($target,$name,$value) {
	var input = document.createElement('input');
	input.type = 'HIDDEN';
	input.name = $name;
	input.value = $value;
	document.getElementById($target).appendChild(input);
}
/**
*  @desc Возвращает позиции текущего скролла страницы
*/
function getPageScroll(){

	var yScroll;

	if (self.pageYOffset) {
		yScroll = self.pageYOffset;
	} else if (document.documentElement && document.documentElement.scrollTop){	 // Explorer 6 Strict
		yScroll = document.documentElement.scrollTop;
	} else if (document.body) {// all other Explorers
		yScroll = document.body.scrollTop;
	}

	arrayPageScroll = new Array('',yScroll) 
	return arrayPageScroll;
}

//
// getPageSize()
// Returns array with page width, height and window width, height
// Core code from - quirksmode.org
// Edit for Firefox by pHaez
//

function getPageSize(){
	
	var xScroll, yScroll;
	
	if (window.innerHeight && window.scrollMaxY) {	
		xScroll = document.body.scrollWidth;
		yScroll = window.innerHeight + window.scrollMaxY;
	} else if (document.body.scrollHeight > document.body.offsetHeight){ // all but Explorer Mac
		xScroll = document.body.scrollWidth;
		yScroll = document.body.scrollHeight;
	} else { // Explorer Mac...would also work in Explorer 6 Strict, Mozilla and Safari
		xScroll = document.body.offsetWidth;
		yScroll = document.body.offsetHeight;
	}
	
	var windowWidth, windowHeight;
	if (self.innerHeight) {	// all except Explorer
		windowWidth = self.innerWidth;
		windowHeight = self.innerHeight;
	} else if (document.documentElement && document.documentElement.clientHeight) { // Explorer 6 Strict Mode
		windowWidth = document.documentElement.clientWidth;
		windowHeight = document.documentElement.clientHeight;
	} else if (document.body) { // other Explorers
		windowWidth = document.body.clientWidth;
		windowHeight = document.body.clientHeight;
	}	
	
	// for small pages with total height less then height of the viewport
	if(yScroll < windowHeight){
		pageHeight = windowHeight;
	} else { 
		pageHeight = yScroll;
	}

	// for small pages with total width less then width of the viewport
	if(xScroll < windowWidth){	
		pageWidth = windowWidth;
	} else {
		pageWidth = xScroll;
	}


	arrayPageSize = new Array(pageWidth,pageHeight,windowWidth,windowHeight) 
	return arrayPageSize;
}