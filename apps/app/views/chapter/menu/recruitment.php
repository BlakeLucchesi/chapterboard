<ul>
	<li><?= html::secondary_anchor('recruitment', 'Recruitment Lists'); ?></li>
  <li><?= html::secondary_anchor('recruitment/add', 'Add Recruit') ?></li>
  <?php if (A2::instance()->allowed('recruit', 'manage')): ?>
    <li><?= html::secondary_anchor('recruitment/announcement', 'Send Announcement') ?></li>
  <?php endif ?>
</ul>