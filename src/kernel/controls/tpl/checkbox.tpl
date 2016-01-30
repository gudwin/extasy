
	<input type="checkbox" id="<?print $id?>" <?php print (!empty( $checked) ? 'checked="checked"' : '')?> value="1">
	<input type="hidden" name="<?php print $name?>" id="<?php print $id?>_hidden" value="<?php print (!empty( $checked) ? $value: '0')?>"/>
	<script type="text/javascript">
	jQuery(function () {
		var $ = jQuery;
		$('#<?php print $id?>').click(function () {
			if (this.checked) {
				$('#<?php print $id?>_hidden').val(<?php print json_encode($value)?>);
			} else {
				$('#<?php print $id?>_hidden').val('0');
			}
		});
	});
	</script>
<?php if (!empty($labelTitle)):?>
<label for="<?php print $id?>"><?php print $labelTitle?></label>
<?php endif;?>