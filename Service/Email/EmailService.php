<?php
App::uses('CakeEmail', 'Network/Email');
App::uses('Router', 'Routing/Router');
App::uses('Configure', 'Core/Configure');
App::uses('ClassRegistry', 'Utility');
App::uses('EmailAlert', 'EmailService.Model');
/**
 * This is the email service that can be used to send all email alerts.
 *
 * from, to, cc, bcc names and email would be grabbed from the database.
 *
 * sender and config are constants set in the constants.php
 *
 * -------------------- TO USE ------------------
 *
 * Step 1: Simple create an object by passing in the name of the email contact list in the database.
 * You can pass in an additional parameter to only to the bccList.
 *
 * $this->EmailService = $this->EmailService($emailAlertName, $test);
 * 
 * Step 2: Set your subject
 *
 * $this->EmailService->setSubject($subject);
 *
 * Step 3: Set your template. <- this is the name of your view file used for the email
 *
 * $this->EmailController->setTemplate($template);
 *
 * Last Step trigger is sendAll function by passing in the view variables. $attachment is optional
 *
 * $this->EmailController->sendAll($data, $attachment);
 *
 * --------------------- END ---------------------
 */
class EmailService {

	private $attachments;
	private $bcc;
	private $cc;
	private $email;
	private $emailConfig = 'default';
	private $emailFormat = null;
	private $from;
	private $httpHost    = 'www.example.com'; // This should overwritten by constructor
	private $sender      = 'do-not-reply@example.com'; // This should overwritten by setEmailAlert
	private $subject     = 'foobar subject'; // This should overwritten by setEmailAlert
	private $template;
	private $to;
	private $debug = false;

	public function __construct($emailConfig) {
		$this->emailConfig = $emailConfig;
	}

	public function setEmailAlert($emailAlertName, $test = false) {
		$this->EmailAlert = ClassRegistry::init('EmailService.EmailAlert');
		$senderRecipients = $this->EmailAlert->prepareSenderRecipients($emailAlertName);
		extract($senderRecipients);

		$this->sender   = $sender;
		$this->from     = $sender;
		$this->to       = $to_list;
		$this->cc       = $cc_list;
		$this->bcc      = $bcc_list;
		$this->testTo   = $test_to;
		$this->subject  = $subject;
		$this->testTo   = $test_to;
		$this->template = $Inflector::underscore($emailAlertName);

	}

	protected function _setupBaseUrl() {
		$_SERVER['HTTP_HOST'] = $this->httpHost;

		if (empty($_SERVER['HTTPS']) && $_SERVER['HTTP_HOST'] == $this->httpHost) {
		  // set this to "on" if your website uses SSL
			$_SERVER['HTTPS'] = '';
		}

		if (!defined('FULL_BASE_URL_FOR_SHELL')) {
			define('FULL_BASE_URL_FOR_SHELL',
		'http'.(empty($_SERVER['HTTPS'])?'':'s').'://'.$_SERVER['HTTP_HOST']);
		}

	}

	protected function attach($attachments) {
		$this->attachments = $attachments;
	}

/**
 * set view template for email..
 */
	public function setTemplate($template) {
		$this->template($template, 'default');
	}

/**
 * set email format
 * @param $format. expecting either html or text or both
 */
	public function setEmailFormat($format) {
		$this->emailFormat = $format;
	}

/**
 * set subject
 */
	public function setSubject($subject) {
		$this->email->subject($subject);
	}

/**
 * set subject
 */
	public function setReplyTo($replyTo) {
		$this->email->replyTo($replyTo);
	}

/**
 * set subject
 */
	public function setFormat($format = null) {
		$this->email->emailFormat($format);
	}

/**
 * set tolist
 * @param $toList.. expecting a json_string
 */
	public function setToList($toList) {
		$this->to($toList);
	}

/**
 * set cclist
 * @param $toList.. expecting a json_string
 */
	public function setCcList($ccList) {
		$this->cc($ccList);
	}

/**
 * set bcclist
 * @param $bccList.. expecting a json_string
 */
	public function setBccList($bccList) {
		$this->bcc($bccList);
	}

/**
 * get tolist
 * @param $toList.. expecting a json_string
 */
	public function getToList() {
		return $this->to();
	}


/**
 * set cclist
 * @param $ccList.. expecting a json_string
 */
	public function getCcList() {
		return $this->cc();
	}

/**
 * set bcclist
 * @param $bccList.. expecting a json_string
 */
	public function getBccList() {
		return $this->bcc();
	}

/**
 * set bcclist
 * @param $bccList.. expecting a json_string
 */
	public function setHttpHost($httpHost) {
		$this->HttpHost = $httpHost;
	}

/**
 * function to set the email
 */
	public function sendAll($data = [], $attachments = [], ) {
		$this->email = new CakeEmail($this->emailConfig);
		$this->email->sender($this->sender);
		$this->email->from($this->sender);
		$this->email->replyTo($this->replyTo);
		$this->email->subject($this->subject);
		$this->email->template($this->template);
		if($this->emailFormat == null) {
			$this->email->emailFormat($emailFormat);
		}

		if($this->attachments == null && !empty($attachments)) {
			$this->email->attachments($attachments);
		}

		if($test) {
			$toList = $testTo;
			$ccList = [];
			$bccList = [];
		}

		if (!empty($toList)) {
			$this->email->to($this->to);
		}

		if (!empty($ccList)) {
			$this->email->cc($this->cc);
		}

		if (!empty($bccList)) {
			$this->email->bcc($this->bcc);
		}

		$this->_setupBaseUrl();

		$data['fullBaseUrl'] = FULL_BASE_URL_FOR_SHELL;

		$this->email->viewVars($data);
		if (!empty($attachments)) {
			$this->email->attachments($attachments);
		}

		if (Configure::read('EMAIL_ON')) {
			
			//this is the content
			$result = $this->email->send();
			
		} else {
			$result = $this->email;
		}
		return $result;

	}

	public function sendEmailToEachUser($data = [], $attachments = []) {
		$toList    = $this->email->to();
		$allEmails = [];
		foreach($toList as $emailAddress => $name) {
			$email = new CakeEmail($this->emailConfig);
			$email->to($emailAddress, $name);
			$email->from($this->from);
			$email->sender($this->sender);
			$email->replyTo($this->replyTo);
			$email->subject($this->subject);
			$email->template($this->template);
			$email->viewVars($data);
			if(!empty($attachments)) {
				$email->attachments($attachments);
			}
			array_push($allEmails, $email);
		}
		return $allEmails;

	}
}

