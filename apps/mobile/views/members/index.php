<div id="members">
  <div class="clearfix">
    <div class="right" id="search">
      <?= form::open(Router::$routed_uri, array('method' => 'get')) ?>
      <?= form::input('q', $this->input->get('q'), 'size="14"') ?>
      <?= form::submit('search', 'Search') ?>
      <?php if ($this->input->get('q')): ?>
        <?= html::anchor(Router::$routed_uri, 'reset') ?>
      <?php endif ?>
      <?= form::close() ?>
    </div>
    <h2 class="title"><?= $this->title ?></h2>
  </div>
  
  <div class="secondary">
    <span><?= html::anchor('members/active', 'Actives') ?></span>
    <span><?= html::anchor('members/pledge', 'New Members') ?></span>
  </div>  
  
  <div class="items">
    <?php foreach ($this->members as $member): ?>
      <?php $current = substr($member->name(), 0, 1); ?>
      <?php if ($last != $current): ?>
        <div class="letter clearfix">
          <?= $current ?>
          <span class="right"><a href="#top">Top</span>
        </div>
      <?php endif ?>
      <div class="item">
        <div class="title"><?= html::anchor('members/'. $member->id, $member->name()) ?></div>
      </div>
      <?php $last = substr($member->name(), 0, 1); ?>
    <?php endforeach ?>
  </div>
</div>