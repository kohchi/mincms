<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" 
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="Content-Style-Type" content="text/css" />
<meta http-equiv="Content-Script-Type" content="text/javascript" />
<link rel="alternate" type="application/rss+xml" title="RSS"
  href="/~foo/MinCMS/?mode=rss&amp;v=1&amp;key=site&amp;val=4" />
<link rel="stylesheet" type="text/css"
  href="<?php theCurrentUri(); ?>style.css" media="all" />
<title><?php theTitle(); ?></title>
</head>
<body>
<div id="container">
  <div id="header">
    <h1><a href="./"><?php theTitle(); ?><span>(デフォルト)</span></a></h1>
    <h2><?php theSubtitle(); ?></h2>
  </div>
  <div id="menu">
    <ul>
      <li><a href="./">ホーム</a></li>
      <li><a href="#">SiteMap</a></li>
    </ul>
  </div>
