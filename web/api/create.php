<?php
require 'boot.php';
$name = trim($_REQUEST['name']);
if (empty($name)) {
	echo json_encode(array('errorcode' => 'invalidname'));
} else {
	$id = Facepalm::create($name);
	if (empty($id)) {
	echo json_encode(array('errormessage' => 'Failed to insert - probably user nickname'));
	} else {
		echo json_encode(array('id' => $id));
	}
}
