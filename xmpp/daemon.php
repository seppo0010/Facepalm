<?php
error_reporting(E_ALL|E_STRICT);
ini_set('display_errors', 'On');

require_once 'classes/XMPPHP/XMPP.php';
require_once 'classes/Curl.php';

#Use XMPPHP_Log::LEVEL_VERBOSE to get more logging for error reports
#If this doesn't work, are you running 64-bit PHP with < 5.2.6?
Chat::$conn = $conn = new XMPPHP_XMPP('talk.google.com', 5222, 'facepalm.service', 'tr!v!alpassword', 'xmpphp', 'gmail.com', $printlog=true, $loglevel=XMPPHP_Log::LEVEL_WARNING);
$conn->connect();

$client = FacepalmClientXMPP::getInstance();
while(!$conn->isDisconnected()) {
	$payloads = $conn->processUntil(array('message', 'session_start', 'end_stream'), 3600);
	foreach($payloads as $event) {
		switch($event[0]) {
		case 'message':
			if ($event[1]['body'] === NULL) continue;
			$from = substr($event[1]['from'], 0, strpos($event[1]['from'], '/'));
			$chat = Chat::chatWithUser($from);
			if ($chat != NULL) {
				$chat->from = $event[1]['from'];
				$chat->process($event[1]['body']);
			} else if (strtolower(substr($event[1]['body'], 0, 8)) == 'facepalm') {
				$chat = new Chat($from, $event[1]['from']);
				$response = $chat->facepalm(trim(substr($event[1]['body'], 8)));
			}
			break;

		case 'session_start':
			$conn->presence($status="Facepalm");
			$conn->autoSubscribe();
			break;
		}
	}
}
class FacepalmClient {
	const BASE_URL = 'http://seppo.is-a-geek.com/facepalm.delapalo.net/web/api/';

	public function facepalm($identifier, $message) {
		$info = $identifier;
		$info['reason'] = $message;
		$url = self::BASE_URL . 'touch.php';
		return json_decode(Curl::do_post($url, $info));
	}

	public function listUsers() {
		$url = self::BASE_URL . 'list.php';
		return json_decode(Curl::do_post($url, null));
	}

	public function create($nickname) {
		$url = self::BASE_URL . 'create.php';
		return json_decode(Curl::do_post($url, array('name' => $nickname)));
	}

	public function addMail($identifier, $email) {
		$info = $identifier;
		$info['email'] = $email;
		$url = self::BASE_URL . 'addmail.php';
		return json_decode(Curl::do_post($url, $info));
	}
}
class FacepalmClientXMPP extends FacepalmClient {
	static public $instance;
	static public function getInstance() {
		if (self::$instance == null) self::$instance = new self();
		return self::$instance;
	}

	private function __construct() {
		self::$instance = $this;
	}

	public function facepalm($email, $message) {
		return parent::facepalm(array('email' => $email), $message);
	}

	public function create($nickname, $email) {
		$info = parent::create($nickname);
		if ($info->id > 0) {
			$this->addMail(array('id' => $info->id), $email);
		}
	}
}

class Chat {
	static public $chats;

	var $user; // email
	var $from; // full chat, with client especification
	var $expecting_method;
	var $pending = array();
	static public $conn;

	public function addme($body) {
		if (substr($body,0,7) == 'agregar') {
			$response = FacepalmClientXMPP::getInstance()->create(trim(substr($body,7)), $this->user);
			if ($this->lookForErrors($response) == FALSE) {
				if (count($this->pending) == 0) $this->message('Listo y agregado!');
				else $this->executePending();
				self::$chats[$this->user] = NULL;
			}
		} else {
			$response = FacepalmClientXMPP::getInstance()->addMail(array('nickname' => $body), $this->user);
			if ($this->lookForErrors($response) == FALSE) {
				if (count($this->pending) == 0) $this->message('Listo y agregado!');
				else $this->executePending();
				self::$chats[$this->user] = NULL;
			}
		}
	}

	public function executePending() {
		foreach ($this->pending as $task) {
			call_user_func_array(array($this, $task['method']), $task['params']);
		}
		$this->pending = array();
	}

	public function __construct($user, $from) {
		$this->user = $user;
		$this->from = $from;
		self::$chats[$user] = $this;
	}

	public function unknownUser() {
		$this->expecting_method = 'addme';
		$client = FacepalmClientXMPP::getInstance();
		$users = $client->listUsers();
		$response = 'No te conozco, sos alguno de estos?' ."\n";
		foreach ($users as $user) {
			$response .= $user->nombre . "\n";
		}

		$this->message($response . 'Sino decime "agregar" seguido de tu nombre');
	}

	private function message($str) {
		self::$conn->message($this->from, $str);
	}
	public function process($body) {
		$this->{$this->expecting_method}($body);
	}

	static public function chatWithUser($user) {
		return isset(self::$chats[$user]) ? self::$chats[$user] : null;
	}

	public function lookForErrors($response) {
		if (isset($response->errormessage)) {
			$this->message($response->errormessage);
		} else if (isset($response->errorcode) && $response->errorcode == 'invaliduser') {
			$this->unknownUser();
		} else {
			return FALSE;
		}
		return TRUE;
	}

	public function facepalm($reason) {
		$response = FacepalmClientXMPP::getInstance()->facepalm($this->user, $reason);
		if ($this->lookForErrors($response) == FALSE) {
			$this->message('Facepalmeado!');
			self::$chats[$this->user] = NULL;
		} else {
			$this->pending[] = array(
				'method' => 'facepalm',
				'params' => array($reason)
			);
		}
	}
}
