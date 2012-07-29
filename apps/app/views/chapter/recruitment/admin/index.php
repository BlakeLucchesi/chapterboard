<div id="recruitment-admin">
  <div class="heading clearfix">
    <h2><?= $this->title ?></h2>
    <ul>
      <li><?= html::anchor('recruitment/admin/add', 'Add List') ?></li>
    </ul>
  </div>

  <div class="help">
    <p>Use the form below to modify your chapter's recruitment lists. Don't forget to save your changes after you are done.</p>
  </div>

  <?= message::get() ?>

  <?= form::open() ?>
  <div id="recruitment-lists">
    <?php foreach ($this->lists as $list): ?>
      <div class="recruitment-list clearfix hoverable admin-hover" weight="<?= $list->weight ?>">
        <div class="handle"><?= html::image('minis/draggable.png') ?></div>
        <h3 class="name"><?= $list->name ?></h3>
        <div class="count">(<?= $list->recruits->count() ?> recruits)</div>
        <div class="admin-links">
          <?= html::anchor('recruitment/admin/remove-list/'. $list->id, 'Delete', array('class' => 'delete alert', 'title' => 'Remove the recruitment list: '. $list->name .'.')) ?>
          <?= html::anchor('recruitment/admin/clear-list/'. $list->id, 'Clear list', array('class' => 'clear-list alert', 'title' => 'Remove all recruits from the list: '. $list->name .'.')) ?>
        </div>
      </div>
    <?php endforeach ?>
  </div>
  <?= form::submit('save', 'Save Changes') ?>
  <?= form::close() ?>
</div>