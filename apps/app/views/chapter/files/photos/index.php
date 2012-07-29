<div class="heading clearfix">
  <h2><?= $this->title ?></h2>
  <ul>
    <li><?= html::anchor('files/photos/album/add', 'Create New Album') ?></li>
  </ul>
</div>

<div id="photos-album" class="clearfix">
  <div class="photos clearfix">
    <?php foreach ($this->albums as $album): ?>
      <div class="hoverable photo col-<?= text::alternate(1, 2, 3, 4, 5) ?>">
        <?= html::anchor('files/photos/album/'. $album->id, theme::image('thumbnail', $album->thumbnail)) ?>
        <div class="title"><?= html::anchor('files/photos/album/'. $album->id, $album->title) ?></div>
      </div>
    <?php endforeach ?>
  </div>
  <div class="right">
    <?= $this->pagination ?>
  </div>
</div>