<div id="topics">

  <div id="breadcrumbs">
    <div class="right">
      <?php if (A2::instance()->allowed($this->forum, 'admin') && $this->topics->count()): ?>
        <?= html::anchor('forum/archive/'. $this->forum->id, 'Archive Topics', array('class' => 'delete alert', 'title' => 'Are you sure you want to archive all topics in this forum?')) ?>
      <?php endif ?>
    </div>
    <div><?= html::anchor('forum', 'Forum Boards') ?> &raquo; <?= $this->forum->title ?></div>
  </div>

  <div class="heading clearfix">
    <h2><?= $this->title; ?></h2>
    <ul>
      <li><?= html::anchor('forum/topic/add?forum_id='. $this->forum->id, 'Post New Topic') ?></li>
    </ul>
    <div class="topic-pager">
      <?= $this->pagination ?>
    </div>
  </div>
  
  <?= message::get() ?>
  
  <div class="topics">
    <?php if ( ! $this->topics->count()): ?>
      <div class="topic">
        <h3 class="title">Be the first to <?= html::anchor('forum/topic/add?forum_id='. $this->forum->id, 'post a topic in this forum') ?>.</h3>
      </div>
    <?php endif ?>
    <?php foreach ($this->topics as $topic): ?>
      <?php $i++; ?>
      <div class="hoverable topic <?= $topic->sticky ? 'sticky' : '' ?> <?= $topic->is_new() ? 'new' : '' ?> hoverable admin-hover clearfix">
        <div class="icon">&nbsp;</div>
        <h3 class="title"><?= html::anchor('forum/topic/'. $topic->id .'#new', ucfirst($topic->title), array('class' => '')) ?></h3>
        <div class="meta clearfix">
          <span class="author">By: <?= $topic->user->name(TRUE) ?></span><br />
          <span class="comments"><?= $topic->comment_count ?> Comments</span>
          · <span class="likes"><?= format::plural($topic->like_count, '@count like', '@count likes') ?></span>
          · <span class="updated">Last updated <?= date::ago($topic->updated) ?></span>
        </div>
        <?php if ($this->admin): ?>
          <div class="admin-links">
            <?= html::anchor('forum/topic/delete/'. $topic->id, 'Unpublish', array('class' => 'delete alert', 'title' => 'Are you sure you wish to delete this topic?')) ?>
            <?php if ($topic->sticky): ?>
              <?= html::anchor('forum/topic/unsticky/'. $topic->id, 'Unsticky', array('class' => 'unsticky', 'title' => 'Remove sticky.')); ?>
            <?php else: ?>
              <?= html::anchor('forum/topic/sticky/'. $topic->id, 'Sticky', array('class' => 'sticky', 'title' => 'Make sticky to the top.')); ?>
            <?php endif ?>
          </div>
        <?php endif ?>
      </div>
    <?php endforeach ?>
  </div>
  <div class="bottom-pager">
    <?= $this->pagination ?>
  </div>
</div>