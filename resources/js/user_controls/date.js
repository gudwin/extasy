
dateControl = function (config) {
	// Создаем панель Ext.js, в нее кладем контрол даты
	var panel = new Ext.Panel({
		renderTo 	: 'dateControl' + config.name,
		width    	: 200,
		items 		: [new Ext.form.DateField({
			name 		: config.name,
			value 		: config.value,
			allowBlank 	: false,
			format		: 'Y-m-d',
			width		: 200
		})]
	});
}