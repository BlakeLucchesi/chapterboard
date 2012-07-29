<div class="heading clearfix">
  <h2><?= $this->title ?></h2>
</div>

<?= message::get() ?>

<fieldset>
  <?= form::open() ?>
  
    <div class="clearfix">
      <?= form::label('name', 'Name:*')?>
      <?= form::input('name', $this->form['name']) ?>
      <span class="error"><?= $this->errors['name'] ?></span>
    </div>

    <div class="clearfix">
      <?= form::label('description', 'Description:')?>
      <?= form::input('description', $this->form['description']) ?>
      <span class="error"><?= $this->errors['description'] ?></span>
    </div>
  
    <div class="clearfix">
      <?= form::label('parent_id', 'Parent Folder:*')?>
      <?= form::dropdown('parent_id', $this->folders, $this->form['parent_id']) ?>
      <span class="error"><?= $this->errors['parent_id'] ?></span>
    </div>

    <div class="clearfix checkbox">
      <label>Available to all chapters: <?= form::checkbox('national', TRUE, $this->form['national']) ?></label> &nbsp;(if not selected, this folder will only be available to national office members)
    </div>
    <?php if ($this->form['id']): ?>
      <?= form::submit('submit', 'Save Changes') ?> or <?= html::anchor('files/folder/'. $this->form['id'], 'cancel') ?>
    <?php else: ?>
      <?= form::submit('submit', 'Add Folder') ?> or <?= html::anchor('files', 'cancel') ?>
    <?php endif ?>

  <?= form::close() ?>
</fieldset>