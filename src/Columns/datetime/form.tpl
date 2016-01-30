
<select style="width:100px;" id="<?=$fieldname?>_year" onchange="<?=$fieldname?>_change_year(this);">
	</select> -
	<select style="width:100px;" id="<?=$fieldname?>_month" onchange="<?=$fieldname?>_change_month(this);">
		<option value=1> Январь  </option>
		<option value=2> Февраль  </option>
		<option value=3> Март  </option>
		<option value=4> Апрель</option>
		<option value=5> Май </option>
		<option value=6> Июнь </option>
		<option value=7> Июль </option>
		<option value=8> Август </option>
		<option value=9> Сентябрь  </option>
		<option value=10> Октябрь </option>
		<option value=11> Ноябрь   </option>
		<option value=12> Декабрь  </option>
	</select> - 
	<select style="width:100px;" id="<?=$fieldname?>_day" onchange="<?=$fieldname?>_setValue();">
		<option value=1> 01 </option>
		<option value=2> 02 </option>
		<option value=3> 03 </option>
		<option value=4> 04 </option>
		<option value=5> 05 </option>
		<option value=6> 06 </option>
		<option value=7> 07 </option>
		<option value=8> 08 </option>
		<option value=9> 09 </option>
		<option value=10> 10</option>
		<option value=11> 11</option>
		<option value=12> 12</option>
		<option value=13> 13</option>
		<option value=14> 14</option>
		<option value=15> 15</option>
		<option value=16> 16</option>
		<option value=17> 17</option>
		<option value=18> 18</option>
		<option value=19> 19</option>
		<option value=20> 20</option>
		<option value=21> 21</option>
		<option value=22> 22</option>
		<option value=23> 23</option>
		<option value=24> 24</option>
		<option value=25> 25</option>
		<option value=26> 26</option>
		<option value=27> 27</option>
		<option value=28 > 28</option>
		<option value=29 > 29 </option>
		<option value=30 > 30 </option>
		<option value=31 > 31</option>
	</select>
	-
	<select style="width:50px;" id="<?=$fieldname?>_hour" onchange="<?=$fieldname?>_setValue();">
		<option value=1> 01 </option>
		<option value=2> 02 </option>
		<option value=3> 03 </option>
		<option value=4> 04 </option>
		<option value=5> 05 </option>
		<option value=6> 06 </option>
		<option value=7> 07 </option>
		<option value=8> 08 </option>
		<option value=9> 09 </option>
		<option value=10> 10</option>
		<option value=11> 11</option>
		<option value=12> 12</option>
		<option value=13> 13</option>
		<option value=14> 14</option>
		<option value=15> 15</option>
		<option value=16> 16</option>
		<option value=17> 17</option>
		<option value=18> 18</option>
		<option value=19> 19</option>
		<option value=20> 20</option>
		<option value=21> 21</option>
		<option value=22> 22</option>
		<option value=23> 23</option>
		<option value=24> 00</option>
	</select> : 
	<select style="width:50px;" id="<?=$fieldname?>_minute" onchange="<?=$fieldname?>_setValue();">
		<option value=0> 00 </option>
		<option value=1> 01 </option>
		<option value=2> 02 </option>
		<option value=3> 03 </option>
		<option value=4> 04 </option>
		<option value=5> 05 </option>
		<option value=6> 06 </option>
		<option value=7> 07 </option>
		<option value=8> 08 </option>
		<option value=9> 09 </option>
		<option value=10> 10</option>
		<option value=11> 11</option>
		<option value=12> 12</option>
		<option value=13> 13</option>
		<option value=14> 14</option>
		<option value=15> 15</option>
		<option value=16> 16</option>
		<option value=17> 17</option>
		<option value=18> 18</option>
		<option value=19> 19</option>
		<option value=20> 20</option>
		<option value=21> 21</option>
		<option value=22> 22</option>
		<option value=23> 23</option>
		<option value=24> 24</option>
		<option value=25> 25</option>
		<option value=26> 26</option>
		<option value=27> 27</option>
		<option value=28> 28</option>
		<option value=29> 29</option>
		<option value=30> 30</option>
		<option value=31> 31 </option>
		<option value=32> 32 </option>
		<option value=33> 33 </option>
		<option value=34> 34 </option>
		<option value=35> 35 </option>
		<option value=36> 36 </option>
		<option value=37> 37 </option>
		<option value=38> 38 </option>
		<option value=39> 39 </option>
		<option value=40> 40</option>
		<option value=41> 41</option>
		<option value=42> 42</option>
		<option value=43> 43</option>
		<option value=44> 44</option>
		<option value=45> 45</option>
		<option value=46> 46</option>
		<option value=47> 47</option>
		<option value=48> 48</option>
		<option value=49> 49</option>
		<option value=50> 50</option>
		<option value=51> 51</option>
		<option value=52> 52</option>
		<option value=53> 53</option>
		<option value=54> 54</option>
		<option value=55> 55</option>
		<option value=56> 56</option>
		<option value=57> 57</option>
		<option value=58> 58</option>
		<option value=59> 59</option>
	</select>
	<a href="#" id="<?=$fieldname?>_setup_now">Установить текущую дату</a>
	<INPUT TYPE="hidden" name=<?=$fieldname?> id="<?=$fieldname?>" >
	<SCRIPT LANGUAGE="JavaScript">
	$<?=$fieldname?>_currentYear = <?=(!empty($current_year)?$current_year:1900)?>;
	$<?=$fieldname?>_currentMonth = <?=(!empty($current_month)?$current_month:1)?>;
	$<?=$fieldname?>_currentDay = <?=(!empty($current_day)?$current_day:1)?>;
	$<?=$fieldname?>_currentHour = <?=(!empty($current_hour)?$current_hour:1)?>;
	$<?=$fieldname?>_currentMinute = <?=(!empty($current_minute)?$current_minute:1)?>;
	$<?=$fieldname?>_nStart = 1900;
	$<?=$fieldname?>_nEnd = 2020;
	$<?=$fieldname?>_aDays = [0,31,28,31,30,31,30,31,31,30,31,30,31]
	////////////////////////////////////////////////////
	function <?=$fieldname?>_setValue() {
		document.getElementById('<?=$fieldname?>').value = 
			document.getElementById('<?=$fieldname?>_year').value.toString() + '-' +
			document.getElementById('<?=$fieldname?>_month').value.toString() + '-' +
			document.getElementById('<?=$fieldname?>_day').value.toString() + ' ' + 
			document.getElementById('<?=$fieldname?>_hour').value.toString() + ':' + 
			document.getElementById('<?=$fieldname?>_minute').value.toString();
	}
	function <?=$fieldname?>_change_year(year) {
		$<?=$fieldname?>_bLeapYear = (year.value % 4 == 0)?true:false;
		<?=$fieldname?>_change_month(document.getElementById('<?=$fieldname?>_month'));
		<?=$fieldname?>_setValue();
	}
	function <?=$fieldname?>_change_month(month) {
		(month.value == 2)?($<?=$fieldname?>_bLeapYear == true?<?=$fieldname?>_setDays(29):<?=$fieldname?>_setDays(28)):<?=$fieldname?>_setDays($<?=$fieldname?>_aDays[month.value]);
		<?=$fieldname?>_setValue();
	}
	// setDays in days listbox
	function <?=$fieldname?>_setDays($nCount) {
		$day = document.getElementById('<?=$fieldname?>_day')
		$oldValue = $day.value;
		// clear collection
		while ($day.length > 0)
			$day.remove(0);
		for ($i = 1; $i <= $nCount; $i++ )
		{
			$option = document.createElement('option');
			$option.text = $i.toString();
			$option.value = $i;
			if (document.all)
			{
				$day.add($option,0);
			} else 
				$day.add($option,null);
			
		}
		if ($oldValue <= $nCount)
		{
			$day.value = $oldValue;
		}
	}
	$year = document.getElementById('<?=$fieldname?>_year');
	for ($i = $<?=$fieldname?>_nStart; $i <= $<?=$fieldname?>_nEnd; $i ++)
	{
		$option = document.createElement('option');
		$option.text = $i.toString();
		$option.value = $i;
		if (document.all)
		{
			$year.add($option,0);
		} else 
			$year.add($option,null);
		
	}
	////////////////////////////////////////////////////
	// Setting current date
	////////////////////////////////////////////////////
	$year.value = $<?=$fieldname?>_currentYear;
	<?=$fieldname?>_change_year($year);
	document.getElementById('<?=$fieldname?>_month').value = $<?=$fieldname?>_currentMonth;
	document.getElementById('<?=$fieldname?>_day').value = $<?=$fieldname?>_currentDay;
	document.getElementById('<?=$fieldname?>_hour').value = $<?=$fieldname?>_currentHour;
	document.getElementById('<?=$fieldname?>_minute').value = $<?=$fieldname?>_currentMinute;
	<?=$fieldname?>_setValue();
	jQuery(document).ready(function () {
		jQuery('#<?=$fieldname?>_setup_now').click(function () {
			var date = new Date();
			
			document.getElementById('<?=$fieldname?>_year').value = date.getFullYear();
			document.getElementById('<?=$fieldname?>_month').value = date.getMonth() + 1
			document.getElementById('<?=$fieldname?>_day').value = date.getDate()
			document.getElementById('<?=$fieldname?>_hour').value = date.getHours()
			document.getElementById('<?=$fieldname?>_minute').value = date.getMinutes()
			<?=$fieldname?>_setValue();
			return false;
		});
	});
	//-->

	</SCRIPT>