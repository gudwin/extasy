jQuery( function ($) {
    var timeout = 60000;
    var auditSelector = '.navButtons .audit';
    var updateFunc = function () {
        api.request({
            methodName : 'Audit.NewMessages',
            onSuccess : function ( response ) {
                response = parseInt( response );
                $('.navButtons .audit span').remove();
                if ( response ) {
                    $('.navButtons .audit a').append('<span class="badge">' + response +'</span>');
                } else {
                }
            }

        })
    };
    if ( $(auditSelector).length > 0 ) {
        updateFunc();
        window.setInterval( function () {
            updateFunc();
        }, timeout);
    }

});