<div id="event-form">
  <div class="heading clearfix">
    <h2><?= $this->title ?></h2>
  </div>

  <div class="help">
    Announcements will be posted on your chapter's dashboard and will also be emailed out to every member of the groups selected. If your members respond back to your announcement you will receive the response via email.
  </div>

  <?= message::get() ?>
  
  <?= form::open() ?>
  <div class="clearfix">
    <div class="split-left">
      <div class="clearfix">
        <?= form::label('title', 'Title*:') ?>
        <?= form::input('title', $this->form['title']) ?>
        <div class="error"><?= $this->errors['title'] ?></div>
      </div>
      
      <div class="clearfix post-until element">
        <?= form::label('post_until', 'Post until*:') ?>
        <?= form::input('post_until', $this->form['post_until'], 'class="date-pick"') ?>
        <div class="error"><?= $this->errors['post_until'] ?></div>
      </div>

      <div class="groups clearfix element">
        <?php foreach ($this->options as $group): ?>
          <div class="group clearfix">
            <label><?= is_null($first) ? 'Visible to*:' : ''; $first = FALSE; ?></label>
            <div class="checkbox">
              <label><?= form::checkbox('groups['. $group->id .']', $group->id, $this->form['groups'][$group->id]) ?> <?= $group->name ?></label>
            </div>
          </div>
        <?php endforeach ?>
        <div class="error"><label></label><?= $this->errors['groups'] ?></div>
      </div>
      
    </div>
    <div class="split-right">
      <?= form::label('message', 'Message*:') ?>
      <div class="error"><?= $this->errors['message'] ?></div>
      <?= form::textarea('message', $this->form['message']) ?>
      <div class="right"><?= form::submit('post', 'Post Announcement'); ?></div>
      <div class="form-help">Plain text formatting only.</div>
    </div>
  </div>

  <?= form::close() ?>

</div>