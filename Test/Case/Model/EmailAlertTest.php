<?php
App::uses('EmailAlert', 'EmailService.Model');

/**
 * EmailAlert Test Case
 *
 */
class EmailAlertTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.email_service.email_alert'
	);

	public $plugin = 'EmailService';

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->EmailAlert = ClassRegistry::init('EmailService.EmailAlert');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->EmailAlert);

		parent::tearDown();
	}

	public function testPrepareSenderRecipients() {
		// GIVEN we run for DailyOutstandingNoOBEmailAlert
		$senderRecipients = $this->EmailAlert->prepareSenderRecipients('DailyOutstandingNoOBEmailAlert');
		// THEN we expect to see
		extract($senderRecipients);
		$this->assertEqual(['full_name' => 'RUPERT', 'email' => 'kimsia@oppoin.com'], $sender);
	}
}
