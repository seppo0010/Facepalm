<?php
require 'boot.php';

$email = trim($_REQUEST['email']);
if (empty($email)) {
	echo json_encode(array('error' => 'Invalid email'));
}

$facepalm = Facepalm::identify($_REQUEST);
if ($facepalm == null) {
	echo json_encode(array('error' => 'Invalid user'));
} else {
	if ($facepalm->addMail($email) > 0) echo '{}';
	else echo json_encode(array('error' => 'Failed to add mail - probably email in use'));
}
