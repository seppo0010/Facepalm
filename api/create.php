<?php
require 'boot.php';
$name = trim($_REQUEST['name']);
if (empty($name)) {
	echo json_encode(array('error' => 'Invalid name'));
} else {
	$id = Facepalm::create($name);
	if (empty($id)) {
	echo json_encode(array('error' => 'Failed to insert - probably user nickname'));
	} else {
		echo json_encode(array('id' => $id));
	}
}
