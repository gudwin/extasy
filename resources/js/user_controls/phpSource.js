/**
 * 
 */
function initPhpSource(id,initData) {
	var $ = jQuery;
	var basePath = "/resources/extasy/vendors/codeMirror/";
	var adminScriptPath = cms.httpCPRoot + 'administrate/php_console.php';
	var layout = $(document.getElementById(id).parentNode);
	var windowCounter = 0;
	// Инициализируем редактор
	
	var editor = CodeMirror.fromTextArea(id, {
        height: "350px",
        parserfile: [
                     "js/util.js",
                     "js/stringstream.js",
                     "js/select.js",                     
                     "js/undo.js",                     
                     "js/editor.js",
                     "js/tokenize.js",
                     "js/parsexml.js", 
                     "js/parsecss.js", 
                     "js/tokenizejavascript.js", 
                     "js/parsejavascript.js",
                     "contrib/php/js/tokenizephp.js", 
                     "contrib/php/js/parsephp.js",
                     "contrib/php/js/parsephphtmlmixed.js"],
        stylesheet: [
                     basePath + "css/xmlcolors.css", 
                     basePath + "css/jscolors.css", 
                     basePath + "css/csscolors.css", 
                     basePath + "contrib/php/css/phpcolors.css"
                   ],
        path: basePath,
        continuousScanning: 500,
        onCursorActivity : function () {
        	
        	var line = editor.cursorLine();
        	var lineNumber = editor.lineNumber(line)
        	var columnNumber = editor.cursorPosition().character;
        	var result = sprintf('Позиция: %d,%d',lineNumber,columnNumber);
        	$('.current-line',layout).html(result);
        	
        	
        }
      });
	// Нажатие на кнопку тестировать
	$('.test a',layout).click(function () {
		$.post(adminScriptPath,{
			source : editor.getCode(),
			initData : JSON.stringify(initData)
		},function (response) {
			var window = new Ext.Window({
				collapsible : true,
				draggable : true,
				closable : true,
				resizable : true,
				html : response,
				layout : 'fit',
				title : 'Ответ от сервера #' + windowCounter,
				width: 400,
				height : 300,
				autoShow : true,
				expandOnShow : true
			});
			window.render(document.body);
			window.show();
			windowCounter++;
		});
	});
	// Серверная проверка кода
};