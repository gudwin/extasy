'use strict';
var AuditApplication = angular.module('AuditApplication', ['ngRoute', 'AuditControllers','ngSanitize']);
AuditApplication.config(['$routeProvider',
    function ($routeProvider) {
        $routeProvider.
            when('/records', {
                templateUrl: '/resources/extasy/Dashboard/administrate/audit/records.html',
                controller : 'RecordsController'
            }).
            when('/events', {
                templateUrl: '/resources/extasy/Dashboard/administrate/audit/events.html',
                controller : 'EventsController'
            }).
            when('/settings', {
                templateUrl: '/resources/extasy/Dashboard/administrate/audit/settings.html',
                controller : 'SettingsController'
            }).
            otherwise({
                redirectTo: '/records'
            });
    }]);

var AuditControllers = angular.module('AuditControllers', []);
AuditControllers.Menu = {
    'activeRoute': ''
}
AuditControllers.SearchRequest = {
    sort_by      : 'id',
    order        : 'desc',
    search_phrase: '',
    user         : '',
    page         : 0,
    limit        : 100,
    dateFrom     : '0000-00-00 00:00:00',
    dateTo       : '',
    searchRequested : false

}
AuditControllers.factory('MenuInfo', function () {
    return AuditControllers.Menu;
});
AuditControllers.factory('SearchRequest', function () {
    return AuditControllers.SearchRequest;
});

angular.element(document).ready(function () {
    angular.bootstrap($('.audit.bootstrap'), ['AuditApplication']);
});
