<h2 class="title">Text Message Billing History</h2>
<?php if ( ! $this->payments->count()): ?>
  <p>Your chapter has not been billed for text messages.</p>
<?php else: ?>
<table class="show-border" style="width: 400px">
  <tbody>
    <thead>
      <tr>
        <th>Bill Date</th>
        <th class="right">Messages Delivered</th>
        <th class="right">Amount</th>
      </tr>
    </thead>
  <?php foreach ($this->payments as $payment): ?>
    <tr>
      <td class="amount"><?= date::display($payment->created, 'M d, Y') ?></td>
      <td class="right"><?= number_format($payment->send_count) ?></td>
      <td class="amount right"><?= money::display($payment->amount) ?></td>
    </tr>              
  <?php endforeach ?>
  </tbody>
</table>
<?php endif ?>
<p><strong>Billing Note:</strong><br />We automatically debit your bank account on file on the first day of the month for any messages sent during the previous month.</p>