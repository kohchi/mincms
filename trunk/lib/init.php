<?php
/**
 * initialized MinCMS
 *
 * include the classes and functions that MinCMS needs.
 *
 * @version $Id:$
 * @package MinCMS
 * @author $Author:$
 */

define('MC_COOKIE_NAME', 'mincmsls');
define('MC_ADMIN_AUTH_VIEW', 1 << 0);
define('MC_ADMIN_AUTH_CREATE', 1 << 1);
define('MC_ADMIN_AUTH_INSPECT', 1 << 2);
define('MC_ADMIN_AUTH_PUBLISH', 1 << 3);
define('MC_ADMIN_AUTH_PAGE', 1 << 4);
define('MC_ADMIN_AUTH_USER_ADMIN', 1 << 5);
define('MC_ADMIN_AUTH_SITE_ADMIN', 1 << 6);
define('MC_ADMIN_AUTH_TMPL_ADMIN', 1 << 7);
define('MC_ADMIN_AUTH_CONF_ADMIN', 1 << 8);
define('MC_ADMIN_AUTH_ADMIN', 0x7fffffff);

define('MC_ADMIN_AUTH_NOSTATUS_NAME', 'no status');
define('MC_ADMIN_AUTH_VIEW_NAME', 'viewer');
define('MC_ADMIN_AUTH_CREATE_NAME', 'creator');
define('MC_ADMIN_AUTH_INSPECT_NAME', 'inspector');
define('MC_ADMIN_AUTH_PUBLISH_NAME', 'publisher');
define('MC_ADMIN_AUTH_PAGE_NAME', 'page admin');
define('MC_ADMIN_AUTH_USER_ADMIN_NAME', 'user admin');
define('MC_ADMIN_AUTH_SITE_ADMIN_NAME', 'site admin');
define('MC_ADMIN_AUTH_TMPL_ADMIN_NAME', 'template admin');
define('MC_ADMIN_AUTH_CONF_ADMIN_NAME', 'configuration admin');
define('MC_ADMIN_AUTH_ADMIN_NAME', 'Administrator');

define('MC_ADMIN_CAN_NOT_EDIT', 'Can not edit');
define('MC_BR', "<br />\n");
define('MC_COMMA', ',');
define('MC_ADMIN_NEW_ARTICLE_MAX', 100);

define('MC_ADMIN_KIND_CATEGORY', 'category');
define('MC_ADMIN_KIND_ARTICLE', 'article');
define('MC_ADMIN_KIND_URL', 'url');
define('MC_ADMIN_KIND_UNKNOWN', 'unknown');

define('MC_CALENDAR_YEAR_COLUMN', 3);

define('MC_PAGINATION_SIZE', 20);

define('MC_STATIC_PATH', '/home/foo/public_html/MinCMS/contents/static');
//define('MC_STATIC_PATH', '');
define('MC_STATIC_SUFFIX', '.html');
//define('MC_STATIC_SUFFIX', '');


$UploadFileType = array(
	'png' => '\.png$',
	'gif' => '\.gif$',
	'jpg' => '\.jpe?g$',
	'doc' => '\.docx?$',
	'xls' => '\.xlsx?$',
	'ppt' => '\.pptx?$',
	'txt' => '\.txt$',
	'pdf' => '\.pdf$',
	'zip' => '\.zip$',
	'lzh' => '\.lzh$'
);
$UploadImageFormat = array(IMG_GIF, IMG_JPG, IMG_PNG);

$LabelOptions = array(
	"whatsnew" => 0x01,
	"event" => 0x02
);

$Messages = array(
	'You need to login.' => 'ログインしてください',
	'whatsnew rss title' => '新着情報RSS',
	'whatsnew rss description' => '新着情報をRSSで配信しています',
	'event rss title' => 'イベント情報RSS',
	'event rss description' => 'イベント情報をRSSで配信しています',
	'Add' => '追加',
	'Modified' => '修正',
	'Deleted' => '削除',
	'add' => '追加',
	'modify' => '修正',
	'delete' => '削除',
	'creator' => '作成',
	'inspector' => '承認',
	'publisher' => '公開',
	'category' => 'カテゴリ',
	'article' => '記事',
	'url' => 'URL',
	'path name is invalid' => 'パス名が無効です',
	'require site name' => 'サイト名が必要です',
	'less authorization to edit' => '権限が足りません',
	'home' => '管理ホーム',
	'edit article' => '記事修正',
	'edit page' => 'ページ修正',
	'user' => 'ユーザ',
	'site' => 'サイト',
	'template' => 'テンプレート',
	'configuration' => '設定'
);

$StaticFilesByShell = array(
	'rss:1:label:1' => 'rss1-label-1.xml',
	'rss:2:site:1' => 'rss2-site-1.xml',
	'home' => 'index.html'
);

include('MinCore.php');
include('MinArticle.php');
include('MinCMS.php');
include('MinCalendar.php');
include('MinDB.php');
include('MinLogin.php');
include('MinNode.php');
include('MinPage.php');
include('MinTemplate.php');
include('MinUpload.php');
include('func.php');
include('func-admin.php');
?>
