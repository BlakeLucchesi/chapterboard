<div id="primary" class="clearfix fixed-wrapper">
  <ul>
    <li><?= html::primary_anchor('', 'Home') ?></li>
    <li><?= html::primary_anchor('forum', 'Forum') ?></li>
    <li><?= html::primary_anchor('calendar', 'Calendar') ?></li>
    <?php if (A2::instance()->allowed('recruit', 'access')): ?>
      <li><?= html::primary_anchor('recruitment', 'Recruitment') ?></li>
    <?php endif ?>
    <li><?= html::primary_anchor('service', 'Service') ?></li>
    <?php if (A2::instance()->allowed('finance', 'manage')): ?>
      <li><?= html::primary_anchor('finances', 'Finances') ?></li>
    <?php endif ?>
    <?php if (A2::instance()->allowed('budget', 'manage')): ?>
      <li><?= html::primary_anchor('budgets', 'Budgets') ?></li>
    <?php endif ?>
    <li><?= html::primary_anchor('files', 'Files &amp; Photos') ?></li>
    <li><?= html::primary_anchor('members', 'Members') ?></li>
  </ul>
</div>