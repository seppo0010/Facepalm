<?php
class Facepalm {
	const POST_FACEPALM_HOOK_PATH = '../hooks/post-facepalm/';
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
		$path = dirname(__FILE__) .'/'.self::POST_FACEPALM_HOOK_PATH;
		foreach ((array)glob($path.'/*') as $hook) {
			require_once $hook;
			$offset = strrpos($hook, '/')+1;
			$class_name = substr($hook, $offset, strrpos($hook, '.') - $offset);
			$instance = new $class_name($this);
			$instance->post_facepalm($this->fecha, $reason);
		}
	}

	function name() {
		return $this->nombre;
	}

	function lastFacepalm() {
		return $this->fecha;
	}

	function remove() {
		self::$db->query('DELETE FROM facepalm WHERe id = ' . $this->id);
		self::$db->query('DELETE FROM facepalm_log WHERe user_id = ' . $this->id);
		self::$db->query('DELETE FROM user_mail WHERe user_id = ' . $this->id);
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

	static function fetchFromMail($mail) {
		$query = self::$db->query('SELECT user_id FROM user_mail WHERE email_hash = ' . self::$db->quote(md5($mail)));
		$user = @$query->fetchObject();
		if ($user == null) return null;
		else return new self($user->user_id);
	}

	function addMail($mail) {
		$hash = md5($mail);
		self::$db->query('INSERT INTO user_mail (user_id, email_hash) VALUES (' . $this->id . ', ' . self::$db->quote($hash) . ')');
		$id = self::$db->lastInsertId();
		return $id;
	}

	static function fetchFromNickname($nickname) {
		$query = self::$db->query('SELECT id FROM facepalm WHERE nombre = ' . self::$db->quote($nickname));
		$user = @$query->fetchObject();
		if ($user == null) return null;
		else return new self($user->id);
	}

	static function identify($info) {
		if (isset($info['id'])) {
			$user = new Facepalm($info['id']);
			if ($user->id > 0) return $user;
		}
		if (isset($info['email'])) {
			$user = self::fetchFromMail($info['email']);
			if ($user != null) return $user;
		}
		if (isset($info['nickname'])) {
			$user = self::fetchFromNickname($info['nickname']);
			if ($user != null) return $user;
		}

		return null;
	}

	function setFacebook($facebook_id, $access_token) {
		self::$db->query('DELETE FROM social_network WHERE network_id = ' . SOCIAL_NETWORK_FACEBOOK .' AND public_info = ' . self::$db->quote($facebook_id)); // deassociate any existing account
		self::$db->query('INSERT INTO social_network (network_id, facepalm_id, public_info, access_token) VALUES (' . SOCIAL_NETWORK_FACEBOOK . ', ' . $this->id .', ' . self::$db->quote($facebook_id) . ', ' . self::$db->quote($access_token) . ')');
	}

	function clearFacebook() {
		self::$db->query('DELETE FROM social_network WHERE network_id = ' . SOCIAL_NETWORK_FACEBOOK .' AND facepalm_id = ' . self::$db->quote($this->id));
	}

	function facebookUserHasFacepalm($facebook_id) {
		$query = self::$db->query('SELECT id FROM social_network WHERE network_id = '.SOCIAL_NETWORK_FACEBOOK.' AND public_info = ' . self::$db->quote($facebook_id));
		$user = @$query->fetchObject();
		if ($user == null) return null;
		else return $user->id > 0;
	}

	function getSocialNetworkCredentiales($social_network) {
		$query = self::$db->query('SELECT public_info, access_token FROM social_network WHERE network_id = '.$social_network.' AND facepalm_id  = ' . self::$db->quote($this->id));
		$user = @$query->fetchObject();
		if ($user == null) return null;
		else return $user;
	}

	static function fetchFromFacebookId($facebook_id) {
		$query = self::$db->query('SELECT facepalm_id FROM social_network WHERE network_id = '.SOCIAL_NETWORK_FACEBOOK.' AND public_info = ' . self::$db->quote($facebook_id));
		$user = @$query->fetchObject();
		if ($user == null) return null;
		else return new self($user->facepalm_id);
	}

	static function hasFacebook($facepalm_id) {
		$query = self::$db->query('SELECT id FROM social_network WHERE network_id = '.SOCIAL_NETWORK_FACEBOOK.' AND facepalm_id = ' . self::$db->quote($facepalm_id));
		$user = @$query->fetchObject();
		if ($user == null) return null;
		else return $user->id > 0;
	}

	static function fetchLogs($user_id, $order) {
		$query = self::$db->query('SELECT facepalm_log.id, facepalm_log.user_id, facepalm_log.reason, facepalm_log.fecha, facepalm.nombre, facepalm_log.vote_up, facepalm_log.vote_down FROM facepalm_log JOIN facepalm ON facepalm_log.user_id = facepalm.id ' . ($user_id === NULL ? '' : ' WHERE facepalm_log.user_id = ' . (int)$user_id ) . ' ORDER BY ' . $order . ' LIMIT 20');
		$return = array();
		while ($row = $query->fetchObject()) $return[] = $row;
		return $return;
	}

	static function vote($id, $is_up) {
		self::$db->query('UPDATE facepalm_log SET vote_' . ($is_up ? 'up' : 'down') . ' = vote_' . ($is_up ? 'up' : 'down') . ' + 1 WHERe id = ' . $id);
	}
}
