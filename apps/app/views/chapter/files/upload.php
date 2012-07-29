<div class="heading clearfix">
  <h2><?= $this->title; ?></h2>
</div>

<?= message::get() ?>

<div class="clearfix">
  <div class="split-left">
    <?= form::open_multipart(NULL, array('method' => 'POST')) ?>
      <fieldset class="clearfix">
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
      </fieldset>
    <?= form::close() ?>
  </div>
  <div class="split-right">
    <div class="help block">
      <p>Maximum file upload size is 20Mb. We only allow a limited set of file types for upload (shown below).  If you need a different file type we recommend you either: (1) zip the file/files or (2) if its a generic file type please send us a <a href="/support">support request</a>.</p>
      <p><b>File types:</b> <?= preg_replace('/,/i', ', ', Kohana::config('upload.types.default')) ?></p>
    </div>
  </div>
</div>
