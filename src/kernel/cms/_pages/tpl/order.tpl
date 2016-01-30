<?
$strings = CMS_Strings::getInstance();
$design = CMSDesign::GetInstance();

$design->begin($aBegin,$szTitle);
$design->documentBegin();
	$design->header($szTitle);
	$design->formBegin();
	$design->tableBegin();
?>
<script>
        function before_submit() {
                var rows = document.getElementById('rows');
                var order_value = document.getElementById('order_value');
                order_value.value = '';
                for (i =0; i < rows.options.length; i++) {
                     order_value.value += "\n" + rows.options.item(i).value;
                }

        }
        function reset_list() {

                 var rows = document.getElementById('rows');
                 var source = document.getElementById('source');
                 while (rows.options.length > 0) {
                      rows.remove(0);
                 }
                 for (i = 0; i < source.options.length; i++) {
                      var item = source.options.item(i);
                      var x =document.createElement("OPTION");;
                      x.innertHTML = item.innerHTML;
                      x.value = item.value;
                      x.text = item.text;
                      rows.options.add(x);
                 }
        }
         function sort() {
                 var selData = document.getElementById("rows");
                 for (i = 0; i < selData.options.length - 1 ; i++) {
                         for (j = i+1; j < selData.options.length; j++) {
                                 var item = selData.options.item(i);
                                 var item2 = selData.options.item(j);
                                 if (item2.text < item.text) {
                                         var x =document.createElement("OPTION");
                                         var y =document.createElement("OPTION");
                                         x.innertHTML = item.innerHTML;
                                         x.value = item.value;
                                         x.text = item.text;
                                         y.innertHTML = item2.innerHTML;
                                         y.value = item2.value;
                                         y.text = item2.text;
                                         selData.remove(i);
                                         selData.remove(j - 1);
                                         selData.options.add(y,i);
                                         selData.options.add(x,j);
                                 }
                         }
                 }
         }
         function move_first() {
                 var selData = document.getElementById("rows");
                 if (selData.selectedIndex >= 0) {
                          var item = selData.options.item(selData.selectedIndex);
                          selData.remove(selData.selectedIndex);
                          selData.options.add(item,0);
                 }
         }
         function move_last() {
                 var selData = document.getElementById("rows");
                 if (selData.selectedIndex >= 0) {
                          var item = selData.options.item(selData.selectedIndex);
                          selData.remove(selData.selectedIndex);
                          selData.options.add(item,selData.options.length );
                 }

         }
         function move_up() {

                 var selData = document.getElementById("rows");
                 var pos = selData.selectedIndex;
                 if (selData.selectedIndex > 0) {
                         var item = selData.options.item(pos);
                         var item2 = selData.options.item(pos - 1);
                         selData.remove(pos);
                         selData.remove(pos - 1);
                         selData.options.add(item,pos - 1);
                         selData.options.add(item2,pos );
                 }
         }
         function move_down() {
                 var selData = document.getElementById("rows");
                 var pos = selData.selectedIndex;
                 if (selData.selectedIndex <= selData.options.length) {
                         var item = selData.options.item(pos);
                         var item2 = selData.options.item(pos + 1);
                         selData.remove(pos);
                         selData.remove(pos);
                         selData.options.add(item,pos);
                         selData.options.add(item2,pos );
                 }
         }
</script>
<tr>
         <td rowspan=5 width=80% >
                 <select size=10 style="width:100%;" id="rows" name="rows[]">

                 </select>
         </td>
         <td>
                 <input type=button class="CommonButton" style="width: 100%;" name="field_sort"  class="sbm" value="<?=$strings->getMessage('TO SORT')?>" onclick="sort();">
         </td>
</tr>
<tr>
         <td>
             <input type=button class="CommonButton" style="width: 100%;" name="field_move_first" class="sbm" value="<?=$strings->getMessage('MOVE FIRST')?>" onclick="move_first()">
         </td>
</tr>
<tr>
         <td>
             <input type=button class="CommonButton" style="width: 100%;" name="field_move_up" class="sbm" value="<?=$strings->getMessage('MOVE UP')?>" onclick="move_up()">
         </td>
</tr>
<tr>
         <td>
             <input type=button class="CommonButton" style="width: 100%;" name="field_move_down" class="sbm" value="<?=$strings->getMessage('MOVE DOWN')?> " onclick="move_down()">
         </td>
</tr>
<tr>
         <td>
             <input type=button class="CommonButton" style="width: 100%;" name="field_smove_last" class="sbm" value="<?=$strings->getMessage('MOVE LAST')?>" onclick="move_last()">
         </td>
</tr>
<tr>
         <td colspan=2>
                          <input type="hidden" name="type" value="<?=$type?>">
                          <input type="hidden" name="func" value=" ">
                          <input type="hidden" name="order_value" id="order_value"value="">
                 <input class="CommonButton" type="submit" name="submit" class="sbm" value="<?=$strings->getMessage('APPLY')?>" onclick="before_submit();"> &nbsp;
                 <input class="CommonButton" type="button" id="reset_button" name="reset_button"  class="sbm" value="<?=$strings->getMessage('RESTORE')?>" onclick="reset_list()">&nbsp;
                 <input class="CommonButton" type="button" id="cancel_button" name="cancel_button" class="sbm"
				 value="<?=$strings->getMessage('BACK')?>" onclick="window.location='<?=$back?>';return false;">



			 </td>
</tr>
<?
	$design->tableEnd();
	foreach ($aHidden as $key=>$row) {
		$design->hidden($key,$row);
	}
	$design->formEnd();
?>
<div style="visibility:hidden">
<select size=10 style="width: 100%;" id="source" name="source">
<?foreach ($aData as $value):?>
	<option value=<?=$value['id']?>> <?=$value['name']?> </option>
<?endforeach?>
</select>
<script>
reset_list();
</script>
<?
$design->documentEnd();
$design->End();
?>