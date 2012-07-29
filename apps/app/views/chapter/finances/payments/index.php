<div id="deposits">
  <div class="heading clearfix">
    <div class="right">
      <?= $this->pagination ?>
    </div>
    <h2><?= $this->title ?></h2>
  </div>
  
  <table>
    <thead>
      <tr>
        <th>Member</th>
        <th>Charge</th>
        <th>Payment Notes</th>
        <th class="right amount">Date</th>
        <th class="right amount">Amount</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($this->payments->count()): ?>
        <?php foreach ($this->payments as $payment): ?>
          <tr class="hoverable">
            <td class="title admin-hover">
              <span class="admin-links">
                <?php if ($payment->is_editable()): ?>
                  <?= html::anchor('finances/payments/delete/'. $payment->id, 'Delete', array('class' => 'delete alert', 'title' => 'Are you sure you want to delete the record of this payment?')) ?>
                  <?= html::anchor('finances/payments/edit/'. $payment->id, 'Edit', array('class' => 'edit')) ?>
                <?php endif ?>
              </span>
              <?= html::anchor('finances/members/'. $payment->user_id, $payment->user->name()) ?>
            </td>
            <td class="title"><?= html::anchor('finances/charges/'. $payment->finance_charge_id, $payment->finance_charge->title) ?></td>
            <td><?= $payment->note(); ?></td>
            <td class="right"><?= date::display($payment->received, 'short', FALSE) ?></td>
            <td class="right"><?= money::display($payment->amount) ?></td>
          </tr>
        <?php endforeach ?>
      <?php else: ?>
        <tr>
          <td colspan="2">Your chapter has not recorded any payments.</td>
        </tr>
      <?php endif ?>
    </tbody>
  </table>
  
  <?= $this->pagination ?>
</div>