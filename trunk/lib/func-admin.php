<?php
/**
 * functions of MinCMS for admin
 *
 * common functions that is used on templates for admin.
 *
 * @version $Id:$
 * @package MinCMS
 * @author $Author:$
 */

/**
 * get users
 *
 * get users that is registed in this system.
 *
 * @return array an array that result a query to user table.
 */
function get_users() {
	global $mc;

	$offset = ($mc->oargs['offset'] ? $mc->oargs['offset'] : 0) *
			MC_PAGINATION_SIZE;
	return array_slice($mc->tmpl->users, $offset, MC_PAGINATION_SIZE);
}

function theAdminHeader($pre = '') {
	include(get_admin_header($pre));
}

function theAdminFooter($pre = '') {
	include(get_admin_footer($pre));
}

function theAdminSideLeft($pre = '') {
	include(get_admin_side_left($pre));
}

function theAdminSideRight($pre = '') {
	include(get_admin_side_right($pre));
}

function get_admin_header($pre = '') {
	global $mc;

	return $mc->tmpl->get_admin_header($pre);
}

function get_admin_footer($pre = '') {
	global $mc;

	return $mc->tmpl->get_admin_footer($pre);
}

function get_admin_side_left($pre = '') {
	global $mc;

	return $mc->tmpl->get_admin_side_left($pre);
}

function get_admin_side_right($pre = '') {
	global $mc;

	return $mc->tmpl->get_admin_side_right($pre);
}

function theAuthority($id) {
	global $mc;

	print $mc->tmpl->my_authority($mc->get_authority($id));
}

function theAuthorityCheckbox($id) {
	global $mc;

	print $mc->tmpl->my_authority_checkbox($mc->get_authority($id));
}

function theUserAdminEditType() {
	global $mc;

	$id = $mc->getpost('id');
	print $mc->tmpl->get_edittype_byID($id);
}

function theSiteAdminEditType() {
	theUserAdminEditType();
}

function theReadOnly($id) {
	print $id ? 'readonly="readonly"' : '';
}

function theRegistUpdateDate($r) {
	print $r['id'] ? $r['rtime'] . '&nbsp;/&nbsp;' .  $r['utime'] :
		'----&nbsp;/&nbsp;----';
}

function get_siteuser($u) {
	global $mc;

	return $u['userid'] .
		'(' . $mc->tmpl->my_authname($u['authority']) . ')';
}

function theSiteUser($u) {
	print get_siteuser($u);
}

function theEditSiteUsers($users) {
	$ret = array();
	$ret[] = '<select name="siteuser[]" size="10" multiple="multiple">';
	foreach ($users as $u) {
		$sel = $u['selected'] == 1 ? 'selected="selected"' : '';
		$ret[] = '  <option value="' . $u['userid'] . "\" $sel>" .
			get_siteuser($u) . '</option>';
	}
	$ret[] = '</select>';

	print implode("\n", $ret);
}

function theArticleTreeLeft() {
	global $mc;

	print $mc->article->htmltree($mc->site, $mc->id);
}

function get_parent_options() {
	global $mc;

	$ret = array();

	foreach ($mc->article->get_categories() as $sa) {
		$sid = $sa[0]['id'];
		$sname = $sa[0]['name'];
		if ($sa[1]) {
			$aid = $sa[1]->record['id'];
			$atitle = $sa[1]->record['title'];
		} else {
			$aid = 0;
			$atitle = 'top';
		}
		$selected = '">';
		if ($mc->tmpl->node) {
			if ($mc->tmpl->node->record['kind'] == 'C') {
				$pid = $mc->tmpl->node->record['id'];
			} else {
				$pid = $mc->tmpl->node->get_parent_id();
			}
//			$site_node = $mc->tmpl->node->get_root_node();
			$site_node = $mc->article->node[$sid]->get_root_node();
			$site_id = $site_node->record['id'];
			if ($site_id == $sid && $pid == $aid) {
				$selected = '" selected="selected">';
			}
		} else if ($mc->getpost('parent')) {
			if ($mc->getpost('parent') == "$sid:$aid") {
				$selected = '" selected="selected">';
			}
		}

		$ret[] = '<option value="' . $sid . ':' . $aid . $selected .
			$sname . '&nbsp;-&nbsp;' . $atitle . '</option>';
	}

	return $ret;
}

function theParentOptions() {
	print implode("\n", get_parent_options());
}

function theArticleHiddenID() {
	global $mc;

	$parent = $mc->getpost('parent');
	$site = $mc->getpost('site');
	$article = $mc->getpost('article');

	print <<<_EOF
<input type="hidden" name="parent" value="$parent" />
<input type="hidden" name="site" value="$site" />
<input type="hidden" name="article" value="$article" />
_EOF;
}

function theArticleEditModeButton() {
	global $mc;

	$article = $mc->getpost('article');
	$site = $mc->getpost('site');

	if ($article == -1) {
		$edit_mode = '<input type="radio" readonly="readonly" ' .
			'name="emode" value="add" checked="checked" />' .
			_M('add');
	} else {
		if ($mc->login->can_create()) {
			$edit_mode = '<input type="radio" ' .
				'name="emode" value="modify"';

			$query = 'SELECT id,parent FROM ' .
				MC_DB_TABLE_ARTICLE_PREFIX . $site .
				' WHERE parent = "' . $article .'"';
			$rows = $mc->db->query($query);
			if (count($rows) == 0) {
				$edit_mode .= ' />' . _M('modify') .
					'&nbsp;<input type="radio" name="emode" value="delete" />' . _M('delete');
			} else {
				$edit_mode .= ' checked="checked" />' . _M('modify');
			}
		} else {
			$edit_mode = '<input type="hidden" name="emode" value="modify" />';
		}
	}

	print $edit_mode;
}

function theArticleStatusBox() {
	global $mc;

	$article = $mc->getpost('article');

	$vi = $article == -1 ? False : $mc->tmpl->is_inspect_status();
	$vp = $article == -1 ? False : $mc->tmpl->is_publish_status();

	$sb = $mc->tmpl->status_hidden(MC_ADMIN_AUTH_CREATE);
	$sb .= $mc->tmpl->status_checkbox(MC_ADMIN_AUTH_INSPECT, $vi,
			$mc->login->can_inspect());
	$sb .= $mc->tmpl->status_checkbox(MC_ADMIN_AUTH_PUBLISH, $vp,
			$mc->login->can_publish());

	print $sb;
}

function theArticleLabelOption() {
	global $mc, $LabelOptions;

	$label = $mc->tmpl->get_label();
	$auth = $mc->login->can_create();
	$ko = '<select name="label[]" multiple="multiple"';
	$ko .= $auth ? '>' : ' disabled="disabled">';
	foreach ($LabelOptions as $key => $val) {
		$ko .= $mc->tmpl->label_option($key, $val, $label);
	}
	$ko .= '</select>';

	print $ko;
}

function canCreate() {
	global $mc;

	return $mc->login->can_create();
}

function theArticleReadOnly() {
	global $mc;

	print $mc->login->can_create() ? '' : 'readonly="readonly"';
}

function theArticleDisabled() {
	global $mc;

	print $mc->login->can_create() ? '' : 'disabled="disabled"';
}

function theUploadType() {
	global $mc;

	print htmlspecialchars($mc->getpost('type'));
}

function theUploadId() {
	global $mc;

	print htmlspecialchars($mc->getpost('id'));
}

function theUploadedFiles() {
	global $mc;

	$mc->up->uploaded_files();
}

function theUploadPagination() {
	global $mc;

	print $mc->up->pagination();
}

function get_new_articles() {
	global $mc;

	$offset = ($mc->oargs['offset'] ? $mc->oargs['offset'] : 0) *
			MC_PAGINATION_SIZE;
	return array_slice($mc->article->get_new_articles(),
				$offset, MC_PAGINATION_SIZE);
}

function get_the_our_categories() {
	global $mc;

	$ret = array();

	foreach ($mc->article->get_categories() as $sa) {
		if ($mc->tmpl->node == NULL) {
			continue;
		}
		$sid = $sa[0]['id'];
		$sname = $sa[0]['name'];

//		$site_node = $mc->tmpl->node->get_root_node();
		$site_node = $mc->article->node[$sid]->get_root_node();
		$site_id = $site_node->record['id'];

		if ($sa[1]) {
			$aid = $sa[1]->record['id'];
			$atitle = $sa[1]->record['title'];
		} else {
			$aid = 0;
			$atitle = 'top';
		}

		if ($sid != $site_id) {
			continue;
		}

		$selected = '';
		$pid = $mc->tmpl->node->get_parent_id();
		if ($pid == $aid) {
			$selected = ' selected="selected"';
		}

		$ret[] = <<<_EOF
<option value="$sid:$aid"$selected>$sname&nbsp;-&nbsp;$atitle</option>
_EOF;
	}

	return $ret;
}

function doesEditArticle() {
	global $mc;

	return ($mc->getpost('mode') == 'editarticle' &&
		$mc->getpost('site') > 0 && $mc->getpost('article') > 0) ?
		True : False;
}

function theOurCategories() {
	print implode("\n", get_the_our_categories());
}

function get_kind_name($kind) {
	switch ($kind) {
	case 'C' :
		return MC_ADMIN_KIND_CATEGORY;
	case 'A' :
		return MC_ADMIN_KIND_ARTICLE;
	case 'U' :
		return MC_ADMIN_KIND_URL;
	}

	return MC_ADMIN_KIND_UNKNOWN;
}

function get_status_name($status) {
	if ($status & MC_ADMIN_AUTH_PUBLISH) {
		return MC_ADMIN_AUTH_PUBLISH_NAME;
	}
	if ($status & MC_ADMIN_AUTH_INSPECT) {
		return MC_ADMIN_AUTH_INSPECT_NAME;
	}
	if ($status & MC_ADMIN_AUTH_CREATE) {
		return MC_ADMIN_AUTH_CREATE_NAME;
	}
	if ($status & MC_ADMIN_AUTH_VIEW) {
		return MC_ADMIN_AUTH_VIEW_NAME;
	}

	return MC_ADMIN_AUTH_NOSTATUS_NAME;
}

function theKindName($kind) {
	print _M(get_kind_name($kind));
}

function theStatusName($status) {
	print _M(get_status_name($status));
}

function get_page_list() {
	global $mc;

	$offset = ($mc->oargs['offset'] ? $mc->oargs['offset'] : 0) *
			MC_PAGINATION_SIZE;
	return array_slice($mc->page->pages, $offset, MC_PAGINATION_SIZE);
}

function thePageHiddenID() {
	global $mc;

	$page = $mc->getpost('page');

	print <<<_EOF
<input type="hidden" name="page" value="$page" />
_EOF;
}

function thePageEditModeButton() {
	global $mc;

	$page = $mc->getpost('page');

	if ($page == -1) {
		$edit_mode = '<input type="radio" readonly="readonly" ' .
			'name="emode" value="add" checked="checked" />' .
			_M('add');
	} else {
		$edit_mode = '<input type="radio" ' .
				'name="emode" value="modify" />' .
				_M('modify') . '&nbsp;' .
		'<input type="radio" name="emode" value="delete" />' .
				_M('delete');
	}

	print $edit_mode;
}

function get_sites() {
	global $mc;

	$offset = ($mc->oargs['offset'] ? $mc->oargs['offset'] : 0) *
			MC_PAGINATION_SIZE;
	return array_slice(get_tmpl_result(), $offset, MC_PAGINATION_SIZE);
}

function get_pagination_url($url, $offset, $name) {
	return '<a href="' . $url . MC_PARAM_SEPARATOR .
		'offset=' . $offset . '">' . $name . '</a>';
}

function get_pagination($mode) {
	global $mc;

	$a = array();
	$offset = $mc->oargs['offset'] ? $mc->oargs['offset'] : 0;
	switch ($mode) {
	case '?mode=editarticle' :
		$a = $mc->article->get_new_articles();
		break;
	case '?mode=editpage' :
		$a = $mc->page->pages;
		break;
	case '?mode=useradmin' :
		$a = $mc->tmpl->users;
		break;
	case '?mode=siteadmin' :
		$a = get_tmpl_result();
		break;
	}

	$prev = $offset == 0 ? 'prev' :
		get_pagination_url($mc->uri . $mode, $offset - 1, 'prev');
	$current = get_pagination_url($mc->uri . $mode, $offset, 'current');
	$next = ($offset + 1) * MC_PAGINATION_SIZE >= count($a) ? 'next' :
		get_pagination_url($mc->uri . $mode, $offset + 1, 'next');

	return <<<_EOF
	<li>$prev</li>
	<li>$current</li>
	<li class="last">$next</li>

_EOF;
}

function get_pagination_new_articles() {
	return get_pagination('?mode=editarticle');
}

function get_pagination_pages() {
	return get_pagination('?mode=editpage');
}

function get_pagination_users() {
	return get_pagination('?mode=useradmin');
}

function get_pagination_sites() {
	return get_pagination('?mode=siteadmin');
}

function thePaginationNewArticles() {
	print get_pagination_new_articles();
}

function thePaginationPages() {
	print get_pagination_pages();
}

function thePaginationUsers() {
	print get_pagination_users();
}

function thePaginationSites() {
	print get_pagination_sites();
}

function get_current_admin_uri() {
	global $mc;

	return $mc->tmpl->get_current_admin_uri();
}

function theCurrentAdminUri() {
	print get_current_admin_uri();
}

function get_redirect() {
	global $mc;

	return $mc->tmpl->redirect;
}

function theRedirect() {
	print get_redirect();
}

function static_files_by_shell() {
	global $mc, $StaticFilesByShell;


	if (MC_STATIC_PATH != '') {
		$mc = new MinCMSAdmin();
		foreach ($StaticFilesByShell as $key => $val) {
			$static_path = MC_STATIC_PATH . '/' . $val;
			if ($key == 'home') {
				$mc->tmpl->set_output();
				$mc->tmpl->view_home();
				$mc->tmpl->output($static_path);
			} else if (preg_match('/^rss:([12]):([^:]+):([^:]+)$/',
					$key, $m)) {
				$mc->tmpl->set_output();
				$data = $mc->article->generate_rss(
						$m[1], $m[2], $m[3]);
				$mc->tmpl->view_rss($m[1], $data);
				$mc->tmpl->output($static_path);
			}
		}
	}
}
?>
