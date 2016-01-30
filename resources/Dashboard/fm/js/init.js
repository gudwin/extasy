/**
 *   @desc Вызывается при старте утилиты
 */
function init() {
    if (net == null) {
        // бывает такое подвешиваем проверку на секунду позже
        window.setTimeout('init();', 500);
        return;
    }
    // Обработчики кнопок
    controller.addToId('uploadButton', 'onclick', 'ii.upload();;return false;');
    controller.addToId('folderButton', 'onclick', 'ii.createFolder();return false;');
//	controller.addToId('viewButton','onclick','ii.view(\'rights\');return false;');
//	controller.addToId('editButton','onclick','ii.view(\'rights\');return false;');
    //controller.addToId('helpButton','onclick','ii.help();return false;');
    controller.addToId('deleteButton', 'onclick', 'ii.unlink();return false;');
    controller.addToId('renameButton', 'onclick', 'ii.rename();return false;');
    controller.addToId('columnFile', 'onclick', 'ii.sortBy(\'name\');return false;');
    controller.addToId('columnSize', 'onclick', 'ii.sortBy(\'fileSize\');return false;');
    //controller.addToId('columnDate','onclick','ii.sortBy(\'date\');return false;');
    //controller.addToId('columnOwner','onclick','ii.sortBy(\'owner\');return false;');
    controller.addToId('columnRights', 'onclick', 'ii.sortBy(\'rights\');return false;');


    // подрубаем горячие клавиши
    aEl = getKeyCodes();

//	controller.onKeyDown('Document',aEl['help'],'ii.help();return false;');
    controller.onKeyDown('Document', aEl['upload'], 'ii.upload();return false;');
    controller.onKeyDown('Document', aEl['onEnter'], 'ii.enter();return false;');
    controller.onKeyDown('Document', aEl['onView'], 'ii.view();return false;');

    controller.onKeyDown('Document', aEl['onEdit'], 'ii.edit();return false;');

    controller.onKeyDown('Document', aEl['onDelete'], 'ii.unlink();return false;');

    controller.onKeyDown('Document', aEl['onDown'], 'ii.moveDown();return false;');
    controller.onKeyDown('Document', aEl['onUp'], 'ii.moveUp();return false;');

    controller.onKeyDown('Document', aEl['createdir'], 'ii.createFolder();return false;');


    controller.onKeyDown('Document', aEl['CtrlR'], 'ii.rename();return false;');
    controller.onKeyDown('Document', aEl['onF2'], 'ii.rename();return false;');
    // подвешиваем расширения на просмотр
    viewer.add('php', './scripts/extensions/view/text.php');
    viewer.add('txt', './scripts/extensions/view/text.php');
    viewer.add('html', './scripts/extensions/view/text.php');
    viewer.add('jpg', './scripts/extensions/view/image.php');
    viewer.add('gif', './scripts/extensions/view/image.php');
    viewer.add('bmp', './scripts/extensions/view/image.php');
    viewer.add('png', './scripts/extensions/view/image.php');
    viewer.setDefault('./scripts/extensions/view/text.php');
    // редактирование
    editor.add('php', './scripts/extensions/edit/text.php');
    editor.add('txt', './scripts/extensions/edit/text.php');
    editor.add('html', './scripts/extensions/edit/text.php');
    editor.add('jpg', './scripts/extensions/edit/image.php');
    editor.add('gif', './scripts/extensions/edit/image.php');
    editor.add('bmp', './scripts/extensions/edit/image.php');
    editor.add('png', './scripts/extensions/edit/image.php');
    editor.setDefault('./scripts/extensions/edit/text.php');
    //
    ii = new InsertImage();
    ii.start();

    // Завершаем загрузку
    if (document.all) {
        //alert(1);
    }
    document.getElementById('Document').focus();
    document.getElementById('PopupBody').focus();
}
/**
 *   @desc
 function B
 */
function bodyOnKeyDown(event) {
    if (event.srcElement != null) {

        if ((event.srcElement.id != null) && (event.srcElement.id == 'rightsCalculator'))
            return true;
    }
    else if (event['target'] != null)
        if ((event.target.id != null) && (event.target.id == 'rightsCalculator'))
            return true;
        else return false;
}
function InsertImage() {
    // загружаем заранее
    var aPreload = document.cookie.match(/fm_path=([^;$]+)/);
    if (aPreload) {
        this.szPath = decodeURIComponent(aPreload[1]);
    } else
        this.szPath = '';
    this.basePath = '/'

    this.aFile = {};
    this.szSort = '-name';
    this.aPath = new Object();
    this.szCurrentFile = '';
    this.rights = new Rights();
    this.nItemCount = 0;
    this.nCurrentItem = 0;
}
InsertImage.prototype.isProcessing = function () {
    if (cms.bModal || net.bProcessing) {
        return true;
    } else
        return false;
}
InsertImage.prototype.start = function () {

    this.openDirectory('', 1);
}
/**
 *   @desc
 function createFolder()
 */
InsertImage.prototype.createFolder = function () {

    if (this.isProcessing()) return;
    var szName = window.prompt('Введите имя каталога', 'NoName');
    // Функция не завершена
    if (szName != null) {
        createFolder(this.inChangeFileInfo, this, this.szPath, szName);
    }

}
/**
 *   @desc
 function upload()
 */
InsertImage.prototype.upload = function () {

    if (this.isProcessing()) return;
    cms.popup.show("uploadFrame", "Закачка изображения", 200, 50);
    document.getElementById('uploadFrameIframe').src = "scripts/upload.php";
}
/**
 *   @desc
 function help()
 */
InsertImage.prototype.help = function () {

    if (this.isProcessing()) return;
    cms.popup.show("helpFrame", "Помощь", 500, 500);
}
/**
 *   @desc Возвращает текущий элемент
 function getCurrentItem()
 */
InsertImage.prototype.getCurrentItem = function () {

    var i = 0;
    for (key in this.aFile) {
        if (i == this.nCurrentItem) {
            this.szCurrentFile = key;
            return this.aFile[key];
        }
        i++;
    }
    return null;
}
/**
 *   @desc Перемещает указатель вверх
 function moveUp()
 */
InsertImage.prototype.moveUp = function () {

    if (this.isProcessing()) return;

    if (this.nCurrentItem > 0) {
        this.nCurrentItem--;
        view.setCurrent(this.nCurrentItem)
    }
}
/**
 *   @desc Перемещает указатель вниз
 function moveDown()
 */
InsertImage.prototype.moveDown = function () {

    if (this.isProcessing()) return;
    if (this.nCurrentItem < this.nItemCount - 1) {
        this.nCurrentItem++;
        view.setCurrent(this.nCurrentItem)
    }
}
/**
 *   @desc Показывает файл
 function view()
 */
InsertImage.prototype.view = function () {

    if (this.isProcessing()) return;
    // Получаю текущий элемент
    var item = this.getCurrentItem();
    if (item != null) {
        // если директория переходим внутрь
        if (item['is_directory'] == 1) {
            folderSize(this.inGetSize, this, this.szPath + this.aFile[this.szCurrentFile]['name'])
        } else
            viewer.exec(this.szPath + this.aFile[this.szCurrentFile]['name']);
    }
}
/**
 *   @desc Вызывается после замера размера объекта
 function inGetSize()
 */
InsertImage.prototype.inGetSize = function (aInfo) {
    if (aInfo['error'].length > 0) {
        alert(aInfo['error']);
    } else {
        var item = this.getCurrentItem();
        view.setSize(this.nCurrentItem, aInfo['size'])
    }
}
/**
 *   @desc Показывает файл
 function edit()
 */
InsertImage.prototype.edit = function () {

    if (this.isProcessing()) return;

    // Получаю текущий элемент
    var item = this.getCurrentItem();

    if (item != null) {
        // если директория переходим внутрь
        if (item['is_directory'] == 1) {
            this.openDirectory(this.szCurrentFile);
        } else {
            var $szPath = this.basePath + 'files/' + this.szPath + this.aFile[ this.szCurrentFile]['name']
            // если определен редактор
            if ((window.opener !== null) && (window.opener.fm_callback)) {
                window.opener.fm_callback.call(null, $szPath);
                this.saveCookieAndClose();
            } else if ((window.opener !== null) && (window.opener.saveBrowserCallBack )) {
                window.opener.saveBrowserCallBack($szPath);
                this.saveCookieAndClose();
            } else if ((window.opener !== null) && (window.opener.savePathToImage )) {
                window.opener.savePathToImage($szPath);
                this.saveCookieAndClose();

            } else if ((window.opener != null) && (window.opener.$setFileControlValue)) {
                window.opener.$setFileControlValue($szPath);
                this.saveCookieAndClose();
            } else
                editor.exec(this.szPath + this.aFile[this.szCurrentFile]['name']);
        }
    }
}
InsertImage.prototype.saveCookieAndClose = function () {
    jQuery.get('./scripts/save_path.php', 'path=' + this.szPath, function () {
    });
    window.setTimeout('window.close();', 1500);
}
/**
 *   @desc
 function setCurrent()
 */
InsertImage.prototype.setCurrent = function (nCurrent) {

    if (this.isProcessing()) return;
    if (this.nCurrentItem < this.nItemCount) {
        this.nCurrentItem = nCurrent;
        view.setCurrent(this.nCurrentItem)
    }
}

/**
 *   @desc Обработчик нажатия Enter
 function enter()
 */
InsertImage.prototype.enter = function () {

    if (this.isProcessing()) return;

    // Получаю текущий элемент
    var item = this.getCurrentItem();
    if (item != null) {

        // если директория переходим внутрь
        if (item['is_directory'] == 1) {

            this.openDirectory(this.szCurrentFile);
        } else
            this.edit();
    }
}

/**
 *   @desc Открывает директорию
 function openDirectory()
 */
InsertImage.prototype.openDirectory = function (szFilename, bNoSeek) {

    var szPath = '';
    if (this.isProcessing()) return;

    if (bNoSeek == null) {

        var file, nCurrent = 0;

        for (file in this.aFile) {
            if (file == szFilename) {
                this.aPath[this.szPath] = file;
                szPath = this.aFile[file]['name'];
            }
        }
    } else {
        this.aPath[this.szPath] = this.nCurrentItem;
        szPath = '';
    }

    getPathInfo(this.inChangeFileInfo, this, this.szPath + szPath);
}

/**
 *   @desc Клик по файлу
 function open()
 */
InsertImage.prototype.open = function (szFilename) {
    this.edit(szFilename);
}
/**
 *   @desc Устанавливаем права на файла
 function rename()
 {}
 */
InsertImage.prototype.rename = function (file) {
    if (this.isProcessing()) return;

    // Выводим диалог с новым именем
    var aFile;
    if (file == null) {
        aFile = this.getCurrentItem();
    } else
        aFile = this.aFile[file];
    var szNewName = prompt('Введите новое имя объекта', aFile['name']);
    if (szNewName != null) {
        renameFile(this.inChangeFileInfo, this, this.szPath + aFile['name'], szNewName);
    }
}
/**
 *   @desc Удаляет файл
 function unlink()
 {}
 */
InsertImage.prototype.unlink = function (file) {

    if (this.isProcessing()) return;
    var szPath;
    if (file == null) {
        szPath = this.szPath + this.getCurrentItem()['name'];
    } else
        szPath = this.szPath + this.aFile[file]['name'];


    var bConfirm = confirm(sprintf('Объект [%s] будет удален (вместе с объектами). Вы уверены?'
        , szPath));
    if (bConfirm) {
        unlinkFile(this.inChangeFileInfo, this, szPath)
    }
}

/**
 *   @desc Устанавливаем права на файла
 function changeRights()
 {}
 */
InsertImage.prototype.changeRights = function (szFileName) {

    if (this.isProcessing()) return;
    this.szCurrentFile = szFileName;
    view.setCurrent(Math.round(szFileName));
    cms.popup.show('changerightsFrame', 'Смена прав : ' + this.aFile[szFileName]['name'], 400, 130);
    this.rights.setDialogData(this.aFile[szFileName]['rights']);
}
/**
 *   @desc Вызывается при завершении диалога назначеиня прав
 function endDialogRights()
 {}
 */
InsertImage.prototype.endDialogRights = function (nOK, nResult) {

    var bRecursive = 0;
    // Если нажат ОК
    if (nOK == 1) {
        // проверяем является ли файл директорией
        if (this.aFile[this.szCurrentFile]['rights'][0] == 'd') {
            this.aFile[this.szCurrentFile]['name'] += '/';
            bRecursive = confirm('Установить права для поддиректорий');
        }

        for (file in this.aFile) {
            if (file == this.szCurrentFile) {
                this.aPath[this.szPath] = file;
            }
        }
        setFileMode(this.inChangeFileInfo, this, this.szPath + this.aFile[this.szCurrentFile]['name'], nResult, bRecursive);
    }
    cms.closePopup();

}
InsertImage.prototype.inLoadPath = function (aInfo) {
}

/**
 *   @desc Вызывается извне, функция обновляет центральное окно системы. Принимает на вход текущий путь
 *   Файлы и директории для отображения, строку ошибки, если она есть
 function inChangeFileInfo()
 {}
 */
InsertImage.prototype.inChangeFileInfo = function (aInfo) {

    if ((aInfo['errorMessage'] != null) && (aInfo['errorMessage'].length > 0)) {
        alert(aInfo['errorMessage']);
    }
    this.szPath = (aInfo['path']) ? aInfo['path'] : '';
    this.aFile = aInfo['aFile'];
    this.nItemCount = 0;
    for (key in this.aFile)
        this.nItemCount++;
    // отделяем каталоги
    var aDirectory = [];
    var aFile = [];

    for (key in this.aFile) {
        if (this.aFile[key]['is_directory'] == 1) {
            aDirectory.push(this.aFile[key]);
        } else {
            aFile.push(this.aFile[key]);
        }
    }

    // Сортируем файлы
    var i = 0;
    var j = 0;
    var tmp;
    var bFlag;
    // есть ли директории вообще?;)
    if (aDirectory.length > 0) {
        if (aDirectory[0]['name'] == '..') {
            i = 1;
        }
        for (; i < aDirectory.length - 1; i++) {
            for (j = i + 1; j < aDirectory.length; j++) {
                // Вычисляем
                value1 = aDirectory[i]['name'].toUpperCase();
                value2 = aDirectory[j]['name'].toUpperCase();
                // Вычисляем с учетом возможного инверта
                bFlag = (value1 > value2);
                if (bFlag) {
                    tmp = aDirectory[i];
                    aDirectory[i] = aDirectory[j];
                    aDirectory[j] = tmp;
                }
            }
        }
    }

    // Сливаем
    this.aDirectory = [];
    this.aFile = [];
    for (i = 0; i < aDirectory.length; i++) {
        this.aFile.push(aDirectory[i]);
    }
    for (i = 0; i < aFile.length; i++) {
        this.aFile.push(aFile[i]);
    }

    //
    if (this.szSort.length > 0) {
        this.sortBy(this.szSort, 1)
    } else
        this.output();

}
/**
 *   @desc Выводит текущий путь
 function output()
 */
InsertImage.prototype.output = function () {

    if (this.aPath[this.szPath] != null) {
        this.nCurrentItem = Math.round(this.aPath[this.szPath]);
    } else
        this.nCurrentItem = 0;

    view.drawPanel(this.aFile);
    view.setCurrent(this.nCurrentItem);

}
/**
 *   @desc Сортирует панель
 function sortBy()
 */
InsertImage.prototype.sortBy = function (szBy, nChange) {

    if (this.isProcessing()) return;
    // устанавливаем инверт
    var bInvert;
    if (nChange == null) {

        var bInvert = (this.szSort == szBy);
        if (bInvert) {
            view.setColumnTitle(szBy, 1);
            this.szSort = '-' + szBy;
        } else {
            view.setColumnTitle(szBy, 0);
            this.szSort = szBy;
        }
    } else {

        if (szBy[0] == '-') {
            szBy = szBy.substr(1, szBy.length - 1);
            bInvert = true;
        } else
            bInvert = false;
    }
    view.setColumnTitle(szBy, bInvert);
    // Сначала отделим каталоги от файлов


    var aDirectory = [];
    var aFile = [];

    for (key in this.aFile) {
        if (this.aFile[key]['is_directory'] == 1) {
            aDirectory.push(this.aFile[key]);
        } else {
            aFile.push(this.aFile[key]);
        }
    }

    // Сортируем файлы
    var i = 0;
    var j = 0;
    var tmp;
    var bFlag;

    for (i = 0; i < aFile.length - 1; i++) {
        for (j = i + 1; j < aFile.length; j++) {
            // Вычисляем
            if ((szBy == 'name') || (szBy == 'date') || (szBy == 'rights')) {
                value1 = aFile[i][szBy];
                value2 = aFile[j][szBy];
            }
            else {
                value1 = Math.round(aFile[i][szBy])
                value2 = Math.round(aFile[j][szBy])
            }
            // Вычисляем с учетом возможного инверта
            if (bInvert)
                bFlag = (value1 > value2);
            else
                bFlag = (value1 < value2);
            if (bFlag) {
                tmp = aFile[i];
                aFile[i] = aFile[j];
                aFile[j] = tmp;
            }
        }
    }
    // Сливаем
    this.aFile = [];
    for (i = 0; i < aDirectory.length; i++) {
        this.aFile.push(aDirectory[i]);
    }
    for (i = 0; i < aFile.length; i++) {
        this.aFile.push(aFile[i]);
    }
    // показываем
    this.output();
    //	this.inChangeFileInfo ({
    //	'path'  :   this.szPath,
    //	'aFile' :   this.aFile
    //});
}
/**
 *   @desc
 function inClosePopup ()
 */
InsertImage.prototype.inClosePopup = function () {
    window.setTimeout('ii.openDirectory(\'\',1);', 200);
    cms.popup.close();

    //getPathInfo(this.inChangeFileInfo,this,this.szPath);

}

function getKeyCodes() {

    help = {
        'help': {
            keyCode: 112,
            shiftKey: false,
            altKey: false,
            ctrlKey: true
        },
        'upload': {
            keyCode: 85,
            shiftKey: false,
            altKey: false,
            ctrlKey: true
        },
        'createdir': {
            keyCode: 118,
            shiftKey: false,
            altKey: false,
            ctrlKey: false
        },
        'onUp': {
            keyCode: 38,
            shiftKey: false,
            altKey: false,
            ctrlKey: false
        },
        'onDown': {
            keyCode: 40,
            shiftKey: false,
            altKey: false,
            ctrlKey: false
        },
        'onEnter': {
            keyCode: 13,
            shiftKey: false,
            altKey: false,
            ctrlKey: false
        },
        'onView': {
            keyCode: 32,
            shiftKey: false,
            altKey: false,
            ctrlKey: false
        },
        'onDelete': {
            keyCode: 46,
            shiftKey: false,
            altKey: false,
            ctrlKey: false
        },

        'onEdit': {
            keyCode: 13,
            shiftKey: false,
            altKey: false,
            ctrlKey: false
        },
        'onF2': {
            keyCode: 113,
            shiftKey: false,
            altKey: false,
            ctrlKey: true
        },
        'CtrlR': {
            keyCode: 82,
            shiftKey: false,
            altKey: false,
            ctrlKey: true
        }
    }

    return help;
}
