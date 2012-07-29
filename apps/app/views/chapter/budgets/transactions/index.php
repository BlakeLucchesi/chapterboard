<div class="clearfix block">
  <h3 class="title">Record Transaction</h3>
    <?php if ( ! count($this->budgets)): ?>
      <div class="help">
        <p>Please <?= html::anchor('budgets/create', 'create a budget') ?> before you begin recording transactions.</p>
      </div>
    <?php elseif ( ! count($this->categories)): ?>
      <div class="help">
        <p>Please <?= html::anchor('budgets/categories', 'add a budget category') ?> before you begin recording transactions.</p>
      </div>
    <?php else: ?>
      <div class="split-left">
        <?= form::open() ?>
        <fieldset>
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
            <?= form::input('date', $this->form['date'], 'class="date-pick"') ?>
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
            <?= form::submit('save', 'Record Transaction') ?>
          </div>
        </fieldset>
        <?= form::close() ?>
      </div>
    <?php endif ?>
</div>


<div class="heading clearfix">
  <h2><?= $this->title ?></h2>
  <span class="right"><em>* Click on the transaction description to edit or delete.</em></span>
</div>
<table class="small">
  <thead>
    <tr>
      <th>Description</th>
      <th>Category</th>
      <th>Budget</th>
      <th class="right">Chk #</th>
      <th class="right amount">Date</th>
      <th class="right amount">Amount</th>
    </tr>
  </thead>
  <tbody>
    <?php if ($this->transactions->count()): ?>
      <?php foreach ($this->transactions as $transaction): ?>
        <tr class="hoverable">
          <td><?= html::anchor('budgets/transactions/edit/'. $transaction->id, $transaction->description) ?></td>
          <td><?= html::anchor('budgets/transactions/category/'. $transaction->budget_category_id, $transaction->budget_category->name) ?></td>
          <td><?= html::anchor('budgets/transactions/budget/'. $transaction->budget_id, $transaction->budget->name) ?></td>
          <td class="right"><?= $transaction->check_no ?></td>
          <td class="right"><?= date::display($transaction->date, 'M d, Y', FALSE) ?></td>
          <td class="right"><?= money::display($transaction->amount) ?></td>
        </tr>
      <?php endforeach ?>
    <?php else: ?>
      <tr>
        <td colspan="5">You have not recorded any transactions yet.</td>
      </tr>
    <?php endif ?>
  </tbody>
</table>

<?= $this->pagination; ?>