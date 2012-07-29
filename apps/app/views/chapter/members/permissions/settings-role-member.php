<div class="member hoverable">
  <?php echo $member->first_name .' '. $member->last_name; ?>
  <?php if ( ! ($member->id == $this->user->id && $role->key == 'admin')): ?>
    <span>
      <?php echo html::anchor('members/permissions?action=remove&role_key='. $role->key .'&user_id='. $member->id, 'Remove', array('class' => 'remove', 'role_key' => $role->key, 'user_id' => $member->id)) ?>
    </span>
  <?php else: ?>
    <span class="notice">You can't remove yourself.</span>
  <?php endif; ?>
</div>