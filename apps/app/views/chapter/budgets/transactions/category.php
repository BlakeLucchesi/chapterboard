<div id="breadcrumbs">
  <?= html::anchor('budgets/transactions', 'Transactions') ?> &raquo; Category: <?= $this->category->name; ?>
</div>

<div class="heading clearfix">
  <h2><?= $this->title ?></h2>
</div>

<div class="clearfix">

  <table>
    <thead>
      <tr>
        <th>Description</th>
        <th>Category</th>
        <th>Budget</th>
        <th class="right amount">Date</th>
        <th class="right amount">Amount</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($this->transactions->count()): ?>
        <?php foreach ($this->transactions as $transaction): ?>
          <tr>
            <td><?= $transaction->description ?></td>
            <td><?= $transaction->budget->name ?></td>
            <td><?= $transaction->budget_category->name ?></td>
            <td class="right"><?= date::display($transaction->date, 'M d, Y', FALSE) ?></td>
            <td class="right"><?= money::display($transaction->amount) ?></td>
          </tr>
        <?php endforeach ?>
      <?php else: ?>
        <tr>
          <td colspan="5">No transactions have been logged for this budget.</td>
        </tr>
      <?php endif ?>
    </tbody>
  </table>


</div>