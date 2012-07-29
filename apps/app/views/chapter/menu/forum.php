<ul>
  <div id="forum-quick-search" class="right">
    <?= form::open('forum/search', array('method' => 'get')) ?>
    <?= form::input('q', $this->input->get('q')) ?>
    <?= form::submit('submit', 'Go') ?>
    <?= form::close(); ?>
  </div>
  <li><?= html::secondary_anchor('forum', 'Forum Boards'); ?></li>
  <li><?= html::secondary_anchor('forum/recent', 'Recent Topics') ?></li>
  <li><?= html::secondary_anchor('forum/unread', 'Unread Topics') ?></li>
  <li><?= html::secondary_anchor('forum/markasread', 'Mark All Read'); ?></li>
  <li><?= html::secondary_anchor('forum/notifications', 'Notifications') ?></li>
  <?php if (A2::instance()->allowed('forum', 'manage')): ?>
    <li><?= html::secondary_anchor('forum/admin', 'Manage Forum Boards'); ?></li>
  <?php endif ?>
</ul>

<?php javascript::add("
  if ($('input[type=text]', '#forum-quick-search').val() == '') {
    $('input[type=text]', '#forum-quick-search').val('Search...').addClass('faded');
  }
  $('input[type=text]', '#forum-quick-search').focus(function() {
    if ($(this).val() == 'Search...') {
      $(this).val('').removeClass('faded');
    }
  }).blur(function() {
    if ($(this).val() == '') {
      $(this).val('Search...').addClass('faded');
    }
  });
", 'inline') ?>