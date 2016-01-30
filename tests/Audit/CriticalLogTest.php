<?php
namespace Extasy\tests\Audit;

use Extasy\tests\Mocks\Mailer;
use Email_Controller;
use Extasy\Audit\Record;
class CriticalLogTest extends base {
	/**
	 * @var \Extasy\tests\Mocks\Mailer
	 */
	protected $mailer = null;
	public function setUp() {
		parent::setUp();
		$this->mailer = new Mailer();
		Email_Controller::setMailer( $this->mailer );
	}
	public function testEmailNotSentOnLogMessage( ) {
		Record::add('Log1','','');
		$this->assertFalse( $this->mailer->isSent( ));
	}
	public function testEmailSentOnCriticalLogMessage( ) {
		Record::add('Log2','','');
		$this->assertTrue( $this->mailer->isSent( ));
	}
}