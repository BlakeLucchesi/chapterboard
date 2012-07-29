<div id="forum-admin" class="manage-admin">
  <div class="heading clearfix">
    <h2><?= $this->title ?></h2>
    <ul>
      <li class="add-forum"><?= html::thickbox_anchor('forum/admin/add', 'Add New Board') ?></li>
    </ul>
  </div>

  <div class="help">
    <p>Only the groups selected for each forum will have access to view and add topics and comments in that forum.  Once you've made your selections be sure to save your changes.</p>
  </div>
  
  <?= message::get() ?>
  
  <?= form::open('forum/admin'); ?>

  <table class="forums clearfix">
    <?php $i = 0; ?>
    <?php foreach ($this->forums as $forum): ?>
      <?php if ($i % 3 == 0): ?>
        <tr>
      <?php endif ?>
      <td class="forum">
        <div class="meta">
          <h3 class="title hoverable">
            <?= $forum->title ?>
            <span class="ops"><?= html::thickbox_anchor('forum/admin/edit/'. $forum->id, 'edit &raquo;') ?></span>
          </h3>
        </div>
        <div class="groups">
          <?php foreach ($this->groups as $group): ?>
            <?php
            ?>
            <div class="group hoverable">
              <?= form::checkbox('groups['. $forum->id .']['. $group->id .']', $group->id, $this->selected[$forum->id][$group->id]) ?>
              <?= form::label('groups['. $forum->id .']['. $group->id .']', $group->name) ?>
            </div>
          <?php endforeach ?>
        </div>
      </td>
      <?php if ($i %3 == 2): ?>
      </tr>
      <?php endif ?>
      <?php $i++; ?>
      
    <?php endforeach ?>
    </table>
    
    <?= form::submit('submit', 'Save Changes') ?>
    <?= form::close() ?>
  </div>

</div>