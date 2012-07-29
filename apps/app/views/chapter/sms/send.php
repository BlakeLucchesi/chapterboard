<div id="send-sms">
  <div class="heading clearfix">
    <h2 class="title"><?= $this->title ?></h2>
  </div>
  <?= message::get(); ?>
  
  <?= form::open() ?>
    <fieldset>
      <div class="split-left">
        <div class="clearfix">
          <div id="text-count" class="right">
            <span>110</span> characters left.
          </div>
          <?= form::label('message', 'Message:')?>
          <?= form::textarea('message', $this->form['message']) ?>
          <p><em>Instead of using a group tag (ex: @actives) select the group(s) you want to message from the list on the right. Also, your contact info will automatically be inserted at the end of the message.</em></p>
        </div>
      </div>
      <div class="split-right">
        <div class="checkbox">
          <strong>Groups:</strong>
          <?php foreach ($this->groups as $group): ?>
            <div><label><?= form::checkbox('groups['. $group->id .']', $group->id, $this->form['groups'][$group->id]) ?> <?= $group->name ?></label></div>
          <?php endforeach ?>
        </div>
        <div>
          <?= form::submit('submit', 'Send Message') ?> or <?= html::anchor('sms', 'cancel') ?>
        </div>
      </div>
    </fieldset>
  <?= form::close() ?>
</div>