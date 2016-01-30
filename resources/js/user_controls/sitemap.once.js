jQuery.fn.sitemapOnce = function (userOptions) {
    var $ = jQuery;
    var self = this;
    var options = {
        name: 'test_name',
        urlInfo: {
            id: 0,
            name: '',
            full_url: ''
        },
        filter: []
    };
    var inputId = 'input_' + options.name;
    $.extend(options, userOptions);

    // Создаем разметку
    var html = [
        '<div class="ui-widget sitemap-once">',
        '<label for="' + inputId + '">Страница (имя или адрес)</label>',
        '<input id="' + inputId + '" type="text" value="">'];
    if (options.urlInfo != null) {
        html.push('<input type="hidden" name="' + options.name + '" value="' + options.urlInfo.id + '"/>');
    } else {
        html.push('<input type="hidden" name="' + options.name + '" value="0"/>');
    }

    html.push('<a href="#" class="clear" style="display:none">Сбросить</a>');
    html.push('<br/>');
    html.push('<div class="address">Полный адрес:<span><!-- --></span></div>');
    html.push('</div>');

    self.append(html.join(''));
    // Приватные методы
    var setupURLField = function (value) {
        self.find('div.address span').html(value);
    };
    var setupHiddenField = function (value) {
        self.find('input[name=' + options.name + ']').val(value);
    };
    var setupInputField = function (value) {
        $('#' + inputId).val(value);
    };

    // Отвечает за изменение
    var change = function (urlInfo) {
        // В поле автозаполнения вставляем title
        setupInputField(urlInfo.name);
        // В поле адреса вставляем значение
        setupURLField(urlInfo.full_url);
        // В поле hidden вставляем начальное
        setupHiddenField(urlInfo.id);
        // Отображаем ссылку скрытия
        if (urlInfo.id > 0) {
            self.find('a.clear').css('display', 'inline');
        } else {
            self.find('a.clear').css('display', 'none');
        }
    };

    // Подключаем автозаполнение
    $('#' + inputId).autocomplete({
        source: systemInfo.http_root + 'sitemap/search.php?filter=' + options.filter.toString(),
        select: function (event, ui) {
            // По выбору, всегда устанавливаем значение hidden-поля
            // Поле инпута, обновляется автоматически
            // Обновляем поле адреса
            change(ui.item);
        }
    });

    // Подключаем нажатие на кнопку "сбросить"
    self.find('a.clear').click(function () {
        var urlInfo = {
            id: 0,
            name: '',
            full_url: ''
        };
        change(urlInfo);
        return false;
    });
    //
    // Инициализация
    if (options.urlInfo != null) {
        change(options.urlInfo);
    }

    return this;
};