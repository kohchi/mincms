<?php
/**
 * Class of Login Session
 *
 * This class handle the Login Session in MinCMS.
 *
 * @version $Id:$
 * @access public
 * @package MinCMS
 * @author $Author:$
 */
class MinSession {
	/** this session name. @var string */		var $name;

	/**
	 * The constructor
	 *
	 * invoke when an instance of this class is generated.
	 */
	function __construct() {
		$this->generate_name();
		session_name($this->name);
		session_start();
	}

	/**
	 * generate session name
	 *
	 * session name is generated of defined name.
	 */
	function generate_name() {
		$this->name = sha1(MC_COOKIE_NAME);
	}

	/**
	 * confirm that you are logging in.
	 *
	 * confirm whether or not you are logging in now.
	 *
	 * @return boolean return true if you login, otherwise false.
	 */
	function do_login() {
		return isset($_SESSION['login']) &&
				$_SESSION['login'] === True ? True : False;
	}

	/**
	 * regist a pair of key and value
	 *
	 * regist a pair of key and value to a instance variable.
	 *
	 * @param string $key a key string
	 * @param string $value a value string of key
	 */
	function register($key, $val) {
		$_SESSION[$key] = $val;
	}

	/**
	 * undefined a session value
	 *
	 * undefined a session value of key.
	 *
	 * @param string $key a key string
	 */
	function undef($key) {
		unset($_SESSION[$key]);
	}

	/**
	 * destroy the cookie
	 *
	 * destroy the current cookie.
	 */
	function destroy() {
		$_SESSION = array();
		session_destroy();
	}

	/**
	 * get a cookie value
	 *
	 * get a cookie value that specified key.
	 *
	 * @param string $key a key string of cookie value that you want to get
	 * @return string the cookie value of specified key
	 */
	function get($key) {
		return $_SESSION[$key];
	}

	/**
	 * get the session ID.
	 *
	 * get the current session ID.
	 *
	 * @return string current session ID.
	 */
	function get_session_id() {
		return session_id();
	}
}

/**
 * Class of MinCMS Login
 *
 * login and logout to MinCMS and keep the your information.
 *
 * @version $Id:$
 * @access public
 * @package MinCMS
 * @author $Author:$
 */
class MinLogin {
	/** instance of MinDB. @var MinDB */		var $db;
	/** session instance. @var MinSession */	var $s;
	/** login id.  @var string */			var $id;
	/** login name of id.  @var string */		var $name;
	/** an mailaddress of login id.  @var string */	var $email;
	/** authority status. @var int */		var $authority;
	/** client IP address. @var int */		var $ip;

	/**
	 * The constructor
	 *
	 * invoke when an instance of this class is generated.
	 *
	 * @param MinDB $db a MinDB instance
	 */
	function __construct($db) {
		$this->db = $db;
		$this->s = new MinSession();

		$this->set();
	}

	function set() {
		$this->id = $this->s->get('id');
		$this->name = $this->s->get('name');
		$this->email = $this->s->get('email');
		$this->authority = $this->s->get('authority');
		$this->ip = $this->s->get('ip');
	}

	function done() {
		if ($this->s->do_login() &&
			$this->s->get('ip') == $_SERVER['REMOTE_ADDR']) {
			return True;
		}

		return False;
	}

	function can_view($auth = -1) {
		$auth = $auth != -1 ? $auth : $this->authority;
		return ($auth & MC_ADMIN_AUTH_VIEW) ==
			MC_ADMIN_AUTH_VIEW ? True : False;
	}

	function can_create($auth = -1) {
		$auth = $auth != -1 ? $auth : $this->authority;
		return ($auth & MC_ADMIN_AUTH_CREATE) ==
			MC_ADMIN_AUTH_CREATE ? True : False;
	}

	function can_inspect($auth = -1) {
		$auth = $auth != -1 ? $auth : $this->authority;
		return ($auth & MC_ADMIN_AUTH_INSPECT) ==
			MC_ADMIN_AUTH_INSPECT ? True : False;
	}

	function can_publish($auth = -1) {
		$auth = $auth != -1 ? $auth : $this->authority;
		return ($auth & MC_ADMIN_AUTH_PUBLISH) ==
			MC_ADMIN_AUTH_PUBLISH ? True : False;
	}

	function can_edit_article($auth = -1) {
		$auth = $auth != -1 ? $auth : $this->authority;
		return $this->can_create($auth) || $this->can_inspect($auth) ||
			$this->can_publish($auth);
	}

	function can_edit_page($auth = -1) {
		$auth = $auth != -1 ? $auth : $this->authority;
		return ($auth & MC_ADMIN_AUTH_PAGE) ==
			MC_ADMIN_AUTH_PAGE ? True : False;
	}

	function have_user_admin($auth = -1) {
		$auth = $auth != -1 ? $auth : $this->authority;
		return ($auth & MC_ADMIN_AUTH_USER_ADMIN) ==
			MC_ADMIN_AUTH_USER_ADMIN ? True : False;
	}

	function have_site_admin($auth = -1) {
		$auth = $auth != -1 ? $auth : $this->authority;
		return ($auth & MC_ADMIN_AUTH_SITE_ADMIN) ==
			MC_ADMIN_AUTH_SITE_ADMIN ? True : False;
	}

	function have_tmpl_admin($auth = -1) {
		$auth = $auth != -1 ? $auth : $this->authority;
		return ($auth & MC_ADMIN_AUTH_TMPL_ADMIN) ==
			MC_ADMIN_AUTH_TMPL_ADMIN ? True : False;
	}

	function have_conf_admin($auth = -1) {
		$auth = $auth != -1 ? $auth : $this->authority;
		return ($auth & MC_ADMIN_AUTH_CONF_ADMIN) ==
			MC_ADMIN_AUTH_CONF_ADMIN ? True : False;
	}

	function is_admin($auth = -1) {
		$auth = $auth != -1 ? $auth : $this->authority;
		return $auth == MC_ADMIN_AUTH_ADMIN ? True : False;
	}

	function get_authname($auth = 0) {
		if ($this->is_admin($auth)) {
			return array(MC_ADMIN_AUTH_ADMIN_NAME);
		}

		$ret = array();
		if ($this->can_view($auth)) {
			$ret[] = MC_ADMIN_AUTH_VIEW_NAME;
		}
		if ($this->can_create($auth)) {
			$ret[] = MC_ADMIN_AUTH_CREATE_NAME;
		}
		if ($this->can_inspect($auth)) {
			$ret[] = MC_ADMIN_AUTH_INSPECT_NAME;
		}
		if ($this->can_publish($auth)) {
			$ret[] = MC_ADMIN_AUTH_PUBLISH_NAME;
		}
		if ($this->can_edit_page($auth)) {
			$ret[] = MC_ADMIN_AUTH_PAGE_NAME;
		}
		if ($this->have_user_admin($auth)) {
			$ret[] = MC_ADMIN_AUTH_USER_ADMIN_NAME;
		}
		if ($this->have_site_admin($auth)) {
			$ret[] = MC_ADMIN_AUTH_SITE_ADMIN_NAME;
		}
		if ($this->have_tmpl_admin($auth)) {
			$ret[] = MC_ADMIN_AUTH_TMPL_ADMIN_NAME;
		}
		if ($this->have_conf_admin($auth)) {
			$ret[] = MC_ADMIN_AUTH_CONF_ADMIN_NAME;
		}

		return $ret;
	}

	function authentication($u, $p) {
		$query = 'SELECT * FROM ' . MC_DB_TABLE_USER .
			' WHERE id = "' . $u .
			'" AND password = SHA1("' . $p .'")';
		$rows = $this->db->query($query);
		if (count($rows) == 1) {
			$this->s->register('id', $rows[0]['id']);
			$this->s->register('name', $rows[0]['name']);
			$this->s->register('email', $rows[0]['email']);
			$this->s->register('authority', $rows[0]['authority']);
			$this->s->register('ip', $_SERVER['REMOTE_ADDR']);
			$this->s->register('login', True);

			$this->set();

			return True;
		}

		return False;
	}

	function logout() {
		$this->s->destroy();

		$this->set();
	}
}
?>
