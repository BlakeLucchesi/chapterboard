<div id="calendar-admin">
  <div class="heading clearfix">
    <h3>Rename Calendar</h3>
  </div>
  
  <?= message::get(); ?>
  
  <div id="calendar-edit-form">
    <?= form::open('calendar/admin/rename') ?>

    <div class="clearfix">
      <?= form::hidden('calendar_id', $this->calendar->id) ?>
      <?= form::input('title', $this->form['title']) ?>
      <?= form::submit('save', 'Save Changes'); ?>
      <span class="error"><?= $this->errors['title'] ?></span>
    </div>    

    <?= form::close() ?>
  </div>
  
  <div id="calendar-delete-form">
    <div class="heading clearfix">
      <h3>Remove Calendar</h3>
    </div>
    <?= form::open('calendar/admin/delete') ?>
    <?= form::hidden('calendar_id', $this->form['id']) ?>
    <div class="notice"><?= form::checkbox('confirm', 'true') ?>
     I acknowledge that by removing this calendar all events associated with this calendar will also be deleted.</div>
    <div class="clearfix">
      <?= form::submit('delete', 'Remove Calendar') ?>
    </div>
    <?= form::close() ?>
  
  </div>
  
  
</div>
