var KeyValueList = Ext.extend(Ext.grid.EditorGridPanel, {
	constructor : function (config) {
		if (!config.name) { throw ('KeyValueList config field `name` not defined'); }
		if (!config.values) { throw ('KeyValueList config field `values` not defined'); }
		if (!config.renderTo) { throw ('KeyValueList config field `renderTo` not defined'); }
		
		// Дополняем конфиг `родным конфигом для панели` полями
		config.columns = [{header : 'Ключ',dataIndex : 'key',width:200,editor : new Ext.form.TextField({allowBlank : false})},
		                  {header : 'Значение',dataIndex : 'value',width:400,editor : new Ext.form.TextField({allowBlank : true})}];
		config.store = new Ext.data.SimpleStore({
			fields : [{name : 'key',type:'string'},
			          {name : 'value',type : 'string'}],
			data   : config.values
		});
		config.width = 620;
		config.height = 400;
		config.selModel = new Ext.grid.RowSelectionModel();
		// Добавляем кнопки
		config.tbar = [{
			text : 'Добавить',
			handler : function () {
				var p = new Ext.data.Record({
					key : '',
					value : '',
				});
				var grid = this.ownerCt.ownerCt; 
				grid.stopEditing();
				grid.getStore().insert(0,p);
			}
		},{
			text : 'Удалить',
			handler : function () {
				var grid = this.ownerCt.ownerCt;
				if (grid.getSelectionModel().getSelected())
				{
					grid.stopEditing();
					grid.getStore().remove(grid.getSelectionModel().getSelected());
				}
			}
		}];
		
		// Вызываем конструктор
		KeyValueList.superclass.constructor.call(this,config);
		//
		
		this.writeValues();
		this.mon(this,'afteredit',this.writeValues,this);
		this.mon(this.getStore(),'remove',this.writeValues,this);
		
	},
	writeValues : function () {
		// Не сохраняем значения, если данный котрол не находится внутри <form>
		if ($(this.el.dom).parents('form').length == 0) { return ; }
		//

		var form = $(this.el.dom).parents('form');
		var self = this;
		form.find('input[type=hidden]').each(function () {
			var pattern = self.name + '\\[';
			var reg = new RegExp(pattern);
			if ($(this).attr('name').match(reg))
			{
				$(this).remove();
			}
		});
		
		for ($i = 0; $i < this.getStore().getCount();$i++) {
			var record = this.getStore().getAt($i);
			// Пропускаем пустые значения
			if (record.get('key').length == 0) { continue;};
			var key = $('<input/>');
			key.attr('type','hidden')
				.attr('name',this.name + '[' + record.get('key') +']')
				.attr('value',record.get('value'));
			$(this.el.dom).after(key);
			
		}

	}
});
