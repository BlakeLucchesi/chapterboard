<div id="transaction-edit" class="clearfix block">
  <h3 class="title">Edit Transaction Details</h3>
    <div class="split-left">
      <?= form::open() ?>
      <fieldset>
        <?= html::anchor('budgets/transactions/delete/'. $this->transaction->id, 'Delete Transaction', array('class' => 'delete')) ?>
        <div class="clearfix">
          <?= form::label('description', 'Description:')?>
          <?= form::input('description', $this->form['description'], 'class="medium"') ?>
          <span class="error"><?= $this->errors['description'] ?></span>
        </div>
        <div class="clearfix">
          <?= form::label('amount', 'Amount:')?>
          $ <?= form::input('amount', $this->form['amount'], 'class="amount"') ?>
          <span class="error"><?= $this->errors['amount'] ?></span>
        </div>
        
        <div class="clearfix">
          <?= form::label('check_no', 'Check #:')?>
          <?= form::input('check_no', $this->form['check_no'], 'class="mini"') ?>
          <span class="error"><?= $this->errors['check_no'] ?></span>
        </div>
        
        <div class="clearfix">
          <?= form::label('date', 'Date:')?>
          <?= form::input('date', date::display($this->form['date'], 'm/d/Y', FALSE), 'class="date-pick"') ?>
          <span class="error"><?= $this->errors['date'] ?></span>
        </div>
        <div class="clearfix">
          <?= form::label('budget_id', 'Budget:')?>
          <?= form::dropdown('budget_id', $this->budgets, $this->form['budget_id']) ?>
          <span class="error"><?= $this->errors['budget_id'] ?></span>
        </div>
        <div class="clearfix">
          <?= form::label('budget_category_id', 'Category:')?>
          <?= form::dropdown('budget_category_id', $this->categories, $this->form['budget_category_id']) ?>
          <span class="error"><?= $this->errors['budget_category_id'] ?></span>
        </div>
        <div class="right">
          <?= form::submit('save', 'Update Transaction') ?> or <?= html::anchor('budgets/transactions', 'cancel') ?>
        </div>
      </fieldset>
      <?= form::close() ?>
    </div>
</div>