<div id="service">
  <div id="service-form" class="heading clearfix">
    <h2><?= $this->title ?></h2>
  </div>

  <?= message::get(); ?>      

  <div class="form">
    <?= form::open(); ?>
    <fieldset>
      <div id="event-select">
        <?php if ($this->errors['event_id']): ?><div class="error"><?= $this->errors['event_id'] ?></div><?php endif; ?>
        <div class="clearfix">
          <?= form::label('event_id', 'Event:')?>
          <?= form::dropdown('event_id', array('' => '') + ORM::factory('service_event')->select_list(), $this->form['event_id']) ?>
            <?php if ($this->form['title'] || $this->form['date']): ?>
              <div><?= html::anchor('#', '- Select Existing Event', array('id' => 'event-toggle', 'class' => 'create-event')) ?></div>
            <?php else: ?>
              <div><?= html::anchor('#', '+ Create New Event', array('id' => 'event-toggle')) ?></div>
            <?php endif ?>
        </div>
      </div>
      <div id="event-create" class="<?= $this->form['title'] || $this->form['date'] ? '' : 'hidden' ?>">
        <div class="clearfix">
          <?= form::label('title', 'Event Title:')?>
          <?= form::input('title', $this->form['title']) ?>
          <span class="error"><?= $this->errors['title'] ?></span>
        </div>
        <div class="clearfix">
          <?= form::label('date', 'Date:')?>
          <?= form::input('date', $this->form['date'], 'class="mini date-pick"') ?>
          <span class="error"><?= $this->errors['date'] ?></span>
        </div>
      </div>
      <br>
      <?php if (A2::instance()->allowed('service_event', 'admin')): ?>
        <div class="clearfix">
          <?= form::label('user_id', 'Member:')?>
          <?= form::dropdown('user_id', $this->select_members, $this->form['user_id'] ? $this->form['user_id'] : $this->user->id ) ?>
          <span class="error user_id"><?= $this->errors['user_id'] ?></span>
        </div>
      <?php else: ?>
        <?= form::hidden('user_id', $this->form['user_id'] ? $this->form['user_id'] : $this->user->id) ?>
        <?php if ($this->record->id): ?>
          <div class="clearfix">
            <label>Member:</label> <?= $this->record->user->name(); ?>
          </div>
        <?php endif ?>
      <?php endif ?>
      
      <div class="clearfix">
        <?= form::label('hours', 'Service Hours:')?>
        <?= form::input(array('name' => 'hours', 'class' => 'small'), $this->form['hours']) ?>
        <span class="form-tip" title="The hours you spent contributing to the community service project."><?= html::image('minis/information.png'); ?></span>
        <span class="error hours"><?= $this->errors['hours'] ?></span>
      </div>
      <div class="clearfix">
        <?= form::label('dollars', 'Dollars Donated:')?>
        $ <?= form::input(array('name' => 'dollars', 'class' => 'small amount'), $this->form['dollars']) ?>
        <span class="form-tip" title="The amount of money you donated to the event/charity, this usually includes buying shirts or raffle tickets."><?= html::image('minis/information.png'); ?></span>
        <span class="error dollars"><?= $this->errors['dollars'] ?></span>
      </div>
      <div class="clearfix">
        <?= form::label('notes', 'Notes:')?>
        <?= form::textarea('notes', $this->form['notes']) ?>
        <span class="error"><?= $this->errors['notes'] ?></span>
      </div>
      
      
      <?php if ($this->record->id): ?>
        <?= form::submit('submit', 'Save Changes') ?>
      <?php else: ?>
        <?= form::submit('submit', 'Record Activity') ?>
      <?php endif ?>
      or 
      <?php if ($this->event->id): ?>
        <?= html::anchor('service/events/'. $this->event->id, 'Cancel') ?>
      <?php else: ?>
        <?= html::anchor('service', 'Cancel') ?>
      <?php endif ?>
      
    </fieldset>
    <?= form::close(); ?>
  </div>
</div>