<?php
class Facepalm {
	static public $db;
	var $id;

	function __construct($id = NULL) {
		$this->id = (int)$id;
	}

	function touch($reason) {
		self::$db->query('INSERT INTO facepalm_log (user_id, fecha, reason) VALUES (' . $this->id . ', ' . time() . ', '. self::$db->quote($reason) .')');
		self::$db->query('UPDATE facepalm SET fecha = ' . time() . ' WHERe id = ' . $this->id);
	}

	function name() {
		$query = self::$db->query('SELECT nombre FROM facepalm WHERE id =' . $this->id);
		$user = $query->fetchObject();
		return $user->nombre;
	}

	function lastFacepalm() {
		$query = self::$db->query('SELECT fecha FROM facepalm WHERE id =' . $this->id);
		$user = $query->fetchObject();
		return $user->fecha;
	}

	function remove() {
		self::$db->query('DELETE FROM facepalm WHERe id = ' . $this->id);
	}


	static function create($name) {
		self::$db->query('INSERT INTO facepalm (nombre, fecha) VALUES (' . self::$db->quote($name) . ', ' . time() . ')');
		$id = self::$db->lastInsertId();
		return $id;
	}

	static function fetchlist() {
		$query = self::$db->query('SELECT * FROM facepalm ORDER BY nombre ASC, id ASC');
		$users = array();
		while ($user = $query->fetchObject()) {
			$users[] = $user;
		}
		return $users;
	}
}
