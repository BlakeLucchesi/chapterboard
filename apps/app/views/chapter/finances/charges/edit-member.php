<div>
  <div class="heading clearfix">
    <h2 class="title"><?= $this->title ?></h2>
  </div>
  
  <?= message::get() ?>
  
  <div class="clearfix">
    <div class="split-left">
      <?= form::open() ?>
      <fieldset>
        <?= form::hidden('redirect', $_GET['redirect'] || $_POST['redirect'] ? TRUE : FALSE); ?>
        <div class="clearfix">
          <label>Amount Paid:</label>
          <div><?= money::display($this->charge_member->payments->sum('amount')) ?></div>
        </div>
        
        <div class="clearfix">
          <?= form::label('amount', 'Charge Amount:')?>
          <?= form::input('amount', number_format($this->form['amount'], 2), 'class="small amount-input"') ?>
          <span class="error"><?= $this->errors['amount'] ?></span>
        </div>

      </fieldset>
      <div class="right">
        <?= form::submit('save', 'Save Changes') ?> or <?= html::anchor('finances/charges/'. $this->charge->id, 'cancel') ?>
      </div>
      <?= form::close() ?>
      
    </div>
    <div class="split-right">
      <div class="help">
        <p><b>NOTE:</b> You cannot change the charge amount to less than the amount the member has already paid.</p>
      </div>
      
    </div>
  </div>
</div>
