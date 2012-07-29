<div id="forum-admin">
  <div class="heading clearfix">
    <h3><?= $this->title ?></h3>
  </div>

  <?= message::get() ?>

  <div id="forum-form">
  <?= form::open(); ?>

      <div class="clearfix">
        <?= form::label('title', 'Title:')?>
        <?= form::input('title', $this->form['title']) ?>
        <span class="error"><?= $this->errors['title'] ?></span>
      </div>
      
      <div class="clearfix">
        <?= form::label('descripton', 'Description:')?>
        <?= form::input('description', $this->form['description']) ?>
        <span class="error"><?= $this->errors['key'] ?></span>
      </div>
      
      <?= form::submit('add', 'Add Forum') ?>
    </fieldset>
  <?= form::close(); ?>
  </div>
</div>