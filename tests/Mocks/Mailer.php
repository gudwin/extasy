<?php
namespace Extasy\tests\Mocks;

class Mailer extends \PHPMailer {
	protected $called = false;
	protected $originalHtml = false;
	protected $originalTo = array();
	public function AddAddress($address, $name = '') {
		$this->originalTo[] = $address;
		return parent::AddAddress( $address, $name );
	}

	public function Send() {
		$this->called = true;

		//
		$log = new \EmailLogModel();
		// Подготовляем для логирования
		$log->to = implode(',',$this->originalTo);
		$log->subject = $this->Subject;
		$log->content = $this->originalHtml;
		$log->date = date('Y-m-d H:i');
		$log->insert();

		$this->originalHtml = '';
		$this->originalTo = [];
	}
	public function isSent( ) {
		return $this->called;
	}
	public function reset( ) {
		$this->called = false;
	}
	public function MsgHTML($message,$basedir='') {
		$this->originalHtml = $message;
		return parent::MsgHTML( $message, $basedir);
	}
}