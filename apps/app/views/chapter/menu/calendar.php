<?php if (Router::$routed_uri == 'calendar'): ?>
  <div class="right">
    <?= form::open('calendar', array('method' => 'get', 'id' => 'calendar-select-form')) ?>
    Calendar: <?= form::dropdown('calendar_id', ORM::factory('calendar')->select_list(TRUE), $this->session->get('calendar_id')) ?>
    <?= form::hidden('month', $this->month) ?>
    <?= form::hidden('year', $this->year) ?>
    <?= form::submit('calendar-select-submit', 'Show') ?>
    <?= form::close() ?>
  </div>
<?php endif; ?>

<ul>
  <li><?= html::secondary_anchor('calendar', 'View Calendar'); ?></li>
  <li><?= html::secondary_anchor('calendar/signups', 'My Events'); ?></li>
  <?php if (A2::instance()->allowed('event', 'add')): ?>
    <li><?= html::secondary_anchor('calendar/add', 'Create New Event'); ?></li>
  <?php endif ?>
  <li><?= html::secondary_anchor('calendar/notifications', 'Notifications') ?></li>
  <?php if (A2::instance()->allowed('calendar', 'manage')): ?>
    <li><?= html::secondary_anchor('calendar/admin', 'Manage Calendars'); ?></li>    
  <?php endif ?>
</ul>
