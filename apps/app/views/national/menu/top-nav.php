<div class="fixed-wrapper">
	<div class="left clearfix">
    <span class="header-nav">
      <?= html::primary_anchor('profile', $this->user->name()); ?>
      <!-- <?= html::primary_anchor('users', 'Settings') ?> -->
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