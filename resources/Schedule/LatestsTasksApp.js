'use strict';

var app = angular.module('latestTasksApp', ['ngRoute', 'extasyApi']);
app.constant('constants', {
    interval: 10000,
    statuses: appConstants.statuses
});
app.config(['$routeProvider',
    function ($routeProvider) {
        $routeProvider.
            when('/latests', {
                templateUrl: '/resources/extasy/Schedule/latests.html',
                controller: 'LatestTasks'
            }).
            when('/add', {
                templateUrl: '/resources/extasy/Schedule/add.html',
                controller: 'AddTask'
            }).
            when('/restart/:id', {
                templateUrl: '/resources/extasy/Schedule/restartTask.html',
                controller: 'RestartTask'
            }).otherwise({
                redirectTo: '/latests'
            });
    }]);


app.controller('LatestTasks', [
    '$scope',
    'extasyApi',
    'constants',
    '$rootScope',
    function ($scope, $api, constants, $rootScope) {
        app.refreshMenu();
        var reloadList = function () {
            $api('schedule.ServerStatus').then(function (response) {
                $scope.status = response;
            });
            $api('schedule.latests').then(function (response) {
                $scope.latests = response;
            })
        };
        var intervalId = window.setInterval(function () {
            reloadList();
        }, constants.interval);
        reloadList();

        var cleanupEventListener= $rootScope.$on('$locationChangeStart', function () {
            window.clearInterval(intervalId);
            cleanupEventListener.call( null );
        });
        $scope.toggleServer = function (flag) {
            $api('schedule.stopServer', { runningFlag: flag }).then(function () {
                $scope.status = flag;
            }, function () {
                dtError('Запуск/Остановка сервера не сработала. Внутренння ошибка');
            })
        }
        $scope.restartServer = function () {
            $api('schedule.restartServer').then( function () {
                dtAlert('Сервер перезагружен');
            }, function () {
                dtError('Ошибка во время перезагрузки сервер');
            })
        }

    }]);
app.controller('AddTask', ['$scope', 'extasyApi', function ($scope, $api) {
    app.refreshMenu();
    $scope.form = {
        'class': '',
        'hash': '',
        'actionDate': (new Date()).ymdhms()
    };
    $scope.add = function () {
        $api('schedule.add', $scope.form).then(function () {
            dtAlert('Задача создана');
            window.location = '#/latests';
        }, function () {
            dtError('Ошибка во время создания задачи');
        })
    }
    $scope.cancel = function () {
        window.location = '#/latests';
    }
}]);
app.controller('RestartTask', ['$scope', 'extasyApi', '$routeParams', function ($scope, $api, $routeParams) {
    app.refreshMenu();
    $scope.form = {
        id: $routeParams.id,
        actionDate: (new Date()).ymdhms()
    };
    $scope.back = function () {
        window.location = '#/latests';
    };
    $scope.cancel = function () {
        $api('schedule.cancel',$scope.form).then( function () {
            dtAlert('Задача отменена');
        }, function () {
            dtError('Ошибка при отмене задачи');
        })
    };
    $scope.submit = function () {
        $api('schedule.restart', $scope.form).then(function (id) {
            dtAlert('Задача создана');
            $scope.back();
        }, function () {
            dtError('Не смогли перезагрузить задачу. Внутренняя ошибка');
        })
    };
}]);
app.directive('statusDescription', ['constants', function (constants) {
    return {
        require: '?ngModel',
        link: function ($scope, element, attrs) {
            var detectValue = function (value) {
                value = parseInt( value );
                for (var key in constants.statuses) {
                    if (value == key) {
                        element.html(constants.statuses[ key ]);
                    }
                }
            }

            $scope.$watch(attrs['ngModel'], detectValue);
        }
    }
}]);
app.refreshMenu = function () {
    var hash = window.location.hash;
    $('.schedule-app .navbar .nav a').each(function () {
        if ($(this).attr('href') == hash) {
            $('.schedule-app .navbar .nav li').removeClass('active');
            $(this).parent().addClass('active');
        }
    })
}
app.directive('datetimeFormat', [function () {
    var currentValue = null;
    var currentConfirmValue = null;
    return {
        require: 'ngModel',
        link: function (scope, element, attrs, controller) {
            var validate = function ( value ) {
                if ( value ) {
                    controller.$setValidity('datetimeFormat', value.match(/^[0-9]{4}\-[0-9]{2}\-[0-9]{2} [0-9]{2}\:[0-9]{2}\:[0-9]{2}$/) != null);
                } else {
                    controller.$setValidity('datetimeFormat', false);
                }
                return value;
            };
            controller.$parsers.push(validate);
            controller.$formatters.push(validate);


        }
    }
}]);


Date.prototype.ymdhms = function () {
    var yyyy = this.getFullYear().toString();
    var mm = (this.getMonth() + 1).toString(); // getMonth() is zero-based
    var dd = this.getDate().toString();
    var minutes = this.getMinutes().toString();
    var hours = this.getHours().toString();
    var seconds = this.getSeconds().toString();
    return yyyy + '-' + (mm[1] ? mm : "0" + mm[0])
        + '-' + (dd[1] ? dd : "0" + dd[0])
        + ' ' + (hours[1] ? hours : "0" + hours[0])
        + ':' + (minutes[1] ? minutes : "0" + minutes[0])
        + ':' + (seconds[1] ? seconds : "0" + seconds[0]);
}

jQuery(function ($) {
    angular.bootstrap($('.schedule-app').get(0), ['latestTasksApp']);
});

