"use strict";
var passwordMod = angular.module('extasyPassword', [])
    .constant('passwordsConfig', {
        length: 8,
        confirmAttribute: 'confirmationModel'
    })
    .directive('extasyPassword', function (passwordsConfig) {
        var currentValue = null;
        var currentConfirmValue = null;
        return {
            require: 'ngModel',
            link: function (scope, element, attrs, controller) {
                if (!attrs[passwordsConfig.confirmAttribute]) {
                    throw "confirmAttribute must be defined";
                }
                scope[attrs['ngModel'] + 'Controller'] = controller;
                controller.$parsers.push(validate);
                controller.$formatters.push(validate);
                scope.$watch( attrs[ passwordsConfig.confirmAttribute ], function ( confirmValue ) {
                    currentConfirmValue = confirmValue;
                    controller.$setValidity('passwordSame', isSamePassword());
                });
                function validate( value) {
                    currentValue = value;
                    controller.$setValidity('passwordLength', isLengthCorrect());
                    controller.$setValidity('passwordAlphabet', isAllSymbolsPresent());
                    controller.$setValidity('passwordDuplicates', isNoSymbolDuplicates());
                    controller.$setValidity('passwordSame', isSamePassword());
                    return value;
                }
                function isSamePassword() {
                    return currentValue == currentConfirmValue;
                }

                function isAllSymbolsPresent() {
                    if ( 0 == currentValue.length) {
                        return true;
                    }
                    var regexps = [
                        /[a-zA-Z]/,
                        /[0-9]/,
                        /[\,\.\\\/\-\_\!\~\#\$\%\^\&\*\(\)\-\+\=\>\<\?\[\]\{\}\@]/
                    ]
                    for (var i = 0; i < regexps.length; i++) {
                        if (!regexps[i].test(currentValue)) {
                            return false;
                        }
                    }
                    return true;
                };
                function isLengthCorrect() {
                    if ( 0 == currentValue.length) {
                        return true;
                    }
                    return currentValue.length >= passwordsConfig.length;
                }

                function isNoSymbolDuplicates() {
                    for (var i = 0; i < currentValue.length; i++) {
                        var char = currentValue.charAt(i);
                        var pos = currentValue.indexOf(char);
                        var count = 0;
                        while (pos != -1) {
                            count++;
                            pos = currentValue.indexOf(char, pos + 1);
                        }
                        if (count > 2) {
                            return false;
                        }
                    }
                    return true;
                };
            }
        }
    });
