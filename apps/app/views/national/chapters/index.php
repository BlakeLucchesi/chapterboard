<div class="heading clearfix">
  <h2><?= $this->title ?></h2>
  <div class="right">
    <span class="excel-link">Export: 
      <?= html::anchor('chapters/export', 'Excel', array('title' => "Export membership report")) ?></span>
  </div>
</div>

<table class="sort">
  <thead>
    <tr>
      <th>Chapter</th>
      <th>University</th>
      <th class="right">Actives</th>
      <th class="right">New Members</th>
      <th class="right">Alumni</th>
    </tr>
  </thead>
  <?php foreach ($this->chapters as $chapter): ?>
    <tr>
      <td><?= html::anchor('chapters/'. $chapter->id, $chapter->chapter_name) ?></td>
      <td><?= $chapter->school->name; ?></td>
      <td class="right"><?= $chapter->actives ?></td>
      <td class="right"><?= $chapter->pledges ?></td>
      <td class="right"><?= $chapter->alumni ?></td>
    </tr>
    <?php $total_actives += $chapter->actives; $total_pledges += $chapter->pledges; $total_alumni += $chapter->alumni; ?>
  <?php endforeach ?>
  <tfoot>
    <tr>
      <td class="right" colspan="2">Totals:</td>
      <td class="right"><?= number_format($total_actives) ?></td>
      <td class="right"><?= number_format($total_pledges) ?></td>
      <td class="right"><?= number_format($total_alumni) ?></td>
    </tr>
  </tfoot>
</table>