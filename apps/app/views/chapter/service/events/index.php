<div id="service">
  <div class="heading clearfix icon">
    <h2><?= $this->title; ?></h2>
  </div>

  <?= message::get(); ?>

  <div>
    <table class="sort">
      <thead>
        <tr>
          <th class="{sorter: 'link_sort'}">Event</th>
          <th class="amount">Date</th>
          <th class="hours right {sorter: 'digit'}">Hours</th>
          <th class="dollars right">Dollars</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($this->events as $event): ?>
        <tr class="hoverable">
          <td class="title"><?= html::anchor('service/events/'. $event->id, $event->title) ?></td>
          <td><?= date::display($event->date, 'M d, Y') ?></td>
          <td class="right"><?= number_format($event->hours, 1) ?></td>
          <td class="right"><?= money::display($event->dollars); ?></td>
        </tr>
      <?php endforeach ?>
      </tbody>
      <tfoot>
        <tr>
          <td class="right" colspan="2">Totals:</td>
          <td class="right"><?= format::plural(number_format($this->events->sum('hours'), 1), '@count hour', '@count hours') ?></td>
          <td class="right"><?= money::display($this->events->sum('dollars')) ?></td>
        </tr>
      </tfoot>
    </table>
  </div>
</div>