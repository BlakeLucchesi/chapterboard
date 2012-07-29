<div class="heading clearfix">
  <h2><?= $this->title ?></h2>
</div>

<?= message::get() ?>

<div class="clearfix">
  <div class="split-left">
    <?= form::open() ?>
    <fieldset>
      <div class="clearfix">
        <?= form::label('name', 'Name:')?>
        <?= form::input('name', $this->form['name'], 'class="medium"') ?>
        <span class="error"><?= $this->errors['name'] ?></span>
      </div>
    </fieldset>
    <div class="right">
      <?php if ($this->budget->id): ?>
        <?= form::submit('save', 'Save Changes') ?> or <?= html::anchor('budgets/'. $this->budget->id, 'cancel') ?>
      <?php else: ?>
        <?= form::submit('create', 'Create Budget') ?> or <?= html::anchor('budgets', 'cancel') ?>
      <?php endif ?>
    </div>
    <?= form::close() ?>
  </div>
  <div class="split-right">
    <div class="help">
      Chapter budgets should be created for time-periods such as an academic semester or quarter.  We recommend naming your budgets based on the year and academic period they are for.  Example: "<?= date('Y') ?> Winter Semester".
    </div>
  </div>
</div>