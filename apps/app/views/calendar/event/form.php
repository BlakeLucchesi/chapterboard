<div id="event-form">

    <div class="clearfix heading">
      <h2><?= $this->title ?></h2>
    </div>

    <?= message::get(); ?>
  
    <?= form::open(); ?>
    <div class="clearfix">
      <div class="split-left">
        <div class="clearfix">
          <?= form::label('title', 'Title:*')?>
          <?= form::input('title', $this->form['title'], 'class="normal"') ?>
          <span class="error title"><?= $this->errors['title'] ?></span>
        </div>
    
        <div class="clearfix">
          <?= form::label('calendar_id', 'Calendar:*')?>
          <?= form::dropdown('calendar_id', ORM::factory('calendar')->select_list(FALSE), $this->form['calendar_id']) ?>
          <span class="error calendar_id"><?= $this->errors['calendar_id'] ?></span>
        </div>

        <div class="clearfix">
          <?= form::label('location', 'Location:')?>
          <?= form::input('location', $this->form['location'], 'class="normal"') ?>
          <span class="error location"><?= $this->errors['location'] ?></span>
        </div>
    
        <div class="clearfix">
          <label>&nbsp;</label>
          <div class="checkbox">
          <?= form::checkbox('mappable', 1, $this->form['mappable']) ?>
          <?= form::label('mappable', 'Show a map of this location.')?>
          </div>
        </div>
        
        <div class="clearfix">
          <div class="clearfix"><label>Date and Time:</label></div>
          <?= form::input('start_day', $this->form['start_day'], 'class="date-pick"') ?>
          <?= form::input('start_time', $this->form['start_time'], 'class="time-pick time-start"') ?> to <?= form::input('end_time', $this->form['end_time'], 'class="time-pick time-end"') ?>
          <?= form::input('end_day', $this->form['end_day'], 'class="date-pick"') ?>

          <div class="error start"><?= $this->errors['start'] ?></div>
          <div class="error start_day"><?= $this->errors['start_day'] ?></div>
          <div class="error start_time"><?= $this->errors['start_time'] ?></div>
          <div class="error end_time"><?= $this->errors['end_time'] ?></div>
          <div class="error end_day"><?= $this->errors['end_day'] ?></div>
          <div class="error end"><?= $this->errors['end'] ?></div>
        </div>
        
        <div class="clearfix">
          <label><?= form::checkbox('all_day', 1, $this->form['all_day']) ?> All Day</label>
          <label class="repeats"><?= form::checkbox('repeats', 1, $this->form['repeats']) ?> Repeats...</label>
          <a href="#TB_inline?inlineId=repeat-popup&amp;width=380&amp;height=300&amp;modal=true" id="repeat-edit" class="thickbox">Edit</a>
        </div>
        
        <div class="errors clearfix">
          <div class="error"><?= $this->errors['period'] ?></div>
          <div class="error"><?= $this->errors['period_option'] ?></div>
          <div class="error"><?= $this->errors['until'] ?></div>
          <div class="error"><?= $this->errors['until_occurrences'] ?></div>
          <div class="error"><?= $this->errors['until_date'] ?></div>
        </div>
        
        <?php if ($this->event->parent_id || $this->event->repeats): ?>
          <div class="checkbox clearfix">
            <i><label><?= form::checkbox('apply_all', 1, $this->form['apply_all']) ?> Apply changes to all related events (This is a repeating event)</label></i>
          </div>  
        <?php endif ?>
                
        <div id="repeat-popup" class="hidden">
          <div id="repeat-content">
            <h3 class="title">Repeat Event</h3>
            <table>
              <tr>
                <td><?= form::label('period', 'Repeats:')?></td>
                <td>
                  <?= form::dropdown('period', array('daily' => 'Daily', 'weekly' => 'Weekly', 'monthly' => 'Monthly', 'yearly' => 'Yearly'), $this->form['period']) ?>
                </td>
              </tr>
              <tr id="period_option" class="">
                <td><label>Repeat by:</label></td>
                <td>
                  <div class="clearfix radios">
                    <p><label><?= form::radio('period_option', 'day_of_month', $this->form['period_option'] == 'day_of_month' ? TRUE : FALSE) ?> Day of month</label></p>
                    <p><label><?= form::radio('period_option', 'day_of_week', $this->form['period_option'] == 'day_of_week' ? TRUE : FALSE) ?> Day of week</label></p>                    
                  </div>
                </td>
              </tr>
              <tr>
                <td><?= form::label('ends', 'Ends:')?></td>
                <td>
                  <div id="until" class="clearfix radios">
                    <p><label><?= form::radio('until', 'occurrences', $this->form['until'] == 'occurrences' ? TRUE : FALSE) ?> After</label> <?= form::input('until_occurrences', $this->form['until_occurrences'], 'style="width:20px"') ?> occurrences</p>
                    <p><label><?= form::radio('until', 'date', $this->form['until'] == 'date' ? TRUE : FALSE) ?> Until </label><?= form::input('until_date', $this->form['until_date'], 'class="date-pick"') ?></p>
                  </div>
                </td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td>
                    <div class="button"><a href="#" id="repeat-done">Done</a></div>
                    <div class="button"><a href="#" id="repeat-cancel">Cancel</a></div>  
                </td>
              </tr>
            </table>
          </div>
        </div>

      </div>
      <div class="split-right">
        <div class="clearfix">
          <?= form::label('body', 'Event Details:')?>
          <?= form::textarea('body', $this->form['body'], 'class="mini"') ?>
          <div class="right">
            <?php if ($this->event->id): ?>
              <?= form::submit('submit', 'Save Changes', 'class="right"') ?> or <?= html::anchor('calendar/event/'. $this->event->id, 'cancel') ?>
            <?php else: ?>
              <?= form::submit('submit', 'Add Event', 'class="right"') ?>
            <?php endif ?>
          </div>

          <!-- <div class="form-help"><?= html::thickbox_anchor('help/general/content', 'Formatting guidelines.') ?></div> -->
        </div>
    
      </div>

    </div>
    <?= form::close(); ?>
</div>