jQuery(function ($) {
    var searchModule = angular.module( 'DashboardSearch', [
        'ui.bootstrap',
        'extasyApi'
    ] );
    searchModule.controller('Search',['$scope','extasyApi','$q', function ($scope, $extasyApi, $q) {
        $scope.getSearchResults = function ( $viewValue ) {
            var deferred = $q.defer();
            return $extasyApi('dashboard.search',{request:$viewValue}).then( function ( response ) {
                return ( response.items );
            })

        }
    }]);
    angular.bootstrap(jQuery('#dashboardSearch').get(0), ['DashboardSearch']);
    var searchForm = $('form[role=search]');



});