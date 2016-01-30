<div class="confirmationCodeForm">
	<div class="statusRow">
		<input type="radio" name="<?= $name ?>_checkbox" id="active<?= $name ?>" value="1" <? if ( empty( $value )):?>checked="checked"<?endif?>>
		<label for="active<?= $name ?>">Активный</label>
	</div>
	<div class="statusRow">
		<input type="radio" name="<?= $name ?>_checkbox" id="inactive<?= $name ?>" value="0" <? if ( !empty( $value )):?>checked="checked"<?endif?>>
		<label for="inactive<?= $name ?>">Неактивный</label>

	</div>
	<div class="activationCodeRow">
		<label for="<?= $name ?>">Код активации:</label>
		<input type="text" id="<?= $name ?>" name="<?= $name ?>" value="<?= $value ?>">
	</div>
	<div class="clear"><!-- --></div>

</div>
<script type="text/javascript">
	jQuery(function ($) {
		var setupStatus = function ( val ) {
			var input = $('#<?=$name?>');
			if ( val == 1 ) {
				input.val("");
			} else {
				input.val( (new Date()).getTime())
			}
		}
		$('#active<?=$name?>,#inactive<?=$name?>').click( function () {
			setupStatus( this.value );
		})
		
	});
</script>