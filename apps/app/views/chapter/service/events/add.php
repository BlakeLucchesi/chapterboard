<div id="service">
  <div class="heading clearfix">
    <h2><?= $this->title ?></h2>
  </div>
  
  <?= message::get() ?>
  
  <div class="clearfix">
    
    <div class="split-left form">
      <?= form::open() ?>
      <fieldset>
        <div class="clearfix">
          <?= form::label('title', 'Title:')?>
          <?= form::input('title', $this->form['title']) ?>
          <div class="form-error"><?= $this->errors['title'] ?></div>
        </div>
        <div class="clearfix">
          <?= form::label('date', 'Event Date:')?>
          <?= form::input('date', date::display($this->form['date'], 'm/d/Y'), 'class="date-pick"') ?>
          <span class="error"><?= $this->errors['date'] ?></span>
        </div>
      </fieldset>
      <?php if ($this->event->id): ?>
        <?= form::submit('submit', 'Save Changes') ?> of <?= html::anchor('service/events/'. $this->event->id, 'Cancel') ?>
      <?php else: ?>
        <?= form::submit('submit', 'Add Event') ?>
      <?php endif ?>
      <?= form::close() ?>
    </div>

  </div>
  
</div>