<?php
/**
 * functions of MinCMS
 *
 * common functions that is used on templates.
 *
 * @version $Id:$
 * @package MinCMS
 * @author $Author:$
 */

/**
 * include header.php
 *
 * include header.php in current template.
 */
function theHeader($pre = '') {
	include(get_header($pre));
}

function theFooter($pre = '') {
	include(get_footer($pre));
}

function theSideLeft($pre = '') {
	include(get_side_left($pre));
}

function theSideRight($pre = '') {
	include(get_side_right($pre));
}

function theCurrentUri() {
	print get_current_uri();
}

function theMessage() {
	print get_message();
}

function theVersion() {
	print get_version();
}

function theTitle() {
	print get_title();
}

function theSubtitle() {
	print get_subtitle();
}

function theName() {
	print get_name();
}

function theURI() {
	print get_uri();
}

function theURILogout() {
	print get_uri_logout();
}

function theURL() {
	print get_url();
}

function theViewURL($sid, $aid) {
	print get_view_url($sid, $aid);
}

/**
 * get header's path
 *
 * get a path of header.php in current template.
 *
 * @return string header.php file path
 */
function get_header($pre = '') {
	global $mc;

	return $mc->tmpl->get_header($pre);
}

function get_footer($pre = '') {
	global $mc;

	return $mc->tmpl->get_footer($pre);
}

function get_side_left($pre = '') {
	global $mc;

	return $mc->tmpl->get_side_left($pre);
}

function get_side_right($pre = '') {
	global $mc;

	return $mc->tmpl->get_side_right($pre);
}

function get_current_uri() {
	global $mc;

	return $mc->tmpl->get_current_uri();
}

function generate_url($opts = array()) {
	global $mc;

	return $mc->generate_url(get_url(), $opts);
}

function get_message() {
	global $mc;

	return $mc->tmpl->get_message();
}

function get_version() {
	global $mc;

	return $mc->get_version();
}

function get_title() {
	global $mc;

	return $mc->get_title();
}

function get_subtitle() {
	global $mc;

	return $mc->get_subtitle();
}

function get_name() {
	global $mc;

	return $mc->get_name();
}

function get_uri() {
	global $mc;

	return $mc->uri;
}

function get_uri_logout() {
	global $mc;

	return $mc->uri . '?mode=logout';
}

function get_url() {
	global $mc;

	return $mc->url;
}

function get_view_url($sid, $aid) {
	global $mc;

	return $mc->generate_url(MC_URL,
		array('site' => $sid, 'article' => $aid));
}


function do_login() {
	global $mc;

	return $mc->do_login();
}

function admin_menu_list() {
	global $mc;

	return $mc instanceof MinCMSAdmin ? $mc->get_menu_list() : array();
}

function get_tmpl_result() {
	global $mc;

	return $mc->tmpl->result;
}

function get_article_title() {
	global $mc;

	return $mc->tmpl->node ? $mc->tmpl->node->record['title'] : '';
}

function get_article_description() {
	global $mc;

	return $mc->tmpl->node ? $mc->tmpl->node->record['description'] : '';
}

function get_article_begin_date() {
	global $mc;

	return $mc->tmpl->node ? $mc->tmpl->node->format_date('btime') : '';
}

function get_article_begin_hms() {
	global $mc;

	return $mc->tmpl->node ? $mc->tmpl->node->format_hms('btime') : '';
}

function get_article_end_date() {
	global $mc;

	return $mc->tmpl->node ? $mc->tmpl->node->format_date('etime') : '';
}

function get_article_end_hms() {
	global $mc;

	return $mc->tmpl->node ? $mc->tmpl->node->format_hms('etime') : '';
}

function theArticleTitle() {
	print get_article_title();
}

function theArticleDescription() {
	print get_article_description();
}

function theArticleBeginDate() {
	print get_article_begin_date();
}

function theArticleBeginHMS() {
	print get_article_begin_hms();
}

function theArticleEndDate() {
	print get_article_end_date();
}

function theArticleEndHMS() {
	print get_article_end_hms();
}

function theArticleSiteID() {
	global $mc;

	print $mc->tmpl->sid;
}

function theArticleID() {
	global $mc;

	print $mc->getpost('article');
}

function get_article_children() {
	global $mc;

	$ret = array();
	foreach ($mc->tmpl->node->children as $c) {
		$opts = array('site' => $mc->tmpl->node->parent['id'],
			'article' => $c->record['id']);
		$ret[$mc->generate_url($mc->uri, $opts)] = $c;
	}

	return $ret;
}

function get_site_name() {
	global $mc;

	return $mc->tmpl->node->record ? $mc->tmpl->node->record['name'] : '';
}

function get_site_contact() {
	global $mc;

	return $mc->tmpl->node->record ? $mc->tmpl->node->record['contact'] : '';
}

function get_site_name_of_article() {
	global $mc;

	return $mc->tmpl->node->parent ? $mc->tmpl->node->parent['name'] : '';
}

function get_site_contact_of_article() {
	global $mc;

	return $mc->tmpl->node->parent ? $mc->tmpl->node->parent['contact'] : '';
}

function theSiteName() {
	print get_site_name();
}

function theSiteContact() {
	print get_site_contact();
}

function theSiteNameOfArticle() {
	print get_site_name_of_article();
}

function theSiteContactOfArticle() {
	print get_site_contact_of_article();
}

function get_site_children() {
	global $mc;

	$ret = array();
	foreach ($mc->tmpl->node->children as $c) {
		$opts = array('site' => $mc->tmpl->node->record['id'],
			'article' => $c->record['id']);
		$ret[$mc->generate_url($mc->uri, $opts)] = $c;
	}

	return $ret;
}

function set_page($id) {
	global $mc;

	$mc->page->set_page($id);
}

function get_page_path() {
	global $mc;

	return $mc->page->get_page_path();
}

function get_page_title() {
	global $mc;

	return $mc->page->get_page_title();
}

function get_page_description() {
	global $mc;

	return $mc->page->get_page_description();
}

function thePagePath() {
	print get_page_path();
}

function thePageTitle() {
	print get_page_title();
}

function thePageDescription() {
	print get_page_description();
}

function get_calendar($key, $id) {
	global $mc;

	if (ereg('^([0-9]{4})-([0-9]{2})$', $mc->cal, $m)) {
		return $mc->calendar->month(
			$mc->article->pick_up($key, $id), $m[1], $m[2]);
	} else if (ereg('^([0-9]{4})$', $mc->cal, $m)) {
		return $mc->calendar->year(
			$mc->article->pick_up($key, $id),
			MC_CALENDAR_YEAR_COLUMN, $m[1]);
	}

	return $mc->calendar->month($mc->article->pick_up($key, $id));
}

function theCalendarWithLabel($id) {
	print get_calendar('label', $id);
}

function theCalendarAtSite($id) {
	print get_calendar('site', $id);
}

function pick_up_with_label($id) {
	global $mc;

	return $mc->article->pick_up('label', $id);
}

function pick_up_at_site($id) {
	global $mc;

	return $mc->article->pick_up('site', $id);
}

function search_result($c) {
	global $mc;

	return array_chunk($mc->article->pick_up('search', $mc->oargs['q']),$c);
}

function get_article_path($sid, $id) {
	global $mc;

	return $mc->generate_article_path($sid, $id);
}

function theArticlePath($sid, $id) {
	print get_article_path($sid, $id);
}

function iso8601($d) {
	return strftime("%Y-%m-%dT%H:%M:%S" . MC_TIMEZONE, strtotime($d));
}

function rfc822($d) {
	return strftime("%a, %d %b %Y %H:%M:%S" . MC_TIMEZONE, strtotime($d));
}

/**
 * simulate gettext() translate messages function.
 */
function _M($key) {
	global $Messages;

	return isset($Messages[$key]) ? $Messages[$key] : $key;
}
?>
