<div id="member-popup">
  <div class="clearfix">
    <div class="right"><em>*Only forum administrators and the topic poster can see these poll results.</em></div>
    <h2 class="title"><?= $this->title ?></h2>
  </div>
  <?php foreach ($this->topic->poll->poll_choices as $option): ?>
    <div class="block">
      <h3 class="title"><?= $option->text ?> (<?= $option->poll_votes->count() ?>)</h3>
      <div class="members clearfix">
        <?php foreach ($option->poll_votes as $vote): ?>
          <div class="item clearfix">
            <div class="picture"><?= theme::image('tiny', $vote->user->picture()) ?></div>
            <div class="body">
              <?= $vote->user->name(TRUE) ?>
              <div class="number"><?= $vote->user->type() ?></div>
            </div>
          </div>
        <?php endforeach ?>
      </div>
    </div>  
  <?php endforeach ?>
</div>