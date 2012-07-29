<div id="budget-categories">
  <div class="heading clearfix">
    <h2><?= $this->title ?></h2>
  </div>


  <div class="clearfix">
    <div class="split-left">
      <div class="block">
        <?= form::open() ?>
        <fieldset>

          <div class="clearfix">
            <?= form::label('name', 'Category Name:')?>
            <?= form::input('name', $this->form['name'], 'class="medium"') ?>
            <span class="error"><?= $this->errors['name'] ?></span>
          </div>
          <div class="clearfix">
            <?= form::label('type', 'Type:')?>
            <?= form::dropdown('type', array('-- Select --', 'income' => 'Income', 'expense' => 'Expense'), $this->form['type']) ?>
            <span class="error"><?= $this->errors['type'] ?></span>
          </div>
          <div class="right">
            <?= form::submit('save', 'Save Changes', 'class="inline"') ?>
          </div>

        </fieldset>

        <?= form::close() ?>
      </div>

    </div>
    <div class="split-right">
    </div>
  </div>
</div>