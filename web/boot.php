<?php
$id = NULL;
//error_reporting(E_NONE);
date_default_timezone_set('America/Argentina/Buenos_Aires');
$filename=dirname(__FILE__) . '/../db.sql';
$is_new_database = !is_file($filename);
//$database = new SQLiteDatabase($filename);
$database = new PDO('sqlite:' . $filename);
if ($is_new_database)
{
        $database->query('CREATE TABLE facepalm ( id INTEGER PRIMARY KEY, nombre TEXT unique, fecha NUMERIC )');
        $database->query('CREATE TABLE facepalm_log ( id INTEGER PRIMARY KEY, user_id NUMERIC, fecha NUMERIC, reason TEXT )');
        $database->query('CREATE TABLE user_mail ( id INTEGER PRIMARY KEY, user_id NUMERIC, email_hash TEXT unique )');
}

require dirname(__FILE__) . '/classes/Facepalm.php';
Facepalm::$db = $database;
