<div class="heading clearfix">
  <h2><?= $this->title ?></h2>
</div>
<p>Deposited into: <?= $this->deposit->deposit_account->name ?>: ***<?= $this->deposit->deposit_account->last_four() ?></p>
<table class="sort">
  <thead>
    <tr>
      <th>Name</th>
      <th>Type</th>
      <th>Transaction ID</th>
      <th class="right amount">Date</th>
      <th class="right amount">Amount</th>
    </tr>
  </thead>
  <tbody>
    <?php if ($this->transactions->count()): ?>
      <?php foreach ($this->transactions as $t): ?>
        <tr class="hoverable">
          <td class="title"><?= $t->name ?></td>
          <td><?= $t->collection_type() ?></td>
          <td><?= $t->details() ?></td>
          <td class="right"><?= date::display($t->created) ?></td>
          <td class="right"><?= money::display($t->amount) ?></td>
        </tr>
      <?php endforeach ?>
    <?php else: ?>
      <tr>
        <td colspan="4">All payments have been paid out.</td>
      </tr>
    <?php endif; ?>
  </tbody>
  <tfoot>
    <tr>
      <td class="right" colspan="4">Total Collected:</td>
      <td class="right"><?= money::display($this->transactions->sum('amount')) ?></td>
    </tr>
    <tr>
      <td class="right" colspan="4">Collection Fee:</td>
      <td class="right">- <?= money::display($this->transactions->sum('amount') - $this->transactions->sum('amount_payable')) ?></td>
    </tr>
    <tr>
      <td class="right" colspan="4">Total Deposit:</td>
      <td class="right"><?= money::display(money::display($this->transactions->sum('amount_payable'))) ?></td>
    </tr>
  </tfoot>
</table>