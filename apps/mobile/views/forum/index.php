<div id="forum">
  <h2 class="title">
    <div class="right">
      <span class="nav"><?= $this->link ?></span>
    </div>
    <?= $this->title ?>
  </h2>
  
  <div class="items">
    <?php foreach ($this->topics as $topic): ?>
      <div class="item boxed <?= $topic->is_new() ? 'new' : 'read' ?>">
        <div class="title"><?= html::anchor('forum/topic/'. $topic->id, $topic->title) ?></div>
        <div class="meta"><?= date::ago($topic->updated) ?> by <?= $topic->last_comment->user->name() ?></div>
      </div>
    <?php endforeach ?>
  </div>
  
  <?= $this->pagination; ?>
</div>