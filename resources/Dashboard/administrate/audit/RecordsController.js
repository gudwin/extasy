'use strict';
    var module = angular.module('AuditControllers');
    module.controller('RecordsController', ['$scope', '$routeParams','MenuInfo','SearchRequest',
        function ($scope, $routeParams,MenuInfo, searchRequest) {
            MenuInfo.activeRoute = 'records';
            $scope.menu = MenuInfo;
            $scope.list = [];
            $scope.paging = {
                pageCount : 0,
                page : 0
            };
            $scope.searchRequest = searchRequest;
            $scope.range = function () {
                var start = Math.max( $scope.paging.page - 5, 0);
                var finish = Math.min( $scope.paging.page + 5, $scope.paging.pageCount - 1 );
                var result = [];
                for ( var i = start; i <= finish;i++ ) {
                    result.push( i );
                }
                return result;
            };
            $scope.setSortBy = function ( fieldName ) {
                if ( searchRequest['sort_by'] == fieldName ) {
                    searchRequest['order'] = searchRequest['order'] == 'desc' ? 'asc' : 'desc';
                } else {
                    searchRequest['sort_by'] = fieldName;
                    searchRequest['order'] = 'desc';
                }
                $scope.reload();
            }
            $scope.setPage = function ( page ) {
                searchRequest.page = page;
                $scope.paging.page = page;
                $scope.reload();

            }
            $scope.reload = function () {
                api.request({
                    methodName : 'Audit.Records',
                    data : searchRequest,
                    onSuccess : function (data) {
                        $(data.list).each( function () {
                            this.shortVariant = true;
                        })
                        $scope.list = data.list;

                        $scope.paging.page = parseInt( data.page );
                        $scope.paging.pageCount = Math.ceil( data.total / searchRequest.limit );
                        $scope.$digest();
                    },
                    onError : function () {
                        dtError('Ошибка загрузки данных');
                    }
                })
            }
            $scope.showExtended = function ( $row ) {
                $row.shortVariant = false;
            }
            $scope.$watch('searchRequest.searchRequested', function () {
                if ( searchRequest.searchRequested == true ) {
                    $scope.reload();
                    searchRequest.search_phrase = '';
                }
                searchRequest.searchRequested = false;
            })
            $scope.reload();
        }]);