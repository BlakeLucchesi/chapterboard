<div id="finances">
  <div class="heading clearfix">
    <h2><?= $this->title ?></h2>
    <ul>
      <li><?= html::anchor('finances/charges/edit/charge/'. $this->charge->id, 'Edit Charge') ?></li>
      <?php if ($this->charge->finance_payments->count() == 0): ?>
        <li><?= html::anchor('finances/charges/delete/charge/'. $this->charge->id, 'Delete Charge', array('class' => 'alert', 'title' => 'Are you sure you want to delete this charge?')) ?></li>
      <?php endif ?>
      
      <li><?= html::anchor('finances/charges/reminder/'. $this->charge->id, 'Send Reminder Email') ?></li>
    </ul>
  </div>

  <?= message::get() ?>
  
  <p class="right">
  <?php if ($this->charge->late_fee): ?>
    <strong>Automatic Late Fee:</strong> <?= $this->charge->late_fee_text() ?>
  <?php endif ?>
  <?php if ($this->charge->deposit_account_id): ?>
    <strong>Deposited into:</strong> <?= $this->charge->deposit_account->name() ?>
  <?php endif ?>
  </p>
  <table class="sort">
    <thead>
      <tr>
        <th>Member</th>
        <th class="right amount">Amount</th>
        <th class="right amount">Collected</th>
        <th class="right amount">Outstanding</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($this->details['members'] as $member): ?>
        <tr class="hoverable admin-hover">
          <td class="title">
            <span class="admin-links">
              <?php if ( ! $member->paid && A2::instance()->allowed('finance', 'manage')): ?>
                <?= html::anchor('finances/charges/delete/member/'.$member->finance_charge_id, 'Delete', array('class' => 'delete alert', 'title' => 'Are you sure you wish to remove the charge for this member?')) ?>
                <?= html::anchor('finances/charges/edit/member/'.$member->finance_charge_id, 'Edit', array('class' => 'edit', 'title' => "Edit the amount this member will be charged.")); ?>
                <?= html::anchor('finances/members/payment/'. $member->user_id, 'Record Payment', array('class' => 'payment', 'title' => 'Record payment for '. $member->name .'.')) ?>
              <?php endif; ?>
            </span>
            <?= html::anchor('finances/members/'. $member->user_id, $member->name) ?>
          </td>
          <td class="right"><?= money::display($member->total) ?></td>
          <td class="right"><?= money::display($member->collected) ?></td>
          <td class="right <?= $member->outstanding > 0 ? 'red' : '' ?>"><?= money::display($member->outstanding) ?></td>
        </tr>
      <?php endforeach ?>
      <tfoot>
        <tr>
          <td class="right">Totals:</td>
          <td class="right"><?= money::display($this->details['totals']['total']) ?></td>
          <td class="right"><?= money::display($this->details['totals']['collected']) ?></td>
          <td class="right"><?= money::display($this->details['totals']['outstanding']) ?></td>
        </tr>
      </tfoot>
    </tbody>
  </table>
  
</div>