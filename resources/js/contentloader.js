var net;
if (!net)
{
	alert('net - library not found!')
} else {
	/**
	*   @desc Модуль для отправки запросов 
	*/
	net.ContentLoader = function (
		url,onload,onerror,method,params,contentType,headers,secure,bAsync
	) {
		this.req = null;

		if (bAsync === undefined) {
			this.bAsync = true;
		} else  {
			this.bAsync = bAsync;
		}
		this.onload = onload;
		this.onerror =  (onerror) ? onerror : this.defaultError;
		this.secure = secure;
		return this.loadXMLDoc(url,method,params,contentType,headers);
	}
	net.ContentLoader.prototype = {
		/**
		*   @desc Получаем XMLHTTPRequest
		*/
		loadXMLDoc:function (url,method,params,contentType,headers) {
			if (!method )
			{
				method = 'GET';
			}
			if (!contentType && method == 'POST')
			{
				contentType = 'application/x-www-form-urlencoded';
			}
			if (window.XMLHttpRequest) 
			{
				this.req = new XMLHttpRequest();
			} else if (window.ActiveXObject) 
			{
				
				this.req = new ActiveXObject("Msxml2.XMLHTTP.3.0");
			}		
			if (this.req)
			{
				try
				{
					try
					{
						if (this.secure && netscape && netscape.security.PrivilegeManage.enablePrivilege)
						{
							netscape.security.PrivilegeManage.enablePrivilege('UniversalBrowserRead');
						}
					} catch (err) {}
					if (!this.bAsync) {
						this.req.open(method,url,true);
					} else {
						this.req.open(method,url);
					}
					if (contentType)
					{
						this.req.setRequestHeader('Content-Type',contentType);
					}
					if (headers)
					{
						for (var h in headers)
						{
							this.req.setRequestHeader(h,headers[h]);
						}
					}
					var loader = this;
					this.req.onreadystatechange = function () {
						loader.onReadyState.call(loader);
					}
					this.req.send(params);
				}
				catch (err) {
					this.onerror.call(this);
				}
			}
		},
		onReadyState:function () {
			var req = this.req;
			var ready = req.readyState;
			if (ready == net.READY_STATE_COMPLETE)
			{
				
				var httpStatus = req.status;
				if (httpStatus == 200 || httpStatus == 0) 
				{
					try
					{
						if (this.secure && netscape && netscape.security.PrivilegeManage.enablePrivilege)
						{
							netscape.security.PrivilegeManage.enablePrivilege('UniversalBrowserRead');
						}
					}
					catch (err){}
					if (this.bAsync) {
						this.onload.call(this,this.req);
					} else {
						return this.req;
					}
					
				} else {
					this.onerror.call(this.req);
				}
			}
		},
		defaultError:function() {
			alert("error fetching data!"
				+ "\n\n readyState: " + this.req.readyState
				+ "\n\nstatus: " + this.req.status
				+"\nheaders:" + this.req.getAllResponseHeaders()
			);
		}
	}

}
