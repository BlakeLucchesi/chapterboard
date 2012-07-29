<div id="forum-notifications">
  <div class="heading clearfix">
    <h2><?= $this->title ?></h2>
  </div>

  <?= message::get(); ?>

  <div class="s">
    <p>Using the form below you can customize which calendars you would like to receive email notifications for.
      You can update your settings at any time to reduce or increase the number of notifications you receive via email.</p>
  </div>

  <div class="content">
      <?= form::open(); ?>
      <h3>Calendars</h3>
      <table>
      <?php if (count($this->calendars)): ?>
        <?php foreach ($this->calendars as $calendar): ?>
          <tr class="hoverable">
            <td><b><?= $calendar->title ?></b></td>
            <td><label><?= form::radio('calendar['. $calendar->id .']', 1, $this->notifications[$calendar->id]->value == 1 ? TRUE : FALSE) ?> New event added</label></td>
            <td><label><?= form::radio('calendar['. $calendar->id .']', 2, $this->notifications[$calendar->id]->value == 2 ? TRUE : FALSE) ?> New events and all comments</label></td>
            <td><label><?= form::radio('calendar['. $calendar->id .']', 0, $this->notifications[$calendar->id]->value ? FALSE : TRUE) ?> No emails</label></td>
          </tr>
        <?php endforeach ?>

      <?php else: ?>
        <tr>
          <td>Sorry, there are no calendars available to you.</td>
        </tr>
      <?php endif; ?>
      </table>
      <div class="form-item checkbox">
        <label>Email me when the time or location of an event I'm attending is changed: <?= form::checkbox('event_notify', 1, $this->user->event_notify) ?></label> &mdash; applies to all calendars.
      </div>
      <div class="right">
        <?= form::submit('submit', 'Save Notification Settings') ?> or <?= html::anchor('calendar', 'cancel') ?>
      </div>
      <?= form::close() ?>    
  </div>
</div>