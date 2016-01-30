var api = {
    apiUrl : '/api/',
    request : function ( config ) {
        var defaultConfig = {
            methodName : '',
            data : {},
            onSuccess : null,
            onError : null,
            onProgress : null
        }
        config = jQuery.extend(defaultConfig, config );
        var formData = new FormData();
        var oReq = new XMLHttpRequest();
        // fill form data
        formData.append('method', config.methodName);
        var storeFormData = function ( prefix, data ) {
            var newPrefix
            if ( data instanceof Array ) {
                for ( var i = 0; i < data.length; i++  ){
                    var newPrefix = prefix + '[' + i + ']'
                    storeFormData( newPrefix, data[i]);
                }
            } else if (data instanceof File ) {
                formData.append( prefix, data)
            } else if ( data instanceof Object ) {
                for ( var i in data ) {
                    newPrefix= prefix + '[' + i + ']';
                    storeFormData( newPrefix, data[i])
                }
            } else {
                formData.append( prefix, data)
            }

        }
        storeFormData('data', config.data);
        //
        oReq.open("POST", this.apiUrl);
        oReq.addEventListener('load', function () {
            try {
                var response = $.parseJSON(this.responseText);
                if ( response && (response instanceof Object)) {
                    if ( "undefined" != typeof response.error ) {
                        if ( "function" == typeof config.onError ) {
                            config.onError.call( null, response );
                            return ;
                        }
                    }
                }
                if ( "function" == typeof config.onSuccess ) {
                    config.onSuccess.call( null, response );
                }

            } catch (e) {
                if (window.console) {
                    console.log(this.responseText);
                    console.log(e);
                }
                dtError('Внутренняя ошибка, извините');
            }
        });
        if ( oReq.upload ) {
            oReq.upload.addEventListener('progress', function ( event ) {
                if ( config.onProgress ) {
                    var howfar = Math.round((event.loaded / event.total) * 100);
                    config.onProgress.call( null, howfar );
                }
            });
        }

        oReq.addEventListener('error', function () {
            if ( ( "undefined" != config.onError ) && ( config.onError != null ) )  {
                config.onError.call( null, {
                    error : 'Error during executing HTTP request'
                }  );
            }
        });
        oReq.addEventListener('abort', function () {
            if ( "undefined" != onError ) {
                config.onError.call( null, {
                    error : 'HTTP request aborted'
                }  );
            }
        });
        oReq.send(formData);

        /**return $.ajax( {
            url : this.apiUrl,
            type : 'POST',
            data : {
                method : methodName,
                data : data
            },
            dataType : 'json',
            success : function ( response ) {
                console.log( response );
                if ( response && (response instanceof Object)) {
                    if ( "undefined" != typeof response.error ) {
                        if ( "function" == typeof onError ) {
                            onError.call( null, response );
                            return ;
                        }
                    }
                }
                if ( "function" == typeof onSuccess ) {
                    onSuccess.call( null, response );
                }
            },
            error : function () {
                if ( "undefined" != onError ) {
                    onError.call( null, {
                        error : 'Error during executing HTTP request'
                    }  );
                }
            }
        })*/
    }
};
