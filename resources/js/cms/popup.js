var cms;
if (!cms)
{
	alert('cms - library not found!')
} else {
	cms.popup = new Object();
	cms.popup.onClose = null
	cms.popup.onCloseObject = null;
	cms.popup.szPopupId = '';
	cms.popup.szPopupClass = '';
	/**
	*   -------------------------------------------------------------------------------------------
	*   @desc Выводит popup (с id) на экран. У popup-а могут быть заданы Заголовок, Ширина, Высота
	*   @return
	*   -------------------------------------------------------------------------------------------
	*/
	cms.popup.show = function (id,title,width,height) {
			if (cms.bModal)
			{
				// Выводим предупреждение : попап может быт только один
				alert('Закройте перед этим другие окна');
				return;
			}
			// Установка начальных данных
			if (!title) {
				title = 'Popup';
			}
			if (!width)
			{
				width = 400;
			}
			if (!height)
			{
				height = 300;
			}

			// Получаем элемент
			cms.bModal = true;
			var targetFrame = document.getElementById(id);
			if (!targetFrame) {
				alert('Объект с id="' + id + '" не найден');
				return;
			}

			// устанавилваем стили и классы
			this.szPopupId = id;
			this.szPopupClass = targetFrame.className;

			// генерируем HTML окна
			szHTML = '<table bgcolor="#EEEEEE" border=1 width="'+ width +'px" height=100% style="border-collapse:collapse"cellpadding=0 cellspacing=0><tr><td width="'+ (width - 10) + 'px;height:20px"><span id="popupOutputTitle">' + title + '</span></td><td width="10px" ><a href="#"id="popupWindowCloser" onclick="cms.popup.close()">[X]</a></td></tr>';
			szHTML += '<tr><td colspan="2" id="popupOutputCell" >';
			szHTML += '</td></tr></table>';

			// создаем объект
			var popup = document.createElement('div');
			popup.id = 'popupWindow';
			popup.innerHTML = szHTML;

			// создаем объект
			var popup = document.createElement('div');
			popup.id = 'popupWindow';
			popup.innerHTML = szHTML;
			// присваиваем стили и позиции
			popup.style.position = 'absolute';
			popup.style.cssText = 'position:absolute;';
			popup.style.width = width + 'px';
			popup.style.height = height + 'px';
			popup.style.marginLeft =  (Math.round(-width) / 2) + 'px';
			popup.style.marginTop = (Math.round(-height) / 2) + 'px';
			popup.style.left = '20%';
			popup.style.top = '50%';
			popup.style.zIndex = 502;
			popup.style.visibility = 'visible';


			// выводим
			document.body.appendChild(popup);
			// подключаем div
			obj = document.getElementById('popupOutputCell');

			targetFrame.className = targetFrame.className.replace('popupFrame','');
			obj.appendChild(targetFrame);
		},
		/**
		*   -------------------------------------------------------------------------------------------
		*   @desc Устанавливает функцию реагирующую на событие закрытие попапа
		*   @return
		*   -------------------------------------------------------------------------------------------
		*/
	cms.popup.setOnClose = function (functionName,object) {
			this.onClose = functionName;
			this.onCloseObject = object;
			controller.addToId('popupWindowCloser','onclick',';cms.popup.close();return false;');

		},
		/**
		*   -------------------------------------------------------------------------------------------
		*   @desc Устанавливает заголовок страницы
		*   @return
		*   -------------------------------------------------------------------------------------------
		*/
	cms.popup.setTitle = function (newTitle) {
			document.getElementById('popupOutputTitle').innerHTML = newTitle;

		},
		/**
		*   -------------------------------------------------------------------------------------------
		*   @desc Вызывает закрытие попапа
		*   @return
		*   -------------------------------------------------------------------------------------------
		*/
	cms.popup.close = function (close) {

			var bResult;
			// Если задана функция, которая будет вызываться по закрытию попапа, вызываем ее
			if (this.onClose) {

				bResult = this.onClose.call(this.onCloseObject);
				// Если функция возвращает false, то прерываем закрытие попапа

				if (!bResult) {
					return;
				}
			};
			// закрываем попап
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

}