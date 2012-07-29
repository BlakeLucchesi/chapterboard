<div class="heading clearfix">
  <h2><?= $this->title; ?></h2>
</div>

<?= message::get() ?>

<div id="crumbs" class="filenav">
  National File Folders &raquo;
</div>

<div id="files" class="clearfix">
  <div class="split-left">
    <div class="files file-list">
      <?php if ($this->folders->count()): ?>
        <?php foreach ($this->folders as $folder): ?>
          <div class="folder hoverable">
            <div class="icon"></div>
            <h3><?= html::anchor('files/nationals/folder/'. $folder->id, $folder->name); ?></h3>
            <p><?= $folder->description; ?></p>
          </div>
        <?php endforeach ?>
      <?php else: ?>
        <div class="folder empty">Your national organization has not yet added any files.</div>
      <?php endif ?>
    </div>    
  </div>
  <div class="split-right">
    
  </div>
</div>
