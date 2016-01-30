<select name=<?=$name?>>
<option value="0" <?=(empty($value)?'selected':'')?> > Пусто </option>
<? foreach ($aList as $key=>$row):?>
<option value="<?=$row['id']?>" <?=(!empty($row['selected'])?'selected':'')?>> <?=$row['name']?> </option>
<? endforeach?>
</select>