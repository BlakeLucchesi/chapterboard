<div id="member-popup">
  <h2 class="title">
    <?php if ($this->object_type == 'recruit'): ?>
      <?= format::plural($this->votes->count(), Kohana::lang('recruitment.good_fit.singular'), Kohana::lang('recruitment.good_fit.plural')) ?>
    <?php else: ?>
      <?= format::plural($this->votes->count(), '@count member', '@count members') ?> like this
    <?php endif ?>
  </h2>
  <div class="clearfix">
    <?php foreach ($this->users as $user): ?>
      <div class="item clearfix">
        <div class="picture"><?= theme::image('tiny', $user->picture()) ?></div>
        <div class="body">
          <?= $user->name(TRUE) ?>
          <div class="number"><?= $user->type() ?></div>
        </div>
      </div>
    <?php endforeach ?>
  </div>
</div>