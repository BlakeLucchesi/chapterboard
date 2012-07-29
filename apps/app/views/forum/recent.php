<div id="topics">

  <div id="breadcrumbs">
    <div><?= html::anchor('forum', 'Forum Boards') ?> &raquo; <?= $this->title ?></div>
  </div>

  <div class="heading clearfix">
    <h2><?= $this->title; ?></h2>
    <ul>
      <li><?= html::anchor('forum/topic/add', 'Post New Topic') ?></li>
    </ul>
    
    <div class="topic-pager">
      <?= $this->pagination ?>
    </div>
  </div>
  
  <?= message::get() ?>
  
  <?php if ($this->topics->count()): ?>
    <?php foreach ($this->topics as $topic): ?>
      <?php $i++; ?>
      <div class="topic <?= $topic->sticky ? 'sticky' : '' ?> <?= $topic->is_new() ? 'new' : '' ?> hoverable admin-hover">
        <h3 class="title"><?= html::anchor('forum/topic/'. $topic->id, ucfirst($topic->title), array('class' => '')) ?></h3>
        <div class="meta clearfix">
          <span class="author">By: <?= $topic->user->name(TRUE) ?></span><br />
          <span class="comments"><?= $topic->comment_count ?> Comments</span>
          <span class="updated">Last post <?= date::ago($topic->updated) ?></span>
        </div>
        <?php if ($this->admin): ?>
          <div class="admin-links">
            <?= html::anchor('forum/topic/delete/'. $topic->id, 'Unpublish', array('class' => 'delete alert', 'title' => 'Are you sure you wish to delete this topic?')) ?>
          </div>
        <?php endif ?>
      </div>
    <?php endforeach ?>
  <?php else: ?>
    <div class="topic clearfix">
      <h3>There are no recent topics.</h3>
    </div>
  <?php endif ?>
  <div class="bottom-pager">
    <?= $this->pagination ?>
  </div>
</div>
