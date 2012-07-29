<?php if ($this->upload_error): ?>
  <div class="upload-error"><?php echo $this->upload_error ?></div>
<?php endif ?>
<?php foreach ($this->uploads as $upload): ?>
  <div class="file file-type-<?= $upload['extension'] ?>">
    <?= $upload['name'] ?> - <?= html::anchor('upload/remove', 'Remove', array('class' => 'remove-file', 'filehash' => upload::filehash($upload['filepath']))) ?>
  </div>
<?php endforeach ?>