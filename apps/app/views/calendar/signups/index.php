<div id="my-events">
  <div class="clearfix">

    <div class="split-left">
      <div class="clearfix heading">
        <h2><?= $this->title ?></h2>
      </div>

      <?php if (count($this->signups)): ?>
        <?php foreach ($this->signups as $signup): ?>
          <?php if ($signup->event->date() != $prev_date): ?>
            <?php if ( ! is_null($prev_date)): ?></div></div><?php endif; ?>
            <div class="date clearfix">
              <div class="date-info">
                <div class="month"><?= date::display($signup->event->start, 'M') ?></div>
                <div class="day"><?= date::display($signup->event->start, 'd') ?></div>
              </div>
              <div class="events">
          <?php endif; ?>
                <div class="event">
                  <?= $signup->event->all_day ? 'All Day' : date::display($signup->event->start, 'time') ?></span> &ndash; <?= html::anchor('calendar/event/'. $signup->event->id, $signup->event->title) ?>
                </div>
          <?php $prev_date = $signup->event->date() ?>
        <?php endforeach ?>
      </div></div>
      <?php else: ?>  
        <p>You have no upcoming events.</p>
      <?php endif ?>  
    </div>
    
    <div class="split-right calendars">
      <div class="block">
        <?= $this->calendar_1 ?>
      </div>
      <div class="block">
        <?= $this->calendar_2 ?>
      </div>
    </div>
    
  </div>
</div>