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
            <?= form::submit('save', 'Add Category', 'class="inline"') ?>
          </div>

        </fieldset>

        <?= form::close() ?>
      </div>

      <div class="block">
        <table>
          <thead>
            <tr>
              <th>Income Categories</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($this->income_categories->count()): ?>
              <?php foreach ($this->income_categories as $category): ?>
                <tr>
                  <td class="admin-hover">
                    <div class="admin-links">
                      <?= html::anchor('budgets/categories/delete/'. $category->id, 'Delete', array('class' => 'delete alert', 'title' => 'Are you sure you wish to delete this budget category? (You will be prompted to reassign all past transactions to a new category so your old data will not be lost.)')) ?>
                      <?= html::anchor('budgets/categories/edit/'. $category->id, 'Edit', array('class' => 'edit', 'title' => 'Edit budget category name.')); ?>
                    </div>
                    <?= html::anchor('budgets/transactions/category/'. $category->id, $category->name); ?>
                  </td>
                </tr>
              <?php endforeach ?>
            <?php else: ?>
              <tr>
                <td colspan="2">Use the form above to add your first income category.</td>
              </tr>
            <?php endif ?>
          </tbody>
        </table>
      </div>
      
      <div class="block">
        <table>
          <thead>
            <tr>
              <th>Expense Categories</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($this->expense_categories->count()): ?>
              <?php foreach ($this->expense_categories as $category): ?>
                <tr>
                  <td class="admin-hover">
                    <div class="admin-links">
                      <?= html::anchor('budgets/categories/delete/'. $category->id, 'Delete', array('class' => 'delete alert', 'title' => 'Are you sure you wish to delete this budget category? (You will be prompted to reassign all past transactions to a new category so your old data will not be lost.)')) ?>
                      <?= html::anchor('budgets/categories/edit/'. $category->id, 'Edit', array('class' => 'edit', 'title' => 'Edit budget category name.')); ?>
                    </div>
                    <?= html::anchor('budgets/transactions/category/'. $category->id, $category->name); ?>
                  </td>
                </tr>
              <?php endforeach ?>
            <?php else: ?>
              <tr>
                <td colspan="2">Use the form above to add your first expense category.</td>
              </tr>
            <?php endif ?>
          </tbody>
        </table>
      </div>
    </div>
    <div class="split-right">
      <div class="help">
        <p>Categories are used to track expenses and non-dues income such as donations or fundraising. Most chapters create a budget category for each of their committees (recruitment, social, etc) and each of their operating fees (national membership fees, chapter insurance, etc.).</p>
      </div>
    </div>
  </div>
</div>