jQuery( function () {
	$('#quickAddForm').submit( function ( e ) {
		e.preventDefault();
		var level = parseInt( $('input[name=level]').val() ) ;

		if ( level > 0 ) {
			$.ajax({
				url     : './quick_add',
				method  : 'POST',
				dataType: 'json',
				data    : {
					level       :  level
				},
				success : function ( data ) {
					var textarea = $('textarea[name=urls]');
					textarea.val( textarea.val() + "\r\n" + data.join("\r\n") );
				},
				error   : function ( ) {
					alert('Error happens during ajax-request execution')
				}
			})
		}
		return false;
	})
});