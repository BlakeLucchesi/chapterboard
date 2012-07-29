<div id="crumbs">
  <?= html::anchor('messages', 'Inbox') ?> &raquo; Send Message
</div>

<div id="messages" class="heading clearfix">
  <h2>Send Message</h2>
</div>

<?= message::get() ?>

<div class="message-form clearfix">
  <fieldset>
    <?= form::open() ?>
      <div class="split-left">
        <div class="clearfix">
          <?= form::label('members', 'Members:')?>
          <?= form::input('members') ?>
          <span class="error"><?= $this->errors['members'] ?></span>
        </div>
        <div class="clearfix">
          <?= form::label('subject', 'Subject:')?>
          <?= form::input('subject', $this->form['subject']) ?>
          <span class="error"><?= $this->errors['subject'] ?></span>
        </div>
        <div class="clearfix">
          <?= form::label('body', 'Message:')?> <span class="error"><?= $this->errors['body'] ?></span>
          <?= form::textarea('body', $this->form['body']) ?>
        </div>
        <div class="clearfix">
          <?= form::submit('submit', 'Send Message'); ?> or <?= html::anchor('messages', 'cancel') ?>
        </div>
      </div>
      <div class="split-right groups">
        <h3>Send to Group:</h3>
        <?php foreach ($this->groups as $group): ?>
          <div class="clearfix">
            <label><?= form::checkbox('groups['. $group->id .']', $group->id, $this->form['groups'][$group->id]) ?> <?= $group->name ?> (<?= $group->users->count() ?>)</label>
          </div>
        <?php endforeach ?>
      </div>
    <?= form::close() ?>
  </fieldset>
</div>