<?
use \Extasy\CMS;
CMSDesign::insertScript( CMS::getResourcesUrl() . 'extasy/js/Password.js');

$constants=  array(
	'length' => \Extasy\Columns\Password::MIN_LENGTH,
	'forceInput' => $forceInput
)
?><div class="userPasswordForm" ng-controller="ChangeController">
	<p>Пароль пользователя нельзя просмотреть, можно только установить. Введите новые значения в поля <i>"Новый
			пароль"</i> и <i>"Подтверждение пароля"</i>, пароль будет обновлен после сохранения формы пользователями
	</p>

	<div>
		<label>Новый пароль </label>
		<input type="password" name="<?= $name ?>"
			   value=""
			   extasy-password=""
			   confirmation-model="confirm_password"
			   ng-model="password"
			   <?if (!empty( $forceInput)):?>required<?endif;?>
			>
	</div>
	<div>
		<label>Подтверждение пароля</label>
		<input type="password" name="<?= $name ?>" value="" ng-model="confirm_password">
	</div>
	<div>
		<label class="error" ng-show="<?=$name?>Controller.$error.required">Введите пароль</label>
		<label class="error" ng-show="<?=$name?>Controller.$error.passwordSame">Пароли должны совпадать</label>
		<label class="error" ng-show="<?=$name?>Controller.$error.passwordLength">Минимальная длина пароля <?=$constants['length']?> символов</label>
		<label class="error" ng-show="<?=$name?>Controller.$error.passwordAlphabet">В пароле должны быть представлены символы алфавита, цифры и спец. символы</label	>
		<label class="error" ng-show="<?=$name?>Controller.$error.passwordDuplicates">Пароль не может содержать один и тот же символ более двух раз</label>
	</div>
</div>
<script type="text/javascript">
	(function ($) {
		var name = <?=json_encode($name)?>;
		var constants = <?=json_encode( $constants )?>;
		var changePasswordApp = angular.module('changePasswordApp', ['extasyPassword']);
		var ChangeController = changePasswordApp.controller('ChangeController', ['$scope', function ($scope) {
			$scope.password = '';
			$scope.confirm_password = '';
		}]);
		changePasswordApp.isReady = function () {
			var el = $('.userPasswordForm .ng-invalid');
			var needToTest = constants.forceInput || scope.password.length > 0;

			return needToTest ? el.length == 0 : true;
		};
		$(function () {
			angular.bootstrap($('.userPasswordForm').get(0), ['changePasswordApp']);
			$('.userPasswordForm').parents('form').on('submit', function () {
				if (!changePasswordApp.isReady()) {
					dtError('Невозможно сохранить данные по пользователю, проверьте поле пароля')
					return false;
				}
			});
		})

	})(jQuery)
</script>
