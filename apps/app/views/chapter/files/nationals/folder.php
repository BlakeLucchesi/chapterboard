<div class="heading clearfix">
  <h2><?= $this->title ?></h2>
</div>

<?= message::get(); ?>

<div class="clearfix">
  <div id="crumbs" class="filenav">
    <?= html::anchor('files/nationals', 'National File Folders') ?> &raquo;
    <?php $parent = $this->folder->parent; ?>
    <?php if ($parent->loaded): ?>
      <?= html::anchor('files/nationals/folder/'. $parent->id, $parent->name) ?> &raquo;
    <?php endif ?>
    <?= $this->folder->name ?>
  </div>
  
  <div class="clear-fix">
    <div id="files" class="split-left">
      <?php if ($this->folder->children->count()): ?>
        <?php foreach ($this->folder->children as $subfolder): ?>
          <?php if ($subfolder->national): ?>
            <div class="folder">
              <div class="icon"></div>
              <h3><?= html::anchor('files/nationals/folder/'. $subfolder->id, $subfolder->name); ?></h3>
              <div><?= $subfolder->description ?></div>
            </div>            
          <?php endif ?>
        <?php endforeach ?>
      <?php endif ?>

      <?php if ($this->folder->files->count()): ?>
        <?php foreach ($this->folder->files as $file): ?>
          <div class="file file-type-<?= $file->type() ?> hoverable">
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