<div id="breadcrumbs">
  <?= html::anchor('finances/banking', 'Bank Accounts') ?> &raquo; Add Bank Account
</div>

<div class="heading clearfix">
  <h2 class="title"><?= $this->title ?></h2>
</div>

<?= message::get() ?>

<div class="split-left">
  <?= form::open(); ?>
    <fieldset>
      <div class="clearfix">
        <?= form::label('bank_name', 'Bank Name:*')?>
        <?= form::input('bank_name', $this->form['bank_name'], 'class="medium"') ?>
        <span class="error"><?= $this->errors['bank_name'] ?></span>
      </div>
      <div class="clearfix">
        <?= form::label('name', 'Account Name:*')?>
        <?= form::input('name', $this->form['name'], 'class="medium"') ?>
        <span class="form-tip" title='This is mainly used for chapters with multiple bank accounts. When you assess dues or create fundraising campaigns, you can select which of your bank accounts the money will be deposited into. Give the account a descriptive name so that you know which account your money will be deposited into. e.g. "Checking Account" or "Housing Corporation".'><?= html::image('minis/information.png'); ?></span>
        <div class="error"><?= $this->errors['name'] ?></div>
      </div>
      <div class="clearfix">
        <?= form::label('routing_number', 'Routing Number:*')?>
        <?= form::input('routing_number', $this->form['routing_number'], 'class="small"') ?>
        <span class="form-tip"><?= html::thickbox_anchor('images/check.gif', html::image('minis/information.png')) ?></span>
        <span class="error"><?= $this->errors['routing_number'] ?></span>
      </div>
      <div class="clearfix">
        <?= form::label('account_number', 'Account Number:*')?>
        <?= form::input('account_number', $this->form['account_number'], 'class="small"') ?>
        <span class="error"><?= $this->errors['account_number'] ?></span>
      </div>          
      <div class="right"><?= form::submit('save', 'Save') ?> or <?= html::anchor('finances/banking', 'cancel') ?></div>
    </fieldset>
  <?= form::close() ?>
</div>