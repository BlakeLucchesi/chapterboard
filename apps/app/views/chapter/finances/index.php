<div id="finances">
  <div class="heading clearfix">
    <h2><?= $this->title ?></h2>
    <ul>
      <li><?= html::anchor('finances/reminder', 'Send Reminder Email') ?></li>
    </ul>
    <div class="no-print right">
      <span class="print-link"><a href="javascript:window.print();">Printer Friendly</a></span>
      <span class="excel-link"><?= html::anchor('finances/members/export', 'Export') ?></span>
    </div>
  </div>
  
  <?= message::get() ?>
  
  <table class="sort">
    <thead>
      <tr>
        <th class="{sorter: 'admin_link_sort'}">Member</th>
        <th class="phone {sorter: false}">Phone</th>
        <th class="">Type</th>
        <th class="right amount">Account Balance</th>
        <th class="right amount">Amount Past Due</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($this->members as $id => $member): ?>
        <?php
          // Running Totals.
          $balance += $member->balance;
          $overdue_amount += $member->overdue_amount;
        ?>
        <tr class="hoverable admin-hover">
          <td class="title">
            <span class="admin-links no-print">
              <?= html::anchor('finances/members/payment/'. $id, 'Record Payment', array('class' => 'payment', 'title' => 'Record payment')) ?>
            </span>
            <?= html::anchor('finances/members/'. $id, $member->name, array('class' => 'title-link')) ?>
          </td>
          <td><?= $member->phone ?></td>
          <td><?= $member->type ?></td>
          <td class="right amount"><?= money::display($member->balance) ?></td>
          <td class="right amount <?= $member->overdue_amount > 0 ? 'red' : '' ?>"><?= money::display($member->overdue_amount) ?></td>
        </tr>
      <?php endforeach ?>
    </tbody>
    <tfoot>
      <tr>
        <td class="right" colspan="3">Totals:</td>
        <td class="right amount"><?= money::display($balance) ?></td>
        <td class="right amount"><?= money::display($overdue_amount) ?></td>
      </tr>
    </tfoot>
  </table>
</div>