<div id="calendar-admin">
  <div class="heading clearfix">
    <h2><?= $this->title ?></h2>
    <ul>
      <li class="add-calendar"><?= html::thickbox_anchor('calendar/admin/add', 'Add New Calendar') ?></li>
    </ul>
  </div>

  <div class="help">
    <p>Only the groups selected for each calendar will have access to view and add events to that specific calendar.  Once you've made your selections be sure to save your changes.</p>
  </div>
  
  <?php echo message::get() ?>
  
  <?= form::open('calendar/admin'); ?>

  <table class="calendars clearfix">
    <?php $i = 0; ?>
    <?php foreach ($this->calendars as $calendar): ?>
      <?php if ($i % 3 == 0): ?>
        <tr>
      <?php endif ?>
      <td class="calendar">
        <div class="meta">
          <h3 class="title hoverable">
            <?= $calendar->title ?>
            <span class="ops"><?= html::thickbox_anchor('calendar/admin/edit/'. $calendar->id, 'edit &raquo;') ?></span>
          </h3>
        </div>
        <div class="groups">
          <?php foreach ($this->groups as $group): ?>
            <?php
            ?>
            <div class="group hoverable">
              <?= form::checkbox('groups['. $calendar->id .']['. $group->id .']', $group->id, $this->selected[$calendar->id][$group->id]) ?>
              <?= form::label('groups['. $calendar->id .']['. $group->id .']', $group->name) ?>
            </div>
          <?php endforeach ?>
        </div>
      </td>
      <?php if ($i %3 == 2): ?>
      </tr>
      <?php endif ?>
      <?php $i++; ?>
      
    <?php endforeach ?>
    </table>
    
    <?= form::submit('submit', 'Save Changes') ?>
    <?= form::close() ?>
  </div>

</div>