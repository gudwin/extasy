"use strict";

describe('Password', function () {
    var scope, $compile;
    var element;
    var passwordEl = null;
    beforeEach(module('extasyPassword'));

    beforeEach(inject(function ($rootScope, _$compile_) {
        scope = $rootScope;
        $compile = _$compile_;
        passwordEl = null;
        element = angular.element(
            '<div>' +
                '<input type="password" extasy-password="" confirmation-model="form.myConfirmPassword" ng-model="form.myPassword">' +
                '<input type="password" ng-model="form.myConfirmPassword">' +
                '</div>');
        scope.form = {
            myPassword: '',
            myConfirmPassword: ''
        };
    }));
    function createPassword() {
        $compile(element)(scope);
        scope.$digest();
        return element.find('[type=password]');
    }

    it('password`s length with at least 8 symbols required', function () {
        scope.form.myPassword = scope.form.myConfirmPassword = 'a?1';
        passwordEl = createPassword();
        expect(passwordEl).toHaveClass('ng-invalid-password-length');
    });
    it('alphabet, numbers and special symbols requires', function () {
        scope.form.myPassword = scope.form.myConfirmPassword = 'aabbccdd';
        passwordEl = createPassword();
        expect(passwordEl).toHaveClass('ng-invalid-password-alphabet');
    });
    it('any symbol can repeat only twice', function () {
        scope.form.myPassword = scope.form.myConfirmPassword = 'aaaPas1?';
        passwordEl = createPassword();

        expect(passwordEl).toHaveClass('ng-invalid-password-duplicates');
    });
    it('should test confirmation field', function () {
        scope.form.myPassword = 'val1dPas!';
        scope.form.myConfirmPassword = 'different';
        passwordEl = createPassword();
        expect(passwordEl).toHaveClass('ng-invalid-password-same');
    })
    it('valid when empty', function () {
        scope.form.myPassword = scope.form.myConfirmPassword = '';
        passwordEl = createPassword();
        expect(passwordEl).not.toHaveClass('ng-invalid');
    })
    it('mark valid password as valid', function () {
        scope.form.myPassword = scope.form.myConfirmPassword = 'val1dPas!';
        passwordEl = createPassword();
        expect(passwordEl).toHaveClass('ng-valid-password-length');
        expect(passwordEl).toHaveClass('ng-valid-password-alphabet');
        expect(passwordEl).toHaveClass('ng-valid-password-duplicates');
        expect(passwordEl).toHaveClass('ng-valid-password-same');
        expect(passwordEl).toHaveClass('ng-valid');

    });
    it('should mark valid after confirmPassword became same to password', function () {
        var fixture = 'val1dPas!';
        scope.form.myPassword = fixture;
        scope.form.myConfirmPassword = '';
        passwordEl = createPassword();
        expect(passwordEl).toHaveClass('ng-invalid-password-same');
        scope.form.myConfirmPassword = fixture;
        scope.$apply();
        expect(passwordEl).toHaveClass('ng-valid-password-same');
    });


});