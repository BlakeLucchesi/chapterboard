<div id="recruits">
  <h2 class="title"><?= $this->title ?></h2>
  <div class="secondary">
    <span><?= html::anchor('recruitment/active', 'Recruiting ('. $this->list_counts[0] .')') ?></span>
    <span><?= html::anchor('recruitment/bidded', 'Bidded ('. $this->list_counts[1] .')') ?></span>
  </div>
  
  <div class="items">
    <?php foreach ($this->recruits as $recruit): ?>
      <div class="item boxed">
        <div class="photo">
          <?= html::anchor('recruitment/'. $recruit->id, theme::image('tiny', $recruit->picture())) ?>
        </div>
        <div class="title"><?= html::anchor('recruitment/'. $recruit->id, $recruit->name) ?></div>
        <div class="meta"><?= $recruit->year ? $recruit->year : 'Unknown' ?></div>
      </div>
    <?php endforeach ?>
  </div>
</div>