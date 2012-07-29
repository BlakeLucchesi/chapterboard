<?= $this->user->help('service') ?>
<div id="service">
  <div class="heading clearfix icon">
    <h2><?= $this->title; ?></h2>
    <ul>
      <li><?= html::anchor('service/record', 'Record Service', array('id' => 'record-service-button')) ?></li>
    </ul>
  </div>

  <?= message::get(); ?>

  <div class="block">
    <table class="sort">
      <thead>
        <tr>
          <th class="{sorter: 'admin_link_sort'}">Event</th>
          <th class="amount">Event Date</th>
          <th class="amount hours right {sorter: 'digit'}">Hours</th>
          <th class="amount dollars right">Dollars</th>
        </tr>
      </thead>
      <tbody>
      <?php if ($this->hours->count()): ?>
        <?php foreach ($this->hours as $hours): ?>
          <tr class="hoverable admin-hover">
            <td class="title">
              <span class="admin-links">
                <?php if (A2::instance()->allowed($hours, 'delete')): ?>
                  <?= html::anchor('service/delete/'. $hours->id, 'Delete', array('class' => 'delete alert', 'title' => 'Are you sure you wish to delete this record?')) ?>
                <?php endif; ?>

                <?php if (A2::instance()->allowed($hours, 'edit')): ?>
                  <?= html::anchor('service/edit/'. $hours->id, 'Edit', array('class' => 'edit', 'title' => "Edit the details of this service record.")); ?>
                <?php endif ?>              
              </span>
              <?= html::anchor('service/events/'. $hours->event->id, $hours->event->title, array('class' => 'title-link')) ?>
              <?php if ($hours->notes): ?>
                <div style="padding: 4px 0 0 4px;"><?= format::html('<strong>Notes:</strong> '. $hours->notes) ?></div>
              <?php endif ?>
            </td>
            <td><?= date::display($hours->event->date, 'M d, Y') ?></td>
            <td class="right"><?= number_format($hours->hours, 1) ?></td>
            <td class="right"><?= money::display($hours->dollars); ?></td>
          </tr>
        <?php endforeach ?>
      <?php else: ?>
        <tr>
          <td class="title" colspan="4">You have not recorded any hours or donations for this school year.</td>
        </tr>
      <?php endif ?>
      </tbody>
      <tfoot>
        <tr>
          <td colspan="2" class="right">Totals:</td>
          <td class="right"><?= format::plural(number_format($this->hours->sum('hours'), 1), '@count hour', '@count hours') ?></td>
          <td class="right"><?= money::display($this->hours->sum('dollars')) ?></td>
        </tr>
      </tfoot>
    </table>
  </div>
</div>