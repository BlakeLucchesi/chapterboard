<div class="fixed-wrapper">
	<div class="left clearfix">
    <?= html::anchor('', html::image('images/logo-small.png', 'ChapterBoard'), array('id' => 'header-logo')) ?>
    <span class="header-nav">
      <?= html::primary_anchor('profile', $this->user->name()); ?>
      <?= html::primary_anchor('messages', $this->inbox_indicator) ?>
      <?php if ($this->site->is_national()): ?>
        <?= html::primary_anchor('users', 'Settings') ?>
      <?php elseif (A2::instance()->allowed($this->site, 'admin')): ?>
        <?= html::primary_anchor('settings', 'Chapter Account') ?>
      <?php endif; ?>
      <?php if ($this->user->has_role('root')): ?>
        <?= html::anchor(Kohana::config('app.admin_url'), 'Administrative Site') ?>
      <?php endif ?>
      &nbsp;
    </span>
  </div>
  <div class="right clearfix header-nav">
    <a id="live-chat-link" href="#" class="hidden">Live Chat</a>
    <?= help::link() ?>
    <?= html::anchor('support?redirect='. Router::$current_uri, 'Support') ?>
    <?= html::anchor('feedback?redirect='. Router::$current_uri, 'Feedback') ?>
    <?= html::anchor('logout', 'Logout'); ?>		
  </div>
</div>