<?php
require 'boot.php';

$facepalm = new Facepalm($_REQUEST['id']);
$last = $facepalm->lastFacepalm();
$facepalm->touch($_REQUEST['reason']);
echo json_encode(array(
	'last' => $last
));
