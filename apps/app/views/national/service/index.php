<div class="heading clearfix">
  <h2><?= $this->title ?></h2>
</div>

<table class="sort">
  <thead>
    <tr>
      <th>Chapter</th>
      <th class="right">Hours</th>
      <th class="right">Dollars</th>
    </tr>
  </thead>
  <?php foreach ($this->chapters as $chapter): ?>
    <tr class="hoverable">
      <td><?= html::anchor('service/chapter/'. $chapter->id, $chapter->site->chapter_name()); ?></td>
      <td class="right hours"><?= number_format($chapter->hours, 2) ?></td>
      <td class="right hours"><?= money::display($chapter->dollars) ?></td>
    </tr>
    <?php $total_hours += $chapter->hours; $total_dollars += $chapter->dollars; ?>
  <?php endforeach ?>
  <tfoot>
    <tr>
      <td class="right">Totals</td>
      <td class="right"><?= number_format($total_hours, 2) ?></td>
      <td class="right"><?= money::display($total_dollars) ?></td>
    </tr>
  </tfoot>
</table>