var editor;
editor = new Object();
editor.aExtension = new Object();
editor.szDefault = '';
/**
*   @desc Очищает список расширений
*/
editor.clear = function () {
	this.aExtension = new Object();
}
/**
*   @desc Добавляет новое расширение 
*/
editor.add = function (extension,path)
{
	this.aExtension[extension.toUpperCase()] = path;
}
editor.setDefault = function  (szPath) {
	this.szDefault = szPath;
}
editor.exec = function (path) {
	if (cms.bModal) return;
	// Ищем расширение
	
	var szExtension = path.substr(path.lastIndexOf('.') + 1,path.length );
	//alert(this.aExtension[szExtension.toUpperCase()]);
	//return false;
	// Поиск скрипта
	if (this.aExtension[szExtension.toUpperCase()] != null)
	{
		document.getElementById('editFrameIframe').src = this.aExtension[szExtension.toUpperCase()] + '?path=' + path;
	} else 
		if (this.szDefault.length > 0){

			document.getElementById('editFrameIframe').src = this.szDefault + '?path=' + path;
		}
		else {
			viewer.exec(path)
			return;
		}
	// Вывод
	cms.popup.show('editFrame','Редактирование файла [ ' + path + ' ]',600,500);
	controller.addToId('popupWindowCloser','onclick','editor.closeDialog();return false;');
}
editor.closeDialog = function () {
	cms.closePopup();
	ii.openDirectory('',1);
}