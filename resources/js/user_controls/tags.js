Tags_Control = function() {
	this.div_textarea = null;
	this.div_tags = null;
	this.inputName = false;
	this.aTags = [];
	this.szValue = '';
}
/**
*   -------------------------------------------------------------------------------------------
*   @desc Устанавливает имя, куда будут сохранены имена тегов
*   @return 
*   -------------------------------------------------------------------------------------------
*/
Tags_Control.prototype.setInputName = function ($szName) {
	this.inputName = $szName;
}
/**
*   -------------------------------------------------------------------------------------------
*   @desc Устанавливает данные для облака тегов
*   @return 
*   -------------------------------------------------------------------------------------------
*/
Tags_Control.prototype.setTags= function ($aData) {
	this.aTags = $aData;
}
/**
*   -------------------------------------------------------------------------------------------
*   @desc Устанавливает данные 
*   @return 
*   -------------------------------------------------------------------------------------------
*/
Tags_Control.prototype.setValue = function ($szValue) {
	this.szValue = $szValue;
}
/**
*   -------------------------------------------------------------------------------------------
*   @desc Отображает контрол в указанном элементе по ID
*   @return 
*   -------------------------------------------------------------------------------------------
*/
Tags_Control.prototype.displayIn = function ($szId) {
	// Определяем есть ли inputName
	if (!this.inputName) {
		alert('inputName not defined. Use setInputName. ');
		return;
	}
	if (!document.getElementById($szId)) {
		alert('Object with id="' + $szId + '" not found');
	}
	// Создаем объекты
	this.div_textarea = document.createElement('div');
	this.div_tags = document.createElement('div');
	// Устанавливаем стили
	this.div_textarea.className = 'tags_textarea';
	this.div_tags.className = 'tags_layer';
	// 
	// Заполняем слой с полем ввода
	jQuery(this.div_textarea).append('<textarea name="' + this.inputName + '" id="' + this.inputName + '"></textarea>');
	jQuery(this.div_textarea).children('textarea').val(this.szValue);
	
	//
	// Узнаем максимальную и минимальную величину
	var min = 9999999;
	var max = 0;
	for (var i = 0; i < this.aTags.length ; i++ ) {
		if (this.aTags[i]['count'] < min) {
			min = this.aTags[i]['count'];
		}
		if (this.aTags[i]['count'] > max) {
			max = this.aTags[i]['count'];
		}
	}
	//
	// Пересчитываем значения размеров
	var step = ((max - min) / 7 );
	// В данной переменной храним лог. значение: нашли ли вовремя рендеринга ссылку с маленьким размером (значит она была скрыта и может быть отображена только спец. ссылкой)
	var foundWithTinySize = false;
	for (var i = 0; i < this.aTags.length ; i++) {
		if (step == 0) {
			this.aTags[i]['size'] = 7;
		} else {
			this.aTags[i]['size'] = Math.round((this.aTags[i]['count'] - min) / step);
		}
		// добавляем в слой
		var link = document.createElement('a');
		link.innerHTML = this.aTags[i]['tag_name'];
		link.href = '#';
		link.className = 'tag-link size' + this.aTags[i]['size'];
		if (this.aTags[i]['size'] <= 1) {
			link.className += ' ExtasyDataBlock';
			foundWithTinySize = true;
		}
		var self = this;
		jQuery(link).click(function() {
			//
			// Вбить код вставки в поле ввода, если тег там не найден
			if (!self.tagExists(this.innerHTML)) {
				// Если длина больше нуля
				if (jQuery('#' + self.inputName).val().length > 0) {
					var new_value = jQuery('#' + self.inputName).val();
					new_value += ',' + this.innerHTML;
					jQuery('#' + self.inputName).val(new_value);
				} else {
					jQuery('#' + self.inputName).val(this.innerHTML);
				}
			}
			return false;
		});
		this.div_tags.appendChild(link);
		jQuery('#' + $szId).append(this.div_textarea).append(this.div_tags);
	}
	if (foundWithTinySize) {
		jQuery(this.div_tags).append('<div class="big"><a href="#" class="important displayTagsWithTinySize">Отобразить все теги (даже самые мелкие)</a></div>');
		jQuery('.displayTagsWithTinySize').click(function (e) {
			$('.tag-link.ExtasyDataBlock').removeClass('ExtasyDataBlock');
			return false;
		});
	}
	jQuery(this.div_tags).append('<div style="clear:both"><!-- --></div>');
	jQuery('#' + $szId).append(this.div_textarea)
	jQuery('#' + $szId).append(this.div_tags)
}

/**
*   -------------------------------------------------------------------------------------------
*   @desc 
*   @return 
*   -------------------------------------------------------------------------------------------
*/
Tags_Control.prototype.tagExists = function ($szTagName) {
	var szContent = jQuery('#' + this.inputName).val();
	var aSplit = szContent.split(',');
	for (var i =0; i < aSplit.length; i++) {
		if ($szTagName == aSplit[i]) {
			
			return true;
		}
	}
	return false;
}
/**
*   -------------------------------------------------------------------------------------------
*   @desc Обрезает пробелы с конца и начала строки
*   @return 
*   -------------------------------------------------------------------------------------------
*/
Tags_Control.prototype.trim = function(szString) {
	// Режем первые пробелы
	while (szString.charAt(0) == ' ') {
		szString = szString.substr(1);
	}
	// Режем последние пробелы
	while (szString.charAt(szString.length - 1) == ' ') {
		szString = szString.substring(0,-1);
	}
	return szString;

}