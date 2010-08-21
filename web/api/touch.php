<?php
require 'boot.php';

$facepalm = Facepalm::identify($_REQUEST);
if ($facepalm == null) {
	echo json_encode(array('errorcode' => 'invaliduser'));
} else {
	$last = $facepalm->lastFacepalm();
	$facepalm->touch($_REQUEST['reason']);
	echo json_encode(array(
		'last' => $last
	));
}
