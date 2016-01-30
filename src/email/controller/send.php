<?
//************************************************************//
//                                                            //
//                     Заголовок                              //
//       Copyright (c) 2008 Ext-CMS (http://www.ext-cms.com/) //
//               отдел/сектор                                 //
//       Email:    info@gisma.ru (http://www.gisma.ru/)       //
//                                                            //
//  Разработчик: Gisma (06.10.2008)                           //
//  Модифицирован:  06.10.2008  by Gisma                      //
//                                                            //
//************************************************************//
//  12.11.2008 Адаптирован к влючению в библиотеку модулей    //
//************************************************************//
use \Faid\Debug\Debug as Debug;
use \Faid\UParser as UParser;

/**
 * @todo Сделать проверку того, что данные почты установлены корректно.
 */
class Email_Controller {
	const LogName = 'email';
	protected static $mailer = null;
	/**
	 * @return ExtasyMailer
	 */
	public static function getMailer() {
		if ( is_null( self::$mailer )) {
			self::$mailer = self::getDefaultMailer();
		}
		return self::$mailer;
	}
	public static function setMailer( $mailer ) {
		if ( is_null( $mailer )) {
			$mailer = self::getDefaultMailer();
		}
		self::$mailer = $mailer;
	}

	/**
	 * @param $aTo       array массив получателей
	 * @param $szSubject string тема письма
	 * @param $szContent string html-код письма
	 *   Отправляет письмо
	 *
	 * @return
	 */
	public static function send($aTo, $szSubject, $szContent) {
		$register = new SystemRegister('System/email');
		if ( !is_array($aTo) ) {
			$aTo = explode(',', $aTo);
		}
		// Если включена отсылка через стандартную функцию mail() то вызываем её
		if ( $register->use_standart_mail_function->value != 0 ) {
			self::sendUsingStandartMail($aTo, $szSubject, $szContent);

			return;

		}
		$mail          = self::getMailer();

		$mail->isSMTP();
		$mail->Subject = $szSubject;

		// Добавляем получаетелей
		foreach ($aTo as $szTo) {
			$mail->AddAddress($szTo);
		}
		// Текст письма		
		$mail->MsgHTML($szContent);

		$mail->IsHTML(true); // send as HTML


		$mail->Send();

		return $mail;


	}

	/**
	 * Парсит и отсылает письмо
	 * @param mixed $to        получатели
	 * @param string $subject  шаблон темы письма
	 * @param string $content  шаблон текста письма
	 * @param array $parseData массив данных
	 */
	public static function parseAndSend($to, $subject, $content, $parseData) {
		$content = UParser::parsePHPCode($content, $parseData);
		$subject = UParser::parsePHPCode($subject, $parseData);
		self::send($to, $subject, $content);
	}

	/**
	 * Отсылает HTML-письма используя стандартную функцию отсылки писем - mail()
	 *
	 * @param array $recipients
	 * @param string $subject
	 * @param string $content
	 */
	public static function sendUsingStandartMail($recipients, $subject, $content) {
		$log = new EmailLogModel();
		// Подготовляем для логирования
		$log->to      = implode(',', $recipients);
		$log->subject = $subject;
		$log->content = $content;
		$log->date    = date('Y-m-d H:i');
		$log->insert();

		$register = new SystemRegister('System/email');
		$headers  = 'MIME-Version: 1.0' . "\r\n" .
			'Content-type: text/html; charset=utf-8' . "\r\n" .
			'From: %s' . "\r\n" .
			'X-Mailer: PHP/%s';
		$headers  = sprintf($headers, $register->from_email->value, phpversion());
		foreach ($recipients as $to) {
			mail($to, $subject, $content, $headers);
		}
	}
	protected static function getDefaultMailer( ) {
		return new ExtasyMailer();
	}
}

?>