jQuery( function ( $ ) {
	$('#testListTable').delegate('.moreParams', 'click', function () {
		var requestInfo = $( this ).parents( '.requestInfo');
		var iconLink = requestInfo.find('.moreParams:last');
		var params = requestInfo.find('.fullParams');
		if ( params.is(':visible') ) {
			params.slideUp( );
			iconLink.removeClass('ui-icon-zoomout').addClass('ui-icon-zoomin');
		} else {
			params.slideDown( );
			iconLink.removeClass('ui-icon-zoomin').addClass('ui-icon-zoomout');
		}
	});
	$('#extasyTestLauncherSelectAll').click( function ( e ) {
		
		e.preventDefault( );
		var checked = $(this).data('checked');

		checked = checked ? false : true;
		try {
			$("#testListTable .testSelector").each( function () {
				this.checked = checked;
			});
			if ( checked ) {
				//$("#testListTable .testSelector").attr("checked","checked"); // ������������� ������� �� ��� checkbox-�
			} else {
				//$("#testListTable .testSelector").removeAttr('checked');
			}
		} catch ( e ) {
			console.log( e );
		}
		$(this).data('checked', checked );
	});
	/**
	 * Open new browser window with link
	 */
	$('#testListTable .targetUrl').click( function ( e ) {
		// check if current url post
		var tr = $(this).parents('tr');
		var method = tr.find('.requestInfo .method').html();
		var isPost = "POST" == method;
		var fillForm = function ( formObj, params, path ) {
			for ( var key in params ) {
				var fieldName = path.length > 0 ? ( path + '[' + key + ']') : key;
				if ( params[key] instanceof Object ) {
					fillForm( formObj, params[ key ], fieldName  );
				} else {
					var input = $('<input/>').attr({
						type		: 'hidden',
						name		: fieldName,
						value		: params[key]
					});
					form.append( input );
				}
			}
		}
		if ( isPost ) {
			e.preventDefault();
			// than build form
			var form = $('<form/>').attr({
					target		: '_blank',
					method		: 'POST',
					action		: tr.find('.targetUrl').attr('href')
			});
			
			var params = eval(  '(' + tr.find('.fullParams pre').html() + ')');
			// append post data			
			fillForm ( form , params,''  )
			$('body').append( form );
			// submit form
			form.submit( );
		} else {
			// else 
			// default befaviour
			
		}
	});
	$('.testMe').on( 'click', function ( e ) {
		e.preventDefault();
		testUrl( $(this).parents('tr:first') , true);
	});
	$('#launchTests').click( function ( ) {
        var force = 0;
        if ( $('.testSelector:checked').length == 0) {
            force = 1;
        } else {
            force = 0;
        }

		var currentTest = $('.testSelector:first').parents( 'tr:first');
		var testDeferred = testUrl( currentTest, force );;
		var onTestFinish = function ( ) {
			var nextTr = currentTest.next( 'tr' );
			
			if ( nextTr.length > 0 ) {
				currentTest = nextTr;
				testDeferred = testUrl( currentTest, force );
				testDeferred.always( onTestFinish );
			}
		}
		testDeferred.always( onTestFinish );
        return false;
	});
	
	/**
	 * ���������� ����� ������������
	 */
	var testUrl = function ( tr, force  ) {
		return ( function ( tr ) {
			var result = null;
            var oldHtml = tr.find( '.testResult').html();

			tr.find( '.testResult').html('<div class="testProcessing"><!--  --></div>');

			var checkbox = tr.find( '.testSelector').get( 0 );
			if (( checkbox.checked ) || force )  {
				result =  $.ajax({
					url				: './index.php',
					type			: 'POST',
					data			: {
						testId			: tr.find('.testSelector').val( )
					},
					dataType		: 'json',
					success			: function ( resultData) {
						tr.find('.testDate').html( resultData.lastTestDate );
						tr.find('.testResult').html( resultData.testResult );
					},
					error			: function ( ) {
						tr.find('.testProcessing').replaceWith( '<div class="error">error during test execution on server side</div>');
					}
				});
			} else {
                tr.find( '.testResult').html( oldHtml );
				result = $.Deferred();
				result.resolve();
			}
			return result;
		}) (tr);		
		
	} 
	
});