/**
 * 
 */
jQuery( function ( $ ) {
	var constScriptUrl = './manage_document.php';
	/**
	 * Function interfaces, code will be below
	 *********************************************************************
	 */
	var getDocumentName = null;
	var getColumnName = null;
	var onDeleteColumn = null;
	/**
	 *********************************************************************
	 * Code 
	 */
	$('#tab_main .OpTable tr').hover( function () {
		var cell = $(this).find('td:eq(0)');
		var link1 = $($('<a/>').get(0));
		var link2 = link1.clone();
		var linkDeletion = link1.clone();
		// Setup data and mark as active
		$(this).data('columnName',cell.html())
			.addClass('active');
		link1.html('Переименовать').attr('class','rename_link').attr('href','#');
		link2.html('Дублировать').attr('class','double_link').attr('href','#');
		linkDeletion.html('[x]').attr('class','delete_link').attr('href','#');
		
		cell.append( link1 ).append( link2 ).append( linkDeletion) ;
		
		link1.click(function (e) {
			
			var newColumnName = prompt('Новое имя?',getColumnName())
			
			e.preventDefault();
			if (newColumnName === null ) {
				return ;
			}
			$.post(constScriptUrl,{ rename: getColumnName(), to : newColumnName, document: getDocumentName() }, function (response) {
				try {
					response = eval( '(' + response + ')');
				} catch ( e ) {
					response = {
							error : 1
					};
				}
				if ( response.error ) {
					alert('Rename failed' );
					console.log(response);
					return ;
				} else if (response.result)  {
					window.location.reload();
				} else {
					alert('Rename failed Unknown error on request to ' + constScriptUrl );
					console.log(response);
					return ;
				}
				 
			});
		});
		link2.click(function (e) {
			e.preventDefault();
			$.post(constScriptUrl,{ dublicate: columnName, document: getDocumentName() }, function (response) {
				try {
					response = eval( '(' + response + ')');
				} catch ( e ) {
					response = {
							error : 1
					};
				}
				if ( response.error ) {
					alert('Internal error. Incorrect respond from ' + constScriptUrl);
					console.log( response );
					return ;
				} else {
					window.location.reload();
				}
				 
			});
		});
		/**
		 * Click on delete function 
		 */
		 linkDeletion.click( onDeleteColumn );
	}, function () {
		$(this).find('td:eq(0) a').remove();
	});
	/**
	 * Deletes active column
	 */
	onDeleteColumn = function() {
		if ( confirm( 'Готов?' ) ) {
			// Ready ? 
			var form = $('<form method="post" action="./manage_document.php"/>');
			form.append('<input type="hidden" name="remove" value="1"/>')
				.append('<input type="hidden" name="field" value="' + getColumnName() + '"/>')
				.append('<input type="hidden" name="document" value="' + getDocumentName() + '"/>')
			// Steady!
			$('body').append(form);
			// Go!
			form.submit();
			return false;
		}
	}
	/**
	 * Current documentName 
	 */
	getDocumentName = function() {
		return $('input[name=document]').val();
	}
	/**
	 * Returns current column name
	 */
	getColumnName = function () {
		return $('#tab_main .OpTable tr.active').data('columnName');
	}
	
});
jQuery( function ( $ ) { 
	$('.OpTable').on('click','.edit_property', function () {
		
		//var values = link.attr('rel').split(';');
		
		
		// Replace with input
		var link = $(this);
		var input = $('<input type="text" class="autosized-field"/>');
		var font = '12px Arial';
		if ( link.hasClass('property')) {
			input.addClass('property');
		}
		input.autoSizeAsText(link.text(),font);
		input.attr('rel', link.attr('rel') );
		input.attr('old', link.text() );
		
		$(this).replaceWith( input );
		// On input blur -> change size of text in it
		input.keyup( function () {
			$(this).autoSizeAsText($(this).val(),font);
		}); 
		// On input blur -> send request and close input
		input.blur( function () {
			var me = $(this);
			var value = me.val();
			if (value.length == 0 ) {
				value = me.attr('old');
			}
			var data = me.attr('rel').split(';');
			var link = $('<a href="#"/>');
			link.addClass('edit_property')
					.attr('rel', me.attr('rel'))
					.html( value );
			if (me.hasClass('property')) {
				link.addClass('property');
			}
			me.replaceWith( link );
			
			var request =  {
					document 		: data[0],
					field			: data[1],
					property		: data[2]
					
			};
			if (link.hasClass('property')) {
				request.new_name = value;
			} else {
				request.value = value;
			}
			$.post('./manage_document.php',request,function ( response ) {
				if ( response != 'OK') { 
					alert( 'Error inside request');
					console.log( response);
					return ;
				};
				
			});
		});
		
	});
	
});
jQuery.fn.autoSizeAsText = function ( text, font ) {
	var div = jQuery('<div/>');
	div.css({
		font			: font,
		width			: 'auto',
		height			: 'auto',
		position		: 'absolute',
		left			: '-10000px',
		top				: '-10000px'
	});
	div.text( text );
	$('body').append(div);
	var width = div.width();

	this.val(text).width( (parseInt( width )) );
	div.remove();
	return this;
}