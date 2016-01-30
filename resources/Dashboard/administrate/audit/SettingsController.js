'use strict';
    var module = angular.module('AuditControllers')
    module.controller('SettingsController', ['$scope', '$routeParams','MenuInfo',
        function ($scope, $routeParams, MenuInfo) {
            $scope.menu = MenuInfo;
            MenuInfo.activeRoute = 'settings';
            $scope.settings = {
                notification_emails : '',
                maximumLogLength : ''
            };

            $scope.submit = function () {
                api.request( {
                    methodName : 'Audit.SetupSettings',
                    data : $scope.settings,
                    onSuccess : function ( data ) {
                        dtAlert('Данные обновлены');
                    },
                    onError : function () {
                        dtError('Ошибка при обновлении данных');
                    }
                })

            }
            $scope.clearHistory = function () {
                api.request( {
                    methodName : 'Audit.MarkEverythingRead',
                    onSuccess : function ( data ) {
                        dtAlert('Данные обновлены');
                    },
                    onError : function () {
                        dtError('Ошибка при обновлении данных');
                    }
                })
            };
            api.request( {
                methodName : 'Audit.GetSettings',
                data : {},
                onSuccess : function ( data ) {
                    $scope.settings = data ;
                    $scope.$digest();
                },
                onError : function () {
                    dtError('Ошибка при загрузке данных');
                }
            })
        }]);
