<?php
require 'boot.php';

$query = $database->query('SELECT * FROM facepalm ORDER BY nombre ASC, id ASC');

header('Content-type:application/json');
echo json_encode(Facepalm::fetchlist());
