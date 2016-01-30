<?
$selector = sprintf('textarea[name=%s]', $name );
?>
		<textarea name="<?print $name?>" id="<?print $name?>" style="height:200px;width:99%"><?php print htmlspecialchars($content)?></textarea>
		<input type="button" class="CommonButton" value="Перейти в визуальный редактор"/>
		<script type="text/javascript">
		jQuery(function () {
			var $ = jQuery;
			var selector = <?=json_encode( $selector );?>;
			$(selector).next().click(function () {
				window.open(
					'/resources/extasy/htmlarea.php?control=htmlarea&method=showHTMLArea&textarea=<?php print $name?>&cssPath=',
					'_blank',
					'location=no,resizable=no,scrollbars=no,titlebar=no,toolbar=no,menubar=no,width=800,height=600');
				return false;
			});
			<? if ( !empty( $required)):
			$errorMsg = sprintf( 'Поле "%s" должно быть заполнено', $title );
			?>

			$( selector ).parents('form').on('submit', function ( e ) {
				var form = $(this);
				var empty = $( selector).val().length == 0;
				if ( empty ) {
					e.preventDefault();
					dtError( <?=json_encode( $errorMsg )?>);
				}

			})
			<? endif;?>
		});
		</script>