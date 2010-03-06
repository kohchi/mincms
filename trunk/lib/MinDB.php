<?php
/**
 * Class of connecting mysql database.
 *
 * This class is used to connect database. Currently, This support only MySQL.
 *
 * @version $Id:$
 * @access public
 * @package MinCMS
 * @author $Author:$
 */
class MinDB
{
	/** Database hostname. @var string */		var $host;
	/** Database name. @var string */		var $name;
	/** Database user @var string */		var $user;
	/** Database password @var string */		var $password;
	/** Database connect resource @var resource */	var $link;

	/**
	 * The constructor
	 *
	 * invoke when an instance of this class is generated.
	 *
	 * @param string $h database hostname
	 * @param string $n database name
	 * @param string $u username
	 * @param string $p password
	 */
	function __construct($h, $n, $u, $p) {
		$this->host = $h;
		$this->name = $n;
		$this->user = $u;
		$this->password = $p;

		$this->link = mysql_connect(
				$this->host, $this->user, $this->password);

		if ($this->link && mysql_select_db($this->name) == False) {
			mysql_close($this->link);
			$this->link = False;
		}
	}

	/**
	 * check connecting to database
	 *
	 * return true if this class is connecting to database,
	 * otherwise false.
	 *
	 * @return boolean return True if connecting to database.
	 */
	function is_link() {
		return $this->link ? True : False;
	}

	/**
	 * close to connect to database
	 *
	 * stopped connecting to database.
	 * It return true if it is successful to close, otherwise false.
	 *
	 * @return boolean return true if it is successful to close.
	 */
	function close() {
		if ($this->is_link()) {
			mysql_close($this->link);
			$this->link = False;
			return True;
		}
		return False;
	}

	/**
	 * get database hostname
	 *
	 * get database hostname to be set when this class was generated.
	 *
	 * @return string database hostname
	 */
	function get_hostname() {
		return $this->host;
	}

	/**
	 * get database name
	 *
	 * get database name to be set when this class was generated.
	 *
	 * @return string database name
	 */
	function get_name() {
		return $this->name;
	}

	/**
	 * get database username
	 *
	 * get database username to be set when this class was generated.
	 *
	 * @return string database username
	 */
	function get_username() {
		return $this->user;
	}

	/**
	 * get database password
	 *
	 * get database password for connecting that is set when this class
	 * was generated.
	 *
	 * @return string database password
	 */
	function get_password() {
		return $this->password;
	}

	/**
	 * invoke a SQL query.
	 *
	 * invoked a SQL query and get a result array when it is "select" query.
	 * If a SQL query is "insert" or "update" or "delete",
	 * get an array of invoked SQL query.
	 *
	 * @param string $sql a SQL query
	 * @return mixed a result array when a SQL query is "select",
	 *              boolean value when a SQL query is "create" or "drop",
	 *		otherwise an array of invoked SQL query.
	 */
	function query($sql) {
		$ret = array();

		$result = mysql_query($sql);
		if (eregi("^[ \t]*select[ \t]", $sql)) {
			while ($r = mysql_fetch_array($result, MYSQL_ASSOC)) {
				$ret[] = $r;
			}
		} else if (eregi("^[ \t]*(create|drop)[ \t]", $sql)) {
			return $result;
		} else {
			$c = mysql_affected_rows();
			for ($i = 0;$i < $c;$i++) {
				$ret[$i] = $sql;
			}
		}

		return $ret;
	}

	/**
	 * get a last insert id
	 *
	 * get a last insert id same thread if you insert a record before.
	 *
	 * @return int the last id if you successfully insert, otherwise 0.
	 */
	function last_id() {
		$rows = $this->query('SELECT LAST_INSERT_ID() AS id');
		if (count($rows) != 1) {
			return 0;
		}

		return $rows[0]['id'];
	}
}
?>
