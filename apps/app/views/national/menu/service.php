<div class="right">
  <?= form::open(Router::$current_uri, array('method' => 'get', 'id' => 'service-year-form')) ?>
  School Year: <?= form::dropdown('period', $this->periods, $this->period) ?>
  <?= form::submit('submit', 'Go') ?>
  <?= form::close() ?>
</div>
<ul>
  <li><?= html::secondary_anchor('service', 'Service By Chapter'); ?></li>
  <li><?= html::secondary_anchor('service/members', 'Top Contributors'); ?></li>
</ul>