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
<script src="<?php theCurrentAdminUri(); ?>js/jquery.js"
  type="text/javascript"></script>
<script src="<?php theCurrentAdminUri(); ?>js/thickbox/thickbox.js"
  type="text/javascript"></script>
<script src="<?php theCurrentAdminUri(); ?>js/tiny_mce/jquery.tinymce.js"
  type="text/javascript"></script>
<script type="text/javascript">
//<![CDATA[

$(document).ready(function() {
	$('textarea.tinymce').tinymce({
		script_url : '<?php theCurrentAdminUri(); ?>js/tiny_mce/tiny_mce.js',
		convert_urls : 0,
		width : '95%',
		height : '240px',
		// for styleselect
		theme_advanced_styles : "Test1=fonttest1;Test2=fonttest2",

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
		external_link_list_url : "<?php theURI(); ?>?mode=uploadlist&amp;type=page&amp;kind=link",
		external_image_list_url : "<?php theURI(); ?>?mode=uploadlist&amp;type=page&amp;kind=image"
	});
});

//]]>
</script>

<title><?php theTitle(); ?></title>
</head>
<body>
<div id="container">
  <div id="header">
    <h1><?php theTitle(); ?><span>(デフォルト)</span></h1>
    <h2><?php theSubtitle(); ?></h2>
    <div id="adminmenu">
      <ul>
    <?php foreach (admin_menu_list() as $key => $val) : ?>
        <li class="<?php print $key; ?>"><a href="<?php theURI(); print "?mode=$key"; ?>"><?php print _M($val); ?></a></li>
    <?php endforeach; ?>
      </ul>
    </div>
  </div>
