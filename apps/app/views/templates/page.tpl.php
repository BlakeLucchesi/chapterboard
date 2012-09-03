<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <title><?php print html::specialchars($this->title); ?></title>
    <link rel="shortcut icon" href="/favicon.ico">
    <link rel="apple-touch-icon-precomposed" href="/images/iphone-icon.png" />
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="/images/ipad-icon.png" />
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="/images/iphone4-icon.png" />
    <?= css::get(); ?>
    <?= ga::render(); // Google Analytics. ?>
  </head>
  <body class="section-<?= Router::$controller ?>">

    <div id="header" class="clearfix">
      <?= View::factory('menu/top-nav'); ?>
    </div>

    <div id="content" class="clearfix">

      <?= $primary; ?>
      <div id="left" class="fixed-wrapper"><div id="left-inner">
        
        <div id="secondary"><div id="secondary-inner" class="clearfix">
          <?= $secondary; ?>
        </div></div><!-- secondary, secondary-inner -->
        
        <div id="main"><div id="main-inner" class="clearfix">
          <?= message::get() ?>
          
          <?= $content ?>
        </div></div><!-- main, main-inner -->

      </div></div><!-- left, left-inner -->

    </div><!-- content -->
    <div id="footer" class="fixed-wrapper">
      <?= View::factory('templates/footer') ?>
    </div> <!-- footer -->
    <div id="footer-print" class="no-screen">
      <?= View::factory('templates/footer-print') ?>
    </div>
    <?= javascript::get() ?>
    
    <script>
      if (navigator.userAgent.match(/(iPhone|iPod|iPad)/i)) {
        document.title = 'ChapterBoard';
      }
    </script>
  </body>
</html>