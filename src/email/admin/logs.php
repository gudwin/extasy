<?php
/**
 * Выводит форму просмотра логов
 */
class Email_Logs_Admin extends AdminPage {
	public function __construct() {
		parent::__construct();
		$this->addPost('clear','clear');
		$this->addGet('id','showRecord');
	}
	public function main() {
		// 
		$logRecords = EmailLogModel::selectAll();
		$count = sizeof($logRecords);
		//
		$title = 'История отосланных сообщений';
		$begin = arraY(
			'Почта' => './index.php',
			$title => '#'
		);
		$tableHeader = array(
			array('Дата','10'),
			array('Кому','15'),
			array('Тема','15'),
			array('Статус','30'),
		);
		
		//
		$this->outputHeader($begin,$title);
		$design = CMSDesign::getInstance();
		$design->formBegin();
		if ($count > 100) {
			$design->contentBegin();
			print <<<HTML
	<p class="important">
	Пора задуматься над очисткой истории. 
	История стала достаточно большой и занимает место на винчестере. <br/> 
	Всего сохранено записей: <span class="big">{$count}</span>
	</p>
HTML;

			$design->contentEnd();
		} 
		$design->submit('clear','Очистить историю');
		$design->formEnd();
		$design->tableBegin();
		$design->tableHeader($tableHeader);
		foreach ($logRecords as $row) {
			$design->rowBegin();
			$design->listCell($row->date->getViewValue());
			$design->listCell($row->to->getViewValue());
			$design->listCell(sprintf( '<a href="logs.php?id=%d">%s</a>',
				$row->id->getValue(),
				$row->subject->getValue()
			));
			$design->listCell(sprintf('<span class="important">%s</span>',
				$row->status->getValue()));
			$design->rowEnd();
		}
		$design->tableEnd();
		$this->outputFooter();
		$this->output();
	}
	public function showRecord($id) {
		$logRecord = new EmailLogModel();
		$found = $logRecord->get($id);
		if (!$found) {
			$this->addError('Log record not found');
			$this->jumpBack();
		}
		$title = 'Просмотр лог-записи от "'.$logRecord->date->getViewValue().'"';
		$begin = array(
			'Почта' => './index.php',
			'Логи' => './logs.php',
			$title => '#'
		);
	
		$this->outputHeader($begin,$title);
		$design = CMSDesign::getInstance();
		$design->tableBegin();
		$design->row2cell('Статус',strlen($logRecord->status->getViewValue()) == 0?'Все ок':('<span class="important">'.$logRecord->status->getViewValue().'</span>'));
		$design->row2cell('Кому',$logRecord->to->getViewValue());
		$design->row2cell('Тема',$logRecord->subject->getViewValue());
		$design->row2cell('HTML-код',htmlspecialchars($logRecord->content->getValue()));
		$design->fullRow( '<a href="logs.php">Назад в лог</a>' );
		$design->tableEnd();
		$this->outputFooter();
		$this->output();
	}
	public function clear() {
		EmailLogModel::deleteAll();
		$this->addAlert('История писем очищена');
		$this->jumpBack();
	}
}