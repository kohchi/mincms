<?php
/**
 * Class of MinTemplate.
 *
 * This class is for the temaplete files of MinCMS.
 *
 * @version $Id:$
 * @access public
 * @package MinCMS
 * @author $Author:$
 */
class MinTemplate
{
	/** instance of MinDB. @var MinDB */			var $db;
	/** the template top directory name  @var string */	var $dir;
	/** the current template directory name @var string */	var $current;
	/** the current URI @var string */		var $current_uri;
	/** a message that you want to display @var string */	var $message;
	/** a redirect URL that you want to set @var string */	var $redirect;
	/** an array what is result by query @var array */	var $result;
	/** instance of MinNode @var MinNode */			var $node;
	/** the current site ID or page ID @var int */		var $sid;
	/** the string to output on template @var string */	var $string;

	/**
	 * The constructor
	 *
	 * invoke when an instance of this class is generated.
	 *
	 * @param MinDB $db a MinDB instance
	 */
	function __construct($db) {
		$this->db = $db;
		$this->dir = MC_TMPL_TOP;
		$rows = $this->db->query('SELECT value FROM ' .
			MC_DB_TABLE_CONFIG. ' WHERE id = "template"');
		$this->current = $this->dir . $rows[0]['value'] . '/';
		$this->current_uri =
			str_replace(MC_TOP_PATH . '/', MC_URI, $this->current);
		$this->node = NULL;
	}

	function set_node($n) {
		$this->node = $n;
	}

	function get_all_info() {
		$ret = '';

		if (is_dir($this->dir)) {
			if ($dh = opendir($this->dir)) {
				while (($f = readdir($dh)) !== False) {
					if ($f == '.' || $f == '..') {
						continue;
					}
					if (is_dir($this->dir . $f)) {
						$ret .= $this->get_info($f);
					}
				}
				closedir($dh);
			}
		}

		return $ret;
	}

	function get_info($f) {
		$screenshot = $this->screenshot($this->dir . $f . '/');
		list($title, $description, $author, $version) = array_values(
			$this->find_comment($this->dir . $f . '/'));
		return <<<_EOF
<div class="mytemplates">
</div>
  <h3>$title</h3>
  <img src="$screenshot" alt="screenshot" title="screenshot" />
  <p>$description</p>
  <p class="authorversion">$author
    &nbsp;Version: <span>$version</span></p>
_EOF;
	}

	function screenshot($tdir) {
		$f = $tdir . MC_TMPL_SCREENSHOT_FILENAME;
		return file_exists($f) ? $f : MC_TMPL_NO_SCREENSHOT;
	}

	function find_comment($tdir) {
		$comment = array(
				'title' => 'No title',
				'description' => '',
				'author' => '',
				'version' => 'No number',
			);

		$f = $tdir . 'index.php';
		if (!file_exists($f)) {
			return $comment;
		}

		$stat = 0;
		foreach (file($f) as $l) {
			if (preg_match('/^ \\*\s*$/', $l)) {
				$stat++;
				continue;
			}
			if (preg_match('/^ \*\/\s*$/', $l)) {
				break;
			}
			if ($stat == 0 && preg_match('/^\/\*\*$/', $l)) {
				$stat++;
				continue;
			}
			if ($stat == 1 && preg_match('/^ \*\s*(.+)$/', $l, $m)) {
				$comment['title'] = $m[1];
				continue;
			}
			if ($stat == 2 && preg_match('/^ \*\s*(.+)$/', $l, $m)) {
				$comment['description'] .= nl2br($m[1]);
				continue;
			}
			if ($stat == 3 && preg_match('/@(author|version)\s+(.+)$/', $l, $m)) {
				$comment[$m[1]] = $m[2];
			}
		}

		return $comment;
	}

	function view($prefix, $cname = '', $ccname = '') {
		$tmpl = $this->current . 'index.php';
		$fa = array();
		if ($cname != '') {
			if ($ccname != '') {
				$fa[] = $this->current . $prefix .
					'-' . $cname . '-' . $ccname . '.php';
			}
			$fa[] = $this->current . $prefix .
				'-' . $cname . '.php';
		}
		$fa[] = $this->current . $prefix . '.php';

		foreach ($fa as $f) {
			if (file_exists($f)) {
				$tmpl = $f;
				break;
			}
		}

		include($tmpl);
	}

	function view_home() {
		$this->view('home');
	}

	function view_search($q) {
		$this->string = htmlspecialchars($q);
		$this->view('search');
	}

	function view_rss($v, $data) {
		$tmpl = implode('', file($this->current .
			'rss' . ($v == 2 ? '20' : '10') . '.xml'));

		$tmpl = preg_replace('/__##RSS_URL##__/',
					$data['RSS_URL'], $tmpl);
		$tmpl = preg_replace('/__##RSS_TITLE##__/',
					$data['RSS_TITLE'], $tmpl);
		$tmpl = preg_replace('/__##RSS_DESCRIPTION##__/',
					$data['RSS_DESCRIPTION'], $tmpl);
		$tmpl = preg_replace('/__##RSS_UPDATE##__/',
					$data['RSS_UPDATE'], $tmpl);
		$tmpl = preg_replace('/__##RDF_LI##__/',
					$data['RDF_LI'], $tmpl);
		$tmpl = preg_replace('/__##ITEMS##__/',
					$data['ITEMS'], $tmpl);

		header("Content-Type: application/xml");
		print $tmpl;
	}

	function view_site($cname) {
		$this->view('site', $cname);
	}

	function view_article($sid, $cname) {
		$this->view('article', $sid, $cname);
	}

	function view_article_redirect() {
		if ($this->node->record['kind'] == 'U') {
			header('Location: ' .
				$this->node->record['description']);
		} else {
			$this->view_article('');
		}
	}

	function view_page($cname) {
		$this->view('page', $cname);
	}

	function view_login($msg = '', $r = '') {
		$this->message = $msg;
		$this->redirect = $r;
		include(MC_TMPL_ADMIN . 'login.php');
	}

	function view_error($cname = '', $msg = '') {
		if ($msg) {
			$this->message = $msg;
		}
		$this->view('error', $cname);
		exit(1);
	}

	function get_header($pre = '') {
		return $this->current . $pre . 'header.php';
	}

	function get_footer($pre = '') {
		return $this->current . $pre . 'footer.php';
	}

	function get_side_left($pre = '') {
		return $this->current . $pre . 'side-left.php';
	}

	function get_side_right($pre = '') {
		return $this->current . $pre . 'side-right.php';
	}

	function get_current_uri() {
		return $this->current_uri;
	}

	function get_message() {
		return $this->message;
	}
}

/**
 * Class of MinTemplateAdmin.
 *
 * This class is for administration template.
 *
 * @version $Id:$
 * @access public
 * @package MinCMS
 * @author $Author:$
 */
class MinTemplateAdmin extends MinTemplate
{
	/** instance of MinLogin. @var MinLogin */		var $login;
	/** users what is registed in MinCMS @var array */	var $users;
	/** the admin template current directory name @var string */
							var $current_admin;
	/** the current URI for admin @var string */	var $current_admin_uri;

	/**
	 * The constructor
	 *
	 * invoke when an instance of this class is generated.
	 *
	 * @param MinDB $db a MinDB instance
	 * @param MinLogin $login a MinLogin instance
	 */
	function __construct($db, $login) {
		parent::__construct($db);

		$this->current_admin = MC_TMPL_ADMIN;
		$this->current_admin_uri = str_replace(MC_TOP_PATH . '/',
						MC_URI, $this->current_admin);
		$this->users = array();
		$this->login = $login;
	}

	function my_authority($auth = 0) {
		return implode(MC_BR, $this->login->get_authname($auth));
	}

	function my_authname($auth = 0) {
		return implode(MC_COMMA, $this->login->get_authname($auth));
	}

	function hidden($name, $value, $disp = '') {
		return <<<_EOF
<input type="hidden" name="$name" value="$value" />$disp
_EOF;
	}

	function checkbox($name, $value, $checked, $disp) {
		return <<<_EOF
<input type="checkbox" name="$name" value="$value" $checked />$disp
_EOF;
	}

	function readonly_checkbox($name, $value, $checked, $disp) {
		if ($checked != '') {
			return <<<_EOF
<input type="hidden" name="$name" value="$value" />
_EOF;
		}
/*
		return <<<_EOF
<input type="checkbox" name="$name" value="$value" $checked onclick="return false;" /><span class="articlereadonly">$disp</span>
_EOF;
*/
	}

	function can_view_checkbox($auth = 0) {
		$c = $this->login->can_view($auth) ? 'checked="checked"' : '';

		return $this->checkbox("authview", "1", $c,
					MC_ADMIN_AUTH_VIEW_NAME);
	}

	function can_create_checkbox($auth = 0) {
		$c = $this->login->can_create($auth) ? 'checked="checked"' : '';

		return $this->checkbox("authcreate", "1", $c,
					MC_ADMIN_AUTH_CREATE_NAME);
	}

	function can_inspect_checkbox($auth = 0) {
		$c = $this->login->can_inspect($auth) ?
			'checked="checked"' : '';

		return $this->checkbox("authinspect", "1", $c,
					MC_ADMIN_AUTH_INSPECT_NAME);
	}

	function can_publish_checkbox($auth = 0) {
		$c = $this->login->can_publish($auth) ?
			'checked="checked"' : '';

		return $this->checkbox("authpublish", "1", $c,
					MC_ADMIN_AUTH_PUBLISH_NAME);
	}

	function can_edit_page_checkbox($auth = 0) {
		$c = $this->login->can_edit_page($auth) ?
			'checked="checked"' : '';

		return $this->checkbox("authpage", "1", $c,
					MC_ADMIN_AUTH_PAGE_NAME);
	}

	function have_user_admin_checkbox($auth = 0) {
		$c = $this->login->have_user_admin($auth) ?
			'checked="checked"' : '';

		return $this->checkbox("authuseradmin", "1", $c,
					MC_ADMIN_AUTH_USER_ADMIN_NAME);
	}

	function have_site_admin_checkbox($auth = 0) {
		$c = $this->login->have_site_admin($auth) ?
			'checked="checked"' : '';

		return $this->checkbox("authsiteadmin", "1", $c,
					MC_ADMIN_AUTH_SITE_ADMIN_NAME);
	}

	function have_tmpl_admin_checkbox($auth = 0) {
		$c = $this->login->have_tmpl_admin($auth) ?
			'checked="checked"' : '';

		return $this->checkbox("authtmpladmin", "1", $c,
					MC_ADMIN_AUTH_TMPL_ADMIN_NAME);
	}

	function have_conf_admin_checkbox($auth = 0) {
		$c = $this->login->have_conf_admin($auth) ?
			'checked="checked"' : '';

		return $this->checkbox("authconfadmin", "1", $c,
					MC_ADMIN_AUTH_CONF_ADMIN_NAME);
	}

	function my_authority_checkbox($auth = 0) {
		if ($this->login->is_admin($auth)) {
			return MC_ADMIN_CAN_NOT_EDIT;
		}

		$ret = array();

		$ret[] = $this->can_view_checkbox($auth);
		$ret[] = $this->can_create_checkbox($auth);
		$ret[] = $this->can_inspect_checkbox($auth);
		$ret[] = $this->can_publish_checkbox($auth);
		$ret[] = $this->can_edit_page_checkbox($auth);
		$ret[] = $this->have_user_admin_checkbox($auth);
		$ret[] = $this->have_site_admin_checkbox($auth);
		$ret[] = $this->have_tmpl_admin_checkbox($auth);
		$ret[] = $this->have_conf_admin_checkbox($auth);

		return implode(MC_BR, $ret);
	}

	function status_hidden($f) {
		switch ($f) {
		case MC_ADMIN_AUTH_CREATE:
			$name = MC_ADMIN_AUTH_CREATE_NAME;
			break;
		case MC_ADMIN_AUTH_INSPECT:
			$name = MC_ADMIN_AUTH_INSPECT_NAME;
			break;
		case MC_ADMIN_AUTH_PUBLISH:
			$name = MC_ADMIN_AUTH_PUBLISH_NAME;
			break;
		default:
			$name = '';
			break;
		}

		return $this->hidden($name, $f);
	}

	function status_checkbox($f, $checked, $checkbox = True) {
		$c = $checked ? 'checked="checked"' : '';
		switch ($f) {
		case MC_ADMIN_AUTH_CREATE:
			$name = MC_ADMIN_AUTH_CREATE_NAME;
			break;
		case MC_ADMIN_AUTH_INSPECT:
			$name = MC_ADMIN_AUTH_INSPECT_NAME;
			break;
		case MC_ADMIN_AUTH_PUBLISH:
			$name = MC_ADMIN_AUTH_PUBLISH_NAME;
			break;
		default:
			$name = '';
			break;
		}

		return $checkbox ?
			$this->checkbox($name, $f, $c, _M($name)) :
			$this->readonly_checkbox($name, $f, $c, _M($name));
	}

	function get_label() {
		return $this->node->record['label'];
	}

	function label_option($n, $v, $lv) {
		$selected = ($lv & $v) == $v ? 'selected="selected"' : '';
		return <<<_EOF
<option value="$v" $selected>$n</option>
_EOF;
	}

	function view_admin($prefix, $cname = '') {
		$tmpl = $this->current_admin . 'index.php';
		$fa = array();
		if ($cname != '') {
			$fa[] = $this->current_admin . $prefix .
				'-' . $cname . '.php';
		}
		$fa[] = $this->current_admin . $prefix . '.php';

		foreach ($fa as $f) {
			if (file_exists($f)) {
				$tmpl = $f;
				break;
			}
		}

		include($tmpl);
	}

	function view_admin_home() {
		$this->view_admin('home');
	}

	function view_admin_error($cname = '', $msg = '') {
		if ($msg) {
			$this->message = $msg;
		}
		$this->view_admin('error', $cname);
		exit(1);
	}

	function view_useradmin() {
		$this->users = $this->db->query(
			'SELECT * FROM ' . MC_DB_TABLE_USER);

		$this->view_admin('useradmin');
	}

	function get_edittype_byID($etype) {
		if ($etype == -1) {
			$add = _M('Add');
			$input = <<<_EOF
<input type="radio" name="edittype" value="insert" checked="checked" />$add
_EOF;
		} else {
			$mod = _M('Modified');
			$del = _M('Deleted');
			$input = <<<_EOF
<input type="radio" name="edittype" value="update" checked="checked" />$mod
<input type="radio" name="edittype" value="delete" />$del
_EOF;
		}

		return $input;
	}

	function view_useradmin_edit($id) {
		$this->result = $id == -1 ? array() : $this->db->query(
			'SELECT * FROM ' . MC_DB_TABLE_USER .
			' WHERE id = "' . $id . '"');

		$this->view_admin('useradmin-edit');
	}

	function view_siteadmin() {
		$sites = $this->db->query(
			'SELECT * FROM ' . MC_DB_TABLE_SITE);

		$smax = count($sites);
		for ($i = 0; $i < $smax; $i++) {
			$sites[$i]['user'] = $this->db->query(
				'SELECT userid,authority FROM ' .
				MC_DB_TABLE_SITEUSER . ' LEFT JOIN ' .
				MC_DB_TABLE_USER .
				' ON ' . MC_DB_TABLE_SITEUSER . '.userid = ' .
				MC_DB_TABLE_USER . '.id ' .
				' WHERE siteid = "' . $sites[$i]['id'] . '"');
		}

		$this->result = $sites;

		$this->view_admin('siteadmin');
	}

	function view_siteadmin_edit($id) {
		$this->result = $id == -1 ? array() : $this->db->query(
			'SELECT * FROM ' . MC_DB_TABLE_SITE .
			' WHERE id = "' . $id . '"');
		$users = $this->db->query(
			'SELECT * FROM ' . MC_DB_TABLE_USER);
		$mysiteusers = $id == -1 ? array() : $this->db->query(
			'SELECT userid FROM ' . MC_DB_TABLE_SITEUSER .
			' WHERE siteid ="' . $id . '"');
		$lambda = create_function('$a', 'return $a["userid"];');
		$mysiteusers = array_map($lambda, $mysiteusers);

		$siteusers = array();
		foreach ($users as $u) {
			if (!$this->login->is_admin($u['authority']) &&
				($this->login->can_view($u['authority']) ||
				$this->login->can_create($u['authority']) ||
				$this->login->can_inspect($u['authority']) ||
				$this->login->can_publish($u['authority']))) {
				$selected = in_array($u['id'], $mysiteusers) ?
					1 : 0;
				$siteusers[] = array(
					'userid' => $u['id'],
					'authority' => $u['authority'],
					'selected' => $selected
					);
			}
		}
		$this->result[0]['siteusers'] = $siteusers;

		$this->view_admin('siteadmin-edit');
	}

	function get_tmpl_image($d) {
		$sshot = $d . '/' . MC_TMPL_SCREENSHOT_FILENAME;
		return file_exists(MC_TMPL_TOP . $sshot) ?
			MC_TMPL_TOP_URL . $sshot : MC_TMPL_NO_SCREENSHOT;
	}

	function parse_tmpl_index_php($d) {
		$index_php = MC_TMPL_TOP . $d . '/index.php';
		$ret = array('No title', 'No description');
		if (file_exists($index_php)) {
			$comment_index = 0;
			foreach (file($index_php) as $l) {
				if (preg_match('/^\/\*\*\s*$/', $l)) {
					$comment_index++;
					continue;
				}
				if ($comment_index == 5 &&
					preg_match('/^\s*\*\/\s*$/', $l)) {
					break;
				}
				if (preg_match('/^\s*\*\s*$/', $l)) {
					$comment_index++;
					continue;
				}
				if ($comment_index == 1 &&
				preg_match('/^\s*\*\s*(.+)\s*$/', $l, $m)) {
					$ret[0] = $m[1];
					$comment_index++;
					continue;
				}
				if ($comment_index == 3 &&
				preg_match('/^\s*\*\s*(.+)\s*$/', $l, $m)) {
					$ret[1] = $m[1];
					$comment_index++;
					continue;
				}
				if ($comment_index == 4 &&
				preg_match('/^\s*\*\s*(.+)\s*$/', $l, $m)) {
					$ret[1] .= $m[1];
					continue;
				}
			}
		}

		return $ret;
	}

	function view_tmpladmin() {
		$conf = $this->db->query(
			'SELECT * FROM ' . MC_DB_TABLE_CONFIG .
			' WHERE id = "template"');

		$tmpls = array();
		if (is_dir(MC_TMPL_TOP) && ($dh = opendir(MC_TMPL_TOP))) {
			while (($dir = readdir($dh)) !== False) {
				if ($dir == '.' || $dir == '..' ||
					!is_dir(MC_TMPL_TOP . '/' . $dir)) {
					continue;
				}
				list($tmpl_title, $tmpl_desc) =
					$this->parse_tmpl_index_php($dir);
				$tmpls[] = array(
					'title' => $tmpl_title,
					'desc' => $tmpl_desc,
					'image' => $this->get_tmpl_image($dir),
					'dir' => $dir,
					'current' => ($conf[0]['value'] == $dir)
					);
			}
			closedir($dh);
		}

		$this->result = $tmpls;

		$this->view_admin('tmpladmin');
	}

	function view_confadmin() {
		$conf = $this->db->query(
			'SELECT * FROM ' . MC_DB_TABLE_CONFIG .
			' WHERE id != "template"');

		$confs = array();
		foreach ($conf as $c) {
			$confs[] = array(
				'id' => $c['id'],
				'name' => $c['name'],
				'value' => $c['value']
				);
		}
		$this->result = $confs;

		$this->view_admin('confadmin');
	}

	function view_editarticle($article) {
		$this->view_admin('editarticle');
	}

	function view_editarticle_article($article, $sid, $node) {
		$this->sid = $sid;
		$this->node = $node;
		$this->view_admin('editarticle-article');
	}

	function view_editarticle_category($article, $sid, $node) {
		$this->sid = $sid;
		$this->node = $node;
		$this->view_admin('editarticle-category');
	}

	function view_editarticle_url($article, $sid, $node) {
		$this->sid = $sid;
		$this->node = $node;
		$this->view_admin('editarticle-url');
	}

	function is_status($f) {
		return ($this->node->record['status'] & $f) != 0 ? True: False;
	}

	function is_create_status() {
		return $this->is_status(MC_ADMIN_AUTH_CREATE);
	}

	function is_inspect_status() {
		return $this->is_status(MC_ADMIN_AUTH_INSPECT);
	}

	function is_publish_status() {
		return $this->is_status(MC_ADMIN_AUTH_PUBLISH);
	}

	function view_upload() {
		$this->view_admin('upload');
	}

	function view_editpage() {
		$this->view_admin('editpage');
	}

	function view_editpage_page($page) {
		$this->sid = $page;
		$this->view_admin('editpage-page');
	}

	function get_admin_header($pre = '') {
		return $this->current_admin . $pre . 'header.php';
	}

	function get_admin_footer($pre = '') {
		return $this->current_admin . $pre . 'footer.php';
	}

	function get_admin_side_left($pre = '') {
		return $this->current_admin . $pre . 'side-left.php';
	}

	function get_admin_side_right($pre = '') {
		return $this->current_admin . $pre . 'side-right.php';
	}

	function get_current_admin_uri() {
		return $this->current_admin_uri;
	}

	function generate_static_path($path_array) {
		$ret = '';

		if (MC_STATIC_PATH != '' && MC_STATIC_SUFFIX != '') {
			$ret = MC_STATIC_PATH . '/' .
				implode('/', $path_array) . MC_STATIC_SUFFIX;
		}

		return $ret;
	}

	function set_output() {
		ob_start();
	}

	function output($path) {
		$output = ob_get_contents();
		ob_end_clean();

		if (!is_dir(dirname($path))) {
			mkdir(dirname($path), 0777, True);
		}
		if (($fp = fopen($path, 'w')) !== False) {
			if (flock($fp, LOCK_EX)) {
				fwrite($fp, $output);
				flock($fp, LOCK_UN);
			}
			fclose($fp);
		}
	}

	function unlink_output($path) {
		if (file_exists($path)) {
			return unlink($path);
		}

		return False;
	}
}
?>
