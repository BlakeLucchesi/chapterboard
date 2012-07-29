<div id="sms-index" class="clearfix">

  <div id="sms-log" class="split-left">
    <div class="heading clearfix">
      <h2 class="title"><?= $this->title ?></h2>
      <ul><li><?= html::anchor('sms/send', 'Send Message') ?></li></ul>
    </div>
    
    <?php if ( ! $this->messages->count()): ?>
      <div style="margin-top: 25px"><div class="message">Use the number to the right to send your first message.</div></div>
    <?php else: ?>
      <?php foreach ($this->messages as $message): ?>
        <div class="item">
          <div class="message"><?= $message->message ?></div>
          <div class="sender">
            Sent to <?= join(', ', $message->groups) ?>
            <?php if ($message->status): ?>
              (<?= format::plural($message->send_count, '@count member', '@count members') ?>)
            <?php else: ?>
              (processing&hellip;)
            <?php endif ?>
            by <?= $message->user->name(TRUE) ?> on <?= date::display($message->created, 'F d, Y') ?>
          </div>
        </div>
      <?php endforeach ?>
    <?php endif ?>
    <?= $this->pagination ?>
  </div><!-- .split-left -->
  
  <div class="split-right">
    
    <div id="sms-number" class="block">
      <p>Send text messages to the following number:</p>
      <p class="number">(415) 800-3041</p>
      <p class="smaller">Save this number in your phone book to send texts super fast!</p>
    </div>
    
    <div class="groups block">
      <h3>Messaging groups <span class="right header-span"><a href="#TB_inline?width=600&amp;inlineId=sms-help" class="thickbox">Help &amp; Tips</a></span></h3>
      <table>
        <?php foreach ($this->groups as $group): ?>
          <tr>
            <td><?= $group->name ?><br />@<span class="inline-edit" group_id="<?= $group->id ?>"><?= $group->sms_key ?></span></td>
            <td class="right"><?= html::thickbox_anchor('members/popup/'. $group->id, format::plural($group->users->count(), '@count member', '@count members')) ?></td></tr>
        <?php endforeach ?>          
      </table>
      
      <div id="sms-help" class="block" style="display: none;">
        <div id="help">
          <h3 class="title">Sending messages to groups</h3>
          <p>Did you know that you can send text messages to members based on the groups they belong to on ChapterBoard? Just include the group tag at the beginning of your text message and only members in the group will receive your message.</p>
          <strong>Examples:</strong>
          <p>@actives Tonight's game has been rescheduled to 7:00pm. See you all there!<br /><em>(sent only to members in the active group)</em></p>
          <p>@actives @pledges Check in at the Greek Week booth today so we get points - let’s win this thing!<br /><em>(sent only to members in the active group and pledge group)</em></p>

          <h3>Changing Group Tags</h3>
          <p>The group tags (@actives, @alumni, etc.) are auto-created by ChapterBoard, but don’t worry, you can change them! To change a group tag, just click on the tag (highlighted in yellow) and enter in a new tag name for that group.</p>
          <p>When changing a group tag, remember to keep the tag short, simple, and something everyone can remember. Also, if you change a group tag, be sure to notify the other chapter members who have permission to send text messages.</p>

          <h3>Messaging Permissions</h3>
          <p>If you want to edit the members who have permission to send text messages, just click on “<?= html::anchor('members/permissions#role-sms', 'Permissions') ?>” and add or remove members. Only text messages we receive from these members will be relayed to your chapter. Note: Only your chapter's administrators can change these permissions.</p>
        </div>
      </div>
    </div>

    <div class="block">
      <h3>
        Members allowed to send messages
        <?php if (A2::instance()->allowed($this->site, 'admin')): ?><span class="right header-span"><?= html::anchor('members/permissions#role-sms', 'Permissions') ?><?php endif; ?></span>
      </h3>
      <div id="sms-users" class="content">
        <?php if ( ! $this->users->count()): ?>

        <?php else: ?>
          <div class="clearfix">
            <?php foreach ($this->users as $user): ?>
              <div class="item clearfix">
                <div class="picture"><?= theme::image('tiny', $user->picture()) ?></div>
                <div class="body">
                  <?= $user->name(TRUE) ?>
                  <div class="number"><?= $user->phone() ?></div>
                </div>
              </div>
            <?php endforeach ?>                
          </div>
          <div><em>* Messages must be sent from the numbers shown above.</em></div>
        <?php endif ?>
      </div>
    </div>
  </div><!-- .split-right -->

</div>

<?php
$js = <<<EOT
  $().ready(function() {
    $('.inline-edit').editable('/sms/update', {
      submit: "Save",
      cancel: "Cancel",
      tooltip: "Click to edit...",
      indicator : "<img src='/images/throbber.gif'>",
      submitdata: function() {
        return {id: $(this).attr('group_id') }
      },
      onsubmit: function() {
        var value = $(this).find('input').val();
        if (value.length < 3 || value.match(/[^a-zA-Z0-9]/)) {
          alert("The group tag you entered is invalid. Group tags must be at least 3 characters long and cannot contain any symbols. Please try again.");
        }
      }
    });
  });
EOT;
javascript::add($js, 'inline');
?>
