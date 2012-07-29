<?= $this->user->help('forum') ?>

<div id="forums">
  <div class="heading clearfix">
    <h2><?= $this->title ?></h2>
    <ul>
      <li><?= html::anchor('forum/topic/add', 'Post New Topic') ?></li>
    </ul>
  </div>
  
  <?= message::get(); ?>
  
  <div class="forums">
    <?php if (count($this->forums)): ?>
      <?php foreach ($this->forums as $forum): ?>
        <div class="forum hoverable clearfix <?= $forum->has_unread_topics($this->user->id) ? 'unread' : ''; ?>">
          <div class="title">
            <div class="icon">&nbsp;</div>
            <h3><?= html::anchor('forum/'. $forum->id, $forum->title) ?></h3>
            <div class="description"><?= $forum->description ?></div>
          </div>
          <?php if ($forum->last_updated->loaded): ?>
            <div class="last-post">
              Last post by:<br />
              <?= $forum->last_updated->user->name() ?> <?= date::ago($forum->last_updated->created) ?>
            </div>
          <?php endif ?>
        </div>
      <?php endforeach ?>      
    <?php else: ?>
      <div class="forum hoverable clearfix">
        <div class="title">
          <h3>Sorry, there are no forums available to you.</h3>
        </div>
      </div>
    <?php endif ?>
  </div>
</div>