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
		var $bFlag = false;
		var $szResult = '';
		for (key in aValue )
		{
			$szResult += net.responseParam(aValue[key],key,path) + "\r\n";
			$bFlag = true;
		}
		if (!$bFlag) {
			$szResult = '<param name="' + path+ '"><![CDATA[||]]></param>';
		}
		return $szResult;
	} else {
		return '<param name="' + path+ '"><![CDATA[|' + aValue + '|]]></param>';
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
		$xmlDoc = $loader.responseXML;
		$xmlDoc = (new DOMParser()).parseFromString($loader.responseText, "text/xml");
		//_debug($xmlDoc.childNodes.length);
	}
	//alert($loader.getResponseHeader('Content-Type'));
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
		if (!$value) {
			$value =  $aData[$i].innerHTML.substr(9,$aData[$i].innerHTML.length - 12);
		}
		
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

			$aResult['result'] = $value.substr(1,$value.length - 2);
		}

	}
	
	stopLoading();
	net.bProcessing = false;
	if (typeof(net.currentFunction) == 'function') {
		net.currentFunction.call(net.currentObject,$aResult['result']);
	} else 
		$aResult['result'];
}
net.callRPC = function ($functionName,$function,$object,$scriptName,$param1,$param2,$param3,$param4,$param5,$param6,$param7) {
	var $szAddParam = '<?=$szAddParam?>';
	var $szParam  ='';
	var $bAsync = true;
	if (typeof($function) == 'function' ) {
		if ($param7 != null) $szParam = net.responseParam($param7,'param7') + $szParam;
		if ($param6 != null) $szParam = net.responseParam($param6,'param6') + $szParam;
		if ($param5 != null) $szParam = net.responseParam($param5,'param5') + $szParam;
		if ($param4 != null) $szParam = net.responseParam($param4,'param4') + $szParam;
		if ($param3 != null) $szParam = net.responseParam($param3,'param3') + $szParam;
		if ($param2 != null) $szParam = net.responseParam($param2,'param2') + $szParam;
		if ($param1 != null) $szParam = net.responseParam($param1,'param1') + $szParam;
	} else {
		if ($param5 != null) $szParam = net.responseParam($param5,'param7') + $szParam;
		if ($param4 != null) $szParam = net.responseParam($param4,'param6') + $szParam;
		if ($param3 != null) $szParam = net.responseParam($param3,'param5') + $szParam;
		if ($param2 != null) $szParam = net.responseParam($param2,'param4') + $szParam;
		if ($param1 != null) $szParam = net.responseParam($param1,'param3') + $szParam;
		if ($object != null) $szParam = net.responseParam($object,'param2') + $szParam;
		if ($function != null) $szParam = net.responseParam($function,'param1') + $szParam;
		$bAsync = false;
	}
	$szCode = "<Extasy><call>";
	$szCode += '<Function name="' + $functionName + '">'
	$szCode += $szParam;
	$szCode += '</Function>';
	$szCode += '</call></Extasy>';
	net.currentFunction = $function;

	net.currentObject = $object;
	net.bProcessing = true;
	startLoading();
	$szParam = 'rpc=' + $szCode;
	if ($szAddParam.length > 0)
		$szParam += '&' + $szAddParam;
	if (!$bAsync) {
		var $req =  new net.ContentLoader(
			$scriptName,
			net.responseRPCResult,
			false,
			'POST',
			$szParam,
			'application/x-www-form-urlencoded',
			new Array(),
			null,
			$bAsync
		);
		return net.responseRPCResult($req);
	} else {
		net.loader = new net.ContentLoader(
			$scriptName,
			net.responseRPCResult,
			false,
			'POST',
			$szParam,
			'application/x-www-form-urlencoded',
			new Array(),
			null,
			$bAsync
		);
	}
}



<?=$szFunctions?>
