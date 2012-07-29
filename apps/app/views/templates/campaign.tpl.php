<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <title><?php print html::specialchars($this->title); ?> | ChapterBoard</title>
    <link rel="shortcut icon" href="/favicon.ico"> 
    <?= css::get(); ?>
    <meta property="og:title" content="<?= $this->title ?>" />
    <meta property="og:type" content="university" />
    <?php if (isset($this->donation_form)): ?>
      <meta property="og:url" content="<?= $this->donation_form->url() ?>" />      
      <?php if ($this->donation_form->picture): ?>
        <meta property="og:image" content="<?= $this->donation_form->picture_url() ?>" />
      <?php endif ?>
    <?php endif ?>
    <meta property="og:site_name" content="ChapterBoard" />
    <?= ga::render(); // Google Analytics. ?>  
  </head>
  <body id="donate-page" class="section-<?= Router::$controller ?>">
    <div id="header" class="clearfix">
      <div class="fixed-wrapper">
        <div id="header-logo"><?= html::anchor(Kohana::config('app.public_url'), html::image('images/payrally-mini.png', 'PayRally.com')) ?></div>
        <div class="site-name"><?= isset($this->campaign) ? $this->campaign->site->name() : 'ChapterBoard Donations' ?></div>
      </div>
    </div>

    <div id="content" class="clearfix">

      <div id="left" class="fixed-wrapper"><div id="left-inner">
        
        <div id="main"><div id="main-inner" class="clearfix">
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

  </body>
</html>