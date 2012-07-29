<div id="view-topic">
  <div id="breadcrumbs">
    <div><?= html::anchor('forum', 'Forum Boards') ?> &raquo; <?= html::anchor('forum/'. $this->topic->forum_id, $this->topic->forum->title) ?> &raquo; <?= $this->topic->title ?></div>
  </div>

  <?= message::get() ?>

  <!-- View Topic -->
  <div class="view-topic">
    <div class="topic-header"><div class="topic-header-inner clearfix">
      <div class="author-photo">
        <?= html::anchor('profile/'. $this->topic->user->id, theme::image('tiny', $this->topic->user->picture(), array('class' => 'userphoto'))); ?>
      </div>
      <h3><?= $this->topic->title ?></h3>
      <div class="author-name">
        <span class="post-date">On <?php print date::display($this->topic->created, 'M d, Y g:i a')?></span> by <?= $this->topic->user->name(TRUE) ?>
        <?php if ($this->topic->like_count): ?>
        · <span class="likes"><?= html::thickbox_anchor('votes/show/topic/'. $this->topic->id, format::plural($this->topic->like_count, '@count like', '@count likes')) ?></span>
        <?php else: ?>
        · <span class="likes"><?= format::plural($this->topic->like_count, '@count like', '@count likes') ?></span>
        <?php endif ?>
        · <?php if ($this->topic->is_liked()): ?>
            <?= html::anchor('forum/topic/unlike/'. $this->topic->id, 'Unlike') ?>
          <?php else: ?>
            <?= html::anchor('forum/topic/like/'. $this->topic->id, 'Like') ?>
          <?php endif; ?>
      </div>

      <div class="topic-links">
        <?php if (A2::instance()->allowed($this->topic->forum, 'admin')): ?>
          <?php if ($this->topic->locked): ?>
            <?= html::anchor('forum/topic/unlock/'. $this->topic->id, 'Unlock Topic') ?>
          <?php else: ?>
            <?= html::anchor('forum/topic/lock/'. $this->topic->id, 'Lock Topic') ?>
          <?php endif ?>
        <?php endif ?>
        <?php if (A2::instance()->allowed($this->topic, 'edit')): ?>
          <?= html::anchor('forum/topic/edit/'. $this->topic->id, 'Edit Topic') ?>
        <?php endif ?>
        <?php if (A2::instance()->allowed($this->topic, 'edit') || A2::instance()->allowed($this->topic->forum, 'admin')): ?>
          <?= html::anchor('forum/topic/delete/'. $this->topic->id, 'Unpublish', array('class' => 'alert', 'title' => 'Are you sure you want to delete this topic?')) ?>
        <?php endif ?>
      </div>
    </div></div>
    <div class="topic-body">
      <p><?= format::html($this->topic->body); ?></p>
    </div>
    
    <?php if ($this->topic->poll->loaded): ?>
      <div id="topic-poll">
        <?php if ($this->vote->loaded || $this->user->has_role('root')): ?>
          <h3><?= $this->topic->poll->question; ?></h3>
          <div id="poll-results" class="clearfix">
            <?php foreach ($this->topic->poll->poll_choices as $choice): ?>
              <div class="choice clearfix">
                <?php if ( ( ! $this->topic->poll->private) || (A2::instance()->allowed($this->topic, 'edit') || A2::instance()->allowed($this->topic->forum, 'admin'))): ?>
                  <div class="bar"><?= $choice->percent(1) ?>% (<?= $choice->votes ? $choice->votes : 0 ?>)</div>
                <?php endif; ?>
                <div class="text">
                  <?= $choice->text ?>
                  <?php if ($this->vote->poll_choice_id == $choice->id): ?>
                    <span class="your-vote">(Your Vote)</span> &mdash; <?= html::anchor('forum/topic/unvote/'. $this->topic->id, 'remove vote') ?>
                  <?php endif ?>
                </div>
              </div>
            <?php endforeach ?>
            <?php if ( ( ! $this->topic->poll->private) || (A2::instance()->allowed($this->topic, 'edit') || A2::instance()->allowed($this->topic->forum, 'admin'))): ?>
              <div class="vote-count">
                <b>Total:</b>
                  <?php if (A2::instance()->allowed($this->topic, 'edit') || A2::instance()->allowed($this->topic->forum, 'admin')): ?>
                    <?= format::plural($this->topic->poll->votes, '@count vote', '@count votes') ?>
                    <?= html::thickbox_anchor('forum/topic/votes/'. $this->topic->id, '(show results)') ?>
                  <?php else: ?>
                    <?= format::plural($this->topic->poll->votes, '@count vote', '@count votes') ?>
                  <?php endif; ?> 
              </div>
            <?php endif; ?>
          </div>
        <?php else: ?>
          <h3><?= $this->topic->poll->question; ?></h3>
          <div id="poll-choices" class="clearfix">
            <?= form::open('forum/topic/vote') ?>
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
              <?= html::thickbox_anchor('file/original/'. $file->filename, $file->name, array('rel' => 'topic['. $this->topic->id .']')) ?>
            <?php else: ?>
              <?= html::anchor('file/original/'. $file->filename, $file->name) ?>
            <?php endif ?>
          </div>
        <?php endforeach ?>
      </div>
    <?php endif ?>

    <?php if ($this->topic->locked): ?>
      <div class="clearfix"><h3 class="locked">This topic has been locked. No more comments are allowed.</h3></div>
    <?php endif ?>
  </div>

  <!-- Comments -->
  <?php foreach ($this->topic->comments() as $comment): ?>
    <?php if ($this->last_viewed < $comment->created) { $this->new++; } else { $this->new = 0; } ?>
    <div id="<?= $this->new == 1 ? 'new' : '' ?>" class="comment depth-<?= strlen(preg_replace('/\./', '', $comment->thread)) ?>">
      <div id="comment-<?= $comment->id ?>" class="comment-header"><div class="comment-header-inner clearfix">
        <div class="author-photo"><?= html::anchor('profile/'. $comment->user->id, theme::image('tiny', $comment->user->picture(), array('class' => 'userphoto'))); ?></div>
        <div class="author-name">
          <?= $comment->user->name(TRUE) ?>
          <div class="post-date">
            <?= $this->last_viewed < $comment->created ? '<span class="new">New</span>' : '' ?> <em>On <?php print date::display($comment->created, 'M d, Y g:i a')?></em>
            <?php if ($comment->like_count): ?>
            · <span class="likes"><?= html::thickbox_anchor('votes/show/comment/'. $comment->id, format::plural($comment->like_count, '@count like', '@count likes')) ?></span>
            <?php else: ?>
            · <span class="likes"><?= format::plural($comment->like_count, '@count like', '@count likes') ?></span>
            <?php endif ?>
            · <?php if ($comment->liked): ?>
                <?= html::anchor('forum/comment/unlike/'. $comment->id, 'Unlike') ?>
              <?php else: ?>
                <?= html::anchor('forum/comment/like/'. $comment->id, 'Like') ?>
              <?php endif; ?>
          </div>
        </div>
        <div class="topic-links">
          <?php if (A2::instance()->allowed($comment, 'edit') || A2::instance()->allowed($this->topic->forum, 'admin')): ?>
            <?= html::anchor('forum/comment/edit/'. $comment->id, 'Edit Comment') ?>
          <?php endif ?>
          <?php if (A2::instance()->allowed($comment, 'delete') || A2::instance()->allowed($this->topic->forum, 'admin')): ?>
            <?= html::anchor('forum/comment/delete/'. $comment->id, 'Unpublish', array('class' => 'alert', 'title' => 'Are you sure you want to delete this comment?')) ?>
          <?php endif; ?>
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
                <?= html::thickbox_anchor('file/original/'. $file->filename, $file->name, array('rel' => 'comment['. $comment->id .']')) ?>
              <?php else: ?>
                <?= html::anchor('file/original/'. $file->filename, $file->name) ?>
              <?php endif ?>
            </div>
          <?php endforeach ?>
        </div>
      <?php endif ?>
    </div>
  <?php endforeach ?>
  
</div> <!-- /#View-Topic -->

<!-- Comment Form -->
<div id="comment-form">
  <?php if ($this->topic->locked): ?>
    <div class="clearfix"><h3 class="locked">This topic has been locked. No more comments are allowed.</h3></div>
  <?php else: ?>
    <h3>Add Comment</h3>
    <?= form::open('forum/topic/'. $this->topic->id, array('id' => 'comment-form-id')); ?>
    <div class="clearfix">
      <div class="split-left">
        <div class="clearfix">
          <?= form::textarea('body', $this->form['body']) ?>
          <?= form::submit('post', 'Post Comment'); ?>
          <!-- <div class="right form-help"><?= html::thickbox_anchor('help/general/content', 'Formatting guidelines.') ?></div> -->
        </div>
      </div>
      <div class="split-right">
        <div id="attachment" class="block">
          <?= form::hidden('key', $this->form['key']) ?>
          <div class="clearfix">
            <h3>Attachments</h3>
          </div>
          <div id="upload-wrapper" class="clearfix">
            <div id="upload-status"><?= html::image('images/loadingAnimation.gif') ?></div>
            <div id="upload-form"><?= form::upload(array('name' => 'attach')); ?> <?= form::button('upload', 'Attach', 'class="unbound"') ?> <!-- <em><?= html::thickbox_anchor('help/general/upload', 'File upload help.') ?></em> --></div>
          </div>
          <div id="attachments" class="attachments" class="uploaded-files pp_attachment">
            <?php if ($this->upload_error): ?>
              <div class="upload-error"><?= $this->upload_error ?></div>
            <?php endif ?>
            <?php foreach ($this->uploads as $upload): ?>
              <div class="file file-type-<?= $upload['extension'] ?>">
                <?= $upload['name'] ?>
              </div>
            <?php endforeach ?>
          </div>
        </div>
      </div>
    </div>
    <?= form::close(); ?>
  <?php endif ?>
</div>