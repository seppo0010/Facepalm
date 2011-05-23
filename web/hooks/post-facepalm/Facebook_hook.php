<?php
require_once dirname(__FILE__) . '/../../classes/Curl.php';

class Facebook_hook {

	var $user;

	function __construct($user) {
		$this->user = $user;
	}

	function post_facepalm($time, $reason) {
		if (empty($reason)) $str = 'Facepalm';
		else $str = 'Facepalm ' . $reason;
		$credentials = $this->user->getSocialNetworkCredentiales(SOCIAL_NETWORK_FACEBOOK);
		if (!$credentials) return;
		$base_url = $GLOBALS['config']['base_url'];
		$response = Curl::do_post('https://graph.facebook.com/me/feed', array(
			'message' => $str,
			'access_token' => $credentials->access_token,
			'picture' => $base_url . 'facepalm1.jpg',
			'link' => $base_url,
			'name' => 'Facepalm!',
		));
	}
}
