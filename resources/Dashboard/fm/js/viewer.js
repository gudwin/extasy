var viewer;
viewer = new Object();
viewer.aExtension = new Object();
viewer.szDefault = '';

/**
*   @desc Очищает список расширений
*/
viewer.clear = function () {
	this.aExtension = new Object();
}
/**
*   @desc Добавляет новое расширение 
*/
viewer.add = function (extension,path)
{
	this.aExtension[extension.toUpperCase()] = path;
}
viewer.setDefault = function  (szPath) {
	this.szDefault = szPath;
}
viewer.exec = function (path) {
	if (cms.bModal) return;
	// Ищем расширение
	var szExtension = path.substr(path.lastIndexOf('.') + 1,path.length );
	//alert(this.aExtension[szExtension.toUpperCase()]);
	//return false;
	// Поиск скрипта
	if (this.aExtension[szExtension.toUpperCase()] != null)
	{
		document.getElementById('viewFrameIframe').src = this.aExtension[szExtension.toUpperCase()] + '?path=' + path;
	} else 
		if (this.szDefault.length > 0)
			document.getElementById('viewFrameIframe').src = this.szDefault + '?path=' + path;
		else 
			return;
	// Вывод
	cms.popup('viewFrame','Просмотр файла [ ' + path + ' ]',600,500);
	controller.addToId('popupWindowCloser','onclick','viewer.closeDialog();return false;');
}
viewer.closeDialog = function () {
	cms.closePopup();
}