<ul>
  <li><?= html::secondary_anchor('finances', 'Member Balances'); ?></li>
  <li><?= html::secondary_anchor('finances/charges', 'Charges'); ?></li>
  <li><?= html::secondary_anchor('finances/payments', 'Payments') ?></li>
  <li><?= html::secondary_anchor('finances/fundraising', 'Fundraising') ?></li>
  <?php if ($this->site->bank_on_file()): ?>
    <li><?= html::secondary_anchor('finances/deposits', 'Deposits'); ?></li>
    <!-- <li><?= html::secondary_anchor('finances/collections', 'Debt Collection') ?></li> -->
  <?php endif ?>
  <li><?= html::secondary_anchor('finances/banking', 'Bank Accounts') ?></li>
</ul>