<?
use \Extasy\CMS;
use \Faid\Configure\Configure;
require_once CLASS_PATH . 'dumper/dumper.class.php';
class AdminSqlConsole extends AdminPage {
	protected $aSession = array();
	protected $aResult = array();
	protected $szError = '';
	public function __construct() {
		parent::__construct();
		
		$this->addPost('sql','post');
		$this->addGet('dump','getDump');
		$this->addPost('import','import');
		if (empty($_SESSION['cms_sql_session'])) {
			$_SESSION['cms_sql_session'] = array();
		}
		$this->aSession = &$_SESSION['cms_sql_session'];
		// Не более десяти запросов 
		if (sizeof($this->aSession) > 30) {
			array_shift($this->aSession);
		}
		// Сохраняем результат
		if (empty($_SESSION['cms_sql_result'])) {
			$_SESSION['cms_sql_result'] = array();
		}
		$this->aResult = &$_SESSION['cms_sql_result'];
		// Сохраняем ошибку
		if (empty($_SESSION['cms_sql_error'])) {
			$_SESSION['cms_sql_error'] = array();
		}
		$this->szError = &$_SESSION['cms_sql_error'];

	}
	public function main() {
		$szTitle = _msg('mySQL-консоль');
		$aBegin = array(
			_msg('Администрирование') => 'index.php',
			$szTitle => '#'
			);
		$aButton = array(
			_msg('Получить всю БД') => 'sql.php?dump=1'
			);
		$this->outputHeader($aBegin,$szTitle,CMS::getResourcesUrl() . 'extasy/Dashboard/administrate/sql_console.js');
		
		// Выводим список запросов
		$design = CMSDesign::getInstance();
		$this->outputError();
		$design->decor->buttons($aButton);

		$design->text->header(_msg('Последние запросы:'));
		
		$i = 0;
		$szLastSQL = $this->outputSessionRequests();
		$this->outputResults($szLastSQL);
		$this->outputRequestForm($szLastSQL);
		$this->outputImportDBForm();
		// Выводим футер
		$this->outputFooter();
		$this->output();
	}
	/**
	*   @desc Обрабатываем запрос и пересылаем на вывод
	*   @return
	*/
	public function post($sql) {
		$this->szError ='';
		$this->aResult =array();
		// Замеряем время
		$timeStart = microtime(true);
		$connection = Configure::read('DB');
		$mysql = new mysqli($connection['host'],$connection['user'],$connection['password'],$connection['database']);
		$mysql->set_charset('utf8');
		$result = $mysql->query($sql);
		
		$timeFinish = microtime(true);
		// Если у запроса есть результаты
		if ($result !== FALSE) {
			
			// Если имеется результат запроса 
			if (!is_bool($result)) {
				
				$row = $result->fetch_assoc();
				while ($row) {
					$this->aResult[] = $row;
					$row = $result->fetch_assoc();	
				}
			}
		} else {
			$result = array();
			$this->szError = $mysql->error;
			
		}
		
		array_push($this->aSession,$sql);
		$_SESSION['cms_sql_query_time'] = $timeFinish - $timeStart;
		// Выводим результат запроса
		ob_start();
		$this->outputResults();
		$content = ob_get_clean();
		
		// Получаем последний список запросов
		ob_start();
		$this->outputSessionRequests();
		$requests = ob_get_clean();
		// Вывод блока ошибки
		ob_start();
		$this->outputError();
		$error = ob_get_clean();
		// Время запроса
		$time = $_SESSION['cms_sql_query_time'];

		// Формируем результат
		$output =  array(	
			'request' => $sql,
			'time' => $time,
			'sql_log' => $requests,
			'error' => $error,
			'size' => sizeof($result),
			'results' => $content,
		);
		
		print json_encode($output);
		
		die();
	}
	/**
	*   -------------------------------------------------------------------------------------------
	*   @desc Посылает дамп текста
	*   @return
	*   -------------------------------------------------------------------------------------------
	*/
	public function getDump() {
		$dumper =new CDumper();
		$szDump = ($dumper->export());
		$this->sendFile("sql_export_".SITE_NAME.'_'.date('Y-m-d h:i').'.sql','text/sql',$szDump);
	}
	/**
	*   -------------------------------------------------------------------------------------------
	*   Импортирует SQL-console
	*   @return
	*   -------------------------------------------------------------------------------------------
	*/
	public function import() {
		if (!empty($_FILES['file'])) {
			$fs = DAO_FileSystem::getInstance();
			$fs->upload('file',FILE_PATH.'import.sql');
			if (file_exists(FILE_PATH.'import.sql')) {
				$szImport = file_get_contents(FILE_PATH.'import.sql');
				$dumper =new CDumper();
				$szDump = ($dumper->import($szImport));
				$this->addAlert('Файл импортирован');
			}
		}
		$this->jump('sql.php');
	}
	protected function outputSessionRequests() {
		$i = 0;
		
		$design = CMSDesign::getInstance();
		$design->tableBegin('sql_log');
		foreach ($this->aSession as $row) {
			$design->rowBegin();
			$design->listCell($i+1);
			$design->listCell(htmlspecialchars($row));
			$design->cellBegin();
				?>
			<div style="display:none"><?print htmlspecialchars($row);?></div>
			<a href="#" class="sql_request_append" >Выбрать</a>
				<?
			$design->cellEnd();
			$design->rowEnd();
			$i++;
		}
		$lastSQL = '';
		if (!empty($row)) {
			$lastSQL = $row;
		}
		$design->tableEnd();
		return $lastSQL;	
	}
	/**
	 * Вывод результатов запроса
	 */
	protected function outputResults() {
		$design = CMSDesign::getInstance();
		// Выводим последний результат, если он есть:)
		// формируем заголовок таблицы
		print '<div id="sql_results">';
		
		if (!empty($this->aResult)) {
			$aTableHeader = array_keys($this->aResult[0]);
			foreach ($aTableHeader as &$row) {
				$row = array(
					htmlspecialchars($row),'1'
					);
			}
			unset($row);
			
			$design->header(_msg('Результаты запроса'));
			$design->tableBegin();
			$design->tableHeader($aTableHeader);
			foreach ($this->aResult as $row) {
				$design->rowBegin();
				foreach ($row as $field) {
					$design->listCell(htmlspecialchars($field));
				}
				$design->rowEnd();
			}
			$design->tableEnd();

		}
		
		if (!empty($_SESSION['cms_sql_query_time'])) {
			$design->header2('Время последнего запроса:'.$_SESSION['cms_sql_query_time']);
		}
		print '</div>';
	}
	/**
	 * Выводит форму, для принятия дампа
	 */
	protected function outputImportDBForm() {
		$design = CMSDesign::getInstance();
		$design->formBegin();
		$design->tableBegin();
		$design->row2cell('Импорт файла','<input type="file" name="file" />');
		$design->tableEnd();
		$design->hidden('import','1');
		$design->hidden('import-x','1');
		$design->submit('submit',_msg('Импорт файла'));
		$design->formEnd();
		
	}
	/**
	 * 
	 * @param unknown_type $lastSQL
	 */
	protected function outputRequestForm($lastSQL) {
		$design = CMSDesign::getInstance();
		$design->formBegin('','post','sql_request');
		$design->submit('submit',_msg('Послать запрос'));
		
		?>
		<textarea style="width:100%" name="sql" id="sql_textarea"><?=$lastSQL?></textarea>
		<script type="text/javascript">document.getElementById('sql_textarea').focus();</script>
		<?

		$design->submit('submit',_msg('Послать запрос'));
		$design->formEnd();
		
	} 
	/**
	 * 
	 */
	protected function outputError() {
		print '<div id="sql_error">';
		if (!empty($this->szError)) {
			$design = CMSDesign::getInstance();
			$design->error($this->szError);
			
		}
		print '</div>';		
	}		
}
?>