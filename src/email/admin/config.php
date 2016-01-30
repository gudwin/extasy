<?

class Email_Admin_Config extends AdminPage
{
	protected $title = '';
	protected $begin = array();
	/**
	 * 
	 * @var SystemRegister
	 */
	protected $register  = null;
	public function __construct()
	{
		parent::__construct();
		$this->addPost('ssl,server,port,name,password,from_email,from_name','post');
		$this->title = 'Редактирование данных для аутентификации';
		$this->begin = array(
			'Почта' => './index.php',
			$this->title => '#',
			);
		$this->register = new SystemRegister('System/email');	
	}
	public function main() {
		$this->outputHeader($this->begin,$this->title);
		// Вывод поясняющего текста
		$design = CMSDesign::getInstance();
		$design->contentBegin();
		print '<p>В данной форме вы устанавливаете настройки для работы Вашего сайта с почтой. Если настройки на этой странице будут неверными, это может нарушить работу сайта</p>';
		$design->contentEnd();
		// Вывод формы редактирования
		$this->outputForm();
		$this->outputFooter();
		$this->output();
	}
	/**
	 * Выводит форму редактирования
	 */
	protected function outputForm() {
		$ssl = new CCheckbox();
		$ssl->name = 'ssl';
		$ssl->checked = (bool)$this->register->enable_ssl->value;
		$ssl->label = 'Да/Нет';
		$ssl->value = 1;
		$ssl->force_send = true;
		//
		$server = new CInput();
		$server->name = 'server';
		$server->value = $this->register->smtp_server->value;
		$port = new CInput();
		$port->name = 'port';
		$port->value = $this->register->smtp_port->value;
		$name = new CInput();
		$name->name = 'name';
		$name->value = $this->register->smtp_user->value;
		$password = new CInput();
		$password->name = 'password';
		$password->value = $this->register->smtp_password->value;
		$from_email = new CInput();
		$from_email->name = 'from_email';
		$from_email->value = $this->register->from_email->value;
		$from_name = new CInput();
		$from_name->name = 'from_name';
		$from_name->value = $this->register->from_name->value;
		
		$design = CMSDesign::getInstance();
		$design->formBegin();
		$design->submit('submit','Сохранить');
		$design->tableBegin();
			$design->row2cell('Требуется SSL-соединение',$ssl);
			$design->row2cell('Сервер',$server);
			$design->row2cell('Порт',$port);
			$design->row2cell('Пользователь',$name);
			$design->row2cell('Пароль',$password);
			$design->row2cell('E-mail, откуда шлется письмо',$from_email);
			$design->row2cell('Подпись пользователя',$from_name);
		$design->tableEnd();
		$design->submit('submit','Сохранить');
		$design->formEnd();
	}
	public function post($ssl,$server,$port,$name,$password,$from_email,$from_name) {
		$this->register->enable_ssl->value = $ssl;
		$this->register->smtp_server->value = $server;
		$this->register->smtp_port->value = $port;
		$this->register->smtp_user->value = $name;
		$this->register->smtp_password->value = $password;
		$this->register->from_email->value = $from_email;
		$this->register->from_name->value = $from_name;
		SystemRegisterSample::createCache();
		
		$this->addAlert('Настройки сохранены');
		$this->jumpBack();
	}

}
?>