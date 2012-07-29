<div class="heading clearfix">
  <?php if (A2::instance()->allowed('announcement', 'manage')): ?>
    <span class="right"><?= html::anchor('announcements/delete/'. $this->announcement->id, 'Remove Announcement', array('class' => 'alert', 'title' => 'Are you sure you want to permanently remove this announcement?')) ?></span>
  <?php endif ?>
  <h3><?= $this->announcement->title ?></h3>
</div>

<div class="block">
  <?= format::html($this->announcement->message) ?>
</div>