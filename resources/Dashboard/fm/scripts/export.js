if (!net)
{
	alert('net - library not found!')
} else {
	if (!net.ContentLoader) {
		alert('net.ContentLoader - library not found!')
	} 
}

net.responseParam = function (aValue,name,path) {
	
	if (!path)
	{
		path= name;
	} else {
		path += '[\'' + name + '\']';
	}
	if (aValue instanceof Object)
	{
		szResult = '';
		for (key in aValue )
		{
			szResult += net.responseParam(aValue[key],key,path) + "\r\n";
		}
		return szResult
	} else {
		return '<param name="' + path+ '">|' + aValue + '|</param>';
	}
}
net.parseParam = function($aData,$aResult,$position,$value) {
	var $aTmp;
	if ($aResult == null) {
		$aResult = new Object();
	}
	if ($position < $aData.length - 1)
	{
		$aTmp = net.parseParam($aData,$aResult[$aData[$position]],$position+1,$value);
		$aResult[$aData[$position]] = $aTmp;
		return $aResult;
	} else {
		$aResult[$aData[$position]] = $value.substr(1,$value.length -2);
		return $aResult;
	}
}
net.responseRPCResult = function ($loader) {
	if ($loader.responseXML) {
		$xmlDoc = $loader.responseXML;
		
	} else {
		$xmlDoc = (new DOMParser()).parseFromString($loader.responseText, "text/xml");
		//_debug($xmlDoc.childNodes.length);
	}
	
	var $aData,$basename,$value,$name,$i;
	$aData = $xmlDoc.getElementsByTagName('results');
	$aResult = [];
	
	for ($i = 0; $i < $aData.length ;$i++) {
		// получаю новый параметр
		
		$name = $aData[Math.round($i)].getAttribute('name');
		if ($name == null) {
			
			//window.setTimeout('net.responseRPCResult(net.loader.req)',100);
			return;
		}
		
		$value = $aData[$i].textContent?$aData[$i].textContent:$aData[$i].text;
		
		// получаю базовое имя
		$basename = $name.substr(0,$name.search(/\[/g));
		// получаю массив подэлементов
		$aMatch = $name.match(/\[([^\]]+)\]/g)
		
		if ($aMatch instanceof Array) {
			for ($j = 0; $j < $aMatch.length;$j++) {
				$aMatch[$j] = $aMatch[$j].substr(2,$aMatch[$j].length - 4);
			}
			$aMatch.unshift($basename);
			$aResult = net.parseParam($aMatch,$aResult,0,$value)
		} else {
			$aResult['result'] = $value;
		}
	}
	
	stopLoading();
	net.bProcessing = false;
	net.currentFunction.call(net.currentObject,$aResult['result']);
}
net.callRPC = function ($functionName,$function,$object,$param1,$param2,$param3,$param4,$param5,$param6,$param7) {
	
// Определяем параметры
	
	var $szParam  ='';
	if ($param7 != null) $szParam = net.responseParam($param7,'param7') + $szParam;
	if ($param6 != null) $szParam = net.responseParam($param6,'param6') + $szParam;
	if ($param5 != null) $szParam = net.responseParam($param5,'param5') + $szParam;
	if ($param4 != null) $szParam = net.responseParam($param4,'param4') + $szParam;
	if ($param3 != null) $szParam = net.responseParam($param3,'param3') + $szParam;
	if ($param2 != null) $szParam = net.responseParam($param2,'param2') + $szParam;
	if ($param1 != null) $szParam = net.responseParam($param1,'param1') + $szParam;
	
	$szCode = "<Extasy><call>";
	$szCode += '<Function name="' + $functionName + '">'
	$szCode += $szParam;
	$szCode += '</Function>';
	$szCode += '</call></Extasy>';
	net.currentFunction = $function;
	
	net.currentObject = $object;
	net.bProcessing = true;
	startLoading();
	net.loader = new net.ContentLoader("/nasos/admin/fm/scripts/pathinfo.php",net.responseRPCResult,false,'POST','rpc=' + $szCode)
}


function getPathInfo($function,$object,$param1,$param2,$param3,$param4,$param5,$param6,$param7) {
	net.callRPC('getPathInfo',$function,$object,$param1,$param2,$param3,$param4,$param5,$param6,$param7)
}
function setFileMode($function,$object,$param1,$param2,$param3,$param4,$param5,$param6,$param7) {
	net.callRPC('setFileMode',$function,$object,$param1,$param2,$param3,$param4,$param5,$param6,$param7)
}
function renameFile($function,$object,$param1,$param2,$param3,$param4,$param5,$param6,$param7) {
	net.callRPC('renameFile',$function,$object,$param1,$param2,$param3,$param4,$param5,$param6,$param7)
}
function unlinkFile($function,$object,$param1,$param2,$param3,$param4,$param5,$param6,$param7) {
	net.callRPC('unlinkFile',$function,$object,$param1,$param2,$param3,$param4,$param5,$param6,$param7)
}
function createFolder($function,$object,$param1,$param2,$param3,$param4,$param5,$param6,$param7) {
	net.callRPC('createFolder',$function,$object,$param1,$param2,$param3,$param4,$param5,$param6,$param7)
}
function folderSize($function,$object,$param1,$param2,$param3,$param4,$param5,$param6,$param7) {
	net.callRPC('folderSize',$function,$object,$param1,$param2,$param3,$param4,$param5,$param6,$param7)
}
