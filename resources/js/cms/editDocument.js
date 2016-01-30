/**
 * User: Gisma
 * Date: 04.03.13
 * Functions for document editing support
 */
cms.editDocument = {
    /**
     * Sends ajax request to change specific field in current editing document
     * @param fieldName
     * @param action string
     * @param data
     * @param successFunc callback function that will be executed
     * @return {*}
     */
    changeField         : function ( fieldName, action, data, successFunc  ) {
        var requestData, error ;
        var url = this.getActionUrl( );
	    var self = this;
	    var deferred = $.Deferred();
	    deferred.done( successFunc );
        this.checkEditDocumentFormExists();
	    data = this.prepareRequestData( fieldName, action, data );

        // create closure for save ajaxCall
        ( function ( deferred, fieldName ) {
            var ajaxQuery = jQuery.ajax( {
                url             : url,
                type            : 'POST',
                data            : data,
                dataType        : 'json',
                success         : function ( data ) {
                    deferred.resolve( data );
                },
                error           : function ( response ) {
                    self.log( 'Error catched during updating documentField field `' + fieldName + '`');
                    self.log( 'Response object:');
                    self.log( response );
                    deferred.reject( response );
                }
            });
        })( deferred, fieldName);
        return deferred;
    },
    getRequestVariables     : function () {
	    var result = {
	        ajaxCall                : true
	    };
		// check that sitemapPage is editing right now
	    var isSitemapDocument = $('#user_form').find('input[name=sitemapId]').length > 0;
	    // or regular document
	    var isRegularDocument = ( $('#user_form').find('input[name=typeName]').length > 0 )
	    // or hasManyDocument (must contain valid index value)
	    // that will work only with hasMany plugin
	    var isHasManyDocument = window.location.search.indexOf( 'parentIndex' ) > 0;
	    // Add data required for document detection
	    if (isSitemapDocument) {
		    result['sitemapId'] = $('#user_form').find('input[name=sitemapId]').val();
	    } else if ( isRegularDocument ) {
		    result['typeName'] = $('#user_form').find('input[name=typeName]').val();
		    result['id'] = $('#user_form').find('input[name=id]').val();
	    } else if ( isHasManyDocument) {
	        var request = window.location.search;
	        // extract parentIndex variable from GET-request
		    var parentIndex = ((request.substr( request.indexOf( 'parentIndex' ) ).split('&'))[0]).split('=',2)[1];
	        result['parentIndex'] = $('#user_form').find('input[name=parentIndex]').val();
		    result['id'] = $('#user_form').find('input[name=id]').val();
	    } else {
		    error = 'changeField failed - edit form doesn`t contains document identification data ( for example field `id`)';
		    this.log( error );
		    throw error;
	    }
	    return result;
    },
    // PROTECTED METHODS
	/**
	 * Used to add technical data that required on server-side
	 * @param action string
	 * @param data Object
	 * @return Object
	 */
    prepareRequestData         : function ( fieldName, action, data ) {
	    // Add variables for document controller
	    if ( "object" != typeof data ) {
		    error = ' changeField: `data` object is empty ';
		    self.log( error  );
		    throw error;
	    }
	    //
	    var result = this.getRequestVariables( );
		// add field name in request data
		result['fieldName'] = fieldName;
		result['action'] = action;
		result['data'] = data;

	    return result;
    },
	/**
	 * Detects if document management form present in a page. If not exists application will throw exception
	 */
    checkEditDocumentFormExists : function ( ) {
        var exists = jQuery('#user_form').length > 0;
        if ( !exists ) {
            var error = 'Form object #user_form not found';
            this.log( error );
            throw error;
        }

    },
	/**
	 * Detects url where ajax-requests will be sent
	 * @return {*}
	 */
    getActionUrl                : function () {
	    // get form url, if action empty than build url
	    url = $('#user_form').attr('action');
	    if ( "undefined" == typeof(url) ) {
	        throw "Form '#user_form' not found";
	    }
	    url = url.length > 0 ? url : (window.location.pathname + window.location.search);
	    return url;
    },
	/**
	 * Logs message in cosole
 	 * @param msg
	 */
    log                         : function ( msg ) {
        if ( "undefined" == typeof window.console ) {
            return ;
        }
        if ( typeof msg == "object") {
            console.log( msg );
        } else {
            console.log( 'cms.editDocument: ' + msg );
        }
    }
};