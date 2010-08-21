<?php
require 'boot.php';

$facepalm = Facepalm::identify($_REQUEST);
if ($facepalm == null) {
	echo json_encode(array('errorcode' => 'invaliduser'));
} else {
	echo json_encode(array('history' => $facepalm->fetchHistory()));
}
