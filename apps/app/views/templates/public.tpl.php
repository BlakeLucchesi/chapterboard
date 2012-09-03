<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <title><?php print html::specialchars($this->title); ?> | ChapterBoard</title>
    <link rel="shortcut icon" href="/favicon.ico"> 
    <?= css::get(); ?>
    <?= ga::render(); // Google Analytics. ?>
  </head>
  <body class="section-<?= Router::$controller ?>">
  	<div id="header" class="clearfix">
      <div class="fixed-wrapper">
          <?= html::anchor('', html::image('images/logo-small.png', 'ChapterBoard'), array('id' => 'header-logo')) ?>
          <?php if ($this->site->loaded): ?>
            <div class="site-name"><?= $this->site->name() ?></div>
          <?php endif ?>
          <!-- <span class="header-nav">
            <?= html::primary_anchor('profile', $this->user->name()); ?>
            <?= html::primary_anchor('messages', $this->inbox_indicator) ?>
            <?php if ($this->site->is_national()): ?>
              <?= html::primary_anchor('users', 'Settings') ?>
            <?php elseif (A2::instance()->allowed($this->site, 'admin')): ?>
              <?= html::primary_anchor('settings', 'Chapter Account') ?>
            <?php endif; ?>
            &nbsp;
          </span> -->
        </div>
        <!-- <div class="right clearfix header-nav">
          <?= html::anchor('support?redirect='. Router::$current_uri, 'Support') ?>
          <?= html::anchor('feedback?redirect='. Router::$current_uri, 'Feedback') ?>
          <?= html::anchor('logout', 'Logout'); ?>    
        </div> -->
  	</div>

  	<div id="content" class="clearfix">    	

  		<div id="left" class="fixed-wrapper"><div id="left-inner">
        
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

  </body>
</html>