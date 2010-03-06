<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" 
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="Content-Style-Type" content="text/css" />
<meta http-equiv="Content-Script-Type" content="text/javascript" />
<link rel="stylesheet" type="text/css"
  href="<?php theCurrentAdminUri(); ?>style.css" media="all" />
<link rel="stylesheet" type="text/css"
  href="<?php theCurrentAdminUri(); ?>js/thickbox/thickbox.css" media="screen" />
<link rel="stylesheet" type="text/css"
  href="<?php theCurrentAdminUri(); ?>js/treeview/style.css" media="screen" />
<?php if (canCreate()) : ?>
<link rel="stylesheet" type="text/css"
  href="<?php theCurrentAdminUri(); ?>js/jquery-ui/themes/base/ui.all.css" media="screen" />
<link rel="stylesheet" type="text/css"
  href="<?php theCurrentAdminUri(); ?>js/timePicker/style.css" media="screen" />
<?php endif; ?>
<script src="<?php theCurrentAdminUri(); ?>js/jquery.js"
  type="text/javascript"></script>
<script src="<?php theCurrentAdminUri(); ?>js/thickbox/thickbox.js"
  type="text/javascript"></script>
<script src="<?php theCurrentAdminUri(); ?>js/tiny_mce/jquery.tinymce.js"
  type="text/javascript"></script>
<script src="<?php theCurrentAdminUri(); ?>js/treeview/jquery.treeview.js"
  type="text/javascript"></script>
<script src="<?php theCurrentAdminUri(); ?>js/treeview/jquery.cookie.js"
  type="text/javascript"></script>
<?php if (canCreate()) : ?>
<script src="<?php theCurrentAdminUri(); ?>js/jquery-ui/ui/ui.core.js"
  type="text/javascript"></script>
<script src="<?php theCurrentAdminUri(); ?>js/jquery-ui/ui/ui.datepicker.js"
  type="text/javascript"></script>
<script src="<?php theCurrentAdminUri(); ?>js/timePicker/jquery.timePicker.js"
  type="text/javascript"></script>
<?php endif; ?>
<script type="text/javascript">
//<![CDATA[

$(document).ready(function() {
	$("#sitetree ul").treeview({
		collapsed: true,
		persist: "location"
	});
	$('textarea.tinymce').tinymce({
		script_url : '<?php theCurrentAdminUri(); ?>js/tiny_mce/tiny_mce.js',
		convert_urls : 0,
		width : '95%',
		height : '240px',
<?php if (!canCreate()) : ?>
		readonly : 1,
<?php endif; ?>
		// for styleselect
		theme_advanced_styles : "クラス0=class0;クラス1=class1",

		// General options
		theme : "advanced",
		language : "ja",
		plugins : "safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",

		// Theme options
		theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,styleselect,formatselect,fontsizeselect",
		theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,bullist,numlist,|,outdent,indent,blockquote,|,link,unlink,anchor,image,cleanup,code,|,preview,|,forecolor,backcolor",
		theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,|,sub,sup,|,charmap,iespell,media,|,fullscreen",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true,

		// Example content CSS (should be your site CSS)
		content_css : "<?php theCurrentUri(); ?>style.css",
		external_link_list_url : "<?php theURI(); ?>?mode=uploadlist&amp;type=site&amp;kind=link&amp;id=<?php theArticleSiteID(); ?>",
		external_image_list_url : "<?php theURI(); ?>?mode=uploadlist&amp;type=site&amp;kind=image&amp;id=<?php theArticleSiteID(); ?>"
	});
<?php if (canCreate()) : ?>
	$("#bdatepicker").datepicker({dateFormat: 'yy-mm-dd'});
	$("#edatepicker").datepicker({dateFormat: 'yy-mm-dd'});
	$("#btimePicker").timePicker();
	$("#etimePicker").timePicker();
<?php endif; ?>
});

//]]>
</script>

<title><?php theTitle(); ?></title>
</head>
<body>
<div id="container">
  <div id="header">
    <h1><a href="./"><?php theTitle(); ?><span>(デフォルト)</span></a></h1>
    <h2><?php theSubtitle(); ?></h2>
    <div id="adminmenu">
      <ul>
    <?php foreach (admin_menu_list() as $key => $val) : ?>
        <li class="<?php print $key; ?>"><a href="<?php theURI(); print "?mode=$key"; ?>"><?php print _M($val); ?></a></li>
    <?php endforeach; ?>
      </ul>
    </div>
  </div>
