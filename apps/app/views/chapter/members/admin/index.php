<div id="member-admin" class="member-roster">
  <div class="heading clearfix">
    <h2><?= $this->title ?></h2>
  </div>

  <?= message::get() ?>

  <div class="clearfix">
    <div id="tabbed-section">
      <ul>
        <?php foreach ($this->groups as $group): ?>
          <li><?= html::anchor('members/admin/'. $group->static_key, $group->name .' ('. $this->type_count[$group->static_key] .')', array('class' => $this->type == $group->static_key ? 'active' : '')) ?></li>
        <?php endforeach ?>
        <li><?= html::anchor('members/admin/leadership', 'Leadership ('. $this->type_count['leadership'] .')', array('class' => $this->type == 'leadership' ? 'active' : '')) ?></li>
        <li><?= html::anchor('members/admin/archive', 'Archived ('. $this->type_count['archive'] .')', array('class' => $this->type == 'archived' ? 'active' : '')) ?></li>
      </ul>
    </div>
    
    <?= form::open() ?>
      <div id="members">
        <?php if ($this->members->count()): ?>
          <table>
            <thead>
              <tr>
                <th>Name</th>
                <th>Leadership Position</th>
                <th>Scroll # / Initiation Order</th>
                <th>Student ID #</th>
                <th class="right">Change Member Status</th>
              </tr>
            </thead>
            <tbody>
            <?php foreach ($this->members as $member): ?>
              <tr class="hoverable">
                <td class="title"><?= $member->name(TRUE) ?></td>
                <td><?= form::input($member->id .'[position]', $this->form[$member->id]['position'], 'class="medium"') ?></td>
                <td><?= form::input($member->id .'[scroll_number]', $this->form[$member->id]['scroll_number'], 'class="mini"') ?></td>
                <td><?= form::input($member->id .'[student_id]', $this->form[$member->id]['student_id'], 'class="small"') ?></td>
                <td class="right"><?= form::dropdown($member->id .'[type]', $this->status_options) ?></td>
              </tr>
            <?php endforeach;?>
            </tbody>
          </table>
          <div class="right">
            <?= form::submit('submit', 'Save Changes') ?> or <?= html::anchor(Router::$current_uri, 'cancel') ?>
          </div>
        <?php else: ?>
          <table>
            <thead>
              <tr>
                <th>Name</th>
                <th class="right">Change Member Status</th>
              </tr>
            </thead>
            <tbody>
            <tr><td><?= Kohana::lang('members.no_results.'. $this->type) ?></td></tr>
            </tbody>
          </table>
        <?php endif?>
      </div>
    <?= form::close() ?>
    
  </div>
</div>

<!-- // <?= html::anchor('members/admin/move/'. $member->id .'/alumni', 'Move to Alumni') ?> -->