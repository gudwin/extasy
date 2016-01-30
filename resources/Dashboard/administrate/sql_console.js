jQuery(function() {
	var $ = jQuery;
	$('#sql_textarea').focus();
	$('#sql_request').submit(function () {
		$.post('./sql.php',{
			sql : $('#sql_textarea').val()
		},function (data) {
			data = eval('(' + data + ')');
			
			$('#sql_error').replaceWith(data.error)
			// Вывод таблицы результатов
			$('#sql_results').replaceWith(data.results);
			// Замена блока логов
			$('#sql_log').replaceWith(data.sql_log);
			$('#sql_textarea').focus();
		});
		return false;
	});
	$('.LayoutCenter').on('click','.sql_request_append',function () {
		$('#sql_textarea').val($(this).prev().html());
		return false;
	});
})
