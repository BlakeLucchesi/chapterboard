<div id="events" class="block">
  <h2 class="title">
    <div class="right">
      <span class="nav"><?= html::anchor($this->prev, '&laquo; '. strftime('%h', mktime(0, 0, 0, $this->month - 1, 1, $this->year))) ?></span>
      <span class="nav"><?= html::anchor($this->next, strftime('%h', mktime(0, 0, 0, $this->month + 1, 1, $this->year)) .' &raquo;') ?></span>
    </div>
    <?= $this->title ?>
  </h2>
  
  <div class="items">
    <?php if ($this->events): ?>
      <?php foreach ($this->events as $event): ?>    
        <div class="item clearfix">
          <?php if (date::display($event->start, 'ymd') != $last): ?>
            <div class="date-header">
              <?= date::display($event->start, 'M jS (D)') ?>
            </div>
          <?php endif ?>
          <div class="title"><?= html::anchor('events/'. $event->id, $event->title) ?></div>
          <div class="meta date"><span class="time"><?= $event->all_day ? 'All Day' : ''. date::time($event->start) ?></span></div>          
        </div>
        <?php
          $last = date::display($event->start, 'ymd');
        ?>
      <?php endforeach ?>
    <?php else: ?>
      <div class="item">There are no events for this month.</div>
    <?php endif ?>
  </div>
</div>
