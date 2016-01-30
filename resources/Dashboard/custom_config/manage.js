jQuery(function () {
	var $ = jQuery;
	var xtypeSelectSelector = '#tab_add select[name=xtype]'; 
	$(xtypeSelectSelector).after('<div class="additional_config"><!-- --></div>');
	$(xtypeSelectSelector).change(function () {
		callAdditionalConfigForm();
	});
	// Создаем функцию и тут же вызываем её
	var callAdditionalConfigForm = function () {
		var xtype = $('#tab_add select[name=xtype]').val();
		$.post('./manage',{
			getAdminForm : 1,
			xtype : xtype
		},function (resultHtml) {
			$(xtypeSelectSelector).next().html(resultHtml);
		});
	};
	callAdditionalConfigForm();
	
});
