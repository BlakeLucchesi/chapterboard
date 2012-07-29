<div class="heading clearfix">
  <h2><?= $this->title ?></h2>
  <ul>
    <li><?= html::anchor('budgets/create', 'Create Budget') ?></li>
  </ul>
</div>

<?= message::get(); ?>

<div id="budgets" class="clearfix">
  <div class="split-left">
    <table>
      <thead>
        <tr>
          <th>Budget Name</th>
          <th class="right"></th>
        </tr>
      </thead>
      <tbody>
        <?php if ($this->budgets->count()): ?>
          <?php foreach ($this->budgets as $budget): ?>
            <tr>
              <td class="title admin-hover">
                <span class="admin-links">
                  <?= html::anchor('budgets/delete/'. $budget->id, 'Delete', array('class' => 'delete alert', 'title' => 'Are you sure you wish to delete this budget? If there are member charges or transactions for this budget, you will be asked to reassign them to a different budget before you can delete this budget.')) ?>
                  <?= html::anchor('budgets/edit/'. $budget->id, 'Edit', array('class' => 'edit', 'title' => "Edit the name of this budget..")) ?>
                </span>
                <?= html::anchor('budgets/'. $budget->id, $budget->name) ?>
              </td>
              <td class="right"><?= html::anchor('budgets/transactions/'. $budget->id, 'View Transactions') ?></td>
            </tr>
          <?php endforeach ?>
        <?php else: ?>
          <tr>
            <td>There are no budgets available.  <?= html::anchor('budgets/create', 'Create your first budget') ?>.</td>
          </tr>
        <?php endif ?>
      </tbody>
    </table>
  </div>
  
  <div class="split-right">
    <!-- <div class="help">
      <p>This is help text that should help you figure out what is going on with budgets.</p>
    </div> -->
  </div>
</div>