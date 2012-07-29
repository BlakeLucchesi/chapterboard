<div id="role-<?= $role->key ?>" class="permission-block">
  <div class="heading icon">
    <h3><?= $role->name; ?></h3>
  </div>
  <fieldset>
    <div class="clearfix">
      <div class="split-left permission-description">
        <?= text::auto_p($role->description); ?>
      </div>
      <div class="split-right">
        <?= form::open('members/permissions', array('class' => 'ajax '. $role->key)) ?>
          <?= form::hidden('role_key', $role->key); ?>
          <?= form::hidden('action', 'add'); ?>
          <?= form::dropdown('user_id', $this->members, '') ?>
          <?= form::submit('submit', 'Add') ?>
        <?= form::close() ?>
        <div class="members">  
          <?php foreach ($users as $member): ?>
            <?= View::factory('members/permissions/settings-role-member')->set('member', $member)->set('role', $role) ?>
          <?php endforeach ?>
        </div>
      </div>
    </div>
  </fieldset>
</div>