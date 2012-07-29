<div id="crumbs" class="heading clearfix">
  <div class="right">
    <?= html::anchor('files/photos/album/upload/'. $this->album->id .'?KeepThis=true&TB_iframe=true&height=400&width=600&modal=true', 'Add photos', array('id' => 'add-photos', 'class' => 'thickbox')) ?>
  </div>  
  <?= html::anchor('files/photos', 'Chapter Photos') ?> &raquo; <?= $this->title ?> 
</div>

<?= message::get(); ?>

<div id="photos-album">    
  <div class="clearfix photos">
    <?php if ($this->photos->count()): ?>
      <?php foreach ($this->photos as $photo): ?>
        <div class="photo hoverable col-<?= text::alternate(1, 2, 3, 4, 5) ?>">
          <?= html::anchor('files/photos/album/photo/'. $photo->id, theme::image('thumbnail', $photo->filename)) ?>
        </div>
      <?php endforeach ?>
    <?php endif ?>
  </div>
  <div class="clearfix">
    <div class="right">
      <?= $this->pagination ?>
    </div>
    <p>
      Album created by <?= $this->album->user->name(TRUE) ?> <?= date::ago($this->album->created) ?>
      <?php if (A2::instance()->allowed($this->album, 'edit')): ?>
        <?= html::anchor('files/photos/album/edit/'. $this->album->id, '[edit album]') ?>
        <?= html::anchor('files/photos/album/delete/'. $this->album->id, '[delete album]', array('class' => 'alert delete', 'title' => 'Are you sure you want to delete this album and all of it\'s contents?')) ?>
      <?php endif ?>
    </p>
  </div
  
  <div class="clearfix">
    <?php if ($this->album->description): ?>
      <div class="help">
        <h3><?= $this->album->title ?></h3>
        <?= format::html($this->album->description) ?>
      </div>
    <?php endif ?>
  </div>
</div>