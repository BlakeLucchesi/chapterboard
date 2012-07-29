<ul>
  <li><?= html::secondary_anchor('members', 'Chapter Roster'); ?></li>
  <?php if (A2::instance()->allowed('user', 'manage')): ?>
    <li><?= html::secondary_anchor('members/invite', 'Invite Members') ?></li>
    <li><?= html::secondary_anchor('members/admin', 'Manage Members'); ?></li>
    <li><?= html::secondary_anchor('members/groups', 'Manage Groups'); ?></li>
  <?php endif ?>
  <?php if (A2::instance()->allowed($this->site, 'admin')): ?>
    <li><?= html::secondary_anchor('members/permissions', 'Manage Permissions') ?></li>
  <?php endif ?>
</ul>
