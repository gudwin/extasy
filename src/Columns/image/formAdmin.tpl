<?php
use \Extasy\CMS;
if ( ! $librariesLoaded ) {
	$design = CMSDesign::getInstance();
	$design->insertCSS( CMS::getResourcesUrl().'extasy/vendors/dropzone/css/basic.css' );
	$design->insertScript( CMS::getResourcesUrl().'extasy/vendors/dropzone/dropzone.js' );
}
$id = sprintf( '%sDropzone', $name );
$selector = 'input[name='.$name.']';
?>
<div id="<? print $name?>Form">
	<table width=100% border="1" cellpadding="1">
        <tr>
        	<td>
	        	<div id="<?php print $id?>" class="form">
					<input type="hidden" name="columnName" value="<?php print $name?>">
					<input type="hidden" name="action" value="upload">
					<input type="hidden" name="<? print $name?>" value="<?php print htmlspecialchars( $value );?>">

					<div class="message">
						Перетащите сюда свой файл или кликните по изображению
					</div>
				</div>
				
        	</td>
			<td class="td_main thumb">
				<? if (!empty($thumbnailSrc)):?>
					<img src="<?php print $thumbnailSrc?>" id="<?php print $name ?>Thumbnail" title="Image thumbnail"/>
				<? else:?>
					no thumbnail
				<? endif?>
			</td>
			<td>
				<h3>Варианты изображения</h3>
				<? include dirname( __FILE__ ) . '/resizes.tpl';?>
				<a href="#" id="<?php print $name?>Delete" class="delete">Удалить изображение</a>
			</td>

		</tr>
	</table>
</div>
<?php
$jQuerySelector = sprintf( '#%s', $id );
 
?>

<script type="text/javascript">
	jQuery( function ( $ ) {
		var requestData = cms.editDocument.getRequestVariables();
		var nameSelector = <?=json_encode( $selector );?>;
		requestData['action'] = 'upload';
		requestData['fieldName'] = <?=json_encode( $name)?>;
		// Upload functions 
		$(<?=json_encode( $jQuerySelector )?>).dropzone({ 
			url: <?=json_encode( httpHelper::getCurrentUrl() );?>,
			data : requestData,
			parallelUploads : 1,
			paramName : <?=json_encode( $name .'_file')?>,
			fallback : function () {
				var html = [
						'<input type="file" name="<?=$name?>_file"> <br>',
						'<input type="hidden" name="<?=$name?>" value="<?=htmlspecialchars( $value );?>">'
				]; 
				$(<?=json_encode( $jQuerySelector )?>).replaceWith( html.iplode(''));
			}
		}).addClass('dropzone');
		var dropzone = $( <?=json_encode( $jQuerySelector )?> ).data('dropzone');
		dropzone.on('success', function ( file, response  ) {	
			try {
				response = $.parseJSON( response );
			} catch ( e ) {
				console.log( 'Error catched during image upload request');
				console.log( e ) ;
				return ;
			}
			// Update image
			var html = '<img src="<%=thumbnail%>?rand=<%=rand%>" id="<?=$name?>Thumbnail" title="Image thumbnail">';
			html = tmpl( html, {
				thumbnail : response.imagePath,
				rand : Math.random()
			})
			$('#<?=$name?>Form .thumb').html( html );
			$('#<? print $name?>Form .resizes').replaceWith( response.resizes );
			$('input[name=<?=$name?>]').val( response.value );
		});
		// Delete function 
		$('#' + <?=json_encode( $name.'Delete' )?> ).click ( function ( e ) {
			e.preventDefault();
			cms.editDocument.changeField( <? print json_encode($name) ?>, 'clear',{}, function () {
				// clear field value
				$(nameSelector).val('');
				// remove image
				$('#<? print $name?>Form .td_main').empty().html('no thumbnail');
				// empty resizes
				$('#<? print $name?>Form .resizes tr:gt(0)').remove();
			} );
		});
		<? if ( !empty( $required)):
			$errorMsg = sprintf( 'Изображение "%s" должно быть заполнено', $title );
		?>

		$( nameSelector ).parents('form').on('submit', function ( e ) {
			var form = $(this);
			var empty = $( nameSelector ).val().length == 0;
			if ( empty ) {
					e.preventDefault();
					dtError( <?=json_encode( $errorMsg )?>);
			}
		})
		<? endif;?>
	});
</script>
