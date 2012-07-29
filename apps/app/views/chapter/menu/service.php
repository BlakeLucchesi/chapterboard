<?php if ( ! preg_match(':^service/events/[0-9]+|service/record:i', Router::$current_uri)): ?>
  <div class="right">
    <?= form::open(Router::$current_uri, array('method' => 'get', 'id' => 'service-year-form')) ?>
    School Year: <?= form::dropdown('period', $this->periods, $this->period) ?>
    <span id="service-year-custom-range">
      <?= form::input('start_date', $this->start_date, 'class="date-pick" placeholder="Start Date"') ?>
      <?= form::input('end_date', $this->end_date, 'class="date-pick" placeholder="End Date"') ?>
    </span>
    <?= form::submit('submit', 'Go') ?>
    <?= form::close() ?>
  </div>  
<?php endif ?>
<ul>
  <li><?= html::secondary_anchor('service', 'My Service'); ?></li>
  <li><?= html::secondary_anchor('service/members', 'Chapter Report'); ?></li>
  <li><?= html::secondary_anchor('service/events', 'Events Summary'); ?></li>
</ul>

