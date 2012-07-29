<table>
  <thead>
    <tr>
      <th colspan="2">Name</th>
    </tr>
  </thead>
  <tbody>
    <?php if ($this->users->count()): ?>
      <?php foreach ($this->users as $user): ?>
        <tr class="hoverable">
          <td class="title"><?= $user->name(TRUE) ?></td>
          <td class="amount"><?= html::anchor('members/groups/remove/'. $this->group->id .'/'. $user->id, 'Remove') ?></td>
        </tr>
      <?php endforeach;?>
    <?php else: ?>
      <tr>
        <td colspan="2">No members belong to this group.</td>
      </tr>
    <?php endif ?>
  </tbody>
</table>

<div><center><em><?= format::plural($this->users->count(), '@count member', '@count members'); ?></em></center></div>