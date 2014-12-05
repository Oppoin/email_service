<?php
App::uses('EmailServiceAppModel', 'EmailService.Model');
/**
 * EmailAlert Model
 *
 */
class EmailAlert extends EmailServiceAppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'name';

	public $name = 'EmailAlert';

	public function prepareSenderRecipients($name) {
		$recipients = $this->find('first', [
			'conditions' => [
				'EmailAlert.name' => $name,
			],
		]);

		if (empty($recipients)) {
			throw new Exception('no such email alert');
		}

		$toList = json_decode($recipients['EmailAlert']['to_list'], true);

		$ccList = json_decode($recipients['EmailAlert']['cc_list'], true);

		$bccList = json_decode($recipients['EmailAlert']['bcc_list'], true);

		$sender = json_decode($recipients['EmailAlert']['sender'], true);

		$testTo = json_decode($recipients['EmailAlert']['test_to'], true);

		$replyTo = json_decode($recipients['EmailAlert']['reply_to'], true);

		$subject = $recipients['EmailAlert']['subject'];

		return compact('toList', 'ccList', 'bccList', 'sender', 'testTo', 'replyTo', 'subject');
	}
}
