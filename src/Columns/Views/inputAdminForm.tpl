<?

$id = htmlspecialchars( $name ) . 'InputField';
$selector = '#' . $id;
print '<input style="width:99%" type=input name="' . htmlspecialchars( $name ). '" ';

print 'id="'. $id . '" ';

print 'value="' . htmlspecialchars( $value) . '" ';

if ( !empty($formEdit ) ) {
	print $formEdit;
}
print '>';
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