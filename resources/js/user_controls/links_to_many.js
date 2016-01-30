var LinksToManyControl = Ext.extend(Ext.Panel, {
	constructor : function (config) {
		var self = this;
		if (!config.name) { throw ('LinksToManyControl config field `name` not defined'); }
		if (!config.values) { throw ('LinksToManyControl config field `values` not defined'); }
		// Значала загружаем значения
		var listData = [];
		
		config.height = 460;
		config.width  = 620;
		config.title  = 'Выбор страниц';
		config.layout = 'absolute';
		config.renderTo = config.name;
		this.config = config;
		if (!config.searchUrl) {
			config.searchUrl = '/admin/sitemap/search.php';
		}
		
		
		if (config.values.length > 0) {
			var request = {
				getTitle : true
			};
			$.each(config.values,function (key,value) {
				request['id[' + key +']'] = value;
			});
			// Тогда сначала загружаем данные
			$.post(config.searchUrl,request,function (result) {
				listData = eval( '(' + result + ')');
				config.items  = [
					              self.getSearchPanel(config),		               
					              self.getGridPanel(config,listData)
				];
				
				// Инициализируем конструктор (согласен, криво я сделал с двумя вызовами конструктора)
				LinksToManyControl.superclass.constructor.call(self,config);
				self.writeValues.call(self);
			});
		} else {
			config.items  = [
				              this.getSearchPanel(config),		               
				              this.getGridPanel(config)
			];
			LinksToManyControl.superclass.constructor.call(this,config);
			this.writeValues();
		}
		
		
		
	},
	getSearchPanel : function (config) {
		
		var store = new Ext.data.Store({
	        proxy: new Ext.data.ScriptTagProxy({
	            url: config.searchUrl
	        }),
	        reader: new Ext.data.JsonReader({
	            root: 'item',
	            totalProperty: 'totalcount',
	            id: 'id'
	        }, [
	            {name: 'name', mapping: 'name'},
	            {name: 'id', mapping: 'id'}
	        ])
	    });
			
	    var self = this;

	    // Custom rendering Template
	    var resultTpl = new Ext.XTemplate(
	        '<tpl for="."><div class="search-item" style="padding:10px">',
	            '<p style="padding-left: 20px; background-image: url(/resources/extasy/ext3/resources/images/default/shared/right-btn.gif); background-repeat: no-repeat; background-position: 0px 0px; height: 20px;margin-bottom:10px">{name}</p>',
	       '</div></tpl>'
	    );
	    
	    var result = new Ext.Panel({
			height : 28,
			width : 620,
			x: 0,
			y: 0,
			layout : 'absolute',
			items:[
				new Ext.Panel ({
					x : 0,
					y : 0,
					html : 'Введите имя',
					padding : 3
				}),
				new Ext.form.ComboBox({
					x : 156,
					y : 0,
					height : 28,
					tpl : resultTpl,
					id : 'plugin-search-combo',
					store: store,
					displayField:'name',
					valueFIeld : 'id',
					typeAhead: false,
					loadingText: 'Искать...',
					width: 230,
					pageSize:10,
					hideTrigger:true,
					minLength : 2,
					itemSelector: 'div.search-item',
					onSelect: function(record){ 
						// Добавляем в грид результатов
						var grid = self.getComponent(config.name + '_Grid');
						var store = grid.getStore();
						for (var i = 0; i < store.getCount();i++) {
							var oldRecord = store.getAt(i);
							if (record.get('id') == oldRecord.get('id')) { return;}
						}
						// Проверяем, что 
						var p = new Ext.data.Record({
							id : record.get('id'),
							name : record.get('name')
						}); 
						grid.stopEditing();
						grid.getStore().insert(0,p);
						
						self.writeValues();
					}
				})
				]
	    });
	    return result;
	},
	getGridPanel : function (config,values) {
		if (!values) {
			values = [];
		}
		
		var result = new Ext.grid.GridPanel({
			id    : config.name + '_Grid',

			x : 0,
			y : 28,
			width : 620,
			height: 400,
			columns : [{header : 'Имя',dataIndex : 'name',width:400}
			],
			store : new Ext.data.SimpleStore({
				fields : [{name : 'id',type:'number'},
				          {name : 'name',type : 'string'}],
				data   : values
			}),
			selModel : new Ext.grid.RowSelectionModel(),
			tbar 	 : [{
				text : 'Удалить',
				handler : function () {
					var grid = this.ownerCt.ownerCt;
					if (grid.getSelectionModel().getSelected())
					{
						grid.stopEditing();
						grid.getStore().remove(grid.getSelectionModel().getSelected());
					}
				}
			}]
		});
		result.mon(result.getStore(),'datachanged',this.writeValues,this);
		result.mon(result.getStore(),'add',this.writeValues,this);
		result.mon(result.getStore(),'remove',this.writeValues,this);
		
		return result;
	},
	writeValues : function () {

		// Не сохраняем значения, если данный котрол не находится внутри <form>
		if ($(this.el.dom).parents('form').length == 0) { return ; }
		//
		
		var form = $(this.el.dom).parents('form');
		var self = this;
		var pattern = self.name + '[';
		form.find('input[type=hidden]').each(function () {
			if ($(this).attr('name').substr(0,pattern.length) == pattern)
			{
				$(this).remove();
			}
		});
		var grid = this.getComponent(this.config.name + '_Grid');
		for ($i = 0; $i < grid.getStore().getCount();$i++) {
			var record = grid.getStore().getAt($i);
			
			var key = $('<input/>');
			key.attr('type','hidden')
				.attr('name',this.name + '[]')
				.attr('value',record.get('id'));
			$(this.el.dom).after(key);
			
		}

	}

});