
/**
*   @desc Объект элемента
*/
function FileObject(name,is_directory,fileSize,date,owner,rights) {
	this.name = name;
	this.is_directory = is_directory;
	this.fileSize = fileSize;
	this.date = date;
	this.owner = owner;
	this.rights = rights;
}
////////////////////////////////////////////////////////////////
/**
*   @desc Класс для отображения сайта
*/

function View() {
	this.nCurrent = 0;
}
View.prototype.draw = function (aFile) {
	this.drawPanel(aFile);
}

/**
*   @desc Перерисовывает текущий путь 
function redrawPath;
*/
View.prototype.redrawPath = function () {
	var obj = ii.getCurrentItem();
	document.getElementById('path').innerHTML = '[' + ii.szPath +  ']';
}
/**
*   @desc 
устанавливает размер указанного объекта
function setSize() 
*/
View.prototype.setSize = function (nFile,nSize) {
	// получаем таблицу
	var obj = document.getElementById('panel');
	// проверяем есть ли указанный ряд
	if (obj.rows[2 + nFile] != null)
	{
		// Обрабатываем размер
		if (nSize > 1024)
		{ 
			nSize = (Math.round(nSize * 10/ 1024) / 10).toString() + 'Kb';
		}
		// выводим
		obj.rows[2 + nFile].childNodes[1].innerHTML = nSize;
	}
}
/**
*   @desc Устанавливает текущий путь
function setCurrent() 
*/
View.prototype.setCurrent = function(nCurrent) {
	this.redrawPath();
	// получаем таблицу
	var obj = document.getElementById('panel');
	var oRow;
	// Убираем className у текущего
	if (obj.rows[2+ this.nCurrent] != null)
	{
		oRow = obj.rows[2 + this.nCurrent];
		oRow.className = oRow.className.replace('currentFile','')
	}
	// Устанавливаем на текущий
	this.nCurrent = nCurrent;
	if (obj.rows[2+ this.nCurrent] != null)
	{
		oRow = obj.rows[2 + this.nCurrent];
		oRow.className = oRow.className.replace('currentFile','');
		oRow.className += ' currentFile';
		
	}
	
}

/**
*   @desc На вход получает массив объектов FileObject выводит их в центральной части
function drawPanel()
*/
View.prototype.drawPanel = function (aFile) {
	// Очищаем таблицу
	this.redrawPath();
	var obj = document.getElementById('panel');
	while (obj.rows.length > 2)
		obj.deleteRow(2);
	// Перебираем весь массив
	var i = 0;
	for (file in aFile)
	{
		
		// Создаем ряд
		oRow = obj.insertRow(-1);
		// Запихиваем элементы
		// Имя
		oCell = oRow.insertCell(-1);
		if (aFile[file]['is_directory'] == 1)
		{
			// Это директория
			oCell.innerHTML = sprintf('<a href="#" onclick="return false;" ondblclick="ii.openDirectory(\'%s\');return false;">[%s]</a>',file,aFile[file]['name']);
			
		} else {
			// Иначе это файлы
			oCell.innerHTML = sprintf('<a href="#" onclick="return false;" ondblclick="ii.open(\'%s\');return false;">%s</a>',aFile[file]['name'],aFile[file]['name']);
		}
		
		oCell.className = 'FieldName3';
		oCell.onclick = new Function('ii.setCurrent(' + i + ')');
		// Размер
		oCell = oRow.insertCell(-1);
		if (aFile[file]['fileSize'] > 1024)
		{
			oCell.innerHTML = (Math.round(aFile[file]['fileSize'] * 10/ 1024) / 10).toString() + 'Kb';
		} else 
			oCell.innerHTML = aFile[file]['fileSize'] ;
		oCell.onclick = new Function('ii.setCurrent(' + i + ')');
		// Дата 
		/*oCell = oRow.insertCell(-1);
		oCell.onclick = new Function('ii.setCurrent(' + i + ')');
		oCell.innerHTML = aFile[file]['date'];
		// Владелец
		oCell = oRow.insertCell(-1);
		oCell.onclick = new Function('ii.setCurrent(' + i + ')');
		oCell.innerHTML = aFile[file]['owner'];*/
		// Права
		oCell = oRow.insertCell(-1);
		oCell.onclick = new Function('ii.setCurrent(' + i + ')');
		oCell.innerHTML = sprintf('<a href="#" onclick="ii.changeRights(\'%s\')">%s</a>'
			,file,aFile[file]['rights']);
		// Переименовать
		oCell = oRow.insertCell(-1);
		oCell.onclick = new Function('ii.setCurrent(' + i + ')');
		if (aFile[file]['name'] == '..')
			oCell.innerHTML = ' ';
		 else  
			oCell.innerHTML = sprintf('<a href="#" title="Переименовать" onclick="ii.rename(\'%s\')"> <img src="pic/icons/edit.gif" border=1 ALT=""> </a>',
			 file);
		
		// Удалить
		oCell = oRow.insertCell(-1);
		oCell.onclick = new Function('ii.setCurrent(' + i + ')');
		if (aFile[file]['name'] == '..')
			oCell.innerHTML = ' ';
		 else  
			oCell.innerHTML = sprintf('<a href="#" title="Удалить" onclick="ii.unlink(\'%s\')"> <img src="pic/icons/delete.gif" border=1 ALT=""> </a>'
				,file);
		i++;
	}
	
}

/**
*   @desc Выводит надпись колонки
function setColumnTitle()
*/
View.prototype.setColumnTitle = function (szBy,bInvert) {
	var name;
	var title;
	switch (szBy)
	{
	case 'name':
		name = 'columnFile';
		break;
	case 'date':
		name = 'columnDate';
		break;
	case 'fileSize':
		name = 'columnSize';
		break;
	case 'owner':
		name = 'columnOwner';
		break;
	case 'rights':
		name = 'columnRights';
		break;
	default:
		name = 'columnFile'
	}
	// получаем объект
	document.getElementById('columnFile').innerHTML = 'Файл';
	//document.getElementById('columnDate').innerHTML = 'Дата изменения';
	document.getElementById('columnSize').innerHTML = 'Размер';
	//document.getElementById('columnOwner').innerHTML = 'Владелец';
	document.getElementById('columnRights').innerHTML = 'Права';
	var obj = document.getElementById(name);
	obj.innerHTML = ((bInvert)?'&uarr;':'&darr;') + '&nbsp;'+ obj.innerHTML;
}

view = new View();
