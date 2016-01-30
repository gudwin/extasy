<div class="bootstrap">
	<table class="table">
		<thead>
		<tr>
			<th></th>
			<th>Пн</th>
			<th>Вт</th>
			<th>Ср</th>
			<th>Чт</th>
			<th>Пт</th>
			<th>Суб</th>
			<th>Вс</th>
		</tr>
		</thead>
		<tbody>
		<tr>
			<td>День доступа</td>
			<td><input type="checkbox" name="<?=$name?>[0][day]" <? if ( !empty( $value[0]['day'])):?>checked="checked"<?endif?> value="1"/></td>
			<td><input type="checkbox" name="<?=$name?>[1][day]" <? if ( !empty( $value[1]['day'])):?>checked="checked"<?endif?> value="1"/></td>
			<td><input type="checkbox" name="<?=$name?>[2][day]" <? if ( !empty( $value[2]['day'])):?>checked="checked"<?endif?> value="1"/></td>
			<td><input type="checkbox" name="<?=$name?>[3][day]" <? if ( !empty( $value[3]['day'])):?>checked="checked"<?endif?> value="1"/></td>
			<td><input type="checkbox" name="<?=$name?>[4][day]" <? if ( !empty( $value[4]['day'])):?>checked="checked"<?endif?> value="1"/></td>
			<td><input type="checkbox" name="<?=$name?>[5][day]" <? if ( !empty( $value[5]['day'])):?>checked="checked"<?endif?> value="1"/></td>
			<td><input type="checkbox" name="<?=$name?>[6][day]" <? if ( !empty( $value[6]['day'])):?>checked="checked"<?endif?> value="1"/></td>
		</tr>
			<tr>
				<td>Время доступа</td>
				<td><input type="text" name="<?=$name?>[0][time]" value="<?=$value[0]['time']?>"/></td>
				<td><input type="text" name="<?=$name?>[1][time]" value="<?=$value[1]['time']?>"/></td>
				<td><input type="text" name="<?=$name?>[2][time]" value="<?=$value[2]['time']?>"/></td>
				<td><input type="text" name="<?=$name?>[3][time]" value="<?=$value[3]['time']?>"/></td>
				<td><input type="text" name="<?=$name?>[4][time]" value="<?=$value[4]['time']?>"/></td>
				<td><input type="text" name="<?=$name?>[5][time]" value="<?=$value[5]['time']?>"/></td>
				<td><input type="text" name="<?=$name?>[6][time]" value="<?=$value[6]['time']?>"/></td>
			</tr>
		</tbody>
	</table>
</div>