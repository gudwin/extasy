<?php
use \Faid\Debug\Debug as Debug;

class ExtasyMailer extends PHPMailer {
	protected $originalHtml = false;
	protected $originalTo = array();
	public function __construct() {
		$register = new SystemRegister('System/email');
		if ($register->enable_ssl->value)
		{
			$this->SMTPSecure = "ssl";
		}
		$this->Host       = $register->smtp_server->value;
		$this->Port       = $register->smtp_port->value;

		$this->Username   = $register->smtp_user->value;
		$this->Password   = $register->smtp_password->value;

		$this->From       = $register->from_email->value;
		$this->FromName   = $register->from_name->value;

		$this->AltBody    = "To view the message, please use an HTML compatible email viewer!";
		$this->WordWrap   = 50; // set word wrap
		$this->CharSet    = 'utf-8';
	}
	public function AddAddress($address, $name = '') {
		$this->originalTo[] = $address;
		return parent::AddAddress( $address, $name );
	}
	public function Send() {
		//
		$log = new EmailLogModel();
		// Подготовляем для логирования
		$log->to = implode(',',$this->originalTo);
		$log->subject = $this->Subject;
		$log->content = $this->originalHtml;
		$log->date = date('Y-m-d H:i');
		//
		debug::disable();
		//
		$result = parent::Send( );
		//
		debug::enable();
		//
		if (! $result) {
			$log->status = $this->ErrorInfo;
			$log->insert();
			throw new MailException("Mailer Error: " . $this->ErrorInfo);
		} else {
			$log->insert();
		}
		return $result;
	}
	public function MsgHTML($message,$basedir='') {
		$this->originalHtml = $message;
		return parent::MsgHTML( $message, $basedir);
	}
}