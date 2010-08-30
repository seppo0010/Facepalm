<?php
$config['base_url'] = 'http://facepalm.delapalo.net/';
$config['facebook_app_id'] = '146881561998534';
$config['facebook_app_secret'] = 'e70e5c589a57ee66a96796451a063330';
$config['default_timezone'] = 'America/Argentina/Buenos_Aires';

define('SOCIAL_NETWORK_FACEBOOK', 1);
$id = NULL;
if (isset($config['default_timezone'])) date_default_timezone_set($config['default_timezone']);

$filename=dirname(__FILE__) . '/../db.sql';
$is_new_database = !is_file($filename);
$database = new PDO('sqlite:' . $filename);
if ($is_new_database)
{
        $database->query('CREATE TABLE facepalm ( id INTEGER PRIMARY KEY, nombre TEXT unique, fecha NUMERIC )');
        $database->query('CREATE TABLE facepalm_log ( id INTEGER PRIMARY KEY, user_id NUMERIC, fecha NUMERIC, reason TEXT, vote_up NUMERIC, vote_down NUMERIC )');
        $database->query('CREATE TABLE user_mail ( id INTEGER PRIMARY KEY, user_id NUMERIC, email_hash TEXT unique )');
		$database->query('CREATE TABLE social_network (id INTEGER PRIMARY KEY,  facepalm_id INTEGER, network_id INTEGER, public_info TEXT NOT NULL, access_token TEXT NOT NULL )');
}

require dirname(__FILE__) . '/classes/Facepalm.php';
require 'classes/facebook.php';
$facebook = new Facebook(array(
	'appId'  => $config['facebook_app_id'],
	'secret' => $config['facebook_app_secret'],
	'cookie' => true
));

$default_lang = 'es';
$lang = isset($_COOKIE['lang']) ? $_COOKIE['lang'] : $default_lang;
if (!is_file('i18n/' . $lang .'.php')) $lang = $default_lang;
Facepalm::$db = $database;
