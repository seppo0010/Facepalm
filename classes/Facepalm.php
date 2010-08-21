<?php
class Facepalm {
	static public $db;
	var $id;
	var $nombre;
	var $fecha;

	function __construct($id = NULL) {
		$this->id = (int)$id;
		$query = self::$db->query('SELECT nombre, fecha FROM facepalm WHERE id =' . $this->id);
		$user = @$query->fetchObject();
		if ($user == null) {
			$this->id = 0;
		} else {
			$this->nombre = $user->nombre;
			$this->fecha = $user->fecha;
		}
	}

	function touch($reason) {
		$this->fecha = time();
		self::$db->query('INSERT INTO facepalm_log (user_id, fecha, reason) VALUES (' . $this->id . ', ' . $this->fecha . ', '. self::$db->quote($reason) .')');
		self::$db->query('UPDATE facepalm SET fecha = ' . $this->fecha . ' WHERe id = ' . $this->id);
	}

	function name() {
		return $this->nombre;
	}

	function lastFacepalm() {
		return $this->fecha;
	}

	function remove() {
		self::$db->query('DELETE FROM facepalm WHERe id = ' . $this->id);
	}

	function fetchHistory() {
		$query = self::$db->query('SELECT * FROM facepalm_log WHERE user_id = ' . $this->id .' ORDER BY fecha DESC');
		return $query->fetchAll(PDO::FETCH_OBJ);
	}

	static function create($name) {
		if (empty($name)) return 0;
		self::$db->query('INSERT INTO facepalm (nombre, fecha) VALUES (' . self::$db->quote($name) . ', ' . time() . ')');
		$id = self::$db->lastInsertId();
		return $id;
	}

	static function fetchlist() {
		$query = self::$db->query('SELECT * FROM facepalm ORDER BY nombre ASC, id ASC');
		return $query->fetchAll(PDO::FETCH_OBJ);
	}
}
