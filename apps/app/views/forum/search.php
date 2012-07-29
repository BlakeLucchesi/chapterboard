<div id="search-results">
  <h2>Forum Search Results</h2>
  <div class="clearfix">
    <div class="right"><?= $this->pagination ?></div>
    <div class="result-count">
      <?= sprintf('%d-%d of %d', $this->total_count > 0 ? $this->start + 1 : 0, $this->start + $this->count, $this->response->response->numFound) ?> matches.
      Sorted by: 
      <?php if ($_GET['sort'] == 'score'): ?>
        <?= html::anchor('forum/search?q='. $_GET['q'], 'Most Recent') ?> | <strong>Best Match</strong>
      <?php else: ?>
        <strong>Most Recent</strong> | <?= html::anchor('forum/search?sort=score&q='. $_GET['q'], 'Best Match') ?>
      <?php endif ?>
    </div>
  </div>
  <div id="topics" class="clearfix">
    <?php if ( ! count($this->response->response->docs)): ?>
      <div class="topic clearfix">
        <?php if ($this->search_down): ?>
          <h3>Sorry, our search server is currently unavailable.</h3>
          <div>We're working to get it back online as soon as possible.</div>
        <?php else: ?>
        <h3>Sorry no results found.</h3>
        <?php endif ?>
      </div>
    <?php endif ?>
    <?php foreach ($this->response->response->docs as $row): ?>
      <?php $topic = $this->topics[$row->object_id]; ?>
      <div class="hoverable topic <?= $topic->sticky ? 'sticky' : '' ?> <?= $topic->is_new() ? 'new' : '' ?> hoverable admin-hover clearfix">
        <h3 class="title"><?= html::anchor('forum/topic/'. $topic->id .'#new', ucfirst($topic->title), array('class' => '')) ?></h3>
        <div class="matched-text"><?= $this->response->highlighting->{$row->id}->body[0] ? $this->response->highlighting->{$row->id}->body[0] : $this->response->highlighting->{$row->id}->comments[0] ?></div>
        <div class="meta clearfix">
          <span class="author">By: <?= $topic->user->name(TRUE) ?></span><br />
          <span class="comments"><?= $topic->comment_count ?> Comments</span>
          · <span class="likes"><?= format::plural($topic->like_count, '@count like', '@count likes') ?></span>
          · <span class="updated">Last updated <?= date::ago($topic->updated) ?></span>
        </div>
      </div>
    <?php endforeach ?>    
  </div>

  <div class="right"><?= $this->pagination ?></div>
</div>