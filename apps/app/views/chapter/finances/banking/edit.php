<div id="breadcrumbs">
  <?= html::anchor('finances/banking', 'Bank Accounts') ?> &raquo; Edit Bank Account
</div>

<div class="heading clearfix">
  <h2 class="title"><?= $this->title ?></h2>
</div>

<?= message::get() ?>

<div class="split-left">
  <?= form::open(); ?>
    <fieldset>
      <div class="clearfix">
        <?= form::label('name', 'Account Name:*')?>
        <?= form::input('name', $this->form['name'], 'class="medium"') ?>
        <span class="form-tip" title='This is mainly used for chapters with multiple bank accounts. When you assess dues or create fundraising campaigns, you can select which of your bank accounts the money will be deposited into. Give the account a descriptive name so that you know which account your money will be deposited into. e.g. "Checking Account" or "Housing Corporation".'><?= html::image('minis/information.png'); ?></span>
        <div class="error"><?= $this->errors['name'] ?></div>
      </div>     
      <div class="right"><?= form::submit('save', 'Save') ?> or <?= html::anchor('finances/banking', 'cancel') ?></div>
    </fieldset>
  <?= form::close() ?>
</div>
