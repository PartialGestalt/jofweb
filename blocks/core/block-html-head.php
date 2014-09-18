<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-us" lang="en-us">
  <head>
<?php
  if ($_SERVER['HTTP_HOST'] == 'beta.jofumc.net') {
    print '<title>(BETA) Journey of Faith UMC</title>';
  } else {
    print '<title>Journey of Faith UMC</title>';
  }
?>
    <link rel="icon" type="image/vnd.microsoft.icon" href="/skins/common/jofrr.ico"/>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
    <meta name="robots" content="index,follow" /> 
    <meta name="Keywords" content="journey of faith,church,methodist,UMC,round rock"/>
    <script type="text/javascript" src="/library/core/util-core.js"></script>
<?php
/* Import skinning utilities */
require_once("library/core/util-skin.php");
// Skin baseline CSS
print '    <link rel="stylesheet" type="text/css" media="screen" href="/skins/'.skin_getName().'/default.css"/>'."\n";
// Skin baseline JS
print '    <script type="text/javascript" src="/skins/'.skin_getName().'/skin.js"></script>'."\n";
// Other skin bits
skin_include("block-html-head.php");
?>
  </head>
  <body class="page-body">
