
// Обработка чекбоксов
jQuery(document).ready(function () {

	
	
});
// Кнопки сортировки
jQuery(document).ready(function () {
	//
	setupSelectAllCheckbox = function () {
		var html = '<input type="checkbox" id="selectAllCheckbox">';
		$('table.OpTable:eq(0) th:eq(0)').html(html);
		$('#selectAllCheckbox').click(function () {
			var checked = this.checked
			$('table.OpTable:eq(0) tr:even').each(function () {
				var checkbox = $(this).find('input[type=checkbox]:eq(0)');
				if (checkbox.length > 0) {
					checkbox.get(0).checked = checked;
				}
			})
		});
	}
	jQuery('#user_form').on('click','a.order_move_up',function () {
		szUrl = window.location.protocol 
			+ '//' + window.location.host
			+ window.location.pathname;
		// 
		var aResult = window.location.search.toString().match(/page=([0-9]+)/);
		var nPage = (aResult != null?aResult[1]:0);
		var parentId = $('input[name=parentId]').val();
		
		jQuery.post(szUrl,{
			id : jQuery(this).attr('rel'),
			page : nPage,
			parent	: parentId,
			move_up : 1
			
		},function (szCode) {
			jQuery('div.Form div.OpList').remove();
			jQuery('div.Form').prepend(szCode);
			setupSelectAllCheckbox();
		});
		return false;
	});
	jQuery('#user_form').on('click','a.order_move_bottom',function () {
		szUrl = window.location.protocol 
			+ '//' + window.location.host
			+ window.location.pathname;
		// 
		var aResult = window.location.search.toString().match(/page=([0-9]+)/);
		var nPage = (aResult != null?aResult[1]:0);
		var parentId = $('input[name=parentId]').val();
		jQuery.post(szUrl,{
			id : jQuery(this).attr('rel'),
			page : nPage,
			parent	: parentId,
			move_down : 1
			
		},function (szCode) {
			jQuery('div.Form div.OpList').remove();
			jQuery('div.Form').prepend(szCode);
			setupSelectAllCheckbox();
		})
		return false;
	});
});
jQuery.requestGet = function () {
		var szSearch = window.location.search.toString();
		if (szSearch.length == 0)
		{
			return {};
		}
		var aItem = szSearch.march(/([^=]+)\=([^&^$])/g);
		alert(aItem.length)
	
};