<div class="heading clearfix">
  <h2><?= $this->title ?></h2>
  <ul>
    <li><?= html::anchor('files/folder/add?parent_id='. $this->folder->id, 'Add Folder') ?></li>
    <li><?= html::anchor('files/upload?object_id='. $this->folder->id, 'Upload File') ?></li>
  </ul>
</div>

<?= message::get(); ?>

<div class="clearfix">
  <div id="permissions" style="float: right; text-align: right;">*
    <?php if ($this->folder->national): ?>
      <strong>All Chapters</strong> can access this folder.
    <?php else: ?>
      <strong>Only Nationals</strong> can access this folder.
    <?php endif ?>
    <br /><?= html::anchor('files/folder/edit/'. $this->folder->id, 'Edit Folder and Permissions'); ?>
  </div>
  <div id="crumbs" class="filenav">
    <?= html::anchor('files', 'File Folders') ?> &raquo;
    <?php foreach($this->folder->path() as $id => $name): ?>
      <?= html::anchor('files/folder/'. $id, $name) ?> &raquo;
    <?php endforeach ?>
    <?= $this->folder->name ?>
  </div>
  
  <div id="files" class="clear-fix">
    <div class="split-left files">
      <?php if ($this->folder->children->count()): ?>
        <?php foreach ($this->folder->children as $subfolder): ?>
          <div class="folder admin-hover <?= $subfolder->national ? 'shared' : '' ?>">
            <div class="admin-links">
              <?= html::anchor('files/folder/delete/'. $subfolder->id, 'Unpublish', array('class' => 'delete alert', 'title' => 'Are you sure you wish to delete this folder and all of the files and folders inside of it?')) ?>
              <?= html::anchor('files/folder/edit/'. $subfolder->id, 'Edit', array('class' => 'edit', 'title' => 'Edit folder name and sharing.')) ?>
            </div>
            <div class="icon"></div>
            <h3><?= html::anchor('files/folder/'. $subfolder->id, $subfolder->name); ?></h3>
            <div><?= $subfolder->description ?>&nbsp;</div>
            <div class="updated"><?= date::ago($subfolder->updated) ?></div>
          </div>
        <?php endforeach ?>
      <?php endif ?>

      <?php if ($this->folder->files->count()): ?>
        <?php foreach ($this->folder->files as $file): ?>
          <div class="file file-type-<?= $file->type() ?> hoverable admin-hover">
            <div class="admin-links">
              <?= html::anchor('files/delete/'. $file->id, 'Unpublish', array('class' => 'delete alert', 'title' => 'Are you sure you wish to delete this file?')) ?>
              <?= html::anchor('files/edit/'. $file->id, 'Edit', array('class' => 'edit', 'title' => 'Edit name and/or move to a different folder.')) ?>
            </div>
            <div class="icon"></div>
            <h3><?= html::anchor($file->url(), $file->name); ?></h3>
            <p><?= $file->description; ?></p>
          </div>
        <?php endforeach ?>
      <?php endif; ?>
      <?php if ( ! ($this->folder->children->count() || $this->folder->files->count())): ?>
        <div class="folder empty"><p>There are no files in this folder.</p></div>
      <?php endif ?>
    </div>
    
    <div class="split-right">
    </div>
  </div>
</div>