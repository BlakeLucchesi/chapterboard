<div id="manage-groups">
  <div class="heading clearfix">
    <h2><?= $this->title ?></h2>
  </div>
  
  <?= message::get(); ?>
  
  <div class="clearfix">
    <div class="split-left">
      <table>
        <thead>
          <tr>
            <th>Name</th>
            <th class="right">Members</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($this->groups as $group): ?>
            <tr>
              <td><?= html::anchor('members/groups/'. $group->id, $group->name) ?></td>
              <td class="right amount"><?= $group->users->count() ?></td>
              <td class="right amount"><?= html::anchor('members/groups/destroy/'. $group->id, 'Delete', array('title' => 'Are you sure you want to delete this group and any permissions you have configured with it?', 'class' => 'alert')) ?></td>
            </tr>
          <?php endforeach ?>
        </tbody>
      </table>
    </div>
    <div class="split-right">
      <?= form::open() ?>
      <fieldset>
        <h3>Add New Group</h3>
        <div class="clearfix">
          <?= form::label('name', 'Name:*')?>
          <?= form::input('name', $this->form['name'], 'class="normal"') ?>
          <span class="error title"><?= $this->errors['name'] ?></span>
        </div>        
        <?= form::submit('submit', 'Add Group') ?>
      </fieldset>
      <?= form::close() ?>

    </div>
  </div>
  
</div>