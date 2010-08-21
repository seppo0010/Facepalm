<?php
require_once dirname(__FILE__) . '/../../classes/Curl.php';

class Identica {
	const USERNAME = 'facepalmservice';
	const PASSWORD = 'tr!v!alpassword';

	var $user;

	function __construct($user) {
		$this->user = $user;
	}

	function post_facepalm($time, $reason) {
		$str = 'Facepalm para ' .$this->user->nombre . ' por ' . $reason;
		if ($str > 140) $str = substr($str, 0, 137) . '...';
		$response = Curl::do_post('https://' . (self::USERNAME . ':' . self::PASSWORD) . '@identi.ca/api/statuses/update.json', array('status' => $str));
	}
}
