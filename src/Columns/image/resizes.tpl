<table class="resizes" width="100%" border="1" cellpadding="1" style="margin-top:16px">
	<tr>
		<td width="40%">
			<b>Путь:</b>
		</td>
		<td width="20%">
			<b>Размер:</b>
		</td>
		<td width="40%">
			<b>Приближение:</b>
		</td>
	</tr>
<?php if ( !empty( $aList )):?>
	<? foreach ($aList as $key=>$row):?>
		<tr>
			<td class="td_main" style="padding:1px;">
				<?=$key?>
			</td>
			<td class="td_main" style="padding:1px;">
				<?=$row['size_x']?>:<?=$row['size_y']?>
			</td>
			<td class="td_main">
				<a href='<?=\Extasy\CMS::getDashboardWWWRoot()?>zoom.php?url=<?=urlencode( $row['url'] )?>'
				   onclick="window.open('<?=\Extasy\CMS::getDashboardWWWRoot()?>zoom.php?url=<?=urlencode( $row['url'])?>','_blank','location=no,resizable=no,scrollbars=yes,titlebar=no,toolbar=no,menubar=no,width=<?=( $row['size_x'] + 100 )?>,height=<?=( $row['size_y'] + 100 )?>');return false;"> <?=_msg('Приблизить');?> (<?=$row['basename']?>)</a>
			</td>
		</tr>
	<?endforeach;?>
<?php endif;?>
</table>