<div class="heading clearfix">
  <h2><?= $this->title; ?></h2>
  <?php if (A2::instance()->allowed('file', 'manage')): ?>
    <ul>
      <li><?= html::anchor('files/folder/add', 'Add Folder') ?></li>
      <li><?= html::anchor('files/upload', 'Upload File') ?></li>
    </ul>    
  <?php endif ?>
</div>

<?= message::get() ?>

<div id="crumbs" class="filenav">
  Chapter File Folders &raquo;
</div>

<div id="files" class="clearfix">
  <div class="split-left files">
    <?php if ($this->folders->count()): ?>
      <?php foreach ($this->folders as $folder): ?>
        <?php if (A2::instance()->allowed($folder, 'view')): ?>
          <?php $count++; ?>
          <div class="folder hoverable admin-hover">
            <?php if (A2::instance()->allowed('file', 'manage')): ?>
              <div class="admin-links">
                <?= html::anchor('files/folder/delete/'. $folder->id, 'Delete', array('class' => 'delete alert', 'title' => 'Are you sure you wish to delete this folder and all of the files and folders inside of it?')) ?>
                <?= html::anchor('files/folder/edit/'. $folder->id, 'Edit', array('class' => 'edit', 'title' => 'Edit folder name and description.')) ?>
              </div>
            <?php endif; ?>
            <div class="icon"></div>
            <h3><?= html::anchor('files/folder/'. $folder->id, $folder->name); ?></h3>
            <p><?= $folder->description; ?>&nbsp;</p>
            <div class="updated"><?= date::ago($folder->updated) ?></div>
          </div>            
        <?php endif ?>
      <?php endforeach ?>
    <?php endif; ?>
    <?php if ($count < 1): ?>
      <?php if (A2::instance()->allowed('file', 'manage')): ?>
        <div class="folder empty">Please <?= html::anchor('files/folder/add', 'create a folder') ?> to get started.</div>
      <?php else: ?>
        <div class="folder empty">Your chapter has not uploaded any chapter documents yet.</div>
      <?php endif ?>
    <?php endif ?>
  </div>
  <div class="split-right files file-list">
    <h3 class="title">Recently Uploaded Documents</h3>
    <?php foreach ($this->recent as $file): ?>
      <?php if (A2::instance()->allowed($file->folder, 'view')): ?>
        <?php $recent_count++; ?>
        <div class="file file-type-<?= $file->type() ?>">
          <div class="icon"></div>
          <h4><?= html::anchor($file->url(), $file->name) ?></h4>
          <div class="file-updated"><?= date::ago($file->created) ?> in <?= html::anchor('files/folder/'. $file->folder->id, $file->folder->name) ?></div>
        </div>
      <?php endif ?>
    <?php endforeach ?>
    <?php if ($recent_count < 1): ?>
      <div class="folder empty"><div class="file-updated"><p>There are no recently uploaded documents.</p></div></div>
    <?php endif ?>
  </div>
</div>
