<?php
require 'boot.php';
$uid = $facebook->getUser();
$facepalm = Facepalm::fetchFromFacebookId($uid);
if ($facepalm) {
	$facepalm->clearFacebook();
}
header('location: index.php'); // allready logout
