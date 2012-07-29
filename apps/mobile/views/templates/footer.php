<div class="clearfix">
  <?php if ($this->user): ?>
    <div>
      <?= $this->user->name() ?> |
      <?= html::anchor(Kohana::config('app.app_url'), 'Full Version') ?> | 
      <?= html::anchor('logout', 'Logout') ?>
    </div>
  <?php endif ?>
  <div>
    <?= sprintf('&copy; %d ChapterBoard Inc.', date('Y')); ?> 
  </div>
</div>