<?php
require 'boot.php';

$facepalm = Facepalm::identify($_REQUEST);
if ($facepalm == null) {
	echo json_encode(array('error' => 'Invalid user'));
} else {
	echo json_encode(array('history' => $facepalm->fetchHistory()));
}
