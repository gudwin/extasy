// Последние изменения от 2007.03.01
// добавлен editCell
// изменен listCell убран параметр ширины ячейки
var cms = new Object();

cms.bModal = false;
cms.szPopupClass = '';
cms.szPopupId = '';
cms.httpRoot = '/';
cms.httpCPRoot = '/';
cms.httpKernelPath = '/resources/extasy/';
cms.tabSetTitle = function ($tabId,$newTitle) {
	var tab = document.getElementById("title_"+$tabId);
	if (tab == null)
		return
	tab.innerHTML = $newTitle;
}

cms.tabSetCurrent = function ($sender,$tabId) {
	if ($sender != null)
		if ($sender.id == "title_".$tabId)
			return ;
	var tabs=document.getElementById("tabCntTitle").childNodes;
	var cls = "tabBeforeCurrent";
	for(var i=0;i<tabs.length;i++){
		if (tabs[i].tagName == "DIV") {
			if (tabs[i].id == "title_"+$tabId) {
				tabs[i].className = "tabCurrent";
				cls = "tabAfterCurrent";
			} else {
				tabs[i].className = cls;
			}
		}
	}
	var contents=document.getElementById("tabCntContent").childNodes;
	for(var i=0;i<contents.length;i++){
		if (contents[i].tagName == "DIV") {
			if (contents[i].id == $tabId) {
				contents[i].style.display = "block";
			} else {
				contents[i].style.display = "none";
			}
		}
	}

}
cms.rowBegin = function ($oTable) {
	$newRow = $oTable.insertRow(-1);
	return $newRow;
}

cms.listRow = function ($oTable) {
	$newRow = $oTable.insertRow(-1);
	return $newRow;
}
cms.fullRow = function (oTable,content) {
	var oNewRow,oNewCell;
	oNewRow = oTable.insertRow(-1);
	oNewCell = oNewRow.insertCell(-1);
	oNewCell.colSpan =100;
	oNewCell.innerHTML = content;


}

cms.tableHeader = function($oTable,$aHeader) {
	var $nKey,$oRow,$oCell;
	var $newTD;

	$oRow = this.listRow($oTable);
	for ($nKey in $aHeader )
	{
		$newTD = $oRow.insertCell(-1);
		$newTD.width = $aHeader[$nKey]['width'] + '%';
		$newTD.innerHTML = '<strong>' + $aHeader[$nKey]['name'] + '</strong>';
	}
	// формируем сплиттер
	$oRow = this.listRow($oTable);
	$oCell = $oRow.insertCell(-1);
	$oCell.className = 'HeaderSplitter';
	$oCell.colSpan = 100;
	$oCell.innerHTML = '<div></div>';
}

cms.listCell = function ($obj,content,is_bold) {
	var $newTD;
	if ($obj.tagName !='TR')
	{
		alert('Parameter $obj is not TR-object');
		return;
	}
	$newTD = $obj.insertCell(-1);
	if (is_bold)
	{
		$newTD.innerHTML = '<strong>' + content + '</strong>';
	} else {
		$newTD.innerHTML = content;
	}

}
cms.editCell = function ($oRow,$szUrl) {
	var $newTD;
	var $content;
	$newTD = $oRow.insertCell(-1);
	$content = sprintf('<nobr><img alt="Редактировать" src="%skernel/pic/icons/edit.gif" /><a href="%s">Редактировать</a></nobr>',this.httpRoot,$szUrl);
	$newTD.innerHTML = $content;
}
cms.addButton = function ($id,$caption,$url) {

}
/**
*   @desc ��������� �������� �����
function closePopup()
{}
*/
cms.closePopup = function () {

	if (document.getElementById(this.szPopupId) == null)
		return
	cms.bModal = false;
	obj = document.getElementById(this.szPopupId);

	document.body.removeChild(document.getElementById('popupWindow'));
	obj.className = this.szPopupClass;
	this.szPopupId = '';
	this.szPopupClass = '';

	document.body.appendChild(obj);



}
cms.popup = function (id,title,width,height) {
	if (!width)
	{
		width = 400;
	}
	if (!height)
	{
		height = 300;
	}
	// �������� �������
	cms.bModal = true;
	var targetFrame = document.getElementById(id);
	if (!targetFrame)
	{
		return;
	}
	// ������������� ����� � ������
	this.szPopupId = id;
	this.szPopupClass = targetFrame.className;

	// ������������� HTML
	var szHTML;
	szHTML = '<table bgcolor="#EEEEEE" border=1 width="'+ width +'px" height=100% style="border-collapse:collapse"cellpadding=0 cellspacing=0><tr><td width="'+ (width - 10) + 'px">' + title + '</td><td width="10px" ><a href="#"id="popupWindowCloser" onclick="cms.closePopup()">[X]</a></td></tr>';
	szHTML += '<tr><td colspan="2" id="popupOutputCell" >'
	//szHTML = '<span style="background-color:white"> ' + title + ' </span><span style="position:absolute;left:'+ (width - 6) +'px;top:0px;text-align:right"></span><hr>' + "\r\n";
	szHTML += '</td></tr></table>';

	// ������� ������
	var popup = document.createElement('div');
	popup.id = 'popupWindow';
	popup.innerHTML = szHTML;
	// ����������� ����� � �������
	popup.style.position = 'absolute';
	popup.style.cssText = 'position:absolute;background-color:#EEEEEE;';
	popup.style.width = width + 'px';
	popup.style.height = height + 'px';
	popup.style.marginLeft =  (Math.round(-width) / 2) + 'px';
	popup.style.marginTop = (Math.round(-height) / 2) + 'px';
	popup.style.left = '50%';
	popup.style.top = '50%';
	popup.style.zIndex = 500;
	popup.style.visibility = 'visible';
	// �������

	document.body.appendChild(popup);
	// ���������� div
	obj = document.getElementById('popupOutputCell');

	targetFrame.className = targetFrame.className.replace('popupFrame','');
	obj.appendChild(targetFrame);
}

/**
*   -------------------------------------------------------------------------------------------
*   @desc 
*   @return 
*   -------------------------------------------------------------------------------------------
*/
cms.submit = function ($parentObject,$szDiv,$szLabel) {
	var $oInput,$oDiv;
	// 
	$oDiv = document.createElement('div');
	$oDiv.className = 'ContentSpacer';
	$parentObject.appendChild($oDiv);

	// вкладываем в div 
	$oDiv = document.createElement('div');
	$oDiv.className = 'ContentBlock Buttons';
	$oDiv.innerHTML = '<input type="submit" class="SaveButton" id="' + $szDiv + '" name="' + $szDiv +'" value="' + $szLabel+ '" ' 
					+ 'style="float: none"  onclick="return false;" />';
//	$oDiv.appendChild($oInput);

	// вкладываем пробел
	$parentObject.appendChild($oDiv);


}



cms.moreSubmits = function () {
}
cms.header = function () {
}
/**
*   -------------------------------------------------------------------------------------------
*   @desc Вывод результата настройки типа
*   @return 
*   -------------------------------------------------------------------------------------------
*/
cms.onSetting = function ($loader) {
	if ($loader.responseXML) {
		$xmlDoc = $loader.responseXML;
	} else {
		$xmlDoc = $loader.responseXML;
		$xmlDoc = (new DOMParser()).parseFromString($loader.responseText, "text/xml");
		//_debug($xmlDoc.childNodes.length);
	}
	var $aData;
	$aData = $xmlDoc.getElementsByTagName('TYPE_SETUP_RESULT');
	if ($aData.length > 0) {
		eval($aData[0].textContent);
	} else {
		alert( $loader.responseText);
	}
	
}