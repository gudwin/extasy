'use strict';
    var module = angular.module('AuditControllers')
    module.controller('EventsController', ['$scope', '$routeParams','MenuInfo',
        function ($scope, $routeParams, MenuInfo) {
            $scope.menu = MenuInfo;
            $scope.events = [];
            MenuInfo.activeRoute = 'events';

            api.request({
                methodName : 'Audit.Logs',
                onSuccess : function ( data ) {
                    $scope.events = data;
                    $scope.$digest();
                },
                onError : function () {
                    dtError( 'Ошибка при загрузке данных');
                }
            });
            $scope.setupPriority = function ( name, val ) {
                var isFalse = val == "false";
                var data = {
                    name : name,
                    priority : isFalse ? 0 : 1
                }
                api.request({
                    methodName : 'Audit.SetupPriority',
                    'data' : data,
                    onSuccess : function ( data ) {
                        dtAlert('Настройка для "'+ name +'" обновлена');
                    },
                    onError : function () {
                        dtError( 'Ошибка при установке данных');
                    }
                });
            };
            $scope.setupLogging = function ( name, val ) {
                var isFalse = val == "false";
                var data = {
                    name : name,
                    enable_logging : isFalse ? 0 : 1
                }

                api.request({
                    methodName : 'Audit.EditLog',
                    'data' : data,
                    onSuccess : function ( data ) {
                        dtAlert('Настройка для "'+ name +'" обновлена');
                    },
                    onError : function () {
                        dtError( 'Ошибка при установке данных');
                    }
                });
            }
        }]);
