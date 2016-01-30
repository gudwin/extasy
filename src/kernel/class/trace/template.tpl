<?
$nCounter = 0;
$aColors = array(
			'8495BB','FFCC66','00FF66',
			'9999FF','CC99FF','FF9999',
			'FCFC00','CCCCCC','CCDD99',
			'FFFF66','8495BB','FFCC66','00FF66',
			'9999FF','CC99FF','FF9999',
			'FCFC00','CCCCCC','CCDD99',
			'FFFF66','8495BB','FFCC66','00FF66',
			'9999FF','CC99FF','FF9999',
			'FCFC00','CCCCCC','CCDD99',
			'FFFF66','8495BB','FFCC66','00FF66',
			'9999FF','CC99FF','FF9999',
			'FCFC00','CCCCCC','CCDD99',
			'FFFF66','8495BB','FFCC66','00FF66',
			'9999FF','CC99FF','FF9999',
			'FCFC00','CCCCCC','CCDD99','FFFF66'
		);
?>	

	<h1> Отладочные данные </h1>
	<table border="0" cellpadding="0" cellspacing="1" width="100%">
									<?// Выводим алерты
									foreach ($aAlert as $row):?>
		<tr><td><b><?=$row?></b></td></tr>
									<?endforeach?>
	</table>
	<table width="100%" border="1" align="center" cellpadding="0" cellspacing="0" bgcolor="Limegreen">
	<tr> <td colspan="3" align="center"> <h3>Данные</h3></td></tr>
									<? /* Выводим дерево сообщений */ ?>
									<? foreach ($aMessage as $category => $rows):?>
										<? $nCounter++;?>
										<? /* Выводим заголовок категории */ ?>
	<tr> 
		<td colspan="3" align="center"> 
			<table border="0" cellpadding="3" cellspacing="1" width="100%">
				<tr>
					<td colspan="1"  align="center" >
						<div style="background-color:#<?=$aColors[$nCounter % sizeof($aColors)]?>">
							<h3><?=$category?> (<?=sizeof($rows)?>)</h3>
							<table width="100%" border="1" align="center" cellpadding="0" cellspacing="0" >
										<? /* Выводим сообщения внутри категории */ ?>
										<? foreach ($rows as $key=>$row): ?>
	<tr>
		<td bgcolor="Ghostwhite"><b><center><?=($key + 1)?></center></b></td>
											<? if (!empty($row['error'])): ?>
		<td width="80%" valign="top"><center><span style="color:darkRed;font-weight:bold"><?=$row['message']?></span></center></td>
											<? else: ?>
		<td width="80%" valign="top"><pre style="display:block;width:800px;font-size:10px;font-family:Tahoma;"><?=$row['message']?></pre></td>
											<? endif ?>
		<td ><center><?=$row['time']?></center></td>
	</tr>
										<? endforeach ?>
							</table>
						</div>
					</td>
				</tr>
			</table>
		</td>
	</tr>
									<? endforeach ?>
	</table>

<script>
	function showTrace() {
		var div = document.getElementById('__debug');
		if ( 'none' == div.style.display ) {
			div.style.display = '';
		} else {
			div.style.display = 'none';
		}
	}
	var prevKeyDown = document.onkeydown;
	
	document.onkeydown = function (e) {
		if (e == null) {
			e = event
		}
		var isKeyPressed = e.ctrlKey && (e.keyCode == 192 || e.keyCode == 96); 
		if ( isKeyPressed ) {
			showTrace();			
		}
	}
</script>
