<div id="breadcrumbs">
  <?= html::anchor('budgets', 'Budgets') ?> &raquo; <?= html::anchor('budgets/'. $this->budget->id, $this->budget->name); ?> &raquo; Transactions
</div>

<div class="heading clearfix">
  <h2><?= $this->title ?></h2>
</div>

<div class="clearfix">

  <table class="sort">
    <thead>
      <tr>
        <th>Description</th>
        <th class="{sorter: 'digit'} right">Check No.</th>
        <th class="category">Category</th>
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
            <td><?= $transaction->budget_category->name ?></td>
            <td class="right"><?= date::display($transaction->date, 'm/d/Y', FALSE) ?></td>
            <td class="right"><?= money::display($transaction->amount) ?></td>
          </tr>
        <?php endforeach ?>
      <?php else: ?>
        <tr>
          <td colspan="5">No transactions have been logged for this budget.</td>
        </tr>
      <?php endif ?>
    </tbody>
    <tfoot>
      <tr>
        <td colspan="4" class="right">Total:</td>
        <td class="right"><?= money::display($this->transactions->sum('amount')) ?></td>
      </tr>
    </tfoot>
  </table>


</div>