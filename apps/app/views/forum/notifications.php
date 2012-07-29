<div id="forum-notifications">
  <div class="heading clearfix">
    <h2><?= $this->title ?></h2>
  </div>

  <?= message::get(); ?>

  <div class="s">
    <p>Using the form below you can customize which forum boards you would like to receive email notifications for.
      You can update your settings at any time to reduce or increase the number of notifications you receive via email.</p>
  </div>

  <div class="content">
      <?= form::open(); ?>
      <h3>Forum Boards</h3>
      <table>
      <?php if (count($this->forums)): ?>
        <?php foreach ($this->forums as $forum): ?>
          <tr class="hoverable">
            <td><b><?= $forum->title ?></b></td>
            <td><label><?= form::radio('forum['. $forum->id .']', 1, $this->notifications[$forum->id]->value == 1 ? TRUE : FALSE) ?> All posts and comments</label></td>
            <td><label><?= form::radio('forum['. $forum->id .']', 2, $this->notifications[$forum->id]->value == 2 ? TRUE : FALSE) ?> Only topics I post or comment on</label></td>
            <td><label><?= form::radio('forum['. $forum->id .']', 0, $this->notifications[$forum->id]->value ? FALSE : TRUE) ?> No emails</label></td>
          </tr>
        <?php endforeach ?>
      <?php else: ?>
        <tr>
          <td>Sorry, there are no forums available to you.</td>
        </tr>
      <?php endif; ?>
      </table>
      <div class="right">
        <?= form::submit('submit', 'Save Notification Settings') ?> or <?= html::anchor('forum', 'cancel') ?>
      </div>
      <?= form::close() ?>    
  </div>
</div>