<div id="calendar-admin">
  <div class="heading clearfix">
    <h3><?= $this->title ?></h3>
  </div>

  <?= message::get() ?>

  <div id="calendar-form">
  <?= form::open(); ?>

      <div class="clearfix">
        <?php echo form::label('title', 'Calendar Title:')?>
        <?php echo form::input('title', $form['title']) ?>
        <span class="error"><?php echo $errors['title'] ?></span>
      </div>
  
      <?= form::submit('add', 'Add Calendar') ?>
    </fieldset>
  <?= form::close(); ?>
  </div>
</div>