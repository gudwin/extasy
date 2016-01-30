jQuery.fn.imageControl = function (userOptions) {
	var $ = jQuery;
	var options = {
			path_to_fm	: '/extasy/Dashboard/fm/index.php',
			current    	: '',
			name       	: 'image',
			image	   	: {
				size     	: 0,
				width	 	: 0,
				height		: 0
			}
	};
	$.extend(options,userOptions);
	// Вставляем в нужный элемент 
	var html = [
			'<table width="100%" cellpadding="0" cellspacing="0">',
			'<tr>',
			'	<td width="90%" valign="top">',
			'		<img src="' + options.current +'" alt="">',
			'	</td>',
			'	<td valign="top">',
			'		<nobr><b> Размер</b> : ' + options.image.size + '</nobr> <br>',
			'		<nobr><b> Ширина</b> : ' + options.image.width + '</nobr> <br>',
			'		<nobr><b> Высота</b> : ' + options.image.height + '</nobr> <br>',
			'	</td>',
			'</tr>',
			'</table>',
			'<input type="text" name="' + options.name + '" value="' + options.current + '" style="width:300px;">',
			'<input type="button" value="Выбрать файл">'
		];

	this.html(html.join(''));
	var self = this;
	this.find('input[type=button]').click(function () {
		//config_editor_image = szId;
		fm_callback = function (url) {
			self.find('input[type=text]').val(url);
			self.find('img').attr('src',url);
		};
		window.open(options.path_to_fm,'_blank',',scrollbars=yes,width=700,height=500,left=0,top=0');
		return false;
	})
	return this;
}

