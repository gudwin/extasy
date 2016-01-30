<script type="text/javascript">
	(function () {
		var selector = <?=json_encode( $selector )?>;
		$(selector).parents('form').on('submit', function (e) {
			var form = $(this);
			var empty = $(selector).val() == "0";
			if (empty) {
				e.preventDefault();
				dtError(<?=json_encode( $errorMsg )?>);
			}
		})
	})();

</script>