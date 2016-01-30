'use strict';

angular.module('extasyApi',[]).factory('extasyApi',['$rootScope','$q', function ($rootScope, $q) {
    return function ( methodName, data ) {
        var deferred = $q.defer();
        api.request({
            methodName : methodName,
            data : data,
            onSuccess : function (response) {
                $rootScope.$evalAsync(function () {
                    deferred.resolve( response );
                })

            },
            onError : function ( response ) {
                $rootScope.$evalAsync( function () {
                    deferred.reject( response );
                });
            }
        });
        return deferred.promise;
    }
}]);