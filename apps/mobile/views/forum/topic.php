<div id="view-topic">

  <?= message::get() ?>

  <!-- View Topic -->
  <div id="topic" class="view-topic">
    <div class="topic-header"><div class="topic-header-inner clearfix">
      <div class="author-photo"><?= theme::image('tiny', $this->topic->user->picture()) ?></div>
      <h2><?= $this->topic->title ?></h2>
      <div class="post-date">On <?= date::display($this->topic->created, 'M d, Y g:i a')?> by <?= $this->topic->user->name() ?></div>
    </div></div>
    <div class="topic-body">
      <p><?= format::html($this->topic->body); ?></p>
    </div>
    
    <?php if ($this->topic->poll->loaded): ?>
      <div id="topic-poll">
        <h3><?= $this->topic->poll->question; ?></h3>
        <?php if ($this->vote->loaded): ?>
          <div id="poll-results" class="clearfix">
            <?php foreach ($this->topic->poll->poll_choices as $choice): ?>
              <div class="choice clearfix">
                <div class="bar"><?= $choice->percent(1) ?>%</div>
                <div class="text"><?= $choice->text ?> <?= $this->vote->poll_choice_id == $choice->id ? '<span class="your-vote">(Your Vote)</span>' : ''; ?></div>
              </div>
            <?php endforeach ?>
            <div class="vote-count"><b>Total:</b> <?= format::plural($this->topic->poll->votes, '@count vote', '@count votes') ?></div>
          </div>
        <?php else: ?>
          <div id="poll-choices" class="clearfix">
            <?= form::open('forum/vote') ?>
            <?= form::hidden('topic_id', $this->topic->id) ?>
            <?php foreach ($this->topic->poll->poll_choices as $choice): ?>
              <label><?= form::radio('choice_id', $choice->id, $this->vote->poll_choice_id == $choice->id ? 1 : 0) ?> <?= $choice->text ?></label>
            <?php endforeach ?>
            <?= form::submit('save', 'Save Vote') ?>
            <?= form::close() ?>
          </div>
        <?php endif ?>
      </div>
    <?php endif ?>
    
    <?php if ($this->topic->files->count()): ?>
      <div class="attachments">
        <h3>Attachments:</h3>
        <?php foreach ($this->topic->files as $file): ?>
          <div class="file file-type-<?= $file->extension ?>">
            <?php if (in_array($file->extension, array('jpg', 'jpeg', 'png', 'gif'))): ?>
              <?= html::thickbox_anchor('file/original/'. $file->filename, $file->name) ?>
            <?php else: ?>
              <?= html::anchor('file/original/'. $file->filename, $file->name) ?>
            <?php endif ?>
          </div>
        <?php endforeach ?>
      </div>
    <?php endif ?>

  </div>

  <!-- Comments -->
  <div id="comments">
    <?php foreach ($this->topic->comments() as $comment): ?>
      <div class="comment depth-<?= strlen(preg_replace('/\./', '', $comment->thread)) ?>">
        <div class="comment-header"><div class="comment-header-inner clearfix">
          <div class="author-photo"><?= theme::image('tiny', $comment->user->picture()); ?></div>
          <div class="author-name">
            <?= $comment->user->name() ?>
            <div class="post-date"><?= $this->last_viewed < $comment->created ? '<span class="new">New</span>' : '' ?> <em>On <?= date::display($comment->created, 'M d, Y g:i a')?></em></div>
          </div>
        </div></div>
        <div class="comment-body">
          <p><?= format::html($comment->body); ?></p>
        </div>
        <?php if ($comment->files->count()): ?>
          <div class="attachments">
            <h3>Attachments:</h3>
            <?php foreach ($comment->files as $file): ?>
              <div class="file file-type-<?= $file->extension ?>">
                <?php if (in_array($file->extension, array('jpg', 'jpeg', 'png', 'gif'))): ?>
                  <?= html::thickbox_anchor('file/original/'. $file->filename, $file->name) ?>
                <?php else: ?>
                  <?= html::anchor('file/original/'. $file->filename, $file->name) ?>
                <?php endif ?>
              </div>
            <?php endforeach ?>
          </div>
        <?php endif ?>
      </div>
    <?php endforeach ?>
  </div>
  
</div> <!-- /#View-Topic -->

<!-- Comment Form -->
<div id="comment-form">
  <h3>Add Comment</h3>
  <?php if ($this->topic->locked): ?>
    <p>Sorry, you may not leave comments.  This topic has been locked by the chapter administrator.</p>
  <?php else: ?>
    <?= form::open('forum/topic/'. $this->topic->id, array('id' => 'comment-form-id')); ?>
    <div class="clearfix">
      <?= form::textarea('body', $this->form['body']) ?>
      <?= form::submit('post', 'Post Comment'); ?>
    </div>
    <?= form::close(); ?>
  <?php endif ?>
</div>