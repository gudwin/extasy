<?
$id = $name . 'TextField';
$selector = '#' . $id ;

print '<textarea ';

if ( !empty( $style )) {
	print ' style="' . $style . '" ';
} else {
	print ' style="width:99%" rows=12 ';
}

print 'name="' . $name. '" ';
printf(' id="%s" ', $id);
if ( !empty( $class ) ) {
	print 'class="' . $class . '" ';
}
print '>';
if ( !empty( $value ) ) {
	print htmlspecialchars( $value );
}
print '</textarea>';
if ( !empty( $requiredField)):
	$errorMsg = sprintf( 'Поле "%s" должно быть заполнено', $title );

	?>

	<script type="text/javascript">
		(function () {
			var selector = <?=json_encode( $selector )?>;
			$( selector ).parents('form').on('submit', function ( e ) {
				var form = $(this);
				var empty = $( selector).val().length == 0;
				if ( empty ) {
					e.preventDefault();
					dtError( <?=json_encode( $errorMsg )?>);
				}

			})
		})();

	</script>
<?endif;?>