<div class="heading clearfix">
  <h2><?= $this->title; ?></h2>
</div>

<?= message::get() ?>

<fieldset class="clearfix">
  <?= form::open_multipart(NULL, array('method' => 'POST')) ?>

  <div class="split-left">
    <div class="clearfix">
      <?= form::label('file', 'File:*')?>
      <?= form::upload('file', $this->form['file']) ?>
      <span class="error"><?= $this->errors['file'] ?></span>
    </div>

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
      <?= form::label('object_id', 'Folder:*')?>
      <?= form::dropdown('object_id', $this->folders, $this->form['object_id']) ?>
      <span class="error"><?= $this->errors['object_id'] ?></span>
    </div>
    <?= form::submit('submit', 'Upload Document') ?>
  </div>

  <?= form::close() ?>
</fieldset>