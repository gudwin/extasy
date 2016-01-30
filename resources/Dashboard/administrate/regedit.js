
/*!
 * Ext JS Library 3.1.1
 * Copyright(c) 2006-2010 Ext JS, LLC
 * licensing@extjs.com
 * http://www.extjs.com/license
 */
var RegisterTree = null	;
function updateNode (node)
{
	jQuery.post('./regedit.php',{
		update : 1,
		id     : node.attributes.id?node.attributes.id:'',

		name   : node.attributes.name?node.attributes.name:'',
		comment : node.attributes.comment?node.attributes.comment:'',
		value : node.attributes.value?node.attributes.value:''
	},function (szCode) {
		
		
	});
}
/**
 * СОздает узел в БД
 */
function createNode(node,parent)
{
	jQuery.post('./regedit.php',{
		create : 1,
		parent : parent,
		name   : node.attributes.name?node.attributes.name:'',
		type   : node.attributes.type?node.attributes.type:'string',
		comment : node.attributes.comment?node.attributes.comment:'',
		value : node.attributes.value?node.attributes.value:''
	},function (szCode) {
		var nodeId = parseInt( szCode );
		if ( isNaN( nodeId ) ) {
			alert('Неверный ответ от сервера, возможна ошибка на серверной стороне');
			return ;
		}
		node.attributes['id'] = nodeId;
	});
}
function removeNode(node)
{

						jQuery.post('./regedit.php?',{
							delete : node.attributes['id']
							},function (szCode) {
								//
								if (szCode == 'Ok')
								{
									alert('Элемент удален')
								}
							});
						// Получаем объект по его id
						RegisterTree.getNodeById(node.id).remove();
}
var RegKeyEditWindow = Ext.extend(Ext.Window, {
	constructor : function (config) {
		config.layout = 'form';
		config.width = 400;
		config.height = 300;
		config.resizable = false;
		config.modal = true;
		// Если передается конкретный id 
		if (config.editId)
		{
			this.node = RegisterTree.getNodeById(config.editId);

			config.title = 'Редактирование ключа : ' + this.node.attributes['name'];
		}
		else if (config.parentId) 
		{
			// Значит создаем новый 
			config.title = 'Добавление ключа';
			this.parentNode = RegisterTree.getNodeById(config.parentId);
		}
		else
		{
			throw new Exception('Fuck!');
		}
		// Добавляем элементы
		config.defaultType = 'textfield';
		config.padding = '5';
		config.items = [{							// Имя ключа
			fieldLabel       : 'Имя ключа',
			name             : 'name',
			id               : 'name',
			width            : 250,
			value            : !this.node?'Новый ключ':this.node.attributes['name']
		},{											// Поле значения
			xtype : 'textarea',
			fieldLabel : 'Значение',
			id : 'value',
			name : 'value',
			width: 250,
			height : 100,
			value : !this.node?'':this.node.attributes['value']
		},{											// Поля коммента
			xtype : 'textarea',
			fieldLabel : 'Комментарий',
			name : 'comment',
			id : 'comment',
			width: 250,
			height : 100,
			style : 'width:100%',
			value : !this.node?'':this.node.attributes['comment']
		}];
		var self = this;
		config.buttons = [{
			text: 'Сохранить',
			handler : function () {
				if (self.node)
				{
					self.node.attributes['name'] = self.getComponent('name').getValue();
					self.node.attributes['comment'] = self.getComponent('comment').getValue();
					self.node.attributes['value'] = self.getComponent('value').getValue();

					var new_node = new Ext.tree.TreeNode({text : self.node.attributes['name']});
					new_node.attributes = self.node.attributes;

					self.node.parentNode.replaceChild(new_node,self.node);
					new_node.parentNode.expand(true);
					//
					updateNode(new_node);
					
				}
				else
				{
					var new_node = new Ext.tree.TreeNode({text : self.getComponent('name').getValue()});
					new_node.attributes = {
						name : self.getComponent('name').getValue(),
						comment : self.getComponent('comment').getValue(),
						value : self.getComponent('value').getValue(),
						type : 'string', // Bug fix *3005265
					};
					self.parentNode.appendChild(new_node);
					self.parentNode.expand(true);
					//
					createNode(new_node,self.parentNode.attributes['id']);
				}
				self.close();

			},
		},{
			text : 'Закрыть',
			handler : function () {
				self.close();
			}
		}]
		//
		RegKeyEditWindow.superclass.constructor.call(this,config);
	}
});
var RegBranchWindow = Ext.extend(Ext.Window, { 
	constructor : function (config) {
		config.layout = 'form';

		config.width = 400;
		config.height = 250;
		config.resizable = false;
		config.modal = true;
		// Если передается конкретный id 
		if (config.editId)
		{
			this.node = RegisterTree.getNodeById(config.editId);

			if (this.node.parentNode.attributes.parent == undefined)
			{
				alert('Вы не можете редактировать корневые разделы');
				return false;
			}
			this.node = RegisterTree.getNodeById(config.editId);
			config.title = 'Редактирование ветви : ' + this.node.attributes['name'];

		}
		else if (config.parentId) 
		{
			// Значит создаем новый 
			config.title = 'Добавление ветви';
			this.parentNode = RegisterTree.getNodeById(config.parentId);
		}
		else
		{
			throw new Exception('Fuck!');
		}
		// Добавляем элементы
		config.defaultType = 'textfield';
		config.padding = '5';
		var self = this;
		config.items = [{							// Имя ключа
			fieldLabel       : 'Имя ветви',
			name             : 'name',
			id               : 'name',
			width            : 250,
			enableKeyEvents  : true,
			value            : !this.node?'Новая ветвь':this.node.attributes['name'],
			listeners        : {
				keypress        : function (input,event) {
					if (event.getCharCode() == 13)
					{
						self.savebutton.fireEvent('click');
					}
				}
			}
		},{											// Поля коммента
			xtype : 'textarea',
			fieldLabel : 'Комментарий',
			name : 'comment',
			id : 'comment',
			width: 250,
			height : 100,
			style : 'width:100%',
			value : !this.node?'':this.node.attributes['comment']
		}];

		config.buttons = [{
			text: 'Сохранить',
			ref : '../savebutton',
			listeners : {
			click : function () {
				if (self.node)
				{
					self.node.attributes['name'] = self.getComponent('name').getValue();
					self.node.attributes['comment'] = self.getComponent('comment').getValue();
					self.node.setText(self.node.attributes['name']);

					//
					updateNode(self.node);
				}
				else
				{
					var new_node = new Ext.tree.TreeNode({text : self.getComponent('name').getValue(),leaf:false,expandable : true});
					new_node.attributes = {
						name : self.getComponent('name').getValue(),
						type : 'branch',
						comment : self.getComponent('comment').getValue(),
						value : ''
					};
					self.parentNode.appendChild(new_node);
					self.parentNode.expand(true);
					createNode(new_node,self.parentNode.attributes['id']);
				}
				self.close();

			}
			},
		},{
			text : 'Закрыть',
			handler : function () {
				self.close();
			}
		}]
		RegKeyEditWindow.superclass.constructor.call(this,config);
	}
});
Ext.onReady(function() {
    Ext.QuickTips.init();
	function getCurrentId() 
	{
		return RegisterTree.getSelectionModel().getSelectedNode().id;
	}
    RegisterTree = new Ext.ux.tree.TreeGrid({
        title: 'Редактор реестра',
        width: 800,
        height: 600,
        renderTo: 'regedit_layer',
		animate : false,
        columns:[{
            header: 'Ключ',
            dataIndex: 'name',
            width: 230
        },{
            header: 'Тип',
            width: 100,
            dataIndex: 'type',
            align: 'center',
        },{
            header: 'Значение',
            width: 150,
            dataIndex: 'value'
        },{
            header: 'Комментарий',
            width: 250,
            dataIndex: 'comment'
        }],
		tbar : [{
			text : 'Добавить ключ',
			handler : function () {
				nId = getCurrentId();
				if (nId)
				{
					if (RegisterTree.getNodeById(nId).attributes['type'] == 'branch')
					{
						var hWindow = new RegKeyEditWindow({parentId : nId});
						hWindow.show();
					}
					else
					{
						alert('Добавлять элемент можно только в ветви');
					}
					
				}
				else
				{
					alert('Вам нужно выбрать элемент куда вставлять');
				}

			}
		},{
			text : 'Добавить ветвь',
			handler : function () {
				nId = getCurrentId();
				if (nId)
				{
					if (RegisterTree.getNodeById(nId).attributes['type'] == 'branch')
					{
						var hWindow = new RegBranchWindow({parentId : nId});
						hWindow.show();
					}
					else
					{
						alert('Добавлять элемент можно только в ветви');
					}
					
				}
				else
				{
					alert('Вам нужно выбрать элемент куда вставлять');
				}
			}
		},{
			text : 'Удалить',
			handler : function () {
				var nId = getCurrentId()
				if (nId)
				{
					if (confirm('Вы уверены что хотите удалить этот элемент'))
					{
						removeNode(RegisterTree.getNodeById(nId));
					}
					//

				}
			}
		},{
			text : 'Редактировать',
			handler : function () {
				// Текущий индекс
				var nId = getCurrentId();
				// Объект окна
				var hWindow = null;
				if (nId)
				{
					if (RegisterTree.getNodeById(nId).attributes['type'] == 'branch')
					{
						// Вызов окна редактора узла
						hWindow = new RegBranchWindow({editId : nId});
					}
					else
					{	
						// Вызов окна редактора элемента
						hWindow = new RegKeyEditWindow({editId : nId});
					}
					// Вызов редактирующего окна
					
					hWindow.show();

				}
				else
				{
					alert('Вам нужно выбрать элемент куда вставлять');
				}
			}
		}],
		listeners : {
			'dblclick' : function (node) 
			{
				// 
				if (node.attributes['type'] == 'branch')
				{
					// Ничего не делаем
				}
				else
				{	
					// Вызов окна редактора элемента
					hWindow = new RegKeyEditWindow({editId : node.id	});
					// Вызов редактирующего окна
					hWindow.show();
				}
				
					
				
			}
		},

        dataUrl: './regedit.php?json=1'
    });

});