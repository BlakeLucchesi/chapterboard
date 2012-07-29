<div id="member-popup">
  <h2 class="title"><?= $this->title ?> <span class="header-span"><?= format::plural($this->users->count(), '@count member', '@count members') ?></span></h2>
  <div class="clearfix">
    <?php foreach ($this->users as $user): ?>
      <div class="item clearfix">
        <div class="picture"><?= theme::image('tiny', $user->picture()) ?></div>
        <div class="body">
          <?= $user->name(TRUE) ?>
          <div class="number"><?= $user->phone() ?></div>
        </div>
      </div>
    <?php endforeach ?>
  </div>
</div>