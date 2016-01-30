/**
 * 
 */
jQuery(function () {
	var $ = jQuery;
	
	Ext.QuickTips.init();
    var ACLTree = new Ext.ux.tree.TreeGrid({
        title: 'Редактор прав',
        width: 800,
        height: 600,
        renderTo: 'actionLayout',
		animate : false,
        columns:[{
            header: 'Ключ',
            dataIndex: 'name',
            width: 230
        },{
            header: 'Подпись',
            dataIndex: 'title',
            width: 230
        },{
            header: 'Полный путь',
            dataIndex: 'fullPath',
            width: 460
        }
        ],
		tbar : [{
			text : 'Добавить ',
			handler : function () {
				if (ACLTree.getSelectionModel().getSelectedNode()) {
					var path = ACLTree.getSelectionModel().getSelectedNode().attributes.fullPath;
					var parentId = ACLTree.getSelectionModel().getSelectedNode().id;
				} else {
					path = '';
					parentId = 0;
				}
				var text = prompt('Введите имя ключа');
				
				if (text) {
					var title = prompt('Введите подпись к ключу');
					if (path.length > 0 ) {
						path = path + '/' + text;
					} else {
						path = text;
					}
					
					$.post('./acl.php',{
						method : 'create',
						path : path,
						title : title
						},function (newId) {
							var newNode = new Ext.tree.TreeNode({text : text});
							newNode.attributes = {
									id : newId,
									name : text,
									title : title,
									fullPath : path,
									type : 'string' // Bug fix *3005265
							};
							if (parentId ) {
								var parentNode = ACLTree.getNodeById(parentId);
								parentNode.appendChild(newNode);
								parentNode.expand(true);
							} else {
								ACLTree.getRootNode().appendChild(newNode);
							}
					});
				}
				
			}
		},{
			text : 'Удалить',
			handler : function () {
				
				if (ACLTree.getSelectionModel().getSelectedNode())
				{
					var path = ACLTree.getSelectionModel().getSelectedNode().attributes.fullPath;
					var id = ACLTree.getSelectionModel().getSelectedNode().id;
					if (confirm('Вы уверены что хотите удалить этот элемент'))
					{
						$.post('./acl.php',{
							method : 'remove',
							path : path
						},function () {
							ACLTree.getNodeById(id).remove();
						});
					}
					//

				}
			}
		}],
        dataUrl: './acl.php?json=1'
    });
});