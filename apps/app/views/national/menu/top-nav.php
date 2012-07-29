<div class="fixed-wrapper">
	<div class="left clearfix">
    <?= html::anchor('', html::image('images/logo-small.png', 'ChapterBoard'), array('id' => 'header-logo')) ?>
    <span class="header-nav">
      <?= html::primary_anchor('profile', $this->user->name()); ?>
      <!-- <?= html::primary_anchor('users', 'Settings') ?> -->
      <?php if ($this->user->has_role('root')): ?>
        <?= html::anchor(Kohana::config('app.admin_url'), 'Administrative Site') ?>
      <?php endif ?>
      &nbsp;
    </span>
  </div>
  <div class="right clearfix header-nav">
    <?= html::anchor('support?redirect='. Router::$current_uri, 'Support') ?>
    <?= html::anchor('feedback?redirect='. Router::$current_uri, 'Feedback') ?>
    <!-- <?= html::thickbox_anchor('help', 'Support') ?> -->
    <?= html::anchor('logout', 'Logout'); ?>		
  </div>
</div>