<?php
require 'boot.php';

$facepalm = new Facepalm($_REQUEST['id']);
if ($facepalm->id == 0) {
	echo json_encode(array('error' => 'Invalid user'));
} else {
	echo json_encode(array('history' => $facepalm->fetchHistory()));
}
