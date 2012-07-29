<div id="finances">
  <div class="heading clearfix">
    <h2><?= $this->title ?></h2>
  </div>

  <div class="clearfix">
    <div class="split-left">
      <?= form::open() ?>
      <div class="help">Please click the send button below to send reminders to the following members who have an outstanding balance:</div>
      <fieldset>
        <ul>
        <?php foreach ($this->members as $member): ?>
          <li><?= $member->name ?></li>
        <?php endforeach ?>
        </ul>
      </fieldset>
      <div class="right">
        <?= form::submit('send', 'Send Reminder') ?> or <?= html::anchor('finances/members', 'cancel') ?>
      </div>
      <?= form::close(); ?>
    </div>
    <div class="split-right">
      <div class="help">
        <p><strong>Note:</strong> We personalize the content of each email to include the member's name, their current outstanding balance and the charge due date. If you have online collections enabled they will be provided a link to login to their account and prompted to pay online.</p>
      </div>
    </div>
  </div>  
</div>