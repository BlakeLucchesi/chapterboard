<div id="service">
  <div class="heading clearfix icon">
    <h2><?= $this->title; ?></h2>
    <ul>
      <li><?= html::anchor('service/record/'. $this->event->id, 'Record Service') ?></li>
      <?php if (A2::instance()->allowed($this->event, 'edit')): ?>
        <li><?= html::anchor('service/events/edit/'. $this->event->id, 'Edit Event') ?></li>
      <?php endif ?>
      <?php if (A2::instance()->allowed($this->event, 'delete')): ?>
        <li><?= html::anchor('service/events/delete/'. $this->event->id, 'Delete Event') ?></li>
      <?php endif ?>
    </ul>
  </div>

  <?= message::get() ?>
  
  <div>
    <table class="sort">
      <thead>
        <tr>
          <th class="{sorter: 'admin_link_sort'}">Member</th>
          <th class="amount">Date Recorded</th>
          <th class="right hours {sorter: 'digit'}">Hours</th>
          <th class="right dollars">Dollars</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($this->event->service_hours as $hours): ?>
        <tr class="hoverable admin-hover <?= text::alternate('odd', 'even') ?>">
          <td class="title">
            <span class="admin-links">
              <?php if (A2::instance()->allowed($hours, 'delete')): ?>
                <?= html::anchor('service/delete/'. $hours->id, 'Delete', array('class' => 'delete alert', 'title' => 'Are you sure you wish to delete this record?')) ?>
              <?php endif; ?>
              
              <?php if (A2::instance()->allowed($hours, 'edit')): ?>
                <?= html::anchor('service/edit/'. $hours->id, 'Edit', array('class' => 'edit', 'title' => "Edit the details of this service record.")); ?>
              <?php endif ?>              
            </span>
            <?= html::anchor('service/members/'. $hours->user->id, $hours->user->name(), array('class' => 'title-link')) ?>
            <?php if ($hours->notes): ?>
              <div style="padding: 4px 0 0 4px;"><?= format::html('<strong>Notes:</strong> '. $hours->notes) ?></div>
            <?php endif ?>
          </td>
          <td><?= date::display($hours->created, 'M d, Y') ?></td>
          <td class="right"><?= number_format($hours->hours, 1) ?></td>
          <td class="right"><?= money::display($hours->dollars); ?></td>
        </tr>
      <?php endforeach ?>
      </tbody>
      <tfoot>
        <tr>
          <td colspan="2" class="right">Totals:</td>
          <td class="right"><?= format::plural(number_format($this->event->service_hours->sum('hours'), 1), '@count hour', '@count hours') ?></td>
          <td class="right"><?= money::display($this->event->service_hours->sum('dollars')) ?></td>
        </tr>
      </tfoot>
    
    </table>
  </div>
</div>