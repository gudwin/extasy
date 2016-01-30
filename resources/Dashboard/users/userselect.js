/**
 * Контрол выбора пользователя с автодополнением
 */

function UserSelect(initConfig) {
    this.config = {
        searchURL : cms.httpCPRoot + 'users/search',
        targetId : 'userSelect',
        currentValue : 0,
        currentTitle : '',
        onSelect : false
    }
    jQuery.extend(this.config,initConfig);
    // В данную переменную позже будет загружен jQuery объект с контролом

    this.targetObj = $('#' + this.config.targetId);


    // Создаем разметку
    this.createLayout();
    // Подключаем события и формируем вывод списка дополнения
    this.bindEvents();

}
UserSelect.prototype.createLayout = function () {
    var $ = jQuery;
    var html = [
        '<div class="user-select">',
        '<div clas="inputfield">',
        '<input type="text" class="title" name="' + this.config.targetId +'_text" value="' + this.config.currentTitle +'"/>',
        '<input type="hidden" class="value" name="' + this.config.targetId +'" value="'+ this.config.currentValue +'"/>',
        '<span rel="help_link"><img src="' + cms.httpKernelPath + 'img/help_ico.gif" />',
        '<div class="hideToolTip"> <h3>Поиск пользователя</h3> Чтобы найти необходимого пользователя, введите его email или логин и выберите его из списка результатов поиска. <br/> Если пользователь не установлен, то Вам будет отображена соотв. иконка</div>',
        '</span>',
        '</div>',
        '<div class="status not-found">',
        'Пользователь не установлен',
        '</div>',
        '</div>'
    ];

    this.targetObj.html(html.join("\r\n"));

    if (this.config.currentValue != 0) {
        this._setupStatus(this.config.currentValue);
    } else {
        this._setupStatus(0);
    }
    if (this.config.currentTitle != 0) {
        this.targetObj.find('.title').val(this.config.currentTitle);
    }
}
UserSelect.prototype.bindEvents = function () {
    var self = this;

    this.targetObj.find('.title').autocomplete({
        minLength: 2,
        source: self.config.searchURL,
        select : function (event,ui) {
            self._change(event,ui);
            return false;
        },
        focus : function (event,ui) {
            self.targetObj.find('.title').val( ui.item.login );
            return false;
        },
        change : function (event,ui) {
            self._change(event,ui);

        }
    }).data( "ui-autocomplete" )._renderItem = function( ul, item ) {

        return $( "<li></li>" )
            .data( "item.autocomplete", item )
            .append( "<a>" + item.login + " - " + item.email + "</a>" )
            .appendTo( ul );
    };


}
UserSelect.prototype._setupStatus = function (value) {

    if (value) {
        this.targetObj.find('.value').val(value);
        this.targetObj.find('.status').removeClass('not-found').addClass('found').html('Пользователь установлен');
    } else {
        this.targetObj.find('.value').val(0);
        this.targetObj.find('.status').addClass('not-found').removeClass('found').html('Пользователь не установлен');
    }
}
UserSelect.prototype._change = function (event, ui) {

    if (ui.item) {

        this.targetObj.find('.title').val(ui.item.login);
        this._setupStatus(ui.item.id);
        if (this.config.onSelect) {
            this.config.onSelect.call(self,ui.item);
        }
    } else {
        this._setupStatus(0);
    }
}