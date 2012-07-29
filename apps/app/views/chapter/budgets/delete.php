<div id="budget-delete">
  <div class="heading clearfix">
    <h3 class="title"><?= $this->title ?></h3>
  </div>
  
  <div class="help">So that you don't lose your old budget data, we ask that you select a new budget for us to file all associated transactions and member charges for <?= $this->budget->name ?>.</div>
  
  <?= form::open() ?>
  <div class="clearfix checkbox">
    <?= form::label('budget_id', 'Reassign to budget:')?>
    <?= form::dropdown('budget_id', ORM::factory('budget')->options(FALSE, $this->budget->id), $this->form['budget_id']) ?>
    <span class="error"><?= $this->errors['budget_id'] ?></span>
  </div>
  
  <?= form::submit('delete', 'Reassign and delete') ?> or <?= html::anchor('budgets', 'cancel') ?>
  <?= form::close() ?>
</div>