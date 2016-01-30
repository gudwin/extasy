<?
switch ($lang){
	case 'RUSSIAN':
		$lang = 'ru';
		break;
	case 'ENGLISH':
		$lang = 'en';
		break;
}
$aMessages = array(
	'Delete'    => _msg('Delete'),
	'Add'       => _msg('Add'),
	'Move Down' => _msg('Move Down'),
	'Move Up'   => _msg('Move Up'),
);
// Сохраняем размер массив (оптимизация типо)
$nSize = sizeof($aTableHeader) - 1;
?>
<script type="text/javascript">
Ext.onReady(function(){

    Ext.state.Manager.setProvider(new Ext.state.CookieProvider());
	<?
	// Обозначаем данные
	?>
    var <?=$szName?>_data = [
		<?
		$aResult = '';
		if (is_array($aData)) {
			foreach ($aData as $key=>$row) {
				foreach ($row as &$field) {
					$field = to_ajax_string($field);
				}
				$aResult[] = '['.implode(',',$row).']';
			}
			print implode(',',$aResult);

		}
		?>
    ];

    // create the data store
    <?=$szName?>_store = new Ext.data.SimpleStore({
        fields: [
<?/* Вставка описаний полей*/?>
			<?foreach ($aTableHeader as $key=>$row):?>
			{name:'field<?=$key?>',type:'string'}<?=($key != $nSize?',':'')?>

			<?endforeach?>
        ]
    });
<?/*Загрузка данных в хранилище*/?>
    <?=$szName?>_store.loadData(<?=$szName?>_data);
    var fm = Ext.form;
<?/*Объявление колонок грида*/?>
    var <?=$szName?>_cm = new Ext.grid.ColumnModel([
		<?foreach ($aTableHeader as $key=>$row):?>
		{
			header: <?=to_ajax_string($row)?>,
			dataIndex: 'field<?=$key?>',
			sortable: true,
			editor: new fm.TextField({
				allowBlank: true
			})
		}<?=($key != $nSize?',':'')?>
		<?endforeach?>
    ]);
    <?=$szName?>_cm.defaultSortable = true;

<?//Создаем Грид?>
	var <?=$szName?>_grid = new Ext.grid.EditorGridPanel({
		store             : <?=$szName?>_store,
		cm                : <?=$szName?>_cm,

		renderTo          : '<?=$szName?>_grid',
		autowidth         : true,
		width             : 600,
		height            : 400,
		title             : '<?=$szComment?>',
		frame             : true,
		clicksToEdit      : 1,
		selModel          : new Ext.grid.RowSelectionModel(),
		<?//Создаем тулбар?>
		tbar: [{
			<?//Кнопка добавить?>
			text: '<?=$aMessages['Add']?>',
			handler : function(){
				var p = new Ext.data.Record({
		<?foreach ($aTableHeader as $key=>$row):?>
					field<?=$key?>  : ''<?=($key != $nSize?',':'')?>
		<?endforeach?>
					});
				<?=$szName?>_grid.stopEditing();
				<?=$szName?>_store.insert(0, p);
				<?=$szName?>_grid.startEditing(0, 0);
			}

		},{
			<?//Кнопка удалить?>
			text: '<?=$aMessages['Delete']?>',
			handler : function(){
				<?=$szName?>_grid.stopEditing();
				//grid.getSelectionModel().getSelected()
				<?=$szName?>_store.remove(<?=$szName?>_grid.getSelectionModel().getSelected());
			}
		},{
			<?//Кнопка поднять наверх?>
			text: '<?=$aMessages['Move Up']?>',
			handler : function(){
				<?=$szName?>_grid.stopEditing();
				// Определяем индекс
				var oRecord = <?=$szName?>_grid.getSelectionModel().getSelected();
				var nIndex = <?=$szName?>_store.indexOf(oRecord);
				// Если больше нуля удаляем добавляем
				if (nIndex > 0) {
					<?=$szName?>_store.remove(oRecord);
					<?=$szName?>_store.insert(nIndex - 1,[oRecord]);
					oRecord.selectRow(nIndex);
				}
			}
		},{
			<?//Кнопка опустить вниз?>
			text: '<?=$aMessages['Move Down']?>',
			handler : function(){
				// Определяем индекс
				var oRecord = <?=$szName?>_grid.getSelectionModel().getSelected();
				var nIndex = <?=$szName?>_store.indexOf(oRecord);
				// Если больше нуля удаляем добавляем
				if (nIndex < <?=$szName?>_store.getCount() - 1) {
					<?=$szName?>_store.remove(oRecord);
					<?=$szName?>_store.insert(nIndex + 1,[oRecord]);
					oRecord.selectRow(nIndex);
				}
			}
		}]

	});
    <?=$szName?>_grid.getSelectionModel().selectFirstRow();
});
</script>
<div id="<?=$szName?>_hiddenField"></div>
<div id="<?=$szName?>_grid" class="noPadding"></div>
<script type="text/javascript">
function save_array_list() {
	createHidden('<?=$szName?>_hiddenField','<?php print $szName?>','');
	for ($i = 0; $i < <?=$szName?>_store.getCount();$i++) {
		var aRecord = <?=$szName?>_store.getAt($i);
		<?foreach ($aTableHeader as $key=>$row):?>
		createHidden(
			'<?=$szName?>_hiddenField',
			'<?=$szName?>[' + $i + '][<?='field'.$key?>]',
			aRecord.get('<?='field'.$key?>'));
		<?endforeach?>
	}
}
jQuery('#user_form').submit(save_array_list);
</script>