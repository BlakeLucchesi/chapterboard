<div id="forum-admin">
  <div class="heading clearfix">
    <h3>Rename Forum</h3>
  </div>
  
  <?= message::get(); ?>
  
  <div id="forum-edit-form">
    <?= form::open('forum/admin/rename') ?>

    <div class="clearfix">
      <?= form::hidden('forum_id', $this->forum->id) ?>
      <?= form::label('title', 'Title:') ?>
      <?= form::input('title', $this->form['title']) ?>
      <span class="error"><?= $this->errors['title'] ?></span>
    </div>
    <div class="clearfix">
      <?= form::label('description', 'Description:')?>
      <?= form::input('description', $this->form['description']) ?>
      <span class="error"><?= $this->errors['description'] ?></span>
      <?= form::submit('save', 'Save Changes'); ?>
    </div>
    <?= form::close() ?>
  </div>
  
  <div id="forum-delete-form">
    <div class="heading clearfix">
      <h3>Remove Forum</h3>
    </div>
    <?= form::open('forum/admin/delete') ?>
    <?= form::hidden('forum_id', $this->form['id']) ?>
    <div class="notice"><?= form::checkbox('confirm', 'true') ?>
     I acknowledge that by removing this forum all topics associated with this forum will also be deleted.</div>
    <div class="clearfix">
      <?= form::submit('delete', 'Remove Forum') ?>
    </div>
    <?= form::close() ?>
  
  </div>
  
  
</div>
