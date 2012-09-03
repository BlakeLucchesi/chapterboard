<div class="fixed-wrapper">
	<div class="left clearfix">
    <span class="header-nav">
      <?= html::primary_anchor('profile', $this->user->name()); ?>
      <?= html::primary_anchor('messages', $this->inbox_indicator) ?>
      <?php if ($this->site->is_national()): ?>
        <?= html::primary_anchor('users', 'Settings') ?>
      <?php elseif (A2::instance()->allowed($this->site, 'admin')): ?>
        <?= html::primary_anchor('settings', 'Chapter Account') ?>
      <?php endif; ?>
      &nbsp;
    </span>
  </div>
  <div class="right clearfix header-nav">
    <?= help::link() ?>
    <?= html::anchor('support?redirect='. Router::$current_uri, 'Support') ?>
    <?= html::anchor('feedback?redirect='. Router::$current_uri, 'Feedback') ?>
    <?= html::anchor('logout', 'Logout'); ?>
  </div>
</div>