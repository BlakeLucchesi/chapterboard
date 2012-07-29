<div id="budget-delete">
  <div class="heading clearfix">
    <h2 class="title"><?= $this->title ?></h2>
  </div>
  
  <div class="help">So that you don't lose your old transaction data, we ask that you select a new category for us to file all past transactions for <?= $this->category->name ?> under.</div>

  <?= form::open() ?>
  <div class="clearfix checkbox">
    <?= form::label('category_id', 'Reassign to category:')?>
    <?= form::dropdown('category_id', ORM::factory('budget_category')->options($this->category->id), $this->form['category_id']) ?>
    <span class="error"><?= $this->errors['category_id'] ?></span>
  </div>
  
  <?= form::submit('delete', 'Reassign and delete') ?> or <?= html::anchor('budgets', 'cancel') ?>
  <?= form::close() ?>
</div>