var SitemapSelectControl = Ext.extend(Ext.Panel, {
	constructor : function (config) {
		
		if (!config.name) { throw ('SitemapSelectControl config field `name` not defined'); }
		if (!config.values) { throw ('SitemapSelectControl config field `values` not defined'); }
		config.height = 460;
		config.width  = 620;
		config.title  = 'Выбор страниц';
		config.layout = 'absolute';
		
		config.items  = [
		                this.getSearchPanel(config),		               
		                this.getGridPanel(config)
		];
		config.renderTo = config.name;
		
		if (!config.searchUrl) {
			config.searchUrl = '/admin/sitemap/search.php';
		}
		
		SitemapSelectControl.superclass.constructor.call(this,config);
		
		this.writeValues();
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
	            {name: 'id', mapping: 'id'},
	            {name: 'full_url', mapping: 'full_url'}
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
					html : 'Введите имя/адрес страницы',
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
						var grid = self.getComponent('sitemapGrid');
						var store = grid.getStore();
						for (var i = 0; i < store.getCount();i++) {
							var oldRecord = store.getAt(i);
							if (record.get('id') == oldRecord.get('id')) { return;}
						}
						// Проверяем, что 
						var p = new Ext.data.Record({
							id : record.get('id'),
							name : record.get('name'),
							full_url : record.get('full_url'),
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
	getGridPanel : function (config) {
		
		var result = new Ext.grid.GridPanel({
			id    : 'sitemapGrid',

			x : 0,
			y : 28,
			width : 620,
			height: 400,
			columns : [{header : 'Имя страния',dataIndex : 'name',width:200},
			           {header : 'URL',dataIndex : 'full_url',width:400}
			],
			store : new Ext.data.SimpleStore({
				fields : [{name : 'id',type:'number'},
				          {name : 'name',type : 'string'},
				          {name : 'full_url',type : 'string'}],
				data   : config.values
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
			if ($(this).attr('name').substr(0,pattern.length))
			{
				$(this).remove();
			}
		});

		var grid = this.getComponent('sitemapGrid');
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