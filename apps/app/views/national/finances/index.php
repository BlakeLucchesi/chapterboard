<div class="heading clearfix">
  <h2><?= $this->title ?></h2>
</div>

<table class="sort">
  <thead>
    <tr>
      <th>Chapter</th>
      <th class="right">Outstanding</th>
      <th class="right">Past Due</th>
    </tr>
  </thead>
  <?php foreach ($this->chapters as $chapter): ?>
    <tr>
      <td><?= html::anchor('chapters/show/'. $chapter->id, $chapter->chapter_name()) ?></td>
      <td class="right dollars"><?= money::display($chapter->balance) ?></td>
      <td class="right dollars"><?= money::display($chapter->past_due) ?></td>
    </tr>
  <?php endforeach ?>
  <tfoot>
    <tr>
      <td class="right">Totals:</td>
      <td class="right"></td>
      <td class="right"></td>
    </tr>
  </tfoot>
</table>