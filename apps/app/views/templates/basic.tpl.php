<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <title><?php print html::specialchars($this->title); ?> | ChapterBoard</title>
    <link rel="shortcut icon" href="/favicon.ico"> 
    <?= css::get() ?>
    <link rel="stylesheet" href="/styles/print.css" type="text/css" media="all" title="printer friendly" charset="utf-8">
  </head>
  
  <body class="section-<?php echo Router::$controller ?>">
    <?= $content ?>
    <?= javascript::get() ?>
  </body>
</html>

