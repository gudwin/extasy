<?php
use \Faid\UParser;
class PhpConsole extends AdminPage {
	public function __construct() {
		parent::__construct();
		$this->AddPost('source,initData','exec');
		$this->AddPost('source','exec');
	}
	public function main() {
		$title = 'Php-консоль';
		$begin = array(
			$title => '#'
		);
		$this->outputHeader($begin, $title);

		$control = new CPhpSource();
		$control->name = 'phpCode';
		$control->source = '<? print "Hello world!";?>';
		print $control;
		?>
		<ul class="operations">
		<li class="test">
			<a href="#" title="Выполняет данный код на основе тестовых данных"> Протестировать </a>
		</li>		
		</ul>
		<script type="text/javascript">
		jQuery( function ( $ ) {
			// Нажатие на кнопку тестировать
			$('.operations .test a').click(function () {
				var adminScriptPath = cms.httpCPRoot + 'administrate/php_console.php';
				var editor = $('.phpSource').data('phpEditor'); 
				var windowCounter = 0;
				$.post(adminScriptPath,{
					source : editor.getValue()
				},function (response) {
					var window = new Ext.Window({
						collapsible 			: true,
						draggable 				: true,
						closable 				: true,
						resizable 				: true,
						html 					: response,
						layout 					: 'fit',
						title 					: 'Ответ от сервера #' + windowCounter,
						width					: 400,
						height					: 300,
						autoShow				: true,
						expandOnShow			: true
					});
					window.render(document.body);
					window.show();
					windowCounter++;
				});
			});
		});
		</script>
		<?php 
		$this->outputFooter();
	}
	public function exec($source,$initData = null) {
		$auth = CMSDesign::getInstance();
		$initData = json_decode($initData,true);
		if (!empty($initData)) {
			$source = $initData.$source;
		}
		if (!$auth->isSuperAdmin( UsersLogin::getCurrentUser() ) ) {
			print 'Only system administrator can use this feature';die();
		}  else {
			print UParser::parsePHPCode($source, array());
		}

		die();
	}
}