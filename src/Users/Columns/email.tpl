<input type="email" name="<?=$name?>" value="<?=$value?>" placeholder="example@email.com" style="width:99%"/>
<script type="text/javascript">
	jQuery( function () {
		var input = $('input[name=<?=$name?>]')
		input.parents('form').on('submit', function () {
			if (input.val().length == 0) {
				dtError('Заполните поле email')
				return false;
			}
		});
	})
</script>
<?php
