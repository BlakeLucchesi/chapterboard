<div id="deposits">
  <div class="heading clearfix">
    <h2><?= $this->title ?></h2>
  </div>
  
  <div class="clearfix">
    <table class="split-left">
      <thead>
        <tr>
          <th>Deposit Date</th>
          <th>Account</th>
          <th class="amount right">Deposit Amount</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($this->pending->count() && $_GET['page'] < 2): ?>
          <?php foreach ($this->pending as $pending): ?>
            <tr>
              <td>Pending</td>
                <td><?= $pending->deposit_account->name ?>: ***<?= $pending->deposit_account->last_four() ?></td>
              <td class="right"><?= money::display($pending->payable) ?></td>
            </tr>
          <?php endforeach ?>
        <?php endif ?>
        <?php if ($this->deposits->count()): ?>
          <?php foreach ($this->deposits as $deposit): ?>
            <tr class="hoverable">
              <td class="title"><?= html::anchor('finances/deposits/'. $deposit->id, date::display($deposit->created, 'F jS, Y', FALSE)) ?></td>
              <td><?= $deposit->deposit_account->name ?>: ***<?= $deposit->deposit_account->last_four() ?></td>
              <td class="right"><?= money::display($deposit->deposit_transactions->sum('amount_payable')) ?></td>
            </tr>
          <?php endforeach ?>
        <?php else: ?>
          <tr>
            <td colspan="2">There are no deposits for your chapter.</td>
          </tr>
        <?php endif ?>
      </tbody>
    </table>
  </div>
</div>

<div class="bottom-pager">
  <?= $this->pagination ?>
</div>