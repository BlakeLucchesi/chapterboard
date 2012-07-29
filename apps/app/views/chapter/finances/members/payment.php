<div id="finances">
  <div class="heading clearfix">
    <h2><?= $this->title ?></h2>
  </div>
  
  <?= message::get(); ?>

  <div class="clearfix">
      <?= form::open() ?>
      <table id="payment-table" class="block">
        <thead>
          <tr>
            <th>Charge</th>
            <th class="date">Due Date</th>
            <th class="right amount">Amount Due</th>
            <th class="right amount">Amount Received</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($this->charges as $charge): ?>
            <tr class="hoverable">
              <td><?= $charge->finance_charge->title ?></td>
              <td><?= date::display($charge->finance_charge->due, 'M d, Y') ?></td>
              <td class="right"><?= money::display($charge->amount - $charge->payments->sum('amount')) ?></td>
              <td class="right"><?= form::input('amount['.$charge->id.']', $this->form['amount'] ? $this->form['amount'] : '0.00', 'class="amount-input"') ?></td>
            </tr>
          <?php endforeach ?>
        </tbody>
        <tfoot>
          <tr>
            <td class="right" colspan="3">Total:</td>
            <td class="right" id="pay-total">$0.00</td>
          </tr>
        </tfoot>
      </table>
    <div class="split-right">      
      <fieldset>
        <div class="clearfix">
          <?php echo form::label('received', 'Date Received:')?>
          <?php echo form::input('received', date::display($this->form['received'], 'm/d/Y'), 'class="date-pick"') ?>
          <span class="error"><?php echo $this->errors['recieved'] ?></span>
        </div>
        
        <div class="clearfix" id="payment-type-select">
          <?= form::label('type', 'Payment Type:')?>
          <?= form::dropdown('type', array('cash' => 'Cash', 'check' => 'Check'), $this->form['type']) ?>
          <span id="check_no" style="<?= $this->form['check_no'] ? 'display: inline;' : '' ?>">Chk # <?= form::input('check_no', $this->form['check_no'], 'class="mini"') ?></span>
        </div>
        
        
        <div class="clearfix">
          <?php echo form::label('note', 'Note:')?>
          <?php echo form::input('note', $this->form['note']) ?>
          <span class="error"><?php echo $this->errors['note'] ?></span>
        </div>
        
      </fieldset>
      <div class="right">
        <?= form::submit('submit', 'Record Payment') ?> or <?= html::anchor('finances/members', 'Cancel') ?>
      </div>
    </div>
    <?= form::close() ?>
  </div>

  
</div>