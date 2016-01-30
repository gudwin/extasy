Ext.onReady(function(){
	// Грузим данные
	jQuery.getJSON('type_info.php?json=1',{}, function(aData) {
		var aTabs = [];
		var template = new Ext.Template([
			"<div style='padding:10px;font-size:125%'>",
			"<h2>{name}</h2>",
			"<ul>" ,
				"<li>Table: <strong>{table}</strong></li>",
				"<li>Title: <strong>{title}</strong></li>",
			"</ul>",
			"<h3> Fields </h3>",
			"<table cellpadding='3'>",
			"<tr><th style='width:150px'><center>Field name</center></th><th><center>Type</center></th> <th><center>Additional</th></center></tr>",
			"{fields_table}",
			"</table>",
			"</div>"
		],{
			 disableFormats: true
		});
		var templateRow = new Ext.Template([
			"<tr><td style='color:#FFFFFF;background-color:#880000;font-size:150%'><center>{name}</center></td>",
			"<td><center>{type}</center>	</td>",
			"<td><center>{additional}</center></td>",
			"</tr><tr><td colspan='3'><hr/></td></tr>"
		]);

		for (var key in aData )
		{
			var row = aData[key];
			var szTitle = row['title']?row['title']:'';
			var szTable ;
			szTable = '';
			// Перебор переменных типа
			for (var field_name in row['fields'])
			{
				var field_info = row['fields'][field_name];
				var field_type = '';
				var field_additional = '';
				// Если данные не массив
				if (field_info instanceof Object)
				{
					
					for (var key2 in field_info)
					{
						if (key2 != 'type')
						{
							field_additional += key2 + ': ' + field_info[key2].toString() + '<br/>';
						}
						else
						{
							field_type = field_info[key2];
						}
					}
				}
				else
				{
					field_type = field_info;
				}
				szTable += templateRow.apply({
					name : field_name,
					type : field_type,
					additional : field_additional
				});
			}


			var szTabHTML = template.applyTemplate ({
				name : key,
				table : row['table'], 
				title : szTitle,
				fields_table : szTable
			});
			//
			aTabs.push({
				title : key,
				html : szTabHTML
			});
		}
		var tabs = new Ext.TabPanel({
			renderTo: 'type_info_layer',
			width:800,
			activeTab: 0,
			frame:true,
			defaults:{autoHeight: true},
			items: aTabs
		});

	});

});
