net = new Object();
net.bProcessing = false;
net.url = document.URL;
net.xend = net.url.lastIndexOf('/') + 1;
net.base_url = net.url.substring(0,net.xend);
net.READY_STATE_UNINITALIZED = 0;
net.READY_STATE_LOADING = 1;
net.READY_STATE_LOADED = 2;
net.READY_STATE_INTERACTIVE = 3;
net.READY_STATE_COMPLETE = 4;

net.doAjax = function (url) {
	var jsel = document.createElement('SCRIPT')
	jsel.type = 'text/javascript';
	jsel.src = url;
	document.body.appendChild(jsel);
}
