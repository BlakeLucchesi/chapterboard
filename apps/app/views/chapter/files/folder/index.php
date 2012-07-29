<div class="heading clearfix">
  <h2><?= $this->title ?></h2>
  <?php if (A2::instance()->allowed('file', 'manage')): ?>
    <ul>
      <li><?= html::anchor('files/folder/add?parent_id='. $this->folder->id, 'Add Folder') ?></li>
      <li><?= html::anchor('files/upload?object_id='. $this->folder->id, 'Upload File') ?></li>
    </ul>
  <?php endif; ?>
</div>

<?= message::get(); ?>

<div class="clearfix">
  <div id="crumbs" class="filenav">
    <?= html::anchor('files', 'Chapter File Folders') ?> &raquo;
    <?php foreach ($this->folder->path() as $id => $name): ?>
      <?= html::anchor('files/folder/'. $id, $name) ?> &raquo;
    <?php endforeach; ?>
    <?= $this->folder->name ?>
  </div>
  
  <div id="files" class="clear-fix">
    <div class="split-left files">
      <?php if ($this->folder->children->count()): ?>
        <?php foreach ($this->folder->children as $subfolder): ?>
          <?php if (A2::instance()->allowed($subfolder, 'view')): ?>
            <?php $subfolders_count++; ?>
            <div class="folder admin-hover">
              <?php if (A2::instance()->allowed('file', 'manage')): ?>
                <div class="admin-links">
                  <?= html::anchor('files/folder/delete/'. $subfolder->id, 'Unpublish', array('class' => 'delete alert', 'title' => 'Are you sure you wish to delete this folder and all of the files and folders inside of it?')) ?>
                  <?= html::anchor('files/folder/edit/'. $subfolder->id, 'Edit', array('class' => 'edit')) ?>
                </div>              
              <?php endif; ?>
              <div class="icon"></div>
              <h3><?= html::anchor('files/folder/'. $subfolder->id, $subfolder->name); ?></h3>
              <p><?= $subfolder->description ?>&nbsp;</p>
              <div class="updated"><?= date::ago($subfolder->updated) ?></div>
            </div>            
          <?php endif ?>
        <?php endforeach ?>
      <?php endif ?>

      <?php if ($this->folder->files->count()): ?>
        <?php foreach ($this->folder->files as $file): ?>
          <div class="file file-type-<?= $file->type() ?> hoverable admin-hover">
            <?php if (A2::instance()->allowed('file', 'manage')): ?>
              <div class="admin-links">
                <?= html::anchor('files/delete/'. $file->id, 'Unpublish', array('class' => 'delete alert', 'title' => 'Are you sure you wish to delete this file?')) ?>
                <?= html::anchor('files/edit/'. $file->id, 'Edit', array('class' => 'edit', 'title' => 'Edit file name or move to another folder.')) ?>
              </div>
            <?php endif; ?>
            <div class="icon"></div>
            <h3><?= html::anchor($file->url(), $file->name); ?></h3>
            <p><?= $file->description; ?></p>
          </div>
        <?php endforeach ?>
      <?php endif; ?>
      <?php if ( ! ($subfolders_count || $this->folder->files->count())): ?>
        <div class="folder empty"><p>There are no files in this folder.</p></div>
      <?php endif ?>
    </div>
    
    <div class="split-right">
      <?php if (A2::instance()->allowed('file', 'manage')): ?>
        <h4>Accessible By:</h4>
        <div id="permissions">
          <ul><li><?= implode('</li><li>', $this->allowed_groups) ?></li></ul>
        </div>
        <?= html::anchor('files/folder/edit/'. $this->folder->id, 'Edit Folder and Permissions'); ?>
      <?php endif ?>
      
    </div>
  </div>
</div>