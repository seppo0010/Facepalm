<?php
require 'boot.php';
$uid = $facebook->getUser();
$session = $facebook->getSession();
$facepalm = new Facepalm($_GET['id']);
$facepalm->setFacebook($uid, $session['access_token']);
header('location: index.php');
