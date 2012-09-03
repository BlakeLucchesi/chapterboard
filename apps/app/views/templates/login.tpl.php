<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <title><?= html::specialchars($this->title); ?> | ChapterBoard</title>
    <link rel="shortcut icon" href="/favicon.ico"> 
    <?= css::get(); ?>
  </head>
  <body class="<?= Router::$controller?>-<?= Router::$method ?>">
    <div id="content">
      <div id="wrap" class="clearfix">
        <div id="wrap-inner">
          <?= $content; ?>
        </div>
      </div>
    </div>
    <div id="footer">
      <div class="links">
        <?= html::anchor('http://www.chapterboard.com', 'Powered by ChapterBoard'); ?> 
      </div>
    </div> <!-- footer -->
    <?= javascript::get() ?>
  </body>
</html>
