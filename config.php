<?php
/**
 * configurations of MinCMS
 *
 * specified and declared global variables.
 *
 * @version $Id:$
 * @package MinCMS
 * @author $Author:$
 */
define('MC_DB_HOST', 'localhost');
define('MC_DB_NAME', 'mincms');
define('MC_DB_USER', 'mincms');
define('MC_DB_PASS', 'mincms');
define('MC_DB_TABLE_USER', 'user');
define('MC_DB_TABLE_CONFIG', 'config');
define('MC_DB_TABLE_SITE', 'site');
define('MC_DB_TABLE_SITEUSER', 'siteuser');
define('MC_DB_TABLE_PAGE', 'page');
define('MC_DB_TABLE_ARTICLE_PREFIX', 'articleSID');

define('MC_FQDN', 'www.foobar.jp');
define('MC_URI', '/~foo/MinCMS/');
define('MC_URI_ADMIN', MC_URI . 'admin/');
define('MC_URL', 'http://' . MC_FQDN . MC_URI);
define('MC_URL_ADMIN', 'http://' . MC_FQDN . MC_URI_ADMIN);
define('MC_URL_REWRITE', False);
define('MC_AUTH_SECURE', False);
define('MC_TOP_PATH', dirname(__FILE__));
define('MC_TMPL_TOP', MC_TOP_PATH . '/contents/tmpl/');
define('MC_TMPL_ADMIN', MC_TOP_PATH . '/admin/tmpl/default/');
define('MC_TMPL_TOP_URL', MC_URI . 'contents/tmpl/');
define('MC_TMPL_SCREENSHOT_FILENAME', 'screenshot.png');
define('MC_TMPL_NO_SCREENSHOT', MC_URI . 'images/no-screenshot.png');
define('MC_UPLOAD_TOP_DIR', MC_TOP_PATH . '/contents/uploads/');
define('MC_UPLOAD_TOP_URL', MC_URI . 'contents/uploads/');
define('MC_UPLOAD_SITE_TOP_DIR', MC_UPLOAD_TOP_DIR . 'sites/');
define('MC_UPLOAD_SITE_TOP_URL', MC_UPLOAD_TOP_URL . 'sites/');
define('MC_UPLOAD_PAGE_TOP_DIR', MC_UPLOAD_TOP_DIR . 'pages/');
define('MC_UPLOAD_PAGE_TOP_URL', MC_UPLOAD_TOP_URL . 'pages/');
define('MC_UPLOAD_INPUT_NAME', 'uploadfile');
define('MC_UPLOAD_THUMBNAIL_SUFFIX', '_thumb');
define('MC_UPLOAD_THUMBNAIL_WIDTH', 32);
define('MC_UPLOAD_LIST_MAX', 10);
define('MC_UPLOAD_ICON_URL', MC_URI . 'images/');
define('MC_UPLOAD_ICON_SUFFIX', '-32.png');
define('MC_UPLOAD_SCRIPT_URL', MC_URI_ADMIN);
define('MC_PARAM_SEPARATOR', '&amp;');
define('MC_TIMEZONE', '+0900');

define('MC_NO_LOGIN_USERNAME', 'guest');

define('MC_MSG_LOGIN_FAILURE', 'You failed to login.');
define('MC_MSG_LOGIN_NEED', 'You need to login.');
define('MC_ADMIN_MENU_HOME', 'home');
define('MC_ADMIN_MENU_ARTICLE', 'edit article');
define('MC_ADMIN_MENU_PAGE', 'edit page');
define('MC_ADMIN_MENU_USER_ADMIN', 'user');
define('MC_ADMIN_MENU_SITE_ADMIN', 'site');
define('MC_ADMIN_MENU_TMPL_ADMIN', 'template');
define('MC_ADMIN_MENU_CONF_ADMIN', 'configuration');

define('MC_VERSION', 'MinCMS version 0.1 $Id:$');
?>
