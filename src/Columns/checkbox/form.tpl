<!-- BEGIN form -->
<input type="hidden" id="<?=$fieldname?>" name="<?=$fieldname?>" value="<?=(!empty($value)?$value:0)?>">
<input type="checkbox" id="<?=$fieldname?>_checkbox" onclick="document.getElementById('<?=$fieldname?>').value=(this.checked?<?=(!empty($default_value)?$default_value:'0')?>:0);" <?=(!empty($checked)?$checked:'')?>> 
&nbsp;<label for="<?=$fieldname?>_checkbox"> <?=$label?> </label>
<!-- END form -->