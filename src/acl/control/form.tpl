<?php
use \Extasy\acl\control\Helper;
if ( empty( $fullGrantList ) ) {
	print  'Список прав пуст.';
	return;
}
?>
<div class="grant-list">
<?
print Helper::drawRecursive( $name, $fullGrantList, $grantList,0);
?>
</div>
<script type="text/javascript">
	jQuery( function () {
		$('.grant-list .checkbox').hover( function () {
			$(this).append('<a href="#">Применить выделение</a>')
		}, function () {
			$(this).find('a').remove();
		})
		$('.grant-list').on('click','a', function ( e ) {
			var value = $(this).parent().find('input').get(0).checked ? 1 : 0;

			$(this).parent().next().find('input').each( function () {
				this.checked = value;
				$(this).next().val( value);
			});
			e.preventDefault();
		});
		$('.grant-list').parents('form').on('submit', function ( e ) {
			var form = $(this);
			var empty = true;
			$('.grant-list input[type=checkbox]').each( function () {
				if ( this.checked) {

//					var input = $(this).nextAll('input');
//					var html = '<input type="hidden" name="'+ input.val() + '" value="1"/>';
//					form.append( html );

					empty = false ;
				}
			});
			if ( empty ) {
				e.preventDefault();
				dtError('Роль пользователя не задана');
			}

		})
	})
</script>