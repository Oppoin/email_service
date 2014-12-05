<?php
App::uses('EmailAlert', 'EmailService.Model');

/**
 * EmailAlert Test Case
 *
 */
class EmailServiceTest extends CakeTestCase {

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
		$this->EmailService = new EmailService('default');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->EmailService);

		parent::tearDown();
	}

}
