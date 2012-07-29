<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <title><?php print html::specialchars($this->title); ?> | ChapterBoard</title>
    <link rel="shortcut icon" href="/favicon.ico"> 
    <link rel="stylesheet" href="/styles/print.css" type="text/css" media="all" title="printer friendly" charset="utf-8">
  </head>
  
  <body class="section-<?php echo Router::$controller ?>">
  	<div id="header-print" class="clearfix">
      <?= View::factory('templates/header-print') ?>
  	</div>
  	<div id="content" class="clearfix">    	
      <div id="main"><div id="main-inner" class="clearfix">
        <?= $content ?>
      </div></div><!-- main, main-inner -->
    </div><!-- content -->
    <div id="footer-print" class="fixed-wrapper">
      <?php echo View::factory('templates/footer-print') ?>
    </div> <!-- footer -->

  </body>
</html>

