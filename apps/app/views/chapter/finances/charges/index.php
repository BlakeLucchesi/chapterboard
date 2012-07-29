<div id="finances">
  <div class="heading clearfix">
    <h2><?= $this->title ?></h2>
    <ul>
      <li><?= html::anchor('finances/charges/add', 'Add New Charge') ?></li>
    </ul>
    <!-- <div class="print-link right"><?= html::anchor(Router::$current_uri .'?format=printable', 'Printer Friendly') ?></div> -->
    <div class="right">
      <?= $this->pagination; ?>
    </div>
  </div>
  
  <?= message::get() ?>
  
  <table>
    <thead>
      <tr>
        <th class="{sorter: false}">Title</th>
        <th class="date right">Due Date</th>
        <th class="right members">Members</th>
        <th class="right amount">Total Value</th>
        <th class="right amount">Collected</th>
        <th class="right amount">Outstanding</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($this->charges as $charge): ?>
        <tr class="hoverable">
          <td class="title"><?= html::anchor('finances/charges/'. $charge->id, ucwords($charge->title)) ?></td>
          <td class="right"><?= date::display($charge->due, 'm/d/Y', FALSE) ?></td>
          <td class="right"><?= number_format($charge->finance_charge_members->count()) ?></td>
          <td class="right"><?= money::display($charge->total) ?></td>
          <td class="right"><?= money::display($charge->collected) ?></td>
          <td class="right <?= $charge->outstanding > 0 ? 'red' : '' ?>"><?= money::display($charge->outstanding) ?></td>
        </tr>
      <?php endforeach ?>
    </tbody>
  </table>
  
  <?= $this->pagination; ?>
</div>