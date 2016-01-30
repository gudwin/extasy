jQuery(function () {
	$('.OpTable tbody tr:gt(0)').each(function () {
		$('td:gt(0)',$(this)).each(function () {
			var text = $(this).html();
			var link = $('<a/>').attr('href','#').addClass('selectRadio').html(text);
			$(this).html(link);
		});
	});;
	$('.selectRadio').click(function () {
		var tr = $(this).parent().parent();
		var radio = $('td:first input',tr);
		radio.get(0).checked = true;
		return false;
	});
});