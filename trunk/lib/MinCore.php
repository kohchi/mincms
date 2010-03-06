<?php
/**
 * Class of MinCore.
 *
 * This class is based on the classes of MinCMS.
 *
 * @version $Id:$
 * @access public
 * @package MinCMS
 * @author $Author:$
 */
class MinCore
{
	/**
	 * The constructor
	 *
	 * invoke when an instance of this class is generated.
	 */
	function __construct() {
	}

	function sanitize($s) {
		return get_magic_quotes_gpc() ? $s : addslashes($s);
	}

	function script_url() {
		return $_SERVER['SCRIPT_URL'];
	}

	function script_uri() {
		return $_SERVER['SCRIPT_URI'];
	}

	function request_uri() {
		return $_SERVER['REQUEST_URI'];
	}

	function encode_redirect_uri() {
		return urlencode($_SERVER['SCRIPT_URI'] .
				'?' . $_SERVER['QUERY_STRING']);
	}

	function redirector($r) {
		$d = urldecode($r);

		if (strpos($d, MC_URL_ADMIN) === False) {
			$d = MC_URL_ADMIN . '?mode=error';
		}

		header("Location: $d");
	}

	function generate_rewrite_url($url, $options) {
		$ret = array();

		foreach ($options as $key => $val) {
			if ($key == 'cal') {
				$ret[] = 'cal' . $val;
			} else {
				$ret[] = $val;
			}
		}

		return $url . implode('/', $ret);
	}

	function generate_general_url($url, $options) {
		$ret = array();

		foreach ($options as $key => $val) {
			$ret[] = $key . '=' . $val;
		}

		return $url . '?' . implode(MC_PARAM_SEPARATOR, $ret);
	}

	function generate_url($url, $options = array()) {
		return MC_URL_REWRITE && $url == MC_URL ?
			$this->generate_rewrite_url($url, $options) :
			$this->generate_general_url($url, $options);
	}
}
?>
