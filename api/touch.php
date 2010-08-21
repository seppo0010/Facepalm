<?php
require 'boot.php';

$facepalm = new Facepalm($_REQUEST['id']);
if ($facepalm->id == 0) {
	echo json_encode(array('error' => 'Invalid user'));
} else {
	$last = $facepalm->lastFacepalm();
	$facepalm->touch($_REQUEST['reason']);
	echo json_encode(array(
		'last' => $last
	));
}
