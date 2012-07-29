<div id="breadcrumbs">
  <?= html::anchor('budgets', 'Budgets') ?> &raquo; <?= html::anchor('budgets/'. $this->budget->id, $this->budget->name) ?> &raquo; Itemized Report
</div>

<div class="heading clearfix">
  <h2><?= $this->title ?></h2>
</div>

<table>
  <thead>
    <tr>
      <th>Description</th>
      <th class="right amount">Check No.</th>
      <th class="right amount">Date</th>
      <th class="right amount">Amount</th>
    </tr>
  </thead>
  <tbody>
    <?php if ($this->transactions->count()): ?>
      <?php foreach ($this->transactions as $transaction): ?>
        <tr class="hoverable">
          <td><?= $transaction->description ?></td>
          <td class="right"><?= $transaction->check_no ?></td>
          <td class="right"><?= date::display($transaction->date, 'M d, Y', FALSE) ?></td>
          <td class="right"><?= money::display($transaction->amount) ?></td>
        </tr>
      <?php endforeach ?>
    <?php else: ?>
      <tr>
        <td colspan="5">You have not recorded any transactions yet.</td>
      </tr>
    <?php endif ?>
  </tbody>
</table>