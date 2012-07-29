<div id="members-invite">
  <div class="heading clearfix">
    <h2><?= $this->title ?></h2>
  </div>

  <?= message::get() ?>
  
  <div class="clearfix">
    <div class="split-left">
      <fieldset>
        <?= form::open() ?>
        <?= form::hidden('group', 1) ?>

        <p><em>In order to join ChapterBoard, each member from your chapter must receive a unique invitation. Use the form below to invite your members.</em></p>
        
        <div class="clearfix">
          <?= form::label('group_id', 'Member Type:')?>
          <?= form::dropdown('group_id', $this->groups, $this->form['group_id']) ?>
          <span class="error"><?= $this->errors['group_id'] ?></span>
        </div>

        <div class="clearfix">
          <?= form::label('emails', 'Email Addresses:')?>
          <?= form::textarea('emails', $this->form['emails']) ?>
          <em>Separate email addresses by line breaks or commas.</em>
        </div>

        <div class="right"><?= form::submit('invite', 'Send Invitations') ?></div>
        <?= form::close() ?>    
        
      </fieldset>
    </div>
    
    <div class="split-right">
      <div class="block">
        <h3 class="title">
          <span class="right header-span"><?= html::anchor('members/invite/resend_all', 'Resend All') ?></span>
          Outstanding Invitations (<?= $this->invitations->count() ?>)
        </h3>
        <?php if ($this->invitations->count()): ?>
          <table class="sort">
            <thead><tr>
              <th>Email Address</th>
              <th>Type</th>
              <th class="{sorter: false}"></th>
              <th class="{sorter: false}"></th>
            </tr></thead>
          <?php foreach ($this->invitations as $invite): ?>
            <tr class="hoverable">
              <td><?= $invite->email ?></td>
              <td><?= ucwords(inflector::singular($invite->group->name)) ?></td>
              <td><?= html::anchor('members/invite/resend/'. $invite->id, 'Resend') ?></td>
              <td><?= html::anchor('members/invite/revoke/'. $invite->id, 'Revoke') ?></td>
            </tr>
          <?php endforeach ?>
          </table>
        <?php else: ?>
          <p>There are no outstanding invitations.</p>
        <?php endif ?>
      </div>
    </div>
  </div>
</div>