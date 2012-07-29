<div class="heading clearfix">
  <h2><?= $this->title ?></h2>
</div>

<?= message::get() ?>

<div class="clearfix">
  <div class="split-left">
    <fieldset>
      <?= form::open() ?>
        <div class="clearfix">
          <?= form::label('title', 'Title:*')?>
          <?= form::input('title', $this->form['title']) ?>
          <span class="error"><?= $this->errors['title'] ?></span>
        </div>
        <div class="clearfix">
          <?= form::label('description', 'Description:')?>
          <span class="error"><?= $this->errors['description'] ?></span>
          <?= form::textarea('description', $this->form['description']) ?>
        </div>
        <div class="right">
          <?php if ($this->album->loaded): ?>
            <?= form::submit('submit', 'Save Changes') ?> of <?= html::anchor('files/photos/album/'. $this->album->id, 'cancel') ?>
          <?php else: ?>
            <?= form::submit('submit', 'Create Album') ?> or <?= html::anchor('files/photos', 'cancel') ?>
          <?php endif ?>
        </div>
      <?= form::close() ?>
  </div>
</div>