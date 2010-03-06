<?php
/**
 * Class of MinCMS.
 *
 * This class is main class of MinCMS.
 *
 * @version $Id:$
 * @access public
 * @package MinCMS
 * @author $Author:$
 */
class MinCMS extends MinCore
{
	/** instance of MinArticle. @var MinArticle */		var $article;
	/** instance of MinCalendar. @var MinCalendar */	var $calendar;
	/** instance of MinDB. @var MinDB */			var $db;
	/** instance of MinLogin. @var MinLogin */		var $login;
	/** instance of MinPage. @var MinPage */		var $page;
	/** instance of MinTemplate. @var MinTemplate */	var $tmpl;
	/** the URI string @var string */			var $uri;
	/** the URL string @var string */			var $url;
	/** mode string @var string */				var $mode;
	/** the site ID @var int */				var $site;
	/** the article or page ID @var int */			var $id;
	/** the calendar value (YYYY-mm or YYYY) @var string */	var $cal;
	/** the other arguments @var array */			var $oargs;

	/**
	 * The constructor
	 *
	 * invoke when an instance of this class is generated.
	 */
	function __construct() {
		$this->db = new MinDB(MC_DB_HOST, MC_DB_NAME,
				MC_DB_USER, MC_DB_PASS);
		$this->login = new MinLogin($this->db);
		$this->uri = MC_URI;
		$this->url = MC_URL;
		$this->tmpl = new MinTemplate($this->db);
		$this->article = new MinArticle($this->db);
		$this->page = new MinPage($this->db);
		$this->calendar = new MinCalendar($this->db);
		$this->args = array();
	}

	function do_login() {
		return $this->login->done() ? True : False;
	}

	function parse_arguments() {
		MC_URL_REWRITE && $this->uri == MC_URI ?
			$this->parse_rewrite() : $this->parse_getpost();

		switch ($this->mode) {
		case 'page' :
			$this->page->set_page($this->id);
			if ($this->page->does_exist() == False) {
				$this->tmpl->view_error('nopage',
						'have no page');
			}
			break;
		case 'site' :
			$this->article->set_site($this->site);
			if (count($this->article->node) == 0) {
				$this->tmpl->view_error('nosite',
						'have no site');
			}
			$this->tmpl->set_node(
				$this->article->node[$this->site]);
			break;
		case 'article' :
			$this->article->set_article($this->site, $this->id);
			if (count($this->article->node) == 0) {
				$this->tmpl->view_error('noarticle',
						'have no article');
			}
			$this->tmpl->set_node(
				$this->article->node[$this->site]);
			break;
		case 'search' :
			$this->oargs['q'] = $this->sanitize($_POST['q']);
			if ($this->oargs['q'] == '') {
				$this->tmpl->view_error('nokeyword',
							'have no keyword');
			}
			break;
		case 'rss' :
			if (!$this->oargs['v']) {
				$this->oargs['v'] = 1;
			}
			break;
		case 'login' :
			$this->oargs['username'] =
				$this->sanitize($_POST['username']);
			$this->oargs['password'] =
				$this->sanitize($_POST['password']);
			break;
		case 'logout' :
			$this->login->logout();
			break;
		default :
			break;
		}
	}

	function getpost($name) {
		return $this->sanitize(
			$_GET[$name] ? $_GET[$name] : $_POST[$name]);
	}

	function to_number($v) {
		return preg_match('/^\d+$/', $v) ? $v : 0;
	}

	function getpost_number($name) {
		return $this->to_number($this->getpost($name));
	}

	/**
	 * parse rewrite path
	 *
	 * ex:
	 *  /site/1
	 *  /article/1/2
	 *  /article/1/2/2010-02
	 *  /rss/1/label/1
	 *  /rss/2/site/1
	 *  /page/1
	 *  /page/1/2010
	 */
	function parse_rewrite() {
		$this->mode = $this->getpost('mode');
		if ($this->mode == 'search') {
			return;
		}

		$p = split('/',
			str_replace($this->uri, '', $_SERVER['REQUEST_URI']));
		$this->mode = $this->sanitize($p[0]);
		if ($this->mode == 'site' || $this->mode == 'article') {
			$this->site = $this->sanitize($p[1]);
			$this->id = $this->sanitize($p[2]);
			$this->cal = $this->sanitize($p[3]);
		} else if ($this->mode == 'rss') {
			$this->oargs['v'] =
				$this->to_number($this->sanitize($p[1]));
			$this->oargs['key'] = $this->sanitize($p[2]);
			$this->oargs['val'] = $this->sanitize($p[3]);
		} else { // if ($this->mode == 'page')
			$this->id = $this->sanitize($p[1]);
			$this->cal = $this->sanitize($p[2]);
		}
	}

	function parse_getpost() {
		$this->site = $this->getpost('site');
		$this->cal = $this->getpost('cal');
		if ($this->getpost('article') != '') {
			$this->mode = 'article';
			$this->id = $this->getpost('article');
		} else if ($this->getpost('page') != '') {
			$this->mode = 'page';
			$this->id = $this->getpost('page');
		} else if ($this->getpost('site') != '') {
			$this->mode = 'site';
		} else {
			$this->mode = $this->getpost('mode');
			if ($this->mode == 'rss') {
				$this->oargs['v'] = $this->getpost_number('v');
				$this->oargs['key'] = $this->getpost('key');
				$this->oargs['val'] = $this->getpost('val');
			}
		}
	}

	function generate_article_path($sid, $id) {
		return $this->generate_url(MC_URL, array(
				'mode' => 'article',
				'site' => $sid,
				'article' => $id
				));
	}

	function get_version() {
		return htmlspecialchars(MC_VERSION);
	}

	function get_title() {
		$rows = $this->db->query('SELECT value FROM ' .
			MC_DB_TABLE_CONFIG. ' WHERE id = "title"');
		return htmlspecialchars($rows[0]['value']);
	}

	function get_subtitle() {
		$rows = $this->db->query('SELECT value FROM ' .
			MC_DB_TABLE_CONFIG. ' WHERE id = "subtitle"');
		return htmlspecialchars($rows[0]['value']);
	}

	function get_name() {
		return $this->do_login() ?
			$this->login->name : MC_NO_LOGIN_USERNAME;
	}

	function view() {
		switch ($this->mode) {
		case 'page' :
			$this->tmpl->view_page($this->id);
			break;
		case 'site' :
			$this->tmpl->view_site($this->site);
			break;
		case 'article' :
			if ($this->article->is_kind_url($this->site)) {
				$this->tmpl->view_article_redirect();
			} else {
				$this->tmpl->view_article(
					$this->site, $this->id);
			}
			break;
		case 'search' :
			$this->tmpl->view_search($this->oargs['q']);
			break;
		case 'rss' :
			$data = $this->article->generate_rss($this->oargs['v'],
						$this->oargs['key'],
						$this->oargs['val']);
			$this->tmpl->view_rss($this->oargs['v'], $data);
			break;
		case 'login' :
			if ($this->login->authentication(
					$this->oargs['username'],
					$this->oargs['password'])) {
				$this->tmpl->view_home();
			} else {
				$this->tmpl->view_login(MC_MSG_LOGIN_FAILURE);
			}
			break;
		default :
			$this->tmpl->view_home();
			break;
		}

		exit(0);
	}

	function need_login($r = '') {
		$this->tmpl->view_login(_M(MC_MSG_LOGIN_NEED), $r);
	}
}

/**
 * Class of MinCMSAdmin.
 *
 * This class is for administration tool.
 *
 * @version $Id:$
 * @access public
 * @package MinCMS
 * @author $Author:$
 */
class MinCMSAdmin extends MinCMS
{
	/** the execute status @var boolean */	var $is_success;
	/** the get or post data array @var array */	var $gp;
	/** the MinUpload instance @var MinUpload */	var $up;

	/**
	 * The constructor
	 *
	 * invoke when an instance of this class is generated.
	 */
	function __construct() {
		$this->db = new MinDB(MC_DB_HOST, MC_DB_NAME,
				MC_DB_USER, MC_DB_PASS);
		$this->login = new MinLogin($this->db);
		$this->uri = MC_URI_ADMIN;
		$this->url = MC_URL_ADMIN;
		$this->tmpl = new MinTemplateAdmin($this->db, $this->login);
		$this->article = new MinArticleAdmin($this->db, $this->login);
		$this->page = new MinPage($this->db);
		$this->calendar = new MinCalendar($this->db);
	}

	function parse_getpost() {
		$this->mode = $this->getpost('mode');
		$this->site = $this->getpost('site');
		$this->id = $this->getpost('article');
	}

	function parse_arguments() {
		parent::parse_arguments();

		switch ($this->mode) {
		case 'execute' :
			$type = $this->getpost('type');
			switch ($type) {
			case 'user' :
				if ($this->login->have_user_admin() === False) {
					$this->tmpl->view_admin_error('noauth',
						'not have user admin');
				}
				$this->is_success = $this->edit_user();
				break;
			case 'site' :
				if ($this->login->have_site_admin() === False) {
					$this->tmpl->view_admin_error('noauth',
						'not have site admin');
				}
				$this->is_success = $this->edit_site();
				break;
			case 'tmpl' :
				if ($this->login->have_tmpl_admin() === False) {
					$this->tmpl->view_admin_error('noauth',
						'not have tmpl admin');
				}
				$this->is_success = $this->edit_tmpl();
				break;
			case 'conf' :
				if ($this->login->have_conf_admin() === False) {
					$this->tmpl->view_admin_error('noauth',
						'not have conf admin');
				}
				$this->is_success = $this->edit_conf();
				break;
			case 'article' :
				if ($this->login->can_edit_article() === False) {
					$this->tmpl->view_admin_error('noauth',
						'can not edit articles');
				}

				$kind = $this->getpost('kind');
				switch ($kind) {
				case 'category' :
					$this->is_success =
						$this->edit_article_category();
					break;
				case 'article' :
					$this->is_success =
						$this->edit_article_article();
					break;
				case 'url' :
					$this->is_success =
						$this->edit_article_url();
					break;
				default :
					$this->tmpl->view_admin_error('',
						'unknown create type');
					break;
				}

				$this->article->read_node();
				break;
			case 'changecategory' :
				if ($this->login->can_edit_article() === False) {
					$this->tmpl->view_admin_error('noauth',
						'can not edit articles');
				}
				$this->is_success = $this->change_category();
				if ($this->is_success) {
					$this->article->read_node();
				}
				break;
			case 'page' :
				if ($this->login->can_edit_page() === False) {
					$this->tmpl->view_admin_error('noauth',
						'can not edit pages');
				}
				$this->is_success = $this->edit_page();
				break;
			default :
				break;
			}
			break;
		case 'upload' :
			if (!($this->login->can_edit_article() ||
					$this->login->can_edit_page())) {
				$this->tmpl->view_admin_error('noauth',
					'can not upload files');
			}
			$type = $this->getpost('type');
			$id = $this->getpost('id');
			$action = $this->getpost('action');
			$offset = $this->getpost_number('offset');
			$this->up = new MinUpload(
				($type == 'site' ?
					MC_UPLOAD_SITE_TOP_DIR . $id . '/' :
					MC_UPLOAD_PAGE_TOP_DIR),
				($type == 'site' ?
					MC_UPLOAD_SITE_TOP_URL . $id . '/' :
					MC_UPLOAD_PAGE_TOP_URL),
				$type, $id, $offset
				);

			switch($action) {
			case 'upload' :
				$this->up->upload();
				break;
			case 'delete' :
				$deletefile = $this->getpost('deletefile');
				$this->up->delete_file($deletefile);
				break;
			default :
				break;
			}
			break;
		default :
			break;
		}
	}

	function view() {
		switch ($this->mode) {
		case 'error' :
			$this->tmpl->view_admin_error('other',
					'cause the other problem');
			break;
		case 'login' :
			if ($this->login->authentication(
					$this->oargs['username'],
					$this->oargs['password'])) {
				$r = $this->getpost('redirect');
				if (empty($r)) {
					$this->tmpl->view_admin_home();
				} else {
					$this->redirector($r);
				}
			} else {
				$this->login->logout();
				$this->tmpl->view_login(MC_MSG_LOGIN_FAILURE);
			}
			exit(0);
		case 'logout' :
			$this->tmpl->view_login(_M(MC_MSG_LOGIN_NEED));
			exit(0);
		}

		if (!$this->do_login()) {
			$this->need_login($this->encode_redirect_uri());
			exit(0);
		}

		switch ($this->mode) {
		case 'editarticle' :
			if ($this->login->can_edit_article() === False) {
				$this->tmpl->view_admin_error('noauth',
						'can not edit articles');
			}

			$site = $this->getpost('site');
			$article = $this->getpost('article');
			if ($site && $article) {
				$n = $this->article->search_node(
							$site, $article);
				$kind = $n == NULL ? $this->getpost('kind') :
					$n->record['kind'];
				switch ($kind) {
				case 'A' :
					$this->tmpl->view_editarticle_article($this->article, $site, $n);
					break;
				case 'C' :
					$this->tmpl->view_editarticle_category($this->article, $site, $n);
					break;
				case 'U' :
					$this->tmpl->view_editarticle_url($this->article, $site, $n);
					break;
				}
			} else {
				$this->oargs['offset'] =
					$this->getpost_number('offset');
				$this->tmpl->view_editarticle($this->article);
			}
			break;
		case 'editpage' :
			if ($this->login->can_edit_page() === False) {
				$this->tmpl->view_admin_error('noauth',
						'can not edit pages');
			}

			$page = $this->getpost('page');
			if ($page) {
				$this->page->set_page($page);
				$this->tmpl->view_editpage_page($page);
			} else {
				$this->oargs['offset'] =
					$this->getpost_number('offset');
				$this->tmpl->view_editpage();
			}
			break;
		case 'upload' :
			if ($this->login->can_edit_article() === False ||
				$this->login->can_edit_page() === False) {
				$this->tmpl->view_admin_error('noauth',
						'can not upload files');
			}

			$this->tmpl->view_upload();
			break;
		case 'uploadlist' :
			if ($this->login->can_edit_article() === False ||
				$this->login->can_edit_page() === False) {
				$this->tmpl->view_admin_error('noauth',
						'can not upload files');
			}

			$type = $this->getpost('type');
			$kind = $this->getpost('kind');

			if ($type == 'site') {
				$id = $this->getpost('id');
				$ap =  MC_UPLOAD_TOP_URL . 'sites/' . $id;
				$dir =  MC_UPLOAD_TOP_DIR . 'sites/' . $id;
			} else {
				$ap =  MC_UPLOAD_TOP_URL . 'pages';
				$dir =  MC_UPLOAD_TOP_DIR . 'pages';
			}

			switch ($kind) {
			case 'link' :
				$l = new MinUploadedLinkList($ap, $dir);
				break;
			case 'image' :
				$l = new MinUploadedImageList($ap, $dir);
				break;
			}

			header('Content-type: text/javascript');
			print $l->javascript_list();
			break;
		case 'useradmin' :
			if ($this->login->have_user_admin() === False) {
				$this->tmpl->view_admin_error('noauth',
						'not have user admin');
			}

			$id = $this->getpost('id');
			$this->oargs['offset'] =
				$this->getpost_number('offset');
			isset($id) ? $this->tmpl->view_useradmin_edit($id) :
					$this->tmpl->view_useradmin();
			break;
		case 'siteadmin' :
			if ($this->login->have_site_admin() === False) {
				$this->tmpl->view_admin_error('noauth',
						'not have site admin');
			}

			$id = $this->getpost('id');
			$this->oargs['offset'] =
				$this->getpost_number('offset');
			isset($id) ? $this->tmpl->view_siteadmin_edit($id) :
					$this->tmpl->view_siteadmin();
			break;
		case 'tmpladmin' :
			if ($this->login->have_tmpl_admin() === False) {
				$this->tmpl->view_admin_error('noauth',
						'not have tmpl admin');
			}

			$this->tmpl->view_tmpladmin();
			break;
		case 'confadmin' :
			if ($this->login->have_conf_admin() === False) {
				$this->tmpl->view_admin_error('noauth',
						'not have conf admin');
			}

			$this->tmpl->view_confadmin();
			break;
		case 'execute' :
			$type = $this->getpost('type');
			switch ($type) {
			case 'user' :
				$this->is_success ?
					$this->tmpl->view_useradmin() :
					$this->tmpl->view_admin_error($type);
				break;
			case 'site' :
				$this->is_success ?
					$this->tmpl->view_siteadmin() :
					$this->tmpl->view_admin_error($type);
				break;
			case 'tmpl' :
				$this->is_success ?
					$this->tmpl->view_tmpladmin() :
					$this->tmpl->view_admin_error($type);
				break;
			case 'conf' :
				$this->is_success ?
					$this->tmpl->view_confadmin() :
					$this->tmpl->view_admin_error($type);
				break;
			case 'article' :
			case 'changecategory' :
				$this->is_success ?
					$this->tmpl->view_editarticle($this->article) :
					$this->tmpl->view_admin_error($type);
				break;
			case 'page' :
				$this->page->read_pages();
				$this->tmpl->view_editpage();
				break;
			default :
				break;
			}
			break;
		case 'home' :
		default :
			$this->do_login() ? $this->tmpl->view_admin_home() :
						$this->need_login();
			break;
		}

		exit(0);
	}

	function get_menu_list() {
		$ret = array('home' => MC_ADMIN_MENU_HOME);
		if ($this->login->can_edit_article()) {
			$ret['editarticle'] = MC_ADMIN_MENU_ARTICLE;
		}
		if ($this->login->can_edit_page()) {
			$ret['editpage'] = MC_ADMIN_MENU_PAGE;
		}
		if ($this->login->have_user_admin()) {
			$ret['useradmin'] = MC_ADMIN_MENU_USER_ADMIN;
		}
		if ($this->login->have_site_admin()) {
			$ret['siteadmin'] = MC_ADMIN_MENU_SITE_ADMIN;
		}
		if ($this->login->have_tmpl_admin()) {
			$ret['tmpladmin'] = MC_ADMIN_MENU_TMPL_ADMIN;
		}
		if ($this->login->have_conf_admin()) {
			$ret['confadmin'] = MC_ADMIN_MENU_CONF_ADMIN;
		}

		return $ret;
	}

	function get_authority($id) {
		if ($id == '') {
			return 0;
		}

		$query = 'SELECT authority FROM ' . MC_DB_TABLE_USER .
				' WHERE id = "' . $id . '"';
		$rows = $this->db->query($query);

		return (count($rows) == 1) ? $rows[0]['authority'] : 0;
	}

	function get_auth_value($n) {
		$v = array(
			'authview' => MC_ADMIN_AUTH_VIEW,
			'authcreate' => MC_ADMIN_AUTH_CREATE,
			'authinspect' => MC_ADMIN_AUTH_INSPECT,
			'authpublish' => MC_ADMIN_AUTH_PUBLISH,
			'authpage' => MC_ADMIN_AUTH_PAGE,
			'authuseradmin' => MC_ADMIN_AUTH_USER_ADMIN,
			'authsiteadmin' => MC_ADMIN_AUTH_SITE_ADMIN,
			'authtmpladmin' => MC_ADMIN_AUTH_TMPL_ADMIN,
			'authconfadmin' => MC_ADMIN_AUTH_CONF_ADMIN
		);
		return $this->getpost($n) ? $v[$n] : 0;
	}

	function get_edit_user() {
		$this->gp = array();
		$this->gp['id'] = $this->getpost('id');
		$this->gp['pw0'] = $this->getpost('password0');
		$this->gp['pw1'] = $this->getpost('password1');
		$this->gp['name'] = $this->getpost('name');
		$this->gp['email'] = $this->getpost('email');
		$this->gp['auth'] = $this->get_auth_value('authview');
		$this->gp['auth'] |= $this->get_auth_value('authcreate');
		$this->gp['auth'] |= $this->get_auth_value('authinspect');
		$this->gp['auth'] |= $this->get_auth_value('authpublish');
		$this->gp['auth'] |= $this->get_auth_value('authpage');
		$this->gp['auth'] |= $this->get_auth_value('authuseradmin');
		$this->gp['auth'] |= $this->get_auth_value('authsiteadmin');
		$this->gp['auth'] |= $this->get_auth_value('authtmpladmin');
		$this->gp['auth'] |= $this->get_auth_value('authconfadmin');
		$this->gp['edittype'] = $this->getpost('edittype');
	}

	function required_edit_user() {
		if ($this->gp['pw0'] != $this->gp['pw1']) {
			$this->tmpl->message = 'not match password';
			return False;
		}

		if ($this->gp['name'] == '') {
			$this->tmpl->message = 'require name';
			return False;
		}

		return True;
	}

	function check_id() {
		$query = 'SELECT COUNT(id) AS cnt FROM ' . MC_DB_TABLE_USER .
			' WHERE id = "' . $this->gp['id'] . '"';
		$rows = $this->db->query($query);

		return $rows[0]['cnt'] == 1 ? True : False;
	}

	function edit_user() {
		$this->get_edit_user();

		if ($this->required_edit_user() === False) {
			return False;
		}

		$setquery = array();

		if ($this->gp['edittype'] == 'insert') {
			if ($this->gp['id'] == '') {
				$this->tmpl->message = 'require ID';
				return False;
			}
			if ($this->gp['pw0'] == '') {
				$this->tmpl->message = 'require password';
				return False;
			}
			if ($this->check_id()) {
				$this->tmpl->message = 'already exist ID';
				return False;
			}
			$setquery[] =  'id="' . $this->gp['id'] . '"';
			$setquery[] =  'rtime="' . date('Y-m-d H:i:s') . '"';
		}

		if ($this->gp['pw0'] != '') {
			$setquery[] = "password=SHA1('" .
				$this->gp['pw0'] . "')";
		}
		$setquery[] =  'name="' . $this->gp['name'] . '"';
		$setquery[] =  'email="' . $this->gp['email'] . '"';
		$setquery[] =  'authority=' . $this->gp['auth'];
		$setquery[] =  'utime="' . date('Y-m-d H:i:s') . '"';

		switch ($this->gp['edittype']) {
		case 'update' :
			$query = 'UPDATE ' . MC_DB_TABLE_USER .
				' SET ' . implode(', ', $setquery) .
				' WHERE id = "' . $this->gp['id'] . '"';
			break;
		case 'insert' :
			$query = 'INSERT INTO ' . MC_DB_TABLE_USER .
				' SET ' . implode(', ', $setquery);
			break;
		case 'delete' :
			$query = 'DELETE FROM ' . MC_DB_TABLE_USER .
				' WHERE id = "' . $this->gp['id'] . '"';
			break;
		default :
			$this->tmpl->message = 'unknown edit type';
			return False;
		}
		$rows = $this->db->query($query);

		if (count($rows) == 1) {
			return True;
		}

		$this->tmpl->message = 'failure the query';

		return False;
	}

	function get_edit_site() {
		$this->gp = array();
		$this->gp['id'] = $this->getpost('id');
		$this->gp['path'] = $this->getpost('path');
		$this->gp['name'] = $this->getpost('name');
		$this->gp['contact'] = $this->getpost('contact');
		$this->gp['siteuser'] = $this->getpost('siteuser');
		$this->gp['edittype'] = $this->getpost('edittype');
	}

	function need_auth($siteusers) {
		$query = 'SELECT id,authority FROM ' . MC_DB_TABLE_USER .
			' WHERE id != "admin"';
		$rows = $this->db->query($query);
		$need = 0;
		foreach ($siteusers as $u) {
			$auth = 0;
			foreach ($rows as $row) {
				if ($row['id'] == $u) {
					$auth = $row['authority'];
					break;
				}
			}
			if ($this->login->can_create($auth)) {
				$need |= MC_ADMIN_AUTH_CREATE;
			}
			if ($this->login->can_inspect($auth)) {
				$need |= MC_ADMIN_AUTH_INSPECT;
			}
			if ($this->login->can_publish($auth)) {
				$need |= MC_ADMIN_AUTH_PUBLISH;
			}
		}

		if ($need == (MC_ADMIN_AUTH_CREATE |
			MC_ADMIN_AUTH_INSPECT | MC_ADMIN_AUTH_PUBLISH)) {
			return True;
		}

		return False;
	}

	function required_edit_site() {
		if (!eregi("^[0-9a-z_-]+$", $this->gp['path'])) {
			$this->tmpl->message = _M('path name is invalid');
			return False;
		}

		if ($this->gp['name'] == '') {
			$this->tmpl->message = _M('require site name');
			return False;
		}

		if (count($this->gp['siteuser']) == 0 ||
			$this->need_auth($this->gp['siteuser']) === False) {
			$this->tmpl->message = _M('less authorization to edit');
			return False;
		}

		return True;
	}

	function insert_to_siteuser($siteid, $siteusers) {
		$siteid = 'siteid="' . $siteid . '"';
		foreach ($siteusers as $u) {
			$userid = 'userid="' . $u . '"';
			$query = 'INSERT INTO ' . MC_DB_TABLE_SITEUSER .
					' SET ' . $siteid . ',' . $userid;
			$rows = $this->db->query($query);
			if (count($rows) != 1) {
				return False;
			}
		}

		return True;
	}

	function delete_from_siteuser($siteid) {
		$query = 'DELETE FROM ' . MC_DB_TABLE_SITEUSER .
				' WHERE siteid = "' . $siteid . '"';
		$rows = $this->db->query($query);

		if (count($rows) == 0) {
			return False;
		}

		return True;
	}

	function create_article_table($id) {
		$table_name = MC_DB_TABLE_ARTICLE_PREFIX . $id;
		$query = <<<_EOF
CREATE TABLE IF NOT EXISTS $table_name (
	id int NOT NULL auto_increment,
	kind varchar(8) NOT NULL,
	parent int NOT NULL default 0,
	status int NOT NULL default 0,
	title varchar(255) NOT NULL,
	description text NOT NULL,
	label int,
	btime datetime,
	etime datetime,
	rtime datetime NOT NULL,
	utime datetime NOT NULL,
	PRIMARY KEY (id),
	KEY parent (parent),
	KEY status (status),
	FULLTEXT (title,description),
	KEY label (label),
	KEY btime (btime),
	KEY etime (etime)
)
_EOF;

		return $this->db->query($query);
	}

	function delete_article_table($id) {
		$query = 'DROP TABLE ' . MC_DB_TABLE_ARTICLE_PREFIX . $id;

		return $this->db->query($query);
	}

	function create_upload_dir($id) {
		$d = MC_UPLOAD_SITE_TOP_DIR . $id;
		if (file_exists($d)) {
			return False;
		}

		return mkdir($d);
	}

	function rm_r($dir) {
		if (!is_dir($dir)) {
			return False;
		}

		$h = opendir($dir);
		while ($f = readdir($h)) {
			if ($f == "." || $f == "..") {
				continue;
			}
			$fpath = $dir . "/" . $f;
			if (is_dir($fpath)) {
				$this->rm_r($fpath);
			} else {
				unlink($fpath);
			}
		}
		closedir($h);

		return rmdir($dir);
	}

	function delete_upload_dir($id) {
		$d = MC_UPLOAD_SITE_TOP_DIR . $id;
		if (file_exists($d)) {
			return $this->rm_r($d);
		}

		return False;
	}

	function edit_site() {
		$this->get_edit_site();

		if ($this->required_edit_site() === False) {
			return False;
		}

		$setquery = array();

		if ($this->gp['edittype'] == 'insert') {
			$setquery[] =  'rtime="' . date('Y-m-d H:i:s') . '"';
		} else {
			$setquery[] =  'id="' . $this->gp['id'] . '"';
		}

		$setquery[] =  'path="' . $this->gp['path'] . '"';
		$setquery[] =  'name="' . $this->gp['name'] . '"';
		$setquery[] =  'contact="' . $this->gp['contact'] . '"';
		$setquery[] =  'utime="' . date('Y-m-d H:i:s') . '"';

		switch ($this->gp['edittype']) {
		case 'update' :
			$query = 'UPDATE ' . MC_DB_TABLE_SITE .
				' SET ' . implode(', ', $setquery) .
				' WHERE id = "' . $this->gp['id'] . '"';
			$rows = $this->db->query($query);
			if (count($rows) != 1) {
				$this->tmpl->message = 'failure update';
				return False;
			}
			if (!$this->delete_from_siteuser($this->gp['id'])) {
				$this->tmpl->message = 'failure delete from ' .
						MC_DB_TABLE_SITEUSER;
				return False;
			}
			if (!$this->insert_to_siteuser($this->gp['id'],
					$this->gp['siteuser'])) {
				$this->tmpl->message = 'failure insert to ' .
						MC_DB_TABLE_SITEUSER;
				return False;
			}
			break;
		case 'insert' :
			$query = 'INSERT INTO ' . MC_DB_TABLE_SITE .
				' SET ' . implode(', ', $setquery);
			$rows = $this->db->query($query);
			if (count($rows) != 1) {
				$this->tmpl->message = 'failure insert';
				return False;
			}
			$lastid = $this->db->last_id();
			if ($lastid == 0) {
				$this->tmpl->message = 'unknown site ID';
				return False;
			}
			if ($this->insert_to_siteuser($lastid,
					$this->gp['siteuser']) == False) {
				$this->tmpl->message = 'failure insert to ' .
						MC_DB_TABLE_SITEUSER;
				return False;
			}
			if ($this->create_article_table($lastid) == False) {
				$this->tmpl->message =
					'failure create an article table';
				return False;
			}
			if ($this->create_upload_dir($lastid) == False) {
				$this->tmpl->message =
					'failure create a directory for upload';
				return False;
			}
			break;
		case 'delete' :
			$query = 'DELETE FROM ' . MC_DB_TABLE_SITE .
				' WHERE id = "' . $this->gp['id'] . '"';
			$rows = $this->db->query($query);
			if (count($rows) != 1) {
				$this->tmpl->message = 'failure delete';
				return False;
			}
			if (!$this->delete_from_siteuser($this->gp['id'])) {
				$this->tmpl->message = 'failure delete from ' .
						MC_DB_TABLE_SITEUSER;
				return False;
			}
			if (!$this->delete_article_table($this->gp['id'])) {
				$this->tmpl->message =
					'failure drop an article table';
				return False;
			}
			if (!$this->delete_upload_dir($this->gp['id'])) {
				$this->tmpl->message =
					'failure unlink a directory for upload';
				return False;
			}
			break;
		default :
			$this->tmpl->message = 'unknown edit type';
			return False;
		}

		$staticid = $this->gp['edittype'] == 'insert' ?
					$lastid : $this->gp['id'];
		$static_path = $this->tmpl->generate_static_path(
				array('site', $staticid));
		if ($static_path != '') {
			switch ($this->gp['edittype']) {
			case 'update' :
			case 'insert' :
				$this->tmpl->set_node($this->article->get_site(
						$staticid));

				$this->tmpl->set_output();
				$this->tmpl->view_site($staticid);
				$this->tmpl->output($static_path);
				break;
			case 'delete' :
				$this->tmpl->unlink_output($static_path);
				break;
			}
		}

		return True;
	}

	function edit_tmpl() {
		$this->gp = array();
		$this->gp['value'] = $this->getpost('value');

		if ($this->gp['value'] == '') {
			$this->tmpl->message = 'require template value';
			return False;
		}

		$setquery = array();
		$setquery[] =  'value="' . $this->gp['value'] . '"';
		$setquery[] =  'utime="' . date('Y-m-d H:i:s') . '"';

		$query = 'UPDATE ' . MC_DB_TABLE_CONFIG .
				' SET ' . implode(', ', $setquery) .
				' WHERE id = "template"';
		$rows = $this->db->query($query);
		if (count($rows) != 1) {
			$this->tmpl->message = 'failure update';
			return False;
		}

		return True;
	}

	function edit_conf() {
		$conf_value = array('title', 'subtitle');
		$this->gp = array();

		foreach ($conf_value as $cv) {
			$this->gp[$cv] = $this->getpost($cv);
			if ($this->gp[$cv] == '') {
				$this->tmpl->message = "require $cv string";
				return False;
			}

			$setquery = array();
			$setquery[] =  'value="' . $this->gp[$cv] . '"';
			$setquery[] =  'utime="' . date('Y-m-d H:i:s') . '"';

			$query = 'UPDATE ' . MC_DB_TABLE_CONFIG .
					' SET ' . implode(', ', $setquery) .
					' WHERE id = "' . $cv . '"';
			$rows = $this->db->query($query);
			if (count($rows) != 1) {
				$this->tmpl->message = 'failure update';
				return False;
			}
		}

		return True;
	}

	function generate_edit_time($date, $hms, $default_hms = '00:00:00') {
		$ret = 'NULL';
		if (ereg("^[0-9]{4}-[0-9]{2}-[0-9]{2}$", $date)) {
			if (ereg("^[0-9]{2}:[0-9]{2}:[0-9]{2}$", $hms)) {
				$ret = $date . ' ' . $hms;
			} else {
				$ret = $date . ' ' . $default_hms;
			}
		}

		return $ret;
	}

	function get_edit_article() {
		$this->gp = array();

		$this->gp['site'] = $this->getpost('site');
		$this->gp['article'] = $this->getpost('article');
		if ($this->gp['site'] == -1 && $this->gp['article'] == -1) {
			list($site, $article) =
				split(':', $this->getpost('parent'));
			$this->gp['site'] = $site;
			$this->gp['parent'] = $article;
			$this->gp['article'] = $article;
		}
		$this->gp['title'] = $this->getpost('title');
		$this->gp['description'] = $this->getpost('description');
		if ($this->gp['title'] == '' ||
				$this->gp['description'] == '') {
			$this->tmpl->view_admin_error('required',
					'required title and description');

		}
		$this->gp['btime'] = $this->generate_edit_time(
			$this->getpost('bdate'), $this->getpost('bhms'));
		$this->gp['etime'] = $this->generate_edit_time(
			$this->getpost('edate'), $this->getpost('ehms'),
			'23:59:59');
		$this->gp['emode'] = $this->getpost('emode');
		$this->gp[MC_ADMIN_AUTH_CREATE_NAME] =
			$this->getpost(MC_ADMIN_AUTH_CREATE_NAME);
		$this->gp[MC_ADMIN_AUTH_INSPECT_NAME] =
			$this->getpost(MC_ADMIN_AUTH_INSPECT_NAME);
		$this->gp[MC_ADMIN_AUTH_PUBLISH_NAME] =
			$this->getpost(MC_ADMIN_AUTH_PUBLISH_NAME);
		$this->gp['label'] = 0;
		if (is_array($this->getpost('label'))) {
			foreach ($this->getpost('label') as $v) {
				$this->gp['label'] |= $v;
			}
		}
	}

	function set_query($a = array()) {
		$qa = $a;
		$status = 0;

		foreach ($this->gp as $key => $val) {
			switch ($key) {
			case MC_ADMIN_AUTH_CREATE_NAME :
			case MC_ADMIN_AUTH_INSPECT_NAME :
			case MC_ADMIN_AUTH_PUBLISH_NAME :
				$status |= $val;
				break;
			case 'site' :
			case 'article' :
			case 'page' :
			case 'emode' :
				break;
			case 'btime' :
			case 'etime' :
				$qa[] = $val == 'NULL' ?
					"$key=$val" : "$key='$val'";
				break;
			case 'label' :
				if ($val >= 0) {
					$qa[] = "$key='$val'";
				}
				break;
			default :
				if ($val != '') {
					$qa[] = "$key='$val'";
				}
				break;
			}
		}

		if ($this->gp['page'] == '') {
			$qa[] = "status='$status'";
		}

		return $qa;
	}

	function edit_article($kind) {
		$this->get_edit_article();

		switch ($this->gp['emode']) {
		case 'add' :
			$preset = array($kind, 'rtime=NOW()', 'utime=NOW()');
			$setquery = $this->set_query($preset);
			$query = 'INSERT INTO ' . MC_DB_TABLE_ARTICLE_PREFIX .
				$this->gp['site'] . ' SET ' .
				implode(', ', $setquery);
			break;
		case 'modify' :
			$preset = array('utime=NOW()');
			$setquery = $this->set_query($preset);
			$query = 'UPDATE ' . MC_DB_TABLE_ARTICLE_PREFIX .
				$this->gp['site'] . ' SET ' .
				implode(', ', $setquery) . ' WHERE id = "' .
				$this->gp['article'] . '"';
			break;
		case 'delete' :
			$query = 'SELECT id,parent FROM ' .
				MC_DB_TABLE_ARTICLE_PREFIX . $this->gp['site'] .
				' WHERE parent = "' . $this->gp['article'] .'"';
			$rows = $this->db->query($query);
			if (count($rows) > 0) {
				$this->tmpl->message = 'can not delete this article because it has children.';
				return False;
			}

			$query = 'DELETE FROM ' . MC_DB_TABLE_ARTICLE_PREFIX .
				$this->gp['site'] . ' WHERE id = "' .
				$this->gp['article'] . '"';
			break;
		default :
			$this->tmpl->message = 'edit mode is wrong';
			return False;
		}

		$rows = $this->db->query($query);
		if (count($rows) != 1) {
			$this->tmpl->message =
				'failure execute to edit an article';
			return False;
		}
		if ($this->gp['emode'] == 'add') {
			$lastid = $this->db->last_id();
			if ($lastid == 0) {
				$this->tmpl->message = 'unknown page ID';
				return False;
			}
		}

		$staticid = $this->gp['emode'] == 'add' ?
					$lastid : $this->gp['article'];
		$static_path = $this->tmpl->generate_static_path(array(
			'article', $this->gp['site'], $staticid));
		if ($static_path != '') {
			switch ($this->gp['emode']) {
			case 'add' :
			case 'modify' :
				$this->tmpl->set_node(
					$this->article->get_article(
						$this->gp['site'], $staticid));

				$this->tmpl->set_output();
				$this->tmpl->view_article(
					$this->gp['site'], $staticid);
				$this->tmpl->output($static_path);
				break;
			case 'delete' :
				$this->tmpl->unlink_output($static_path);
				break;
			}
		}

		return True;
	}

	function edit_article_category() {
		return $this->edit_article('kind="C"');
	}

	function edit_article_article() {
		return $this->edit_article('kind="A"');
	}

	function edit_article_url() {
		if ($this->gp['title'] == '') {
			$this->gp['title'] = $this->gp['description'];
		}

		return $this->edit_article('kind="U"');
	}

	function change_category() {
		list($site, $cid) = split(':', $this->getpost('parent'));
		$query = 'UPDATE ' . MC_DB_TABLE_ARTICLE_PREFIX . $site .
				' SET parent=' . $cid . ' WHERE id = "' .
				$this->getpost('article') . '"';

		$rows = $this->db->query($query);
		if (count($rows) != 1) {
			$this->tmpl->message =
				'failure execute to change category';
			return False;
		}

		return True;
	}

	function get_edit_page() {
		$this->gp = array();

		$this->gp['emode'] = $this->getpost('emode');
		$this->gp['page'] = $this->getpost('page');
		$this->gp['path'] = $this->getpost('path');
		$this->gp['title'] = $this->getpost('title');
		$this->gp['description'] = $this->getpost('description');
		if ($this->gp['path'] == '' || $this->gp['title'] == '' ||
				$this->gp['description'] == '') {
			$this->tmpl->view_admin_error('required',
					'required path, title and description');

		}
		if (!eregi("^[-_0-9a-z]+$", $this->gp['path'])) {
			$this->tmpl->view_admin_error('required',
					'path string is wrong');
		}
		if ($this->gp['emode'] == 'add' &&
			$this->page->exist_path(
				$this->gp['page'], $this->gp['path'])) {
				$this->tmpl->view_admin_error('required',
						'the path already exists');
		}
	}

	function edit_page() {
		$this->get_edit_page();

		switch ($this->gp['emode']) {
		case 'add' :
			$preset = array('rtime=NOW()', 'utime=NOW()');
			$setquery = $this->set_query($preset);
			$query = 'INSERT INTO ' . MC_DB_TABLE_PAGE .
				' SET ' . implode(', ', $setquery);
			break;
		case 'modify' :
			$preset = array('utime=NOW()');
			$setquery = $this->set_query($preset);
			$query = 'UPDATE ' . MC_DB_TABLE_PAGE .
				' SET ' . implode(', ', $setquery) .
				' WHERE id = "' . $this->gp['page'] . '"';
			break;
		case 'delete' :
			$query = 'DELETE FROM ' . MC_DB_TABLE_PAGE .
				' WHERE id = "' . $this->gp['page'] . '"';
			break;
		default :
			$this->tmpl->message = 'edit mode is wrong';
			return False;
		}

		$rows = $this->db->query($query);
		if (count($rows) != 1) {
			$this->tmpl->message = 'failure execute to edit a page';
			return False;
		}
		if ($this->gp['emode'] == 'add') {
			$lastid = $this->db->last_id();
			if ($lastid == 0) {
				$this->tmpl->message = 'unknown page ID';
				return False;
			}
		}

		$staticid = $this->gp['emode'] == 'add' ?
					$lastid : $this->gp['page'];
		$static_path = $this->tmpl->generate_static_path(
				array('page', $staticid));
		if ($static_path != '') {
			switch ($this->gp['emode']) {
			case 'add' :
			case 'modify' :
				$this->page->read_pages();
				$this->page->set_page($staticid);

				$this->tmpl->set_output();
				$this->tmpl->view_page($staticid);
				$this->tmpl->output($static_path);
				break;
			case 'delete' :
				$this->tmpl->unlink_output($static_path);
				break;
			}
		}

		return True;
	}
}
?>
