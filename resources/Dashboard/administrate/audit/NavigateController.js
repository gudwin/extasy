'use strict';
var module = angular.module('AuditControllers')
module.controller('NavigateController', ['$scope', 'MenuInfo','$rootScope','SearchRequest',
    function ($scope, MenuInfo, $rootScope, searchRequest) {
        $scope.menu = MenuInfo;
        $scope.searchRequest = searchRequest;
        $scope.onSearch = function ( ) {
            window.location.hash = '#/records';
            searchRequest.searchRequested = true;
        }
    }]);