<?= $this->user->help('dues') ?>

<div id="finances">
  <div class="heading clearfix">
    <h2><?= $this->title ?></h2>
    <ul>
      <?php if ($this->site->collections_enabled() && $this->member->id == $this->user->id): ?>
        <li><?= html::anchor('finances/payment', 'Pay Online') ?></li>
      <?php endif ?>
      <?php if (A2::instance()->allowed('finance', 'manage')): ?>
        <li><?= html::anchor('finances/members/payment/'. $this->member->id, 'Record Payment') ?></li>
      <?php endif ?>
    </ul>
    <!-- <div class="right"><?= html::anchor('finances/invite', 'Invite a family member to Pay Your Dues') ?></div> -->
  </div>
  
  <?= message::get(); ?>

  <div id="outstanding-charges">
    <?php if ($this->unpaid_charges->count()): ?>
      <?php foreach ($this->unpaid_charges as $charge): ?>
        <table class="payment-history <?= $charge->paid ? 'paid' : 'unpaid' ?>">
          <tbody>
            <tr class="charge">
              <td class="title admin-hover">
                <span class="admin-links">
                  <?php if (A2::instance()->allowed('finance', 'manage')): ?>
                    <?= html::anchor('finances/charges/delete/member/'.$charge->id .'?redirect=finances/members/'. $this->member->id, 'Delete', array('class' => 'delete alert', 'title' => 'Are you sure you wish to remove the charge for this member?')) ?>
                    <?= html::anchor('finances/charges/edit/member/'.$charge->id .'?redirect=finances/members/'. $this->member->id, 'Edit', array('class' => 'edit', 'title' => "Edit the amount this member will be charged.")); ?>
                  <?php endif; ?>
                </span>
                <?php if (A2::instance()->allowed('finance', 'manage')): ?>
                  <b><?= html::anchor('finances/charges/'. $charge->finance_charge_id, $charge->title) ?></b>
                <?php else: ?>
                  <b><?= $charge->title ?></b>
                <?php endif ?>
              </td>
              <td>
                Due on: <?= date::display($charge->due, 'M d, Y', FALSE) ?>
                <?php if ($charge->finance_charge->late_fee): ?>
                  <span class="late-fee">(<?= $charge->finance_charge->late_fee_text(); ?> fee if paid late)</span>
                <?php endif ?>
              </td>
              <td class="amount right"><?= money::display($charge->amount) ?></td>
            </tr>
            <?php foreach ($charge->payments as $payment): ?>
              <tr class="payment hoverable">
                <td class="title admin-hover">
                  <?php if (A2::instance()->allowed('finance', 'manage') && $payment->is_editable()): ?>
                    <span class="admin-links">
                      <?= html::anchor('finances/payments/delete/'. $payment->id, 'Delete', array('class' => 'delete alert', 'title' => 'Are you sure you want to delete the record of this payment?')) ?>
                      <?= html::anchor('finances/payments/edit/'. $payment->id, 'Edit', array('class' => 'edit')) ?>
                    </span>
                  <?php endif ?>
                  <?= $payment->note() ?>
                </td>
                <td>Paid on: <?= date::display($payment->received, 'M d, Y', FALSE) ?></td>
                <td class="amount right green"><?= money::display($payment->amount) ?></td>
              </tr>
            <?php endforeach ?>
        
            <?php if ( ! $charge->paid): ?>
              <tfoot>
                <tr class="due">
                  <td colspan="2" class="right">Amount Due:</td>
                  <td class="right red"><?= money::display($charge->amount - $charge->payments->sum('amount')) ?></td>
                </tr>
              </tfoot>
            <?php endif ?>
          </tbody>
        </table>
        <?php $total += $charge->amount - $charge->payments->sum('amount'); ?>
      <?php endforeach ?>
      <table class="payment-history unpaid">
        <tr>
          <td class="right"><strong>Total Amount Due:</strong></td>
          <td class="right red amount"><strong><?= money::display($total); ?></strong></td>
        </tr>
      </table>
    <?php else: ?>
      <table>
        <tbody>
          <tr>
            <td>No outstanding charges.</td>
          </tr>
        </tbody>
      </table>
    <?php endif ?>
  </div>
  
  <div class="heading clearfix">
    <h2 class="title">Payment History</h2>
  </div>
  
  <?php if ($this->payments->count()): ?>
    <table class="payment-history <?= $charge->paid ? 'paid' : 'unpaid' ?>">
      <tbody>
        <thead>
          <tr>
            <th>Payment for charge:</th>
            <th>Note</th>
            <th class="right date">Payment Date</th>
            <th class="right amount">Amount</th>
          </tr>
        </thead>
        <?php foreach ($this->payments as $payment): ?>
          <tr class="payment hoverable">
            <td class="admin-hover">
              <?php if (A2::instance()->allowed('finance', 'manage') && $payment->is_editable()): ?>
                <span class="admin-links">
                  <?= html::anchor('finances/payments/delete/'. $payment->id, 'Delete', array('class' => 'delete alert', 'title' => 'Are you sure you want to delete the record of this payment?')) ?>
                  <?= html::anchor('finances/payments/edit/'. $payment->id, 'Edit', array('class' => 'edit')) ?>
                </span>
              <?php endif ?>
              <?= $payment->finance_charge->title; ?>
            </td>
            <td><?= $payment->note(); ?></td>
            <td class="right"><?= date::display($payment->received, 'M d, Y', FALSE) ?></td>
            <td class="amount right green"><?= money::display($payment->amount) ?></td>
          </tr>
        <?php endforeach ?>
      </tbody>
    </table>
  <?php else: ?>
    <table>
      <tbody>
        <tr>
          <td>You have not made any payments.</td>
        </tr>
      </tbody>
    </table>
  <?php endif ?>
</div>